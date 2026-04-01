<?php

namespace NovaPoshta\MethodParameters;

/**
 * Параметры метода payEwByLoyaltyPoints модели InternetDocument
 *
 * Class InternetDocument_payEwByLoyaltyPoints
 * @package NovaPoshta\DataMethods
 * @property array Documents
 */
class InternetDocument_payEwByLoyaltyPoints extends MethodParameters
{
    /**
     * Устанавливает номер документа
     *
     * @param $value
     * @return $this
     */
    public function setDocument($value)
    {
        $this->DocumentNumber = $value;
        return $this;
    }
	
	/**
     * Устанавливает сумму оплаты
     *
     * @param $value
     * @return $this
     */
    public function setAmount($value)
    {
        $this->DebitingAmount = $value;
        return $this;
    }
}