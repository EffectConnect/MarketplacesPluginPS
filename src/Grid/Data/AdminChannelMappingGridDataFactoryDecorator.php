<?php

namespace EffectConnect\Marketplaces\Grid\Data;

use Module;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;

/**
 * https://devdocs.prestashop-project.org/9/development/components/grid/#grid-data
 */
final class AdminChannelMappingGridDataFactoryDecorator implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $gridDataFactory;

    /**
     * @param GridDataFactoryInterface $gridDataFactory
     */
    public function __construct(
        GridDataFactoryInterface $gridDataFactory
    ) {
        $this->gridDataFactory = $gridDataFactory;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return GridData
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $productData = $this->gridDataFactory->getData($searchCriteria);

        $productRecords = $this->applyModifications($productData->getRecords());

        return new GridData(
            $productRecords,
            $productData->getRecordsTotal(),
            $productData->getQuery()
        );
    }

    /**
     * @param RecordCollectionInterface $records
     * @return RecordCollection
     */
    private function applyModifications(RecordCollectionInterface $records)
    {
        $modifiedRecords = [];

        foreach ($records as $record) {
            $module = Module::getInstanceById($record['order_import_id_payment_module']);
            $record['payment_module_name'] = $module ? $module->displayName : '-';
            $modifiedRecords[] = $record;
        }

        return new RecordCollection($modifiedRecords);
    }
}