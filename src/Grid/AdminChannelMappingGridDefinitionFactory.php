<?php

namespace EffectConnect\Marketplaces\Grid;

use EffectConnect\Marketplaces\Form\Type\ChannelChoiceType;
use EffectConnect\Marketplaces\Form\Type\ConnectionChoiceType;
use EffectConnect\Marketplaces\Form\Type\PaymentModuleChoiceType;
use EffectConnect\Marketplaces\Grid\Column\Type\ChannelColumn;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class AdminChannelMappingGridDefinitionFactory
 * @package EffectConnect\Marketplaces\Grid
 */
final class AdminChannelMappingGridDefinitionFactory extends AbstractGridDefinitionFactory
{
   /**
     * @var string
     */
    private $resetFiltersUrl;

    /**
     * @var string
     */
    private $redirectUrl;

    /**
     * AdminConnectionGridDefinitionFactory constructor.
     * @param HookDispatcherInterface $hookDispatcher
     * @param string $resetFiltersUrl
     * @param string $redirectUrl
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        string $resetFiltersUrl,
        string $redirectUrl
    ) {
        $this->resetFiltersUrl = $resetFiltersUrl;
        $this->redirectUrl     = $redirectUrl;
        parent::__construct($hookDispatcher);
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'adminconnectiongrid';
    }

   /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Channel mapping', [], 'Modules.Effectconnectmarketplaces.Admin');
    }

   /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new DataColumn('id_channel_mapping'))
                    ->setName($this->trans('ID', [], 'Modules.Effectconnectmarketplaces.Admin'))
                    ->setOptions([
                        'field' => 'id_channel_mapping',
                    ])
            )
            ->add(
                (new ToggleColumn('is_active'))
                    ->setName($this->trans('Active', [], 'Modules.Effectconnectmarketplaces.Admin'))
                    ->setOptions([
                        'field'            => 'is_active',
                        'primary_field'    => 'id_channel_mapping',
                        'route'            => 'effectconnect_marketplaces_adminchannelmapping_active_toggle',
                        'route_param_name' => 'recordId',
                    ])
            )
            ->add(
                (new ChannelColumn('channel'))
                    ->setName($this->trans('Channel', [], 'Modules.Effectconnectmarketplaces.Admin'))
                    ->setOptions([
                        'channel_id'    => 'channel_id',
                        'channel_title' => 'channel_title',
                    ])
            )
            ->add(
                (new DataColumn('connection'))
                    ->setName($this->trans('Connection', [], 'Modules.Effectconnectmarketplaces.Admin'))
                    ->setOptions([
                        'field' => 'connection_name',
                    ])
            )
            ->add(
                (new DataColumn('payment_module_name'))
                    ->setName($this->trans('Payment method', [], 'Modules.Effectconnectmarketplaces.Admin'))
                    ->setOptions([
                        'field' => 'payment_module_name',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Modules.Effectconnectmarketplaces.Admin'))
                    ->setOptions([
                        'actions' => $this->getRowActions(),
                    ])
            )
        ;
    }

   /**
     * {@inheritdoc}
     *
     * Define filters and associate them with columns.
     * Note that you can add filters that are not associated with any column.
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_channel_mapping', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('id_channel_mapping')
            )
            ->add(
                (new Filter('is_active', YesAndNoChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('is_active')
            )
            ->add(
                (new Filter('id_channel', ChannelChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('channel')
            )
            ->add(
                (new Filter('id_connection', ConnectionChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('connection')
            )
            ->add(
                (new Filter('payment_module_id', PaymentModuleChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('payment_module_name')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'data-url'      => $this->resetFiltersUrl,
                            'data-redirect' => $this->redirectUrl,
                        ],
                    ])
                    ->setAssociatedColumn('actions')
            )
        ;
    }

    /**
     * Extracted row action definition into separate method.
     */
    private function getRowActions(): RowActionCollection
    {
        return (new RowActionCollection())
            ->add(
                (new LinkRowAction('edit'))
                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                    ->setOptions([
                        'route'             => 'effectconnect_marketplaces_adminchannelmapping_edit',
                        'route_param_name'  => 'recordId',
                        'route_param_field' => 'id_channel_mapping',
                    ])
                    ->setIcon('edit')
            )
            ->add(
                (new LinkRowAction('delete'))
                    ->setName($this->trans('Delete', [], 'Admin.Actions'))
                    ->setOptions([
                        'route'             => 'effectconnect_marketplaces_adminchannelmapping_delete',
                        'route_param_name'  => 'recordId',
                        'route_param_field' => 'id_channel_mapping',
                    ])
                    ->setIcon('delete')
            )
        ;
    }
}
