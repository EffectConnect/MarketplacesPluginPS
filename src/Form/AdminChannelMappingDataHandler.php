<?php

namespace EffectConnect\Marketplaces\Form;

use EffectConnect\Marketplaces\Exception\ChannelMappingDuplicateEntryException;
use EffectConnect\Marketplaces\Model\ChannelMapping;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use Validate;

/**
 * Class AdminChannelMappingDataHandler
 * @package EffectConnect\Marketplaces\Form
 */
class AdminChannelMappingDataHandler implements FormDataHandlerInterface
{
    /**
     * {@inheritdoc}
     * @throws ChannelMappingDuplicateEntryException
     */
    public function create(array $data)
    {
        $record = new ChannelMapping();
        $record = $this->assignData($record, $data);
        $this->validate($record);
        return $record->save();
    }

    /**
     * {@inheritdoc}
     * @throws ChannelMappingDuplicateEntryException
     */
    public function update($id, array $data)
    {
        $record = new ChannelMapping(intval($id));
        if (Validate::isLoadedObject($record)) {
            $record = $this->assignData($record, $data);
            $this->validate($record);
        }
        return $record->save();
    }

    /**
     * @param ChannelMapping $record
     * @param array $data
     * @return ChannelMapping
     */
    protected function assignData(ChannelMapping $record, array $data)
    {
        $record->is_active                      = intval($data['is_active']);
        $record->id_channel                     = intval($data['id_channel']);
        $record->id_connection                  = intval($data['id_connection']);
        $record->order_import_id_payment_module = intval($data['order_import_id_payment_module']);

        return $record;
    }

    /**
     * @param ChannelMapping $record
     * @return void
     * @throws ChannelMappingDuplicateEntryException
     */
    protected function validate(ChannelMapping $record)
    {
        if (ChannelMapping::existsByIdConnectionAndIdChannel($record)) {
            throw new ChannelMappingDuplicateEntryException();
        }
    }
}
