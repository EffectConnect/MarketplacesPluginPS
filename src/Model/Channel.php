<?php

namespace EffectConnect\Marketplaces\Model;

use Db;
use DbQuery;
use PrestaShopException;
use EffectConnect\PHPSdk\Core\Model\Response\Channel as SdkChannel;

/**
 * Class Channel
 * @package EffectConnect\Marketplaces\Model
 */
class Channel extends AbstractModel
{
    /**
     * @var int
     */
    public $ec_channel_id;

    /**
     * @var string
     */
    public $ec_channel_type;

    /**
     * @var string|null
     */
    public $ec_channel_subtype;

    /**
     * @var string
     */
    public $ec_channel_title;

    /**
     * @var string
     */
    public $ec_channel_language;

    /**
     * @var array
     */
    public static $definition = [
        'table'     => 'ec_channel',
        'primary'   => 'id_channel',
        'multilang' => false,
        'fields'    => [
            'ec_channel_id' => [
                'type'       => self::TYPE_INT,
                'required'   => true,
                'validate'   => 'isUnsignedInt'
            ],
            'ec_channel_type' => [
                'type'       => self::TYPE_STRING,
                'required'   => true
            ],
            'ec_channel_subtype' => [
                'type'       => self::TYPE_STRING,
                'required'   => false,
                'allow_null' => true
            ],
            'ec_channel_title' => [
                'type'       => self::TYPE_STRING,
                'required'   => true
            ],
            'ec_channel_language' => [
                'type'       => self::TYPE_STRING,
                'required'   => true
            ],
        ]
    ];

    //
    // TODO:
    //   The functions below use SQL!
    //   Can we use Symfony for this?
    //

    /**
     * @return bool
     */
    public static function createDbTable(): bool
    {
        return Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . self::$definition['table'] . '`(
                    `id_channel` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `ec_channel_id` INT(11) UNSIGNED NOT NULL,
                    `ec_channel_type` VARCHAR(16) NOT NULL,
                    `ec_channel_subtype` VARCHAR(16) NULL DEFAULT NULL,
                    `ec_channel_title` VARCHAR(64) NOT NULL,
                    `ec_channel_language` VARCHAR(2) NOT NULL,
                    PRIMARY KEY (`id_channel`),
                    UNIQUE KEY `EC_CHANNEL_ID` (`ec_channel_id`)
                    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')
            ;
    }

    /**
     * @return bool
     */
    public static function removeDbTable(): bool
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . self::$definition['table'] . '`');
    }

    /**
     * @return Channel[]
     */
    public static function getListAll()
    {
        return self::getList();
    }

    /**
     * @param Connection $connection
     * @param SdkChannel[] $sdkChannels
     * @return void
     */
    public static function upsertChannels(Connection $connection, array $sdkChannels)
    {
        $savedChannelIds = [];
        foreach ($sdkChannels as $sdkChannel) {
            $channel = self::findByEcChannelId($sdkChannel->getId());
            if ($channel === null) {
                $channel = new Channel();
            }
            $channel->ec_channel_id       = $sdkChannel->getId();
            $channel->ec_channel_type     = $sdkChannel->getType();
            $channel->ec_channel_subtype  = $sdkChannel->getSubtype();
            $channel->ec_channel_title    = $sdkChannel->getTitle();
            $channel->ec_channel_language = $sdkChannel->getLanguage();
            $channel->save(true);
            $savedChannelIds[] = $channel->id;

            // Save channel-connection relation
            ChannelConnection::upsert($channel->id, $connection->id);
        }

        ChannelConnection::clean($connection->id, $savedChannelIds);
    }

    /**
     * Remove channels that are not linked to any connections anymore (which will also delete the ChannelConnection).
     * @return void
     */
    public static function clean()
    {
        $where = '`id_channel` NOT IN (SELECT `id_channel` FROM `' . _DB_PREFIX_ . ChannelConnection::$definition['table'] . '`)';
        $channels = self::getList($where);
        foreach ($channels as $channel) {
            $channel->delete();
        }
    }

    /**
     * @param int $ecChannelId
     * @return Channel|null
     */
    public static function findByEcChannelId(int $ecChannelId)
    {
        $where = '`ec_channel_id` = ' . $ecChannelId;
        $list = self::getList($where);
        if (count($list) === 1) {
            return $list[0];
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        $deleted = parent::delete();
        if ($deleted) {
            // Remove channel mappings and channels that are related to the deleted connection
            foreach (ChannelConnection::getListByIdChannel($this->id) as $channelConnection) {
                $channelConnection->delete();
            }
            foreach (ChannelMapping::getListByIdChannel($this->id) as $channelConnection) {
                $channelConnection->delete();
            }
        }
        return $deleted;
    }
}