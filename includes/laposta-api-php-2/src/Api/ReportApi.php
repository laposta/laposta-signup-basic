<?php

declare (strict_types=1);
namespace LapostaApi230\Api;

use LapostaApi230\Exception\ApiException;
use LapostaApi230\Exception\ClientException;
/** @internal */
class ReportApi extends \LapostaApi230\Api\BaseApi
{
    /**
     * Get a report by campaign ID.
     *
     * @param string $campaignId The ID of the campaign to retrieve the report for.
     *
     * @return array The report data.
     * @throws ApiException
     * @throws ClientException
     * @throws \JsonException
     */
    public function get(string $campaignId) : array
    {
        return $this->sendRequest('GET', [$campaignId]);
    }
    /**
     * Get all reports.
     *
     * @return array All report data.
     * @throws ApiException
     * @throws ClientException
     * @throws \JsonException
     */
    public function all() : array
    {
        return $this->sendRequest('GET');
    }
}
