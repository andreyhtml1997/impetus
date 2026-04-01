<?php

namespace NovaPoshta\MethodParameters;

/**
 * Параметры метода getInventoryOrdersList модели InventoryGeneral
 *
 * Class InventoryGeneral_getInventoryOrdersList
 * @package NovaPoshta\DataMethods
 * @property string Ref
 * @property int    Page
 * @property string Limit
 */
class InventoryGeneral_getInventoryOrdersList extends MethodParameters
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