<?php

namespace NovaPoshta\ApiModels;

use NovaPoshta\Core\ApiModel;
use NovaPoshta\MethodParameters\MethodParameters;

/**
 * Модель для работы с населенными пунктами Украины
 *
 * Class AddressGeneral
 * @package NovaPoshta\ApiModels
 *
 * @property string Ref
 */
class AddressGeneral extends ApiModel
{

    /**
     * Устанавливает реф
     *
     * @param string $value
     * @return $this
     */
    public function setRef($value)
    {
        $this->Ref = $value;
        return $this;
    }

    /**
     * Возвращает реф
     *
     * @return string
     */
    public function getRef()
    {
        return $this->Ref;
    }


    /**
     * Вызвать метод getSettlements() - загрузить справочник населенных пунктов Украины
     *
     * @param MethodParameters $data
     * @return \NovaPoshta\Models\DataContainerResponse
     */
    public static function getSettlements(MethodParameters $data = null)
    {
        return self::sendData(__FUNCTION__, $data);
    }
}
