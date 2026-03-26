<?php

declare (strict_types=1);
namespace LapostaApi230\Http;

use LapostaApi230\Vendor\Psr\Http\Message\RequestFactoryInterface;
use LapostaApi230\Vendor\Psr\Http\Message\RequestInterface;
use LapostaApi230\Vendor\Psr\Http\Message\StreamFactoryInterface;
use LapostaApi230\Vendor\Psr\Http\Message\UriFactoryInterface;
use LapostaApi230\Vendor\Psr\Http\Message\UriInterface;
/** @internal */
class RequestFactory implements RequestFactoryInterface
{
    /**
     * Creates a new RequestFactory instance.
     *
     * @param StreamFactoryInterface $streamFactory Factory to create request streams
     * @param UriFactoryInterface $uriFactory Factory to create URIs
     */
    public function __construct(protected StreamFactoryInterface $streamFactory = new \LapostaApi230\Http\StreamFactory(), protected UriFactoryInterface $uriFactory = new \LapostaApi230\Http\UriFactory())
    {
    }
    /**
     * {@inheritDoc}
     */
    public function createRequest(string $method, $uri) : RequestInterface
    {
        // Convert URI string to UriInterface if necessary
        if (\is_string($uri)) {
            $uri = $this->uriFactory->createUri($uri);
        }
        // Check $uri type
        if (!$uri instanceof UriInterface) {
            throw new \InvalidArgumentException(\sprintf('The $uri argument must be a string or an instance of %s. %s given.', UriInterface::class, \get_debug_type($uri)));
        }
        // Create the Request object
        $request = new \LapostaApi230\Http\Request($method, $uri, $this->streamFactory->createStream());
        return $request;
    }
}
