<?php


namespace NovaPoshta\Models;

use NovaPoshta\Core\BaseModel;

/**
 * Параметры Номенклатуры ТМЦ
 *
 * Class InventoryNomenclature
 * @package NovaPoshta\Models
 * @property string Nomenclature
 * @property int Amount
 */
class InventoryNomenclature extends BaseModel
{
    /**
     * Устанавливает Ref номенклатуры
     *
     * @param string $value
     * @return $this
     */
    public function setNomenclature($value)
    {
        $this->Nomenclature = $value;
        return $this;
    }
	
    /**
     * Устанавливает количество
     *
     * @param int $value
     * @return $this
     */
    public function setAmount($value)
    {
        $this->Amount = $value;
        return $this;
    }
}