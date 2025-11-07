<?php

namespace EffectConnect\Marketplaces\Exception;

/**
 * Class ChannelMappingDuplicateEntryException
 * @package EffectConnect\Marketplaces\Exception
 * @method string __construct()
 */
class ChannelMappingDuplicateEntryException extends AbstractException
{
    /**
     * @inheritDoc
     */
    protected const MESSAGE_FORMAT = 'A record with this channel and this connection already exists.';
}