<?php

use Laposta\SignupBasic\Service\ApiErrorClassifier;

lsb_test('form spam rejection code 211 is safe to mask as success', function (): void {
    lsb_assert_same(true, ApiErrorClassifier::isFormSpamRejection([
        'type' => 'request_failed',
        'code' => 211,
        'message' => 'Submission rejected by the spam check, no subscriber was created',
    ]));
});

lsb_test('string form spam rejection code 211 is safe to mask as success', function (): void {
    lsb_assert_same(true, ApiErrorClassifier::isFormSpamRejection([
        'type' => 'request_failed',
        'code' => '211',
    ]));
});

lsb_test('other API errors are not masked as success', function (): void {
    lsb_assert_same(false, ApiErrorClassifier::isFormSpamRejection([
        'type' => 'invalid_input',
        'code' => 211,
    ]));
    lsb_assert_same(false, ApiErrorClassifier::isFormSpamRejection([
        'type' => 'request_failed',
        'code' => 210,
    ]));
    lsb_assert_same(false, ApiErrorClassifier::isFormSpamRejection(null));
});
