<?php

namespace Laposta\SignupBasic\Service;

class RequestHelper
{
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
}