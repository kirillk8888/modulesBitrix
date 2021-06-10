<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;
Loc::loadMessages(__FILE__);
class task_module extends CModule {
    public function __construct(){

        if(file_exists(__DIR__."/version.php")){

            $arModuleVersion = array();

            include_once(__DIR__."/version.php");

            $this->MODULE_ID            = str_replace("_", ".", get_class($this));
            $this->MODULE_VERSION       = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
            $this->MODULE_NAME          = Loc::getMessage("TASK_MODULE_NAME");
            $this->MODULE_DESCRIPTION  = Loc::getMessage("TASK_MODULE_DESCRIPTION");
            $this->PARTNER_NAME     = Loc::getMessage("TASK_MODULE_PARTNER_NAME");
            $this->PARTNER_URI      = Loc::getMessage("TASK_MODULE_PARTNER_URI");
        }

        return false;
    }

    public function DoInstall()
    {

        global $APPLICATION;

        if (CheckVersion(ModuleManager::getVersion("main"), "14.00.00")) { //Проверка версии bitrix

            $this->InstallFiles();
            $this->InstallDB();

            ModuleManager::registerModule($this->MODULE_ID);

            $this->InstallEvents();
        } else { //Если не поддерживает выводим сообещение

            $APPLICATION->ThrowException(
                Loc::getMessage("TASK_MODULE_INSTALL_ERROR_VERSION")
            );
        }

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage("TASK_MODULE_INSTALL_TITLE") . " \"" . Loc::getMessage("TASK_MODULE_NAME") . "\"",
            __DIR__ . "/step.php"
        );

        return false;
    }
    public function InstallFiles(){ //Копируем скрипты в систему

        CopyDirFiles(
            __DIR__."/assets/scripts",
            Application::getDocumentRoot()."/bitrix/js/".$this->MODULE_ID."/",
            true,
            true
        );

        CopyDirFiles(
            __DIR__."/assets/styles",
            Application::getDocumentRoot()."/bitrix/css/".$this->MODULE_ID."/",
            true,
            true
        );

        return false;
    }
    public function InstallDB(){ //Устанавливаем базу

        global $DB;
        $this->errors = false;
        $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . "/local/modules/task.module/install/db/install.sql");
        if (!$this->errors) {
            return true;
        } else
            return $this->errors;
        return false;
    }
    public function InstallEvents(){ //регистрируем собитие в системе
        EventManager::getInstance()->registerEventHandler(
            "main",
            "OnAdminSaleOrderView",
            $this->MODULE_ID,
            "Task\Module\MyTab",
            "onInit"
        );
        EventManager::getInstance()->registerEventHandler(
            "main",
            "OnBeforeEndBufferContent",
            $this->MODULE_ID,
            "Task\Module\Main",
            "appendScriptsToPage"
        );
        EventManager::getInstance()->registerEventHandler(
            "sale",
            "OnSaleOrderSaved",
            $this->MODULE_ID,
            "Task\Module\Order",
            "setInformationUserOrder"
        );

        return false;
    }

    public function DoUninstall(){//Удаление модуля

        global $APPLICATION;

        $this->UnInstallFiles();
        $this->UnInstallDB();
        $this->UnInstallEvents();

        ModuleManager::unRegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage("TASK_MODULE_UNINSTALL_TITLE")." \"".Loc::getMessage("TASK_MODULE_NAME")."\"",
            __DIR__."/unstep.php"
        );

        return false;
    }
    public function UnInstallFiles(){//Удаление файлов из системы

        Directory::deleteDirectory(
            Application::getDocumentRoot()."/bitrix/js/".$this->MODULE_ID
        );

        Directory::deleteDirectory(
            Application::getDocumentRoot()."/bitrix/css/".$this->MODULE_ID
        );

        return false;
    }
    public function UnInstallDB(){//Удаление БД

        Option::delete($this->MODULE_ID);
        global $DB;
        $this->errors = false;
        $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . "/local/modules/task.module/install/db/uninstall.sql");
        if (!$this->errors) {
            return true;
        } else
            return $this->errors;
        return false;
    }
    public function UnInstallEvents(){ //Удаляем настройки модуля из базы

        EventManager::getInstance()->unRegisterEventHandler(
            "main",
            "OnAdminSaleOrderView",
            $this->MODULE_ID,
            "Task\Module\MyTab",
            "onInit"
        );
        EventManager::getInstance()->unRegisterEventHandler(
            "main",
            "OnBeforeEndBufferContent",
            $this->MODULE_ID,
            "Task\Module\Main",
            "appendScriptsToPage"
        );
        EventManager::getInstance()->unRegisterEventHandler(
            "sale",
            "OnSaleOrderSaved",
            $this->MODULE_ID,
            "Task\Module\Order",
            "setInformationUserOrder"
        );


        return false;
    }




}