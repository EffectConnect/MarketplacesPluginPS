<?php

namespace EffectConnect\Marketplaces\Filter;

use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Class AdminChannelMappingFilter
 * @package EffectConnect\Marketplaces\Filter
 */
final class AdminChannelMappingFilter extends Filters
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaults(): array
    {
        return [
            'limit'     => 10,
            'offset'    => 0,
            'orderBy'   => 'id_channel_mapping',
            'sortOrder' => 'asc',
            'filters'   => [],
        ];
    }
}
