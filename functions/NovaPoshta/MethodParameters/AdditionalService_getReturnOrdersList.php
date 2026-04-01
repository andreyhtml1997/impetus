<?php

namespace NovaPoshta\MethodParameters;

/**
 * Параметры метода getReturnOrdersList модели AdditionalService
 *
 * Class AdditionalService_getReturnOrdersList
 * @package NovaPoshta\DataMethods
 * @property string Number
 */
class AdditionalService_getReturnOrdersList extends MethodParameters
{
	/**
     * Устанавливает Ref документа
     *
     * @param string $value
     * @return $this
     */
    public function setRef($value)
    {
        $this->Ref = $value;
        return $this;
    }
}