<?php

namespace NovaPoshta\ApiModels;

use NovaPoshta\Core\ApiModel;
use NovaPoshta\Models\CounterpartyContact;
use NovaPoshta\Models\InventoryNomenclature;
use NovaPoshta\Config;
use NovaPoshta\MethodParameters\MethodParameters;
use stdClass;

/**
 * InventoryGeneral - Модель для работы с Товарно-материальными ценностями
 *
 * @property CounterpartyContact 	Recipient
 * @property array 					Nomenclatures
 *
 * Class InventoryGeneral
 * @package NovaPoshta\ApiModels
 */
class InventoryGeneral extends ApiModel
{
	
    private function getDataInventoryOrder()
    {
        $data = new stdClass();

        foreach ($this as $key => $attr) {
            if($attr instanceof CounterpartyContact){
                $data->{'City' . $key} = $attr->getCity();
                $data->{$key} = $attr->getRef();
                $data->{$key . 'Address'} = $attr->getAddress();
                $data->{'Contact' . $key} = $attr->getContact();
                $data->{$key . 'sPhone'} = $attr->getPhone();
            } elseif (isset($this->{$key})) {
                $data->{$key} = $attr;
            }
        }

        return $data;
    }

	/**
     * Устанавливает получателя
     *
     * @param CounterpartyContact $counterparty
     * @return $this
     */
    public function setRecipient(CounterpartyContact $counterparty)
    {
        $this->Recipient = $counterparty;
        return $this;
    }
	
    /**
     * Добавляет параметры номенклатуры
     *
     * @param InventoryNomenclature $value
     * @return $this
     */
    public function addInventoryNomenclature(InventoryNomenclature $value)
    {
        if (!isset($this->Nomenclatures)) {
            $this->Nomenclatures = array();
        }
        $this->Nomenclatures[] = $value;
        return $this;
    }
	
	/**
     * Вызвать метод save() - создание заявки ТМЦ
     *
     * @return \NovaPoshta\Models\DataContainerResponse
     */
    public function save()
    {
        return $this->sendData(__FUNCTION__, $this->getDataInventoryOrder());
    }
	
	/**
     * Вызвать метод getInventoryNomenclaturesList() - получить список доступных ТМЦ
     *
     * @param MethodParameters $data
     * @return \NovaPoshta\Models\DataContainerResponse
     */
    public static function getInventoryNomenclaturesList(MethodParameters $data = null)
    {
        return self::sendData(__FUNCTION__, $data);
    }
	
	/**
     * Вызвать метод getInventoryOrdersList() - получает список заявок ТМЦ
     *
     * @param MethodParameters $data
     * @return \NovaPoshta\Models\DataContainerResponse
     */
    public static function getInventoryOrdersList(MethodParameters $data = null)
    {
        return self::sendData(__FUNCTION__, $data);
    }
}