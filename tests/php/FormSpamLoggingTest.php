<?php

namespace Laposta\SignupBasic\Service {
    function error_log(string $message): bool
    {
        $GLOBALS['lsb_logged_messages'][] = $message;

        return true;
    }
}

namespace {
    use Laposta\SignupBasic\Container\Container;
    use Laposta\SignupBasic\Controller\FormController;
    use Laposta\SignupBasic\Exception\LapostaApiException;
    use Laposta\SignupBasic\Service\Logger;

    if (!defined('LAPOSTA_SIGNUP_BASIC_TEMPLATE_DIR')) {
        define('LAPOSTA_SIGNUP_BASIC_TEMPLATE_DIR', dirname(__DIR__, 2).'/templates');
    }

    final class LsbWpDieException extends \RuntimeException
    {
    }

    function sanitize_text_field($value)
    {
        return $value;
    }

    function sanitize_post($value)
    {
        return $value;
    }

    function wp_verify_nonce($nonce, $action)
    {
        return 1;
    }

    function wp_create_nonce($action)
    {
        return 'fresh-nonce';
    }

    function esc_html__($text, $domain = null)
    {
        return $text;
    }

    function esc_html($text)
    {
        return $text;
    }

    function get_option($name, $default = false)
    {
        return $default;
    }

    function apply_filters($hook, $value, ...$args)
    {
        return $value;
    }

    function wp_die(): void
    {
        throw new LsbWpDieException();
    }

    lsb_test('form spam rejection is logged without subscriber data when logging is enabled', function (): void {
        $listId = 'abcdefghij';
        $email = 'private@example.com';
        $token = 'private-token';
        $originalPost = $_POST;
        $originalServer = $_SERVER;
        $GLOBALS['lsb_logged_messages'] = [];
        Logger::setIsEnabled(true);
        $responseJson = '';
        ob_start();

        $_POST = [
            'lsb' => [
                $listId => [
                    'email' => $email,
                    FormController::FIELD_NAME_NONCE => 'valid-nonce',
                    FormController::FIELD_NAME_TOKEN => $token,
                    FormController::FIELD_NAME_POW => '42',
                ],
            ],
        ];
        $_SERVER = [
            'REMOTE_ADDR' => '203.0.113.9',
            'HTTP_REFERER' => 'https://customer.example/signup',
            'HTTP_USER_AGENT' => 'Test browser',
        ];

        $dataService = new class {
            public function getListFields(string $listId): array
            {
                return [[
                    'key' => 'email',
                    'field_id' => 'email',
                    'name' => 'Email',
                ]];
            }

            public function initLaposta(): void
            {
            }
        };
        $apiProxy = new class {
            public function createMember(string $listId, array $memberData): void
            {
                $original = new class extends \RuntimeException {
                    public $json_body = [
                        'error' => [
                            'type' => 'request_failed',
                            'code' => 211,
                            'message' => 'Submission rejected by the spam check, no subscriber was created',
                        ],
                    ];
                };

                throw new LapostaApiException($original);
            }
        };
        $container = new class($dataService, $apiProxy) extends Container {
            private $testDataService;
            private $testApiProxy;

            public function __construct($dataService, $apiProxy)
            {
                $this->testDataService = $dataService;
                $this->testApiProxy = $apiProxy;
            }

            public function getDataService()
            {
                return $this->testDataService;
            }

            public function getLapostaApiProxy()
            {
                return $this->testApiProxy;
            }
        };

        try {
            (new FormController($container))->ajaxFormPost();
        } catch (LsbWpDieException $e) {
        } finally {
            $responseJson = ob_get_clean();
            $_POST = $originalPost;
            $_SERVER = $originalServer;
            Logger::setIsEnabled(false);
        }

        $jsonStart = strpos($responseJson, '{"status"');
        $response = json_decode($jsonStart === false ? $responseJson : substr($responseJson, $jsonStart), true);
        lsb_assert_same('success', $response['status'] ?? null);
        lsb_assert_same([
            '[Laposta Signup Basic] info: signup rejected by Laposta spam protection (API code 211); success shown to visitor, data: {"list_id":"abcdefghij"}',
        ], $GLOBALS['lsb_logged_messages']);

        Logger::logInfo('this message must remain disabled');
        lsb_assert_same(1, count($GLOBALS['lsb_logged_messages']));
    });
}
