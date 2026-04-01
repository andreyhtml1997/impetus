<?php

namespace NovaPoshta\MethodParameters;

/**
 * Параметры метода getSettlements модели AddressGeneral
 *
 * Class AddressGeneral_getSettlements
 * @package NovaPoshta\DataMethods
 * @property string Ref
 * @property string FindByString
 * @property string Warehouse
 * @property string RegionRef
 * @property string Page
 * @property string Limit
 */
class AddressGeneral_getSettlements extends MethodParameters
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
     * Устанавливает строку для поиска города
     *
     * @param string $value
     * @return $this
     */
    public function setFindByString($value)
    {
        $this->FindByString = $value;
        return $this;
    }

    /**
     * Возвращает строку для поиска города
     *
     * @return string
     */
    public function getFindByString()
    {
        return $this->FindByString;
    }

    /**
     * Устанавливает строку для поиска только тех населенных пунктов, в которых есть отделения "Нова пошта"
     *
     * @param string $value
     * @return $this
     */
    public function setWarehouse($value)
    {
        $this->Warehouse = $value;
        return $this;
    }

    /**
     * Возвращает строку для поиска только тех населенных пунктов, в которых есть отделения "Нова пошта"
     *
     * @return string
     */
    public function getWarehouse()
    {
        return $this->Warehouse;
    }
	
	/**
     * Устанавливает строку для поиска населенных пунктов по идентификатору района
     *
     * @param string $value
     * @return $this
     */
    public function setRegionRef($value)
    {
        $this->RegionRef = $value;
        return $this;
    }

    /**
     * Возвращает строку для поиска населенных пунктов по идентификатору района
     *
     * @return string
     */
    public function getRegionRef()
    {
        return $this->RegionRef;
    }
	
	/**
     * Количество записей на странице.
     * Работает в связке с параметром Page
     *
     * @param string $value
     * @return $this
     */
    public function setLimit($value)
    {
        $this->Limit = $value;
        return $this;
    }

    /**
     * Устанавливает страницу (по умолчанию отдает не более 150 на страницу)
     *
     * @param string $value
     * @return $this
     */
    public function setPage($value)
    {
        $this->Page = $value;
        return $this;
    }

    /**
     * Возвращает страницу
     *
     * @return string
     */
    public function getPage()
    {
        return $this->Page;
    }
}