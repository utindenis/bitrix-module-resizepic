<?php

use Bitrix\Main\ModuleManager;
use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;

IncludeModuleLangFile(__FILE__);

class resize_picture extends CModule
{
    var $MODULE_ID = 'resize.picture';

    function __construct()
    {
        $arModuleVersion = array();

        include(__DIR__ . '/version.php');

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_NAME = "Модуль изменение размера картинок";
        $this->MODULE_DESCRIPTION = "Модуль изменение размера загружаемых картинок";
        $this->PARTNER_NAME = "Denis";
        $this->PARTNER_URI = "localhost";
    }

    function RegisterEvents(){
        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandler("main", "OnFileSave", $this->MODULE_ID, "\My\Resizepictures\CResizepic", "ResizePictures");
        return true;
    }

    function UnRegisterEvents(){
        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler("main", "OnFileSave", $this->MODULE_ID, "\My\Resizepictures\CResizepic", "ResizePictures");
        return true;
    }

    public function DoInstall()
    {
        $path = $_SERVER['DOCUMENT_ROOT']. '/upload/' . $this->MODULE_ID;
        global $DB;
        $res = $DB->RunSQLBatch(__DIR__ . '/install.sql');
        $this->RegisterEvents();
        if (!\Bitrix\Main\IO\Directory::isDirectoryExists($path)) {
            \Bitrix\Main\IO\Directory::createDirectory($path);
        }
        ModuleManager::registerModule($this->MODULE_ID);
        Loader::includeModule($this->MODULE_ID);
    }

    public function DoUninstall()
    {
        $path = $_SERVER['DOCUMENT_ROOT']. '/upload/' . $this->MODULE_ID;
        global $DB;
        Loader::includeModule($this->MODULE_ID);
        $this->UnRegisterEvents();
        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path)) {
            \Bitrix\Main\IO\Directory::deleteDirectory($path);
        }
        $DB->RunSQLBatch(__DIR__ . '/uninstall.sql');
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}
