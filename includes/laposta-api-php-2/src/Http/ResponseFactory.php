<?php

declare (strict_types=1);
namespace LapostaApi230\Http;

use LapostaApi230\Vendor\Psr\Http\Message\ResponseFactoryInterface;
use LapostaApi230\Vendor\Psr\Http\Message\ResponseInterface;
/** @internal */
class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createResponse(int $code = 200, string $reasonPhrase = '') : ResponseInterface
    {
        return new \LapostaApi230\Http\Response($code, $reasonPhrase);
    }
}
