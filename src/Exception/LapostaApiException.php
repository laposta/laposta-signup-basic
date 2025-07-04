<?php

namespace Laposta\SignupBasic\Exception;

class LapostaApiException extends BaseException {

    /**
     * @var \Throwable|null
     */
    private $originalException;

    public function __construct(\Throwable $originalException)
    {
        $this->originalException = $originalException;
        parent::__construct($originalException->getMessage(), $originalException->getCode(), $originalException);
    }

    /**
     * Get the original exception that was wrapped
     *
     * @return \Throwable|null
     */
    public function getOriginalException(): ?\Throwable
    {
        return $this->originalException;
    }
}
