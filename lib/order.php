<?php
declare(strict_types=1);

namespace Task\Module;
use Bitrix\Main;
use Bitrix\Main\Type;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;
use Task\Module\data;

class Order
{
    public function setInformationUserOrder( $events)
    {

        $order = $events->getParameter('ENTITY');
        $numberOrder = $order->getId();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://rest.db.ripe.net/search.json?query-string='.$_SESSION['IP_ADDR']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $out = curl_exec($curl);
        curl_close($curl);

        $infoUser = (array)json_decode($out)->objects->object;
        $result = DataTable::add(array(
            'ID' => NULL,
            'ID_ORDER' => $numberOrder,
            'DESCRIPTION' =>  serialize($infoUser),
            'CREATED' => new Type\Date('2002-11-16', 'Y-m-d')
            ));
    }


}