<?php

namespace EffectConnect\Marketplaces\Controller;

use Configuration;
use EffectConnect\Marketplaces\Exception\AbstractException;
use EffectConnect\Marketplaces\Filter\AdminChannelMappingFilter;
use EffectConnect\Marketplaces\Grid\AdminChannelMappingGridDefinitionFactory;
use EffectConnect\Marketplaces\LegacyWrappers\LegacyShopContext;
use EffectConnect\Marketplaces\Model\Channel;
use EffectConnect\Marketplaces\Model\ChannelMapping;
use EffectConnect\Marketplaces\Model\Connection;
use EffectConnect\Marketplaces\Service\Api\ChannelListReadApi;
use Exception;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use PrestaShop\PrestaShop\Core\Grid\Filter\GridFilterFormFactory;
use PrestaShop\PrestaShop\Core\Grid\GridFactory;
use PrestaShop\PrestaShop\Core\Grid\Presenter\GridPresenter;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;
use Validate;

/**
 * Class AdminChannelMappingController
 * @package EffectConnect\Marketplaces\Controller
 */
class AdminChannelMappingController extends CompatibleAdminController
{
    /**
     * @var FormBuilder
     */
    protected $_formBuilder;

    /**
     * @var FormHandler
     */
    protected $_formHandler;

    /**
     * @var AdminChannelMappingGridDefinitionFactory
     */
    protected $_gridDefinitionFactory;

    /**
     * @var GridFactory
     */
    protected $_gridFactory;

    /**
     * @var GridFilterFormFactory
     */
    protected $_gridFilterFormFactory;

    /**
     * @var GridPresenter
     */
    protected $_gridPresenter;

    /**
     * @var LegacyShopContext
     */
    protected $_legacyShopContext;

    /**
     * @var ModuleManager
     */
    protected $_moduleManager;

    /**
     * @var ModuleDataProvider
     */
    protected $_moduleDataProvider;

    /**
     * @var TranslatorInterface
     */
    protected $_translator;

    /**
     * @param FormBuilder $formBuilder
     * @param FormHandler $formHandler
     * @param AdminChannelMappingGridDefinitionFactory $gridDefinitionFactory
     * @param GridFactory $gridFactory
     * @param GridFilterFormFactory $gridFilterFormFactory
     * @param GridPresenter $gridPresenter
     * @param LegacyShopContext $legacyShopContext
     * @param ModuleManager $moduleManager
     * @param ModuleDataProvider $moduleDataProvider
     * @param TranslatorInterface $translator
     */
    public function __construct(
        FormBuilder $formBuilder,
        FormHandler $formHandler,
        AdminChannelMappingGridDefinitionFactory $gridDefinitionFactory,
        GridFactory $gridFactory,
        GridFilterFormFactory $gridFilterFormFactory,
        GridPresenter $gridPresenter,
        LegacyShopContext $legacyShopContext,
        ModuleManager $moduleManager,
        ModuleDataProvider $moduleDataProvider,
        TranslatorInterface $translator
    ) {
        $this->_formBuilder           = $formBuilder;
        $this->_formHandler           = $formHandler;
        $this->_gridDefinitionFactory = $gridDefinitionFactory;
        $this->_gridFactory           = $gridFactory;
        $this->_gridFilterFormFactory = $gridFilterFormFactory;
        $this->_gridPresenter         = $gridPresenter;
        $this->_legacyShopContext     = $legacyShopContext;
        $this->_moduleManager         = $moduleManager;
        $this->_moduleDataProvider    = $moduleDataProvider;
        $this->_translator            = $translator;
    }

    /**
     * @param Request $request
     * @param AdminChannelMappingFilter $filters
     * @return Response|null
     */
    public function indexAction(Request $request, AdminChannelMappingFilter $filters)
    {
        $grid = $this->_gridFactory->getGrid($filters);

        return $this->render('@Modules/effectconnect_marketplaces/views/templates/admin/AdminChannelMappingController.index.html.twig', [
            'layoutTitle'             => $this->_translator->trans('Channel mapping', [], 'Modules.Effectconnectmarketplaces.Admin'),
            'AdminChannelMappingGrid' => $this->_gridPresenter->present($grid),
            'enableSidebar'           => true,
            'hasConnections'          => $this->hasConnections(),
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        $groupDefinition = $this->_gridDefinitionFactory->getDefinition();
        $filtersForm     = $this->_gridFilterFormFactory->create($groupDefinition);
        $filtersForm->handleRequest($request);

        $filters = [];
        if ($filtersForm->isSubmitted()) {
            $filters = $filtersForm->getData();
        }
        return $this->redirectToRoute('effectconnect_marketplaces_adminchannelmapping_index', ['filters' => $filters]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response|null
     */
    public function addAction(Request $request)
    {
        $form = $this->_formBuilder->getForm();
        $form->handleRequest($request);

        try {
            $result = $this->_formHandler->handle($form);
            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->_translator->trans('Channel mapping successful created', [], 'Modules.Effectconnectmarketplaces.Admin'));
                return $this->redirectToRoute('effectconnect_marketplaces_adminchannelmapping_index');
            }
        } catch (AbstractException $e) {
            $this->addFlash('error', $this->_translator->trans($e->getMessage(), [], 'Modules.Effectconnectmarketplaces.Admin'));
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('@Modules/effectconnect_marketplaces/views/templates/admin/AdminChannelMappingController.form.html.twig', [
            'layoutTitle'             => $this->_translator->trans('Add channel mapping', [], 'Modules.Effectconnectmarketplaces.Admin'),
            'requireAddonsSearch'     => false,
            'enableSidebar'           => true,
            'AdminChannelMappingForm' => $form->createView(),
            'isAllOrOnlyShopContext'  => $this->_legacyShopContext->isAllOrOnlyShopContext(),
            'hasConnections'          => $this->hasConnections(),
        ]);
    }

    /**
     * @param int $recordId
     * @param Request $request
     * @return RedirectResponse|Response|null
     */
    public function editAction(int $recordId, Request $request)
    {
        $form = $this->_formBuilder->getFormFor($recordId);
        $form->handleRequest($request);

        try {
            $result = $this->_formHandler->handleFor($recordId, $form);
            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->_translator->trans('Channel mapping successful updated', [], 'Modules.Effectconnectmarketplaces.Admin'));
                return $this->redirectToRoute('effectconnect_marketplaces_adminchannelmapping_index');
            }
        } catch (AbstractException $e) {
            $this->addFlash('error', $this->_translator->trans($e->getMessage(), [], 'Modules.Effectconnectmarketplaces.Admin'));
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('@Modules/effectconnect_marketplaces/views/templates/admin/AdminChannelMappingController.form.html.twig', [
            'layoutTitle'             => $this->_translator->trans('Edit channel mapping', [], 'Modules.Effectconnectmarketplaces.Admin'),
            'requireAddonsSearch'     => false,
            'enableSidebar'           => true,
            'AdminChannelMappingForm' => $form->createView(),
            'isAllOrOnlyShopContext'  => $this->_legacyShopContext->isAllOrOnlyShopContext(),
            'hasConnections'          => $this->hasConnections(),
        ]);
    }

    /**
     * @param int $recordId
     * @return RedirectResponse
     */
    public function toggleActiveAction(int $recordId)
    {
        try {
            $record = new ChannelMapping($recordId);
            if (Validate::isLoadedObject($record)) {
                $record->is_active = !intval($record->is_active);
                $record->save();
                $this->addFlash(
                    'success',
                    $this->_translator->trans('The channel mapping has been successfully updated', [], 'Modules.Effectconnectmarketplaces.Admin')
                );
            }
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('effectconnect_marketplaces_adminchannelmapping_index');
    }

    /**
     * @param int $recordId
     * @return RedirectResponse
     */
    public function deleteAction(int $recordId)
    {
        try {
            $record = new ChannelMapping($recordId);
            if (Validate::isLoadedObject($record)) {
                if ($record->delete()) {
                    $this->addFlash(
                        'success',
                        $this->_translator->trans('Channel mapping successfully deleted', [], 'Modules.Effectconnectmarketplaces.Admin')
                    );
                } else {
                    $this->addFlash(
                        'error',
                        $this->_translator->trans('Channel mapping delete failed', [], 'Modules.Effectconnectmarketplaces.Admin')
                    );
                }
            } else {
                $this->addFlash(
                    'error',
                    $this->_translator->trans('Internal error: recordId is invalid', [], 'Modules.Effectconnectmarketplaces.Admin')
                );
            }
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('effectconnect_marketplaces_adminchannelmapping_index');
    }

    /**
     * @param int|null $recordId
     * @return RedirectResponse
     */
    public function retrieveChannelsAction(int $recordId = null)
    {
        // Fetch channels
        foreach (Connection::getListAll() as $connection) {
            try {
                $channels = (new ChannelListReadApi())->getChannelList($connection);
                Channel::upsertChannels($connection, $channels);
                $this->addFlash('success', $this->_translator->trans('Channel list was updated for connection %s', [$connection->name], 'Modules.Effectconnectmarketplaces.Admin'));
            } catch (Throwable $e) {
                $this->addFlash('error', $this->_translator->trans('Channel list could not be updated for connection %s (API message: %s)', [$connection->name, $e->getMessage()], 'Modules.Effectconnectmarketplaces.Admin'));
            }
        }

        // Remove channels that are not linked to any connection anymore
        Channel::clean();

        if ($recordId === null) {
            // Redirect to channel mapping add page
            return $this->redirectToRoute('effectconnect_marketplaces_adminchannelmapping_insert');
        }
        elseif (ChannelMapping::existsInDatabase($recordId)) {
            // Redirect to channel mapping detail page
            return $this->redirectToRoute('effectconnect_marketplaces_adminchannelmapping_edit', ['recordId' => $recordId]);
        }

        // Fallback: redirect to channel mapping overview (current channel mapping probably was deleted because of deleted channel)
        return $this->redirectToRoute('effectconnect_marketplaces_adminchannelmapping_index');
    }

    /**
     * @return bool
     */
    protected function hasConnections()
    {
        return count(Connection::getListAll()) > 0;
    }
}
