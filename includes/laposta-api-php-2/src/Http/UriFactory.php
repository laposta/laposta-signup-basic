<?php

declare (strict_types=1);
namespace LapostaApi230\Http;

use LapostaApi230\Vendor\Psr\Http\Message\UriFactoryInterface;
use LapostaApi230\Vendor\Psr\Http\Message\UriInterface;
/** @internal */
class UriFactory implements UriFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createUri(string $uri = '') : UriInterface
    {
        return new \LapostaApi230\Http\Uri($uri);
    }
}
