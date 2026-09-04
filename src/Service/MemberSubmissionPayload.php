<?php

namespace Laposta\SignupBasic\Service;

final class MemberSubmissionPayload
{
    public static function build(
        $ip,
        $email,
        $sourceUrl,
        $userAgent,
        array $customFields,
        $token,
        $pow
    ) {
        $payload = [
            'ip' => $ip,
            'email' => $email,
            'source_url' => $sourceUrl,
            'user_agent' => $userAgent,
            'custom_fields' => $customFields,
            'options' => [
                'upsert' => true,
                'spam_check' => true,
            ],
        ];

        if (is_string($token) && $token !== '') {
            $payload['subscribe_token'] = $token;
        }
        if (is_string($pow) && $pow !== '') {
            $payload['subscribe_pow'] = $pow;
        }

        return $payload;
    }
}
