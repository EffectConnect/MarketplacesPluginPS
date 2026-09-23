<?php

namespace EffectConnect\Marketplaces\LegacyWrappers;

use Supplier;

/**
 * In future versions of PrestaShop we might have to use other methods of fetching the correct data.
 */
class SupplierService
{
    /**
     * @param int $idLang
     * @return array
     */
    public function getSuppliers(int $idLang): array
    {
        return Supplier::getSuppliers(false, $idLang);
    }
}