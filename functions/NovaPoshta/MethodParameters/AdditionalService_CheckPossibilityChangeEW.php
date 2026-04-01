<?php

namespace NovaPoshta\MethodParameters;

/**
 * Параметры метода CheckPossibilityChangeEW модели AdditionalService
 *
 * Class AdditionalService_CheckPossibilityChangeEW
 * @package NovaPoshta\DataMethods
 * @property string Number
 */
class AdditionalService_CheckPossibilityChangeEW extends MethodParameters
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