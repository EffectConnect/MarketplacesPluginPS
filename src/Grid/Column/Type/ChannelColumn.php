<?php

namespace EffectConnect\Marketplaces\Grid\Column\Type;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ChannelColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'channel';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired([
                'channel_id',
                'channel_title',
            ])
            ->setDefaults([
                'clickable' => false,
            ])
            ->setAllowedTypes('channel_id', 'string')
            ->setAllowedTypes('channel_title', 'string')
            ->setAllowedTypes('clickable', 'bool');
    }
}