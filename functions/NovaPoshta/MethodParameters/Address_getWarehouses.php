<?php

namespace NovaPoshta\MethodParameters;

/**
 * Параметры метода getWarehouses модели Address
 *
 * Class Address_getWarehouses
 * @package NovaPoshta\DataMethods
 * @property string CityRef
 * @property string SettlementRef
 * @property string FindByString
 * @property string Page
 * @property string Limit
 */
class Address_getWarehouses extends MethodParameters
{
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
     * Устанавливает строку для поиска
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
     * Устанавливает реф города
     *
     * @param string $value
     * @return $this
     */
    public function setCityRef($value)
    {
        $this->CityRef = $value;
        return $this;
    }

    /**
     * Возвращает реф города
     *
     * @return string
     */
    public function getCityRef()
    {
        return $this->CityRef;
    }

    /**
     * Устанавливает страницу (отдает максимум 500 на страницу)
     * Работает в связке с параметром Limit
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
	
	/**
     * Количество записей на странице. (отдает максимум 500 на страницу)
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
     * Возвращает страницу
     *
     * @return string
     */
    public function getLimit()
    {
        return $this->Limit;
    }
}