<?php

namespace NovaPoshta\ApiModels;

use NovaPoshta\Core\ApiModel;
use NovaPoshta\Config;
use NovaPoshta\MethodParameters\MethodParameters;
use stdClass;

/**
 * Payment - Модель для работы со стоимостью услуг и оплатами
 *
 * Class Payment
 * @package NovaPoshta\ApiModels
 */
class Payment extends ApiModel
{

	/**
     * Вызвать метод tlGetRecipientPaymentInfo() - получить данные о стоимости услуг и оплатах
     *
     * @param MethodParameters $data
     * @return \NovaPoshta\Models\DataContainerResponse
     */
    public static function tlGetRecipientPaymentInfo(MethodParameters $data = null)
    {
        return self::sendData(__FUNCTION__, $data);
    }
	
	/**
     * Вызвать метод initPayout() - получить данные для выбора карты для получения наложенного платежа
     *
     * @param MethodParameters $data
     * @return \NovaPoshta\Models\DataContainerResponse
     */
    public static function initPayout(MethodParameters $data = null)
    {
        return self::sendData(__FUNCTION__, $data);
    }
}

