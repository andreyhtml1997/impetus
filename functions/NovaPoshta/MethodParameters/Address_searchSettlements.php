<?php

namespace NovaPoshta\MethodParameters;

/**
 * Параметры метода searchSettlements модели Address
 *
 * Class Address_searchSettlements
 * @package NovaPoshta\DataMethods
 * @property string CityName
 * @property string Limit
 */
class Address_searchSettlements extends MethodParameters
{
    /**
     * Устанавливает название города
     *
     * @param string $value
     * @return $this
     */
    public function setCityName($value)
    {
        $this->CityName = $value;
        return $this;
    }

    /**
     * Возвращает название города
     *
     * @return string
     */
    public function getCityName()
    {
        return $this->CityName;
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
     * Возвращает страницу
     *
     * @return string
     */
    public function getLimit()
    {
        return $this->Limit;
    }
}