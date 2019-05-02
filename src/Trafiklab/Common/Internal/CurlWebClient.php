<?php

namespace Trafiklab\Common\Internal;

use Trafiklab\Common\Model\Exceptions\RequestTimedOutException;

class CurlWebClient implements WebClient
{
    private const CACHE_TTL_SECONDS = 15;  // Cache validity in seconds
    private const CURL_TIMEOUT_SECONDS = 10;

    private $_userAgent;
    private $_cache;

    public function __construct($userAgent)
    {
        $this->_userAgent = $userAgent;
        $this->_cache = CacheImpl::getInstance();
    }

    function makeRequest(string $endpoint, array $parameters): WebResponse
    {
        // Url-encode parameters
        $urlEncodedKeyValueStrings = [];
        foreach ($parameters as $key => $value) {
            $urlEncodedKeyValueStrings[] = $key . '=' . urlencode($value);
        }

        // Construct the URL
        $url = $endpoint;
        if (!empty($urlEncodedKeyValueStrings)) {
            $url .= '?' . join('&', $urlEncodedKeyValueStrings);
        }

        // Check if the url is cached.
        if ($this->_cache->contains($url)) {
            $webResponse = $this->_cache->get($url);
            $webResponse->setIsFromCache(true);
            return $webResponse;
        }

        // Create curl resource
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_USERAGENT, $this->_userAgent);
        // Set the url
        curl_setopt($ch, CURLOPT_URL, $url);
        // Return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Limit the timeout
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CURL_TIMEOUT_SECONDS); //timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, self::CURL_TIMEOUT_SECONDS); //timeout in seconds

        // $output contains the output string
        $output = curl_exec($ch);

        // Check the exit status of CURL.
        $curlErrorCode = curl_errno($ch);
        switch ($curlErrorCode) {
            // The request timed out
            case CURLE_OPERATION_TIMEDOUT:
                throw new RequestTimedOutException($url, self::CURL_TIMEOUT_SECONDS);
                break;

            // More exception/error cases should be handled here.
            case 0:
            default:
                // Get the HTTP response code and create the response object.
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $response = new WebResponse($url, $parameters, $this->_userAgent, $httpCode, $output);

                // close curl resource to free up system resources
                curl_close($ch);

                // Cache the response
                $this->_cache->put($url, $response, self::CACHE_TTL_SECONDS);
                return $response;
        }
    }
}