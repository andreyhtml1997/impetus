<?php

namespace NovaPoshta\ApiModels;

use NovaPoshta\Core\ApiModel;
use NovaPoshta\MethodParameters\MethodParameters;
use stdClass;

/**
 * TrackingDocument - Модель для получения актуальных данных по ЭН
 *
 * Class TrackingDocument
 * @package NovaPoshta\ApiModels
 */
class TrackingDocument extends ApiModel
{
	/**
     * Вызвать метод getStatusDocuments() - получает список ЭН
     *
     * @param MethodParameters $data
     * @return \NovaPoshta\Models\DataContainerResponse
     */
    public static function getStatusDocuments(MethodParameters $data = null)
    {
        return self::sendData(__FUNCTION__, $data);
    }
}