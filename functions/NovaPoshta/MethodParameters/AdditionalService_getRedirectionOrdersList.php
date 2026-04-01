<?php

namespace NovaPoshta\MethodParameters;

/**
 * Параметры метода getRedirectionOrdersList модели AdditionalService
 *
 * Class AdditionalService_getRedirectionOrdersList
 * @package NovaPoshta\DataMethods
 * @property string Number
 */
class AdditionalService_getRedirectionOrdersList extends MethodParameters
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