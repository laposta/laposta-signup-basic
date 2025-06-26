<?php

namespace Laposta\SignupBasic\Container;

use Laposta\SignupBasic\Controller\FormController;
use Laposta\SignupBasic\Controller\SettingsController;
use Laposta\SignupBasic\Plugin;
use Laposta\SignupBasic\Service\DataService;
use Laposta\SignupBasic\Service\LapostaApiProxy;

class Container
{
    /**
     * @var Plugin
     */
    protected $plugin;

    /**
     * @var SettingsController
     */
    protected $settingsController;

    /**
     * @var FormController
     */
    protected $formController;

    /**
     * @var DataService
     */
    protected $dataService;

    /**
     * @var LapostaApiProxy
     */
    protected $lapostaApiProxy;

    public function getPlugin()
    {
        $this->plugin = new Plugin($this);

        return $this->plugin;
    }

    public function initLaposta()
    {
        if (
            $this->getLapostaApiProxy()->getApiVersion() === LapostaApiProxy::API_VERSION_V1_6 &&
            !class_exists('\\Laposta')
        ) {
            require_once realpath(__DIR__.'/../../includes/laposta-api-php-1.6/lib/').'/Laposta.php';
        }

        $apiKey = $this->getDataService()->getApiKey();
        if ($apiKey) {
            $this->getLapostaApiProxy()->setApiKey($apiKey);
        }
    }

    public function getLapostaApiProxy()
    {
        if (!$this->lapostaApiProxy) {
            $this->lapostaApiProxy = new LapostaApiProxy();
        }

        return $this->lapostaApiProxy;
    }

    public function getDataService()
    {
        if (!$this->dataService) {
            $this->dataService = new DataService($this);
        }

        return $this->dataService;
    }

    public function getSettingsController()
    {
        if (!$this->settingsController) {
            $this->settingsController = new SettingsController($this);
        }

        return $this->settingsController;
    }

    public function getFormController()
    {
        if (!$this->formController) {
            $this->formController = new FormController($this);
        }

        return $this->formController;
    }
}
