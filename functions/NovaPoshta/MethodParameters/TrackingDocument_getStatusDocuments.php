<?php

namespace NovaPoshta\MethodParameters;

/**
 * Параметры метода getStatusDocuments модели TrackingDocument
 *
 * Class TrackingDocument_getStatusDocuments
 * @package NovaPoshta\DataMethods
 * @property array Documents
 */
class TrackingDocument_getStatusDocuments extends MethodParameters
{

	/**
     * Устанавливает номера документов
     *
     * @param array $value
     * @return $this
     */
    public function setDocuments(array $value)
    {
        $this->Documents = $value;
        return $this;
    }

}