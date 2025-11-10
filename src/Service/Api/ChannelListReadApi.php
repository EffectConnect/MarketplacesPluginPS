<?php

namespace EffectConnect\Marketplaces\Service\Api;

use EffectConnect\Marketplaces\Exception\ApiCallFailedException;
use EffectConnect\Marketplaces\Exception\InvalidApiCredentialsException;
use EffectConnect\Marketplaces\Exception\SdkCoreNotInitializedException;
use EffectConnect\Marketplaces\Exception\UnknownException;
use EffectConnect\Marketplaces\Model\Connection;
use EffectConnect\PHPSdk\Core\Interfaces\ResponseContainerInterface;
use EffectConnect\PHPSdk\Core\Model\Response\Channel;
use EffectConnect\PHPSdk\Core\Model\Response\ChannelListReadResponseContainer;
use Exception;

/**
 * Class ChannelListReadApi
 * @package EffectConnect\Marketplaces\Service\Api
 */
class ChannelListReadApi extends AbstractApi
{
    /**
     * @param Connection $connection
     * @return Channel[]
     * @throws ApiCallFailedException
     * @throws SdkCoreNotInitializedException
     * @throws InvalidApiCredentialsException
     * @throws UnknownException
     */
    public function getChannelList(Connection $connection): array
    {
        $this->initializeSdkByConnection($connection);
        $channelListReadResponse = $this->channelListReadCall();
        /** @var ChannelListReadResponseContainer $channelListReadResponse */
        if ($channelListReadResponse instanceof ChannelListReadResponseContainer) {
            return $channelListReadResponse->getChannels();
        }
        return [];
    }

    /**
     * @throws SdkCoreNotInitializedException
     * @throws ApiCallFailedException
     */
    protected function channelListReadCall(): ResponseContainerInterface
    {
        $channelListCall = $this->getSdkCore()->ChannelListCall();
        $apiCall         = $channelListCall->read();
        return $this->callAndResolveResponse($apiCall);
    }
}