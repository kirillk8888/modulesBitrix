<?php
declare(strict_types=1);

namespace Task\Module;
use Bitrix\Main\Type;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;
use Task\Module\data;

class MyTab
{
    public static function onInit()
    {

        return array(
            "TABSET" => "MyTab",
            "GetTabs" => array("Task\Module\MyTab", "mygetTabs"),
            "ShowTab" => array("Task\Module\MyTab", "myshowTab"),
            "Action" => array("Task\Module\MyTab", "myaction"),
            "Check" => array("Task\Module\MyTab", "mycheck"),
        );
    }

    public static function myaction($arArgs)
    {
        return true;
    }
    public static function mycheck($arArgs)
    {
        // Проверки перед сохранением. Возвращаем true / false
        return true;
    }

    public static function mygetTabs($arArgs)
    {
        return array(
                array(
                        "DIV" => "infoTabUser",
                        "TAB" => "Информация о заказчике",
                        "ICON" => "sale",
                        "TITLE" => "Пользовательские настройки",
                        "SORT" => 1));
    }

    public static function myshowTab($divName, $arArgs, $bVarsFromForm)
    {
        if ($divName == "infoTabUser") {
            $querytUserInfo = DataTable::getList(
                [
                    'select' => ['*'],
                    'filter' => ['ID_ORDER' => $arArgs['ID']]
                ]);

            $resulttUserInfo = $querytUserInfo->fetch()?>
            <tr>
                <td width="40%" style="">Информация о пользователе:</td>
                <td width="60%">
                <pre>
                    <?
                    if (!empty($resulttUserInfo['DESCRIPTION'])) {
                        print_r(unserialize($resulttUserInfo['DESCRIPTION']));
                    }
                    ?>
                </pre>
                </td>
            </tr>

            <?php
        }




    }
}
