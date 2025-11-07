<?php

namespace EffectConnect\Marketplaces\Form;

use EffectConnect\Marketplaces\Form\Type\ChoiceProvider\ChannelChoiceProvider;
use EffectConnect\Marketplaces\Form\Type\ChoiceProvider\ConnectionChoiceProvider;
use EffectConnect\Marketplaces\Form\Type\ChoiceProvider\PaymentModuleChoiceProvider;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AdminChannelMappingFormType
 * @package EffectConnect\Marketplaces\Form
 */
class AdminChannelMappingFormType extends TranslatorAwareType
{
    /**
     * @var ChannelChoiceProvider
     */
    protected $_channelChoiceProvider;

    /**
     * @var ConnectionChoiceProvider
     */
    protected $_connectionChoiceProvider;

    /**
     * @var PaymentModuleChoiceProvider
     */
    protected $_paymentModuleChoiceProvider;

    /**
     * AdminConnectionFormType constructor.
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param ChannelChoiceProvider $channelChoiceProvider
     * @param ConnectionChoiceProvider $connectionChoiceProvider
     * @param PaymentModuleChoiceProvider $paymentModuleChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        ChannelChoiceProvider $channelChoiceProvider,
        ConnectionChoiceProvider $connectionChoiceProvider,
        PaymentModuleChoiceProvider $paymentModuleChoiceProvider
    ) {
        $this->_channelChoiceProvider       = $channelChoiceProvider;
        $this->_connectionChoiceProvider    = $connectionChoiceProvider;
        $this->_paymentModuleChoiceProvider = $paymentModuleChoiceProvider;
        parent::__construct($translator, $locales);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // General fields
            ->add('id_channel_mapping', HiddenType::class)
            ->add('is_active', SwitchType::class, [
                'label'  => $this->trans('Active', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
            ->add('id_channel', ChoiceType::class, [
                'choices'     => $this->_channelChoiceProvider->getChoices(),
                'choice_attr' => $this->_channelChoiceProvider->getChoicesAttributes(),
                'required'    => true,
                'label'       => $this->trans('Channel', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
            ->add('id_connection', ChoiceType::class, [
                'choices'  => $this->_connectionChoiceProvider->getChoices(),
                'required' => true,
                'label'    => $this->trans('Connection', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
            ->add('order_import_id_payment_module', ChoiceType::class, [
                'choices'     => $this->_paymentModuleChoiceProvider->getChoices(),
                'required'    => true,
                'constraints' => [new NotBlank()],
                'label'       => $this->trans('Payment method', 'Modules.Effectconnectmarketplaces.Admin'),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Modules.Effectconnectmarketplaces.Admin',
        ]);
    }
}
