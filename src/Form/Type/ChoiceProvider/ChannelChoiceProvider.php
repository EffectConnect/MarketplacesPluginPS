<?php

namespace EffectConnect\Marketplaces\Form\Type\ChoiceProvider;

use EffectConnect\Marketplaces\Model\Channel;
use EffectConnect\Marketplaces\Model\ChannelConnection;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class ChannelChoiceProvider
 * @package EffectConnect\Marketplaces\Form\Type\ChoiceProvider
 */
final class ChannelChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @return array
     */
    public function getChoices()
    {
        $choices = [];
        foreach (Channel::getListAll() as $channel) {
            $channelTitle = $channel->ec_channel_title . ' (' . $channel->ec_channel_id . ')';
            $choices[$channelTitle] = $channel->id;
        }
        ksort($choices, SORT_FLAG_CASE | SORT_STRING);
        return $choices;
    }

    /**
     * @return array
     */
    public function getChoicesAttributes()
    {
        $channels = $this->getChoices();

        $connectionIdsByChannelId = [];
        foreach (ChannelConnection::getListByIdChannels($channels) as $channelConnection) {
            if (isset($connectionIdsByChannelId[$channelConnection->id_channel])) {
                $connectionIdsByChannelId[$channelConnection->id_channel][] = $channelConnection->id_connection;
            } else {
                $connectionIdsByChannelId[$channelConnection->id_channel] = [$channelConnection->id_connection];
            }
        }

        $attributes = [];
        foreach ($channels as $channelTitle => $channelId) {
            $attributes[$channelTitle] = ['data-connection-id' => json_encode($connectionIdsByChannelId[$channelId] ?? [])];
        }
        return $attributes;
    }
}
