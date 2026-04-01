<?php

namespace NovaPoshta\ApiModels;

use NovaPoshta\Core\ApiModel;
use NovaPoshta\Models\BackwardDeliveryData;
use NovaPoshta\Config;
use NovaPoshta\MethodParameters\MethodParameters;
use stdClass;

/**
 * AdditionalService - Модель для изменения данных в ЭН
 *
 * @property string              IntDocNumber
 * @property int                 AfterpaymentOnGoodsCost
 * @property string              PayerType
 * @property string              PaymentMethod
 * @property array               BackwardDeliveryData
 *
 * Class AdditionalService
 * @package NovaPoshta\ApiModels
 */
class AdditionalService extends ApiModel
{
	
	public function __construct()
    {
        $this->OrderType = 'orderChangeEW';
    }
	
	private function getDataInternetDocument()
	{
        $data = new stdClass();

        foreach ($this as $key => $attr) {
            if (isset($this->{$key})) {
                $data->{$key} = $attr;
            }
        }

        return $data;
    }
	
	/**
     * Вызвать метод save() - создание ЭН изменения
     *
     * @return \NovaPoshta\Models\DataContainerResponse
     */
    public function save()
    {
        return $this->sendData(__FUNCTION__, $this->getDataInternetDocument());
    }
	
	
    /**
     * Устанавливает номер документа
     *
     * @param string $value
     * @return $this
     */
    public function setIntDocNumber($value)
    {
        $this->IntDocNumber = $value;
        return $this;
    }
	
    /**
     * Устанавливает плательщика
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
     * Устанавливает форму оплаты
     *
     * @param string $value
     * @return $this
     */
    public function setPaymentMethod($value)
    {
        $this->PaymentMethod = $value;
        return $this;
    }

    /**
     * Устанавливает способ получения наложки на карту
     *
     * @param bool $value
     * @return $this
     */
    public function setCash2Card($value)
    {
        $this->Cash2Card = $value;
        return $this;
    }
	
    /**
     * Добавляет обратную доставку
     *
     * @param BackwardDeliveryData $value
     * @return $this
     */
    public function addBackwardDeliveryData(BackwardDeliveryData $value)
    {
        if (!isset($this->BackwardDeliveryData)) {
            $this->BackwardDeliveryData = array();
        }
        $this->BackwardDeliveryData[] = $value;
        return $this;
    }
	
	/**
     * Устанавливает контроль оплаты
     *
     * @param int $value
     * @return $this
     */
    public function setAfterpaymentOnGoodsCost($value)
    {
        $this->AfterpaymentOnGoodsCost = $value;
        return $this;
    }
	
	/**
     * Вызвать метод CheckPossibilityChangeEW() - проверить возможность создания заявки на изменение данных
     *
     * @param MethodParameters $data
     * @return \NovaPoshta\Models\DataContainerResponse
     */
    public static function CheckPossibilityChangeEW(MethodParameters $data = null)
    {
        return self::sendData(__FUNCTION__, $data);
    }

	/**
     * Вызвать метод getRedirectionOrdersList() - получение данных по заявке на переадресацию
     *
     * @param MethodParameters $data
     * @return \NovaPoshta\Models\DataContainerResponse
     */
    public static function getRedirectionOrdersList(MethodParameters $data = null)
    {
        return self::sendData(__FUNCTION__, $data);
    }

	/**
     * Вызвать метод getReturnOrdersList() - получение данных по заявке на возврат
     *
     * @param MethodParameters $data
     * @return \NovaPoshta\Models\DataContainerResponse
     */
    public static function getReturnOrdersList(MethodParameters $data = null)
    {
        return self::sendData(__FUNCTION__, $data);
    }

}