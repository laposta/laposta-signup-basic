<?php

namespace Laposta\SignupBasic\Service;

final class ApiErrorClassifier
{
    const FORM_SPAM_REJECTION_CODE = 211;

    public static function isFormSpamRejection($error)
    {
        return is_array($error)
            && ($error['type'] ?? '') === 'request_failed'
            && (int) ($error['code'] ?? 0) === self::FORM_SPAM_REJECTION_CODE;
    }
}
