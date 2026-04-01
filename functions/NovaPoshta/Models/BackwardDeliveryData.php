<?php

namespace NovaPoshta\Models;

use NovaPoshta\Core\BaseModel;

/**
 * Параметры обратной доставки
 *
 * Class BackwardDeliveryData
 * @package NovaPoshta\Models
 * @property string PayerType
 * @property string CargoType
 * @property string RedeliveryString
 * @property string Cash2CardPayout_Id
 * @property string Cash2CardAlias
 * @property string Cash2CardPAN
 * @property string Description
 * @property array  Trays
 */
class BackwardDeliveryData extends BaseModel
{
    /**
     * Устанавливает тип плательщика
     *
     * @param string $value
     * @return $this
     */
    public function setPayerType($value)
    {
        $this->PayerType = $value;
        return $this;
    }

    /**
     * Возвращает тип плательщика
     *
     * @return string
     */
    public function getPayerType()
    {
        return $this->PayerType;
    }

    /**
     * Устанавливает тип груза
     *
     * @param string $value
     * @return $this
     */
    public function setCargoType($value)
    {
        $this->CargoType = $value;
        return $this;
    }

    /**
     * Возвращает тип груза
     *
     * @return string
     */
    public function getCargoType()
    {
        return $this->CargoType;
    }

    /**
     * Устанавливает описания груза
     *
     * @param string $value
     * @return $this
     */
    public function setRedeliveryString($value)
    {
        $this->RedeliveryString = $value;
        return $this;
    }

    /**
     * Возвращает описания груза
     *
     * @return string
     */
    public function getRedeliveryString()
    {
        return $this->RedeliveryString;
    }

    /**
     * Устанавливает Ref записи в базе НоваПей для зачисления наложки на карту
     *
     * @param string $value
     * @return $this
     */
    public function setCash2CardPayoutId($value)
    {
        $this->Cash2CardPayout_Id = $value;
        return $this;
    }

    /**
     * Устанавливает алиас карты для зачисления наложки на карту (необязательный параметр)
     *
     * @param string $value
     * @return $this
     */
    public function setCash2CardAlias($value)
    {
        $this->Cash2CardAlias = $value;
        return $this;
    }

    /**
     * Устанавливает скрытый номер карты типа 403520xxxxxx6246
     *
     * @param string $value
     * @return $this
     */
    public function setCash2CardPAN($value)
    {
        $this->Cash2CardPAN = $value;
        return $this;
    }

	/**
     * Устанавливает описание
     *
     * @param string $value
     * @return $this
     */
    public function setDescription($value)
    {
        $this->Description = $value;
        return $this;
    }
	
    /**
     * Добавляет поддон
     *
     * @param Cargo $cargo
     * @return $this
     */
    public function addTray(Cargo $cargo)
    {
        if(empty($this->Trays)){
            $this->Trays = array();
        }
        $this->Trays[] = $cargo;
        return $this;
    }

    /**
     * Устанавливает поддон
     *
     * @param array $trays
     */
    public function setTrays(array $trays)
    {
        $this->Trays = $trays;
    }

    /**
     * Возвращает поддоны
     *
     * @return null
     */
    public function getTrays()
    {
        return $this->Trays;
    }

    /**
     * Очищает поддоны
     *
     * @return $this
     */
    public function clearTrays()
    {
        $this->Trays = array();
        return $this;
    }
}