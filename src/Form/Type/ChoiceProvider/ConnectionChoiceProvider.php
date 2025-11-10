<?php

namespace EffectConnect\Marketplaces\Form\Type\ChoiceProvider;

use EffectConnect\Marketplaces\Model\Connection;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class ConnectionChoiceProvider
 * @package EffectConnect\Marketplaces\Form\Type\ChoiceProvider
 */
final class ConnectionChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @return array
     */
    public function getChoices()
    {
        $choices = [];
        foreach (Connection::getListAll() as $connection) {
            $choices[$connection->name] = $connection->id;
        }
        ksort($choices);
        return $choices;
    }
}
