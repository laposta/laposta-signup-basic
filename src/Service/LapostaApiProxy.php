<?php

namespace Laposta\SignupBasic\Service;

use Laposta\SignupBasic\Exception\LapostaApiException;
use LapostaApi\Laposta;

/**
 * Proxy class to handle interaction with both v1.6 and v2 of the Laposta API wrapper
 *
 * This class provides a unified interface for both API versions:
 * - v1.6: Used for PHP < 8.0
 * - v2: Used for PHP >= 8.0
 */
class LapostaApiProxy
{
    public const API_VERSION_V1_6 = 'v1.6';
    public const API_VERSION_V2 = 'v2';

    /**
     * @var string|null
     */
    protected $apiKey;

    /**
     * @var Laposta|null (v2)
     */
    protected $lapostaV2;

    /**
     * @var bool
     */
    protected $isV2;

    public function __construct()
    {
        $this->isV2 = PHP_VERSION_ID >= 80000;
    }

    /**
     * Set the API key for the Laposta service
     *
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;

        if ($this->isV2) {
            // For v2, create Laposta instance with API key
            $this->lapostaV2 = new Laposta($apiKey);
        } else {
            // For v1.6, set global API key
            \Laposta::setApiKey($apiKey);
        }
    }

    /**
     * Get all lists from the API
     *
     * @return array|null
     * @throws \Throwable
     */
    public function getAllLists(): ?array
    {
        return $this->executeApiCall(function () {
            if ($this->isV2) {
                return $this->lapostaV2->listApi()->all();
            } else {
                $lapostaList = new \Laposta_List();
                return $lapostaList->all();
            }
        });
    }

    /**
     * Get all fields for a specific list
     *
     * @param string $listId
     * @return array|null
     * @throws \Throwable
     */
    public function getAllFields(string $listId): ?array
    {
        return $this->executeApiCall(function () use ($listId) {
            if ($this->isV2) {
                return $this->lapostaV2->fieldApi()->all($listId);
            } else {
                $lapostaField = new \Laposta_Field($listId);
                return $lapostaField->all();
            }
        });
    }

    /**
     * Create a member in a specific list
     *
     * @param string $listId
     * @param array $data
     * @return array|null
     * @throws LapostaApiException
     */
    public function createMember(string $listId, array $data): ?array
    {
        return $this->executeApiCall(function () use ($listId, $data) {
            if ($this->isV2) {
                return $this->lapostaV2->memberApi()->create($listId, $data);
            } else {
                $lapostaMember = new \Laposta_Member($listId);
                return $lapostaMember->create($data);
            }
        });
    }

    /**
     * Executes a callable and wraps expected API exceptions.
     *
     * @param callable $callable The API call to execute.
     * @return mixed The result of the callable.
     * @throws LapostaApiException If a Laposta-specific exception is thrown.
     * @throws \Throwable For any other unexpected exceptions.
     */
    protected function executeApiCall(callable $callable)
    {
        try {
            return $callable();
        } catch (\Throwable $e) {
            // Check for specific exception types without causing a fatal error if the class doesn't exist.
            $v2ExceptionClass = 'LapostaApi\Exception\LapostaException';
            if (class_exists($v2ExceptionClass) && $e instanceof $v2ExceptionClass) {
                throw new LapostaApiException($e);
            }

            $v1ExceptionClass = '\Laposta_Error';
            if (class_exists($v1ExceptionClass) && $e instanceof $v1ExceptionClass) {
                throw new LapostaApiException($e);
            }

            // If it's not an exception we specifically handle, re-throw it.
            throw $e;
        }
    }

    /**
     * Check if the API wrapper classes are available
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        if ($this->isV2) {
            return class_exists('\LapostaApi\Laposta');
        } else {
            return class_exists('\Laposta');
        }
    }

    /**
     * Get the current API version being used
     *
     * @return string
     */
    public function getApiVersion(): string
    {
        return $this->isV2 ? self::API_VERSION_V2 : self::API_VERSION_V1_6;
    }
}
