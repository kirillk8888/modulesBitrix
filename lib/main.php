<?php

namespace Task\Module;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;

class Main
{
    public function appendScriptsToPage()
    {
        if (!defined("ADMIN_SECTION") && $ADMIN_SECTION !== true) {

            $module_id = pathinfo(dirname(__DIR__))["basename"];
            $inputJSON = file_get_contents('php://input');
            $input = json_decode($inputJSON, TRUE);

            if (!empty($input['ip'])) {
                $_SESSION['IP_ADDR'] = $input['ip'];
            }
            ?>

            <div id="task"></div>
            <?php
            Asset::getInstance()->addJs("https://unpkg.com/vue");
            Asset::getInstance()->addJs("https://cdnjs.cloudflare.com/ajax/libs/axios/0.16.2/axios.js");
            Asset::getInstance()->addJs("/bitrix/js/" . $module_id . "/jquery.js");
            Asset::getInstance()->addJs("/bitrix/js/" . $module_id . "/script.js");
        }

    }



}