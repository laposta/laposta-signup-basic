<?php

declare (strict_types=1);
namespace LapostaApi\Http;

use LapostaApi\Vendor\Psr\Http\Message\ResponseFactoryInterface;
use LapostaApi\Vendor\Psr\Http\Message\ResponseInterface;
/** @internal */
class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createResponse(int $code = 200, string $reasonPhrase = '') : ResponseInterface
    {
        return new \LapostaApi\Http\Response($code, $reasonPhrase);
    }
}
