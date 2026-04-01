<?php

namespace NovaPoshta\MethodParameters;

/**
 * Параметры метода CheckPossibilityCreateReturn модели AdditionalServiceGeneral
 *
 * Class AdditionalServiceGeneral_CheckPossibilityCreateReturn
 * @package NovaPoshta\DataMethods
 * @property string Number
 */
class AdditionalServiceGeneral_CheckPossibilityCreateReturn extends MethodParameters
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