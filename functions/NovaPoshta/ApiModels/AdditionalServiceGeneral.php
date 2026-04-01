<?php

namespace NovaPoshta\ApiModels;

use NovaPoshta\Core\ApiModel;
use NovaPoshta\Models\CounterpartyContact;
use NovaPoshta\Config;
use NovaPoshta\MethodParameters\MethodParameters;
use stdClass;

/**
 * AdditionalServiceGeneral - Модель для оформления дополнительных услуг
 *
 * @property CounterpartyContact Recipient
 * @property string              Ref
 * @property string              Note
 * @property string              IntDocNumber
 * @property string              Customer
 * @property string              ServiceType
 * @property string              PayerType
 * @property string              PaymentMethod
 * @property string              RecipientContactName
 * @property string              RecipientPhone
 * @property string              RecipientWarehouse
 *
 * Class AdditionalServiceGeneral
 * @package NovaPoshta\ApiModels
 */
class AdditionalServiceGeneral extends ApiModel
{
	
	public function __construct()
    {
        $this->OrderType = 'orderRedirecting';
    }
	
	private function getDataInternetDocument()
	{
        $data = new stdClass();

        foreach ($this as $key => $attr) {
            if($attr instanceof CounterpartyContact){
                $data->{$key} = $attr->getRef();
				$data->{'RecipientWarehouse'} = $attr->getAddress();
				$data->{'RecipientPhone'} = $attr->getPhone();
			} elseif (isset($this->{$key})) {
                $data->{$key} = $attr;
            }
        }

        return $data;
    }
	
	/**
     * Вызвать метод save() - создание ЭН переадресации
     *
     * @return \NovaPoshta\Models\DataContainerResponse
     */
    public function save()
    {
        return $this->sendData(__FUNCTION__, $this->getDataInternetDocument());
    }
	
	/**
     * Вызвать метод delete() - удаление заявки
     *
     * @return \NovaPoshta\Models\DataContainerResponse
     */
    public function delete()
    {
        return $this->sendData(__FUNCTION__, $this->getDataInternetDocument());
    }
	
    /**
     * Устанавливает реф заявки
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
     * Устанавливает причину переадресации
     *
     * @param $value
     * @return $this
     */
    public function setNote($value)
    {
        $this->Note = $value;
        return $this;
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
     * Устанавливает заказчика переадресации (получателю запрещено менять данные получателя)
     *
     * @param string $value
     * @return $this
     */
    public function setCustomer($value)
    {
        $this->Customer = $value;
        return $this;
    }
	
    /**
     * Устанавливает технологию доставки
     *
     * @param string $value
     * @return $this
     */
    public function setServiceType($value)
    {
        $this->ServiceType = $value;
        return $this;
    }

	/**
     * Устанавливает тип заявки
     *
     * @param string $value
     * @return $this
     */
    public function setOrderType($value)
    {
        $this->OrderType = $value;
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
     * Устанавливает блок адреса доставки возврата
     *
     * @param string $value
     * @return $this
     */
    public function setReturnAddressRef($value)
    {
        $this->ReturnAddressRef = $value;
        return $this;
    }
	
    /**
     * Устанавливает причину возврата
     *
     * @param string $value
     * @return $this
     */
    public function setReason($value)
    {
        $this->Reason = $value;
        return $this;
    }

    /**
     * Устанавливает подтип причины возврата
     *
     * @param string $value
     * @return $this
     */
    public function setSubtypeReason($value)
    {
        $this->SubtypeReason = $value;
        return $this;
    }
	
	/**
     * Устанавливает идентификатор отделения получателя
     *
     * @param string $value
     * @return $this
     */
    public function setRecipientWarehouse($value)
    {
        $this->RecipientWarehouse = $value;
        return $this;
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
     * Устанавливает телефон получателя
     *
     * @param string $value
     * @return $this
     */
    public function setRecipientPhone($value)
    {
        $this->RecipientPhone = $value;
        return $this;
    }

    /**
     * Устанавливает ФИО контактного лица получателя
     *
     * @param string $value
     * @return $this
     */
    public function setRecipientContactName($value)
    {
        $this->RecipientContactName = $value;
        return $this;
    }
	
	/**
     * Вызвать метод checkPossibilityForRedirecting() - проверить возможность создания заявки на переадресацию отправления
     *
     * @param MethodParameters $data
     * @return \NovaPoshta\Models\DataContainerResponse
     */
    public static function checkPossibilityForRedirecting(MethodParameters $data = null)
    {
        return self::sendData(__FUNCTION__, $data);
    }

	/**
     * Вызвать метод CheckPossibilityCreateReturn() - проверить возможность создания заявки на возврат отправления
     *
     * @param MethodParameters $data
     * @return \NovaPoshta\Models\DataContainerResponse
     */
    public static function CheckPossibilityCreateReturn(MethodParameters $data = null)
    {
        return self::sendData(__FUNCTION__, $data);
    }
	
}