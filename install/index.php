<?php

IncludeModuleLangFile(__FILE__);
use \Bitrix\Main\ModuleManager;

class bx_tree extends CModule
{
    public $MODULE_ID = "bx.tree";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $errors;

    public function __construct()
    {
        $this->MODULE_VERSION = "0.0.1";
        $this->MODULE_VERSION_DATE = "2023-02-28 11:40:00";
        $this->MODULE_NAME = "Tree builder for bitrix";
        $this->MODULE_DESCRIPTION = "Tree builder for bitrix";
    }

    /**
     * @param string $message
     */
    public function setError(string $message)
    {
        $GLOBALS["APPLICATION"]->ThrowException($message);
    }

    public function DoInstall(): bool
    {
        $result = $this->installRequiredModules();
        if (!$result) {
            return false;
        }

        ModuleManager::RegisterModule($this->MODULE_ID);
        return true;
    }

    public function DoUninstall()
    {
        ModuleManager::UnRegisterModule($this->MODULE_ID);
        return true;
    }

    /**
     * @return bool
     */
    public function installRequiredModules(): bool
    {
        $isInstalled = ModuleManager::isModuleInstalled('bx.model');
        if ($isInstalled) {
            return true;
        }

        $modulePath = getLocalPath("modules/bx.model/install/index.php");
        if (!$modulePath) {
            $this->setError('Отсутствует модуль bx.model - https://github.com/beta-eto-code/bx.model');
            return false;
        }

        require_once $_SERVER['DOCUMENT_ROOT'].$modulePath;
        $moduleInstaller = new bx_model();
        $resultInstall = (bool)$moduleInstaller->DoInstall();
        if (!$resultInstall) {
            $this->setError('Ошибка установки модуля bx.model');
        }

        return $resultInstall;
    }
}
