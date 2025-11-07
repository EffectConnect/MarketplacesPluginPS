<?php

namespace EffectConnect\Marketplaces\Model;

use Db;
use PrestaShopException;

/**
 * Class ChannelConnection
 * @package EffectConnect\Marketplaces\Model
 */
class ChannelConnection extends AbstractModel
{
    /**
     * @var int
     */
    public $id_channel;

    /**
     * @var int
     */
    public $id_connection;

    /**
     * @var array
     */
    public static $definition = [
        'table'     => 'ec_channel_connection',
        'primary'   => 'id_channel_connection',
        'multilang' => false,
        'fields'    => [
            'id_channel' => [
                'type'       => self::TYPE_INT,
                'required'   => true,
                'validate'   => 'isUnsignedInt'
            ],
            'id_connection' => [
                'type'       => self::TYPE_INT,
                'required'   => true,
                'validate'   => 'isUnsignedInt'
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
                    `id_channel_connection` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `id_channel` INT(11) UNSIGNED NOT NULL,
                    `id_connection` INT(11) UNSIGNED NOT NULL,
                    PRIMARY KEY (`id_channel_connection`),
                    UNIQUE KEY `ID_CHANNEL_ID_CONNECTION` (`id_channel`, `id_connection`),
                    KEY `ID_CHANNEL` (`id_channel`),
                    KEY `ID_CONNECTION` (`id_connection`)
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
     * @param int $idChannel
     * @param int $idConnection
     * @return void
     */
    public static function upsert(int $idChannel, int $idConnection)
    {
        $channelConnection = self::findByIdChannelAndIdConnection($idChannel, $idConnection);
        if ($channelConnection === null) {
            $channelConnection = new ChannelConnection();
        }
        $channelConnection->id_channel = $idChannel;
        $channelConnection->id_connection = $idConnection;
        $channelConnection->save(true);
    }

    /**
     * Remove all channels that are currently linked to the given connection ID that don't exist anymore.
     *
     * @param int $idConnection
     * @param array $idChannelIdsToKeep
     * @return void
     */
    public static function clean(int $idConnection, array $idChannelIdsToKeep)
    {
        $where = '`id_connection` = ' . $idConnection;
        if (count($idChannelIdsToKeep) > 0) {
            $where .= ' AND `id_channel` NOT IN (' .  implode(', ', $idChannelIdsToKeep) . ')';
        }
        $unsavedRecords = self::getList($where);
        foreach ($unsavedRecords as $unsavedRecord) {
            $unsavedRecord->delete();
        }
    }

    /**
     * @param int $idChannel
     * @param int $idConnection
     * @return mixed|null
     */
    public static function findByIdChannelAndIdConnection(int $idChannel, int $idConnection)
    {
        $where = '`id_channel` = ' . $idChannel . ' AND `id_connection` = ' . $idConnection;
        $list = self::getList($where);
        if (count($list) === 1) {
            return $list[0];
        }
        return null;
    }

    /**
     * @param array $idChannels
     * @return ChannelConnection[]
     */
    public static function getListByIdChannels(array $idChannels)
    {
        if (count($idChannels) === 0) {
            return [];
        }
        $where = '`id_channel` IN (' . implode(', ', $idChannels) . ')';
        return self::getList($where);
    }

    /**
     * @param int $idChannel
     * @return ChannelConnection[]
     */
    public static function getListByIdChannel(int $idChannel)
    {
        $where = '`id_channel` =' . $idChannel;
        return self::getList($where);
    }

    /**
     * @param int $idConnection
     * @return ChannelConnection[]
     */
    public static function getListByIdConnection(int $idConnection)
    {
        $where = '`id_connection` =' . $idConnection;
        return self::getList($where);
    }
}