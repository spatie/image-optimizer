<?php

namespace Spatie\ImageOptimizer\Exceptions;

use Exception;

class UnableToGetRestrictedSizeException extends Exception
{
    /** @var Context */
    private $context;

    public function __construct(
        string $message,
        int $code = 0,
        Exception $previous = null,
        Context $context = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->context = $context;
    }

    public function context(): Context
    {
        return $this->context;
    }
}
