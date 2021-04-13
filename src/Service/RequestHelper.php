<?php

namespace Laposta\SignupBasic\Service;

class RequestHelper
{
    const STATUS_CODE_UNPROCESSABLE_ENTITY = 422;
    const STATUS_CODE_FORBIDDEN = 403;
    const STATUS_CODE_INTERNAL_SERVER_ERROR = 500;

    /**
     * @param string $name
     * @param mixed $default
     * @param array $allowed
     * @return mixed
     */
    public static function getGetVariable($name, $default = null, $allowed = null)
    {
        return self::getPostHelper($_GET, $name, $default, $allowed);
    }    

    /**
     * @param string $name
     * @param mixed $default
     * @param array $allowed
     * @return mixed
     */
    public static function getPostVariable($name, $default = null, $allowed = null)
    {
        return self::getPostHelper($_POST, $name, $default, $allowed);
    }

    /**
     * @param array $data
     * @param string $name
     * @param mixed $default
     * @param array $allowed
     * @return mixed
     */
    protected static function getPostHelper($data, $name, $default, $allowed)
    {
        $val =  isset($data[$name]) ? $data[$name] : $default;
        if ($allowed && !in_array($val, $allowed)) {
            return $default;
        }

        return $val;
    }

    /**
     * @param array $data
     */
    public static function returnJson($data, $responseCode = null, $addSlashes = false){
        header('Content-type: application/json');
        if ($responseCode) {
            http_response_code($responseCode);
        }
        $json =  json_encode($data);
        if ($addSlashes) {
            $json = addslashes($json);
        }

        echo $json;
        wp_die();
    }

    public static function isRequestType($type)
    {
        return $_SERVER['REQUEST_METHOD'] === strtoupper($type);
    }
}