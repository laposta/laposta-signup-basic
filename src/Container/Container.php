<?php

namespace Laposta\SignupBasic\Container;

use Laposta\SignupBasic\Controller\FormController;
use Laposta\SignupBasic\Controller\SettingsController;
use Laposta\SignupBasic\Plugin;
use Laposta\SignupBasic\Service\DataService;

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

    public function getPlugin()
    {
        if (!class_exists('Laposta\\SignupBasic\\Plugin')) {
            require_once realpath(__DIR__.'/..').'/Plugin.php';
            $this->requireLogger();
            $this->plugin = new Plugin($this);
        }

        return $this->plugin;
    }

    public function initLaposta()
    {
        if (!class_exists('\\Laposta')) {
            require_once realpath(__DIR__.'/../../includes/laposta-api-php-1.6/lib/').'/Laposta.php';
        }
        \Laposta::setApiKey($this->getDataService()->getApiKey());
    }

    public function getDataService()
    {
        if (!class_exists('Laposta\\SignupBasic\\Service\\DataService')) {
            require_once realpath(__DIR__.'/../Service').'/DataService.php';
            $this->dataService = new DataService($this);
        }

        return $this->dataService;
    }

    public function requireRequestHelper()
    {
        if (!class_exists('Laposta\\SignupBasic\\Service\\RequestHelper')) {
            require_once realpath(__DIR__.'/../Service').'/RequestHelper.php';
        }
    }

    public function requireLogger()
    {
        if (!class_exists('Laposta\\SignupBasic\\Service\\Logger')) {
            require_once realpath(__DIR__.'/../Service').'/Logger.php';
        }
    }

    protected function requireBaseController()
    {
        if (!class_exists('Laposta\\SignupBasic\\Controller\\BaseController')) {
            $this->requireRequestHelper();
            require_once realpath(__DIR__.'/../Controller').'/BaseController.php';
        }
    }

    public function getSettingsController()
    {
        if (!class_exists('Laposta\\SignupBasic\\Controller\\SettingsController')) {
            $this->requireBaseController();
            require_once realpath(__DIR__.'/../Controller').'/SettingsController.php';
            $this->settingsController = new SettingsController($this);
        }

        return $this->settingsController;
    }

    public function getFormController()
    {
        if (!class_exists('Laposta\\SignupBasic\\Controller\\FormController')) {
            $this->requireBaseController();
            require_once realpath(__DIR__.'/../Controller').'/FormController.php';
            $this->formController = new FormController($this);
        }

        return $this->formController;
    }
}