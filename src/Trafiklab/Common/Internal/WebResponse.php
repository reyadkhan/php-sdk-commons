<?php


namespace Trafiklab\Common\Internal;


class WebResponse
{
    private $_responseCode;
    private $_body;
    private $_url;
    private $_userAgent;
    private $_parameters;
    private $_isFromCache;

    public function __construct(string $url, array $parameters, string $userAgent, int $responseCode, $body)
    {
        $this->_url = $url;
        $this->_responseCode = $responseCode;
        $this->_body = $body;
        $this->_parameters = $parameters;
        $this->_userAgent = $userAgent;
    }


    /**
     * The HTTP body received from the server.
     * @return mixed The HTTP response code received from the server.
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * The HTTP response code received from the server.
     * @return int The HTTP response code received from the server.
     */
    public function getResponseCode(): int
    {
        return $this->_responseCode;
    }

    /**
     * The request parameters which were used to make this request.
     * @return array The request parameters which were used to make this request.
     */
    public function getRequestParameters(): array
    {
        return $this->_parameters;
    }

    /**
     * Retrieve one of the request parameters which were used to make this request.
     *
     * @param string $name The parameter to retrieve.
     *
     * @return mixed One of the request parameters which were used to make this request.
     */
    public function getRequestParameter(string $name)
    {
        if (key_exists($name, $this->getRequestParameters())) {
            return $this->_parameters[$name];
        } else {
            return null;
        }
    }

    /**
     * The user agent which was used to make this request.
     * @return string The user agent which was used to make this request.
     */
    public function getRequestUserAgent(): string
    {
        return $this->_userAgent;
    }

    /**
     * The URL to which the request was made.
     * @return string The URL to which the request was made.
     */
    public function getUrl(): string
    {
        return $this->_url;
    }

    /**
     * @return bool Whether or not this response is served from cache.
     */
    public function isFromCache(): bool
    {
        return $this->_isFromCache;
    }

    /**
     * @param $isFromCache bool True if this response is served from cache.
     */
    public function setIsFromCache(bool $isFromCache): void
    {
        $this->_isFromCache = $isFromCache;
    }

}