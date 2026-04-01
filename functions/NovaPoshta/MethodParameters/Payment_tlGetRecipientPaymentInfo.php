<?php

namespace NovaPoshta\MethodParameters;

/**
 * Параметры метода tlGetRecipientPaymentInfo модели Payment
 *
 * Class Payment_tlGetRecipientPaymentInfo
 * @package NovaPoshta\DataMethods
 * @property string Document
 * @property string Language
 * @property int    NewPaymentInfo
 */
class Payment_tlGetRecipientPaymentInfo extends MethodParameters
{

	/**
     * Устанавливает номера документа
     *
     * @param string $value
     * @return $this
     */
    public function setDocument($value)
    {
        $this->Document = $value;
        return $this;
    }

	/**
     * Устанавливает язык
     *
     * @param string $value
     * @return $this
     */
    public function setLanguage($value)
    {
        $this->Language = $value;
        return $this;
    }

}

