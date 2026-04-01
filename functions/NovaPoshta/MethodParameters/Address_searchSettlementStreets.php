<?php

namespace NovaPoshta\MethodParameters;

/**
 * Параметры метода searchSettlementStreets модели Address
 *
 * Class Address_searchSettlementStreets
 * @package NovaPoshta\DataMethods
 * @property string StreetName
 * @property string SettlementRef
 * @property string Limit
 * @property string Page
 */
class Address_searchSettlementStreets extends MethodParameters
{

    /**
     * Устанавливает название улицы
     *
     * @param string $value
     * @return $this
     */
    public function setStreetName($value)
    {
        $this->StreetName = $value;
        return $this;
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
     * Устанавливает страницу
     *
     * @param int $value
     * @return $this
     */
    public function setPage($value)
    {
        $this->Page = $value;
        return $this;
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

}