<?php

$GLOBALS['lsb_test_failures'] = 0;

function lsb_test(string $name, callable $test): void
{
    try {
        $test();
        echo "PASS: {$name}\n";
    } catch (\Throwable $e) {
        $GLOBALS['lsb_test_failures']++;
        fwrite(STDERR, "FAIL: {$name}: {$e->getMessage()}\n");
    }
}

function lsb_assert_same($expected, $actual): void
{
    if ($expected !== $actual) {
        throw new \RuntimeException(
            'Expected '.var_export($expected, true).', got '.var_export($actual, true)
        );
    }
}
