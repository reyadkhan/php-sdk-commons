<?php

namespace Trafiklab\Common\Model\Exceptions;

use Exception;
use Throwable;

class QuotaExceededException extends Exception
{
    public function __construct(string $api, string $key, string $details = "", Throwable $previous = null)
    {
        parent::__construct("Key '$key' for '$api' has exceeded its quota. $details", 429, $previous);
    }
}
