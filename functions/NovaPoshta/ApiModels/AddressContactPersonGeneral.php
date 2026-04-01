<?php

namespace NovaPoshta\ApiModels;

use NovaPoshta\Core\ApiModel;

/**
 * Модель для работы с адресами отправителя/получателя
 *
 * Class AddressContactPersonGeneral
 * @package NovaPoshta\ApiModels
 *
 * @property string SettlementRef
 * @property string AddressRef
 * @property string AddressType
 * @property string ContactPersonRef
 * @property string BuildingNumber
 * @property string Flat
 */
class AddressContactPersonGeneral extends ApiModel
{
    /**
     * Вызвать метод save() - создать адрес отправителя/получателя
     *
     * @return \NovaPoshta\Models\DataContainerResponse
     */
    public function save()
    {
        $data = $this->getThisData();

        return $this->sendData(__FUNCTION__, $data);
    }

    /**
     * Устанавливает реф населенного пункта
     *
     * @param string $value
     * @return $this
     */
    public function setSettlementRef($value)
    {
        $this->SettlementRef = $value;
        return $this;
    }

    /**
     * Устанавливает реф адреса
     *
     * @param string $value
     * @return $this
     */
    public function setAddressRef($value)
    {
        $this->AddressRef = $value;
        return $this;
    }
	
    /**
     * Устанавливает тип адреса
     *
     * @param string $value
     * @return $this
     */
    public function setAddressType($value)
    {
        $this->AddressType = $value;
        return $this;
    }

    /**
     * Устанавливает реф контрагента
     *
     * @param string $value
     * @return $this
     */
    public function setContactPersonRef($value)
    {
        $this->ContactPersonRef = $value;
        return $this;
    }

    /**
     * Устанавливает номер дома
     *
     * @param string $value
     * @return $this
     */
    public function setBuildingNumber($value)
    {
        $this->BuildingNumber = $value;
        return $this;
    }

    /**
     * Устанавливает номер квартиры
     *
     * @param string $value
     * @return $this
     */
    public function setFlat($value)
    {
        $this->Flat = $value;
        return $this;
    }

}
