<?php

namespace Laposta\SignupBasic\Service;

use Laposta\SignupBasic\Plugin;
use Laposta\SignupBasic\Container\Container;
use Laposta_List;

class DataService
{
    const STATUS_NO_CURL = 'no_curl';
    const STATUS_NO_API_KEY = 'no_api_key';
    const STATUS_INVALID_API_KEY = 'invalid_api_key';
    const STATUS_NO_LISTS = 'no_lists';
    const STATUS_INVALID_REQUEST = 'invalid_request';
    const STATUS_OK = 'ok';

    const CLASS_TYPE_DEFAULT = 'default';
    const CLASS_TYPE_BOOTSTRAP_V4 = 'bootstrap_v4';
    const CLASS_TYPE_BOOTSTRAP_V5 = 'bootstrap_v5';
    const CLASS_TYPE_CUSTOM = 'custom';

    /**
     * @var Container
     */
    protected $c;

    /**
     * @var array|null
     */
    protected $lists;

    /**
     * @var string|null
     */
    protected $status;

    public function __construct(Container $container)
    {
        $this->c = $container;
    }

    /**
     * Only init library if asked for
     * @return bool
     */
    public function initLaposta(): bool
    {
        if ($this->getApiKey()) {
            $this->c->initLaposta();
        }

        return class_exists('\\Laposta');
    }

    public function getApiKey(): ?string
    {
        return get_option(Plugin::OPTION_API_KEY, null);
    }

    /**
     * Get the status. Note that this should be always called AFTER ::getLists.
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        if (!function_exists('curl_init')) {
            // we just check this activly, because caching this causes a bit of a pain
            return self::STATUS_NO_CURL;
        }

        if ($this->status) {
            return $this->status;
        }

        $this->status = get_transient(Plugin::TRANSIENT_STATUS);

        return $this->status ?: null;
    }

    public function setStatus(?string $status)
    {
        $this->status = $status;
        set_transient(Plugin::TRANSIENT_STATUS, $status, 0);
    }

    /**
     * Get the status message. Note that this should be always called AFTER ::getLists.
     *
     * @return string|null
     */
    public function getStatusMessage()
    {
        if (!$this->getStatus()) {
            return '';
        }

        switch ($this->getStatus()) {
            case self::STATUS_NO_API_KEY:
                return 'Nog geen api-key ingevuld.';
            case self::STATUS_NO_CURL:
                return 'Deze plugin heeft de php-curl extensie nodig, maar deze is niet geinstalleerd.';
            case self::STATUS_INVALID_API_KEY:
                return 'Dit is geen geldige api-key.';
            case self::STATUS_NO_LISTS:
                return 'Geen lijsten gevonden.';
            default:
                return 'Onbekende fout';
        }
    }

    public function getLists(): ?array
    {
        if ($this->lists) {
            return $this->lists;
        }

        $this->lists = get_transient(Plugin::TRANSIENT_LISTS);
        if ($this->lists) {
            return $this->lists;
        }

        if ($this->getStatus()) {
            // attempted to fetch lists with api key already, but no list received. Note that cache is cleared after a new api key has been submitted
            return null;
        }

        if (!$this->getApiKey()) {
            $this->setStatus(self::STATUS_NO_API_KEY);

            return null;
        }

        if (!$this->initLaposta()) {
            // failed to init laposta
            return null;
        }

        $lapostaList = new Laposta_List();
        try {
            $result = $lapostaList->all();
            if (!$result['data']) {
                $this->setStatus(self::STATUS_NO_LISTS);
            } else {
                $items = $result['data'];
                $this->lists = [];
                foreach ($items as $item) {
                    $this->lists[] = $item['list'];
                }
                set_transient(Plugin::TRANSIENT_LISTS, $this->lists, 0);
                $this->setStatus(self::STATUS_OK);
                $this->emptyListFieldsCache();

                return $this->lists;
            }
        } catch (\Throwable $e) {
            $error = @$e->json_body['error'];
            if ($error) {
                if ($error['type'] === 'invalid_request') {
                    $this->setStatus(self::STATUS_INVALID_API_KEY);
                }
            }
            if (!$this->status) {
                $this->setStatus('error-api: '.print_r($e, 1));
            }
        }

        return null;
    }

    /**
     * Returns either an array of fields with in_form: true or an array with top level key error ['error' => []]
     * Note that the field object is enriched with 'key' which is the tag without the {{}}
     *
     * @param $listId
     *
     * @return array[]|null
     */
    public function getListFields($listId): ?array
    {
        $listFields = get_transient(Plugin::TRANSIENT_LIST_FIELDS_PREFIX.$listId);
        if ($listFields) {
            return $listFields;
        }

        if (!$this->initLaposta()) {
            return null;
        }

        try {
            $lapostaField = new \Laposta_Field($listId);
            $result = $lapostaField->all();
            if (!$result['data']) {
                $error = [
                    'error' => [
                        'type' => 'unknown',
                        'message' => 'Onbekende fout',
                        'code' => null,
                        'parameter' => null,
                    ],
                ];
                $this->setListFields($listId, $error);

                return $error;
            } else {
                $listFields = [];
                foreach ($result['data'] as $fieldWrapper) {
                    $field = $fieldWrapper['field'];
                    if ($field['in_form']) {
                        $key = substr($field['tag'], 2, strlen($field['tag']) - 4);
                        $field['key'] = $key;
                        $listFields[] = $field;
                    }
                }
                usort($listFields, function($a, $b) {
                    return $a['pos'] <=> $b['pos'];
                });
                $this->setListFields($listId, $listFields);

                return $listFields;
            }
        } catch (\Throwable $e) {
            $error = @$e->json_body;
            if (!$error) {
                $error = [
                    'error' => [
                        'type' => 'unknown',
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'code' => null,
                        'parameter' => null,
                    ],
                ];
            }

            $this->setListFields($listId, $error);

            return $error;
        }
    }

    /**
     * Store the list fields in a transient. Can also be an error.
     *
     * @param $listId
     * @param $listFields
     */
    public function setListFields($listId, $listFields)
    {
        set_transient(Plugin::TRANSIENT_LIST_FIELDS_PREFIX.$listId, $listFields);
    }

    public function emptyAllCache()
    {
        $this->emptyListFieldsCache();
        delete_transient(Plugin::TRANSIENT_LISTS);
        delete_transient(Plugin::TRANSIENT_STATUS);
    }

    public function emptyListFieldsCache()
    {
        if ($this->getLists()) {
            foreach ($this->getLists() as $list) {
                $this->emptyListFieldCache($list['list_id']);
            }
        }
    }

    public function emptyListFieldCache($listId)
    {
        delete_transient(Plugin::TRANSIENT_LIST_FIELDS_PREFIX.$listId);
    }

    public function getClassTypesKeyValuePairs()
    {
        return [
            self::CLASS_TYPE_DEFAULT => 'Onze default',
            self::CLASS_TYPE_BOOTSTRAP_V4 => 'Bootstrap v4',
            self::CLASS_TYPE_BOOTSTRAP_V5 => 'Bootstrap v5',
            self::CLASS_TYPE_CUSTOM => 'Handmatig instellen',
        ];
    }
}