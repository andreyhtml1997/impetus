<?php

namespace NovaPoshta\MethodParameters;

/**
 * Параметры метода initPayout модели Payment
 *
 * Class Payment_initPayout
 * @package NovaPoshta\DataMethods
 * @property string Phone
 * @property string Number
 */
class Payment_initPayout extends MethodParameters
{

	/**
     * Устанавливает номер телефона
     *
     * @param string $value
     * @return $this
     */
    public function setPhone($value)
    {
        $this->Phone = $value;
        return $this;
    }

	/**
     * Устанавливает номер ТТН (для смены, когда ТТН уже есть)
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

