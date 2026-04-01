<?php

namespace NovaPoshta\MethodParameters;

/**
 * Параметры метода checkPossibilityForRedirecting модели AdditionalServiceGeneral
 *
 * Class AdditionalServiceGeneral_checkPossibilityForRedirecting
 * @package NovaPoshta\DataMethods
 * @property string Number
 */
class AdditionalServiceGeneral_checkPossibilityForRedirecting extends MethodParameters
{
	/**
     * Устанавливает номер документа
     *
     * @param string $value
     * @return $this
     */
    public function setNumber($value)
    {
        $this->Number = $value;
        return $this;
    }
}