<?php

namespace NovaPoshta\MethodParameters;

/**
 * Параметры метода getMoneyTransferDocuments модели InternetDocument
 *
 * Class InternetDocument_getDocumentList
 * @package NovaPoshta\DataMethods
 * @property string IntDocNumber
 * @property string InfoRegClientBarcodes
 * @property string DeliveryDateTime
 * @property string RecipientDateTime
 * @property string CreateTime
 * @property string SenderRef
 * @property string RecipientRef
 * @property float  WeightFrom
 * @property float  WeightTo
 * @property float  CostFrom
 * @property float  CostTo
 * @property int    SeatsAmountFrom
 * @property int    SeatsAmountTo
 * @property float  CostOnSiteFrom
 * @property float  CostOnSiteTo
 * @property array  StateIds
 * @property string DateTime
 * @property string DateTimeFrom
 * @property string DateTimeTo
 * @property bool   isAfterpayment
 * @property int    Page
 * @property string OrderField
 * @property string OrderDirection
 * @property string ScanSheetRef
 * @property bool   GetFullList
 */
class InternetDocument_getMoneyTransferDocuments extends MethodParameters
{
    /**
     * Сортировка по убыванию
     */
    const ORDER_DIRECTION_DESC = 'DESC';
    /**
     * Сортировка по возрастанию
     */
    const ORDER_DIRECTION_ASC = 'ASC';

    /**
     * Сортировка по полю номер документа
     */
    const ORDER_FIELD_IntDocNumber = 'IntDocNumber';
    /**
     * Сортировка по полю дата отправки
     */
    const ORDER_FIELD_DateTime = 'DateTime';
    /**
     * Сортировка по полю дата создания
     */
    const ORDER_FIELD_CreateTime = 'CreateTime';
    /**
     * Сортировка по полю статус доставки
     */
    const ORDER_FIELD_StateId = 'StateId';

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
     * Получить номер документа
     *
     * @return string
     */
    public function getIntDocNumber()
    {
        return $this->IntDocNumber;
    }

    /**
     * Устанавливает номер внутреннего заказа клиента
     *
     * @param string $value
     * @return $this
     */
    public function setInfoRegClientBarcodes($value)
    {
        $this->InfoRegClientBarcodes = $value;
        return $this;
    }

    /**
     * Получить номер внутреннего заказа клиента
     *
     * @return string
     */
    public function getInfoRegClientBarcodes()
    {
        return $this->InfoRegClientBarcodes;
    }

    /**
     * Устанавливает дата доставки
     *
     * @param string $value
     * @return $this
     */
    public function setDeliveryDateTime($value)
    {
        $this->DeliveryDateTime = $value;
        return $this;
    }

    /**
     * Получить дата доставки
     *
     * @return string
     */
    public function getDeliveryDateTime()
    {
        return $this->DeliveryDateTime;
    }

    /**
     * Устанавливает фактическую дату и время получения
     *
     * @param string $value
     * @return $this
     */
    public function setRecipientDateTime($value)
    {
        $this->RecipientDateTime = $value;
        return $this;
    }

    /**
     * Получить фактическую дату и время получения
     *
     * @return string
     */
    public function getRecipientDateTime()
    {
        return $this->RecipientDateTime;
    }

    /**
     * Устанавливает дату и время создания ЕН
     *
     * @param string $value
     * @return $this
     */
    public function setCreateTime($value)
    {
        $this->CreateTime = $value;
        return $this;
    }

    /**
     * Получить дату и время создания ЕН
     *
     * @return string
     */
    public function getCreateTime()
    {
        return $this->CreateTime;
    }

    /**
     * Устанавливает идентификатор отправителя
     *
     * @param string $value
     * @return $this
     */
    public function setSenderRef($value)
    {
        $this->SenderRef = $value;
        return $this;
    }

    /**
     * Получить идентификатор отправителя
     *
     * @return string
     */
    public function getSenderRef()
    {
        return $this->SenderRef;
    }

    /**
     * Устанавливает идентификатор получателя
     *
     * @param string $value
     * @return $this
     */
    public function setRecipientRef($value)
    {
        $this->RecipientRef = $value;
        return $this;
    }

    /**
     * Получить идентификатор получателя
     *
     * @return string
     */
    public function getRecipientRef()
    {
        return $this->RecipientRef;
    }

    /**
     * Устанавливает статусы
     *
     * @param array $value
     * @return $this
     */
    public function setStateIds(array $value)
    {
        $this->StateIds = $value;
        return $this;
    }

    /**
     * Получить статусы
     *
     * @return string
     */
    public function getStateIds()
    {
        return $this->StateIds;
    }

    /**
     * Устанавливает дату отправки
     *
     * @param string $value
     * @return $this
     */
    /* public function setDate($value)
    {
        $this->Date = $value;
        return $this;
    } */

    /**
     * Получить дату отправки
     *
     * @return string
     */
    /* public function getDate()
    {
        return $this->Date;
    } */

    /**
     * Устанавливает дату от
     *
     * @param string $value
     * @return $this
     */
    public function setDateFrom($value)
    {
        $this->DateFrom = $value;
        return $this;
    }

    /**
     * Получить дату от
     *
     * @return string
     */
    public function getDateFrom()
    {
        return $this->DateFrom;
    }

    /**
     * Устанавливает дату до
     *
     * @param string $value
     * @return $this
     */
    public function setDateTo($value)
    {
        $this->DateTo = $value;
        return $this;
    }

    /**
     * Получить дату до
     *
     * @return string
     */
    public function getDateTo()
    {
        return $this->DateTo;
    }

    /**
     * Устанавливает количество на страницу
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
     * Получитьстраницу
     *
     * @return string
     */
    public function getPage()
    {
        return $this->Page;
    }

    /**
     * Устанавливает параметр сортировки
     *
     * @param string $value
     * @return $this
     */
    public function setOrderField($value)
    {
        $this->OrderField = $value;
        return $this;
    }

    /**
     * Получить параметр сортировки
     *
     * @return string
     */
    public function getOrderField()
    {
        return $this->OrderField;
    }

	/**
     * Устанавливает Включение или отключение постраничной загрузки (0 - работает постраничная загрузка, 1 - весь список)
     *
     * @param string $value
     * @return $this
     */
    public function setGetFullList($value)
    {
        $this->GetFullList = $value;
        return $this;
    }
}