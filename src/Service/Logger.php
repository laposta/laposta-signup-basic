<?php

namespace Laposta\SignupBasic\Service;

class Logger
{
    /**
     * @var bool
     */
    protected static $isEnabled = false;

    protected static $messagePrefix = '[Laposta Signup Basic] ';

    /**
     * @return bool
     */
    public static function isEnabled(): bool
    {
        return self::$isEnabled;
    }

    /**
     * @param bool $isEnabled
     */
    public static function setIsEnabled(bool $isEnabled): void
    {
        self::$isEnabled = $isEnabled;
    }

    /**
     * @return string
     */
    public static function getMessagePrefix(): string
    {
        return self::$messagePrefix;
    }

    /**
     * @param string $messagePrefix
     */
    public static function setMessagePrefix(string $messagePrefix): void
    {
        self::$messagePrefix = $messagePrefix;
    }

    /**
     * @param string $message
     * @param array|\Throwable $data
     *
     * @return void'
     */
    public static function logError(string $message, $data = []): void
    {
        if (self::$isEnabled) {
            $dataString = '';
            if ($data instanceof \Throwable) {
                $e = $data;
                $data = [
                    'error' => [
                        'code' => $e->getCode(),
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTrace(),
                    ],
                ];
            }
            if (is_array($data) && $data && function_exists('json_encode')) {
                $dataString = json_encode($data);
                if ($dataString) {
                    $dataString = ', data: '.$dataString;
                }
            }

            $message = self::$messagePrefix.'error: '.$message.$dataString;
            error_log($message);
        }
    }
}