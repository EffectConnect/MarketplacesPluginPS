<?php

use EffectConnect\Marketplaces\Manager\TabManager;
use EffectConnect\Marketplaces\Model\Channel;
use EffectConnect\Marketplaces\Model\ChannelConnection;
use EffectConnect\Marketplaces\Model\ChannelMapping;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_4_2_0($object)
{
    return TabManager::addChildTab('AdminConnectionControllerLegacyClass', 'Connections', 'Connections')
        && Channel::createDbTable()
        && ChannelMapping::createDbTable()
        && ChannelConnection::createDbTable();
}
