<?php

namespace EffectConnect\Marketplaces\Form\Type;

use EffectConnect\Marketplaces\Form\Type\ChoiceProvider\ConnectionChoiceProvider;
use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ConnectionChoiceType
 * @package EffectConnect\Marketplaces\Form\Type
 */
class ConnectionChoiceType extends CommonAbstractType
{
    /**
     * @var ConnectionChoiceProvider
     */
    protected $_connectionChoiceProvider;

    /**
     * ShopChoiceType constructor.
     * @param ConnectionChoiceProvider $connectionChoiceProvider
     */
    public function __construct(
        ConnectionChoiceProvider $connectionChoiceProvider
    ) {
        $this->_connectionChoiceProvider = $connectionChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices'  => $this->_connectionChoiceProvider->getChoices(),
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
