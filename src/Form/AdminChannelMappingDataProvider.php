<?php

namespace EffectConnect\Marketplaces\Form;

use EffectConnect\Marketplaces\Model\ChannelMapping;
use PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use Validate;

/**
 * Class AdminChannelMappingDataProvider
 * @package EffectConnect\Marketplaces\Form
 */
class AdminChannelMappingDataProvider implements FormDataProviderInterface
{
   /**
     * {@inheritdoc}
     */
    public function getData($id)
    {
        $objectPresenter = new ObjectPresenter();

        $record = new ChannelMapping(intval($id));
        if (Validate::isLoadedObject($record)) {
            return $objectPresenter->present($record);
        }

        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        $objectPresenter = new ObjectPresenter();

        $record = new ChannelMapping();
        return $objectPresenter->present($record);
    }
}
