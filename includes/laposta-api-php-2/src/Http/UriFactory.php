<?php

declare (strict_types=1);
namespace LapostaApi\Http;

use LapostaApi\Vendor\Psr\Http\Message\UriFactoryInterface;
use LapostaApi\Vendor\Psr\Http\Message\UriInterface;
/** @internal */
class UriFactory implements UriFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createUri(string $uri = '') : UriInterface
    {
        return new \LapostaApi\Http\Uri($uri);
    }
}
