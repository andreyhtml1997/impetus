<?php

namespace NovaPoshta\ApiModels;

use NovaPoshta\Core\ApiModel;
use NovaPoshta\Config;
use NovaPoshta\MethodParameters\MethodParameters;
use stdClass;

/**
 * LoyaltyUser - Модель для программы лояльности
 *
 * Class LoyaltyUser
 * @package NovaPoshta\ApiModels
 */
class LoyaltyUser extends ApiModel
{
	/**
     * Вызвать метод getLoyaltyInfoByApiKey() - получить выписку по бонусам
     *
     * @param MethodParameters $data
     * @return \NovaPoshta\Models\DataContainerResponse
     */
    public static function getLoyaltyInfoByApiKey(MethodParameters $data = null)
    {
        return self::sendData(__FUNCTION__, $data);
    }
}
