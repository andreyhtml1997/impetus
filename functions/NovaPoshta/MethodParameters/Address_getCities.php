<?php

namespace NovaPoshta\MethodParameters;

/**
 * Параметры метода getCities модели Address
 *
 * Class Address_getCities
 * @package NovaPoshta\DataMethods
 * @property string Ref
 * @property string FindByString
 * @property string Page
 * @property string Limit
 */
class Address_getCities extends MethodParameters
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
     * Устанавливает страницу
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