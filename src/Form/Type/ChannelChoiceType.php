<?php

namespace EffectConnect\Marketplaces\Form\Type;

use EffectConnect\Marketplaces\Form\Type\ChoiceProvider\ChannelChoiceProvider;
use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ChannelChoiceType
 * @package EffectConnect\Marketplaces\Form\Type
 */
class ChannelChoiceType extends CommonAbstractType
{
    /**
     * @var ChannelChoiceProvider
     */
    protected $_channelChoiceProvider;

    /**
     * ShopChoiceType constructor.
     * @param ChannelChoiceProvider $channelChoiceProvider
     */
    public function __construct(
        ChannelChoiceProvider $channelChoiceProvider
    ) {
        $this->_channelChoiceProvider = $channelChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices'  => $this->_channelChoiceProvider->getChoices(),
            'required' => false
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
