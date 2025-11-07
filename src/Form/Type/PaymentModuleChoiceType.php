<?php

namespace EffectConnect\Marketplaces\Form\Type;

use EffectConnect\Marketplaces\Form\Type\ChoiceProvider\PaymentModuleChoiceProvider;
use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PaymentModuleChoiceType
 * @package EffectConnect\Marketplaces\Form\Type
 */
class PaymentModuleChoiceType extends CommonAbstractType
{
    /**
     * @var PaymentModuleChoiceProvider
     */
    protected $_paymentModuleChoiceProvider;

    /**
     * ShopChoiceType constructor.
     * @param PaymentModuleChoiceProvider $paymentModuleChoiceProvider
     */
    public function __construct(
        PaymentModuleChoiceProvider $paymentModuleChoiceProvider
    ) {
        $this->_paymentModuleChoiceProvider = $paymentModuleChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices'  => $this->_paymentModuleChoiceProvider->getChoices(),
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
