<?php

require dirname(__DIR__, 2).'/autoload.php';
require __DIR__.'/bootstrap.php';
require __DIR__.'/FormSpamLoggingTest.php';
require __DIR__.'/MemberSubmissionPayloadTest.php';
require __DIR__.'/ApiErrorClassifierTest.php';

exit($GLOBALS['lsb_test_failures'] > 0 ? 1 : 0);
