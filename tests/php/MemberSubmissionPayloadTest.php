<?php

use Laposta\SignupBasic\Service\MemberSubmissionPayload;

lsb_test('spam checking stays enabled without browser proof', function (): void {
    $payload = MemberSubmissionPayload::build(
        '203.0.113.9',
        'visitor@example.com',
        'https://customer.example/signup',
        'Mozilla/5.0',
        ['email' => 'visitor@example.com'],
        null,
        null
    );

    lsb_assert_same([
        'upsert' => true,
        'spam_check' => true,
    ], $payload['options']);
    lsb_assert_same(false, array_key_exists('subscribe_token', $payload));
    lsb_assert_same(false, array_key_exists('subscribe_pow', $payload));
});

lsb_test('empty browser proof is omitted without forcing token checks', function (): void {
    $payload = MemberSubmissionPayload::build(
        '203.0.113.9',
        'visitor@example.com',
        'https://customer.example/signup',
        'Mozilla/5.0',
        ['email' => 'visitor@example.com'],
        '',
        ''
    );

    lsb_assert_same([
        'upsert' => true,
        'spam_check' => true,
    ], $payload['options']);
    lsb_assert_same(false, array_key_exists('subscribe_token', $payload));
    lsb_assert_same(false, array_key_exists('subscribe_pow', $payload));
});

lsb_test('valid browser proof is forwarded under API parameter names', function (): void {
    $payload = MemberSubmissionPayload::build(
        '203.0.113.9',
        'visitor@example.com',
        'https://customer.example/signup',
        'Mozilla/5.0',
        ['email' => 'visitor@example.com'],
        'signed-token',
        '42'
    );

    lsb_assert_same('signed-token', $payload['subscribe_token']);
    lsb_assert_same('42', $payload['subscribe_pow']);
});
