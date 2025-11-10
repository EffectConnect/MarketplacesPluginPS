<?php

namespace EffectConnect\Marketplaces\Model;

use Db;
use PrestaShopException;

/**
 * Class ChannelMapping
 * @package EffectConnect\Marketplaces\Model
 */
class ChannelMapping extends AbstractModel
{
    /**
     * @var bool
     */
    public $is_active;

    /**
     * @var int
     */
    public $id_connection;

    /**
     * @var int
     */
    public $id_channel;

    /**
     * @var int|null
     */
    public $order_import_id_payment_module = null;

    /**
     * @var array
     */
    public static $definition = [
        'table'     => 'ec_channel_mapping',
        'primary'   => 'id_channel_mapping',
        'multilang' => false,
        'fields'    => [
            'is_active' => [
                'type'     => self::TYPE_BOOL,
                'required' => true
            ],
            'id_connection' => [
                'type'       => self::TYPE_INT,
                'required'   => true,
                'validate'   => 'isUnsignedInt'
            ],
            'id_channel' => [
                'type'       => self::TYPE_INT,
                'required'   => true,
                'validate'   => 'isUnsignedInt'
            ],
            'order_import_id_payment_module' => [
                'type'       => self::TYPE_INT,
                'required'   => false,
                'allow_null' => true
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
    public static function createDbTable()
    {
        return Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . self::$definition['table'] . '`(
                    `id_channel_mapping` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT \'0\',
                    `id_connection` INT(11) UNSIGNED NOT NULL,
                    `id_channel` INT(11) UNSIGNED NOT NULL,
                    `order_import_id_payment_module` INT(11) UNSIGNED NULL DEFAULT NULL,
                    PRIMARY KEY (`id_channel_mapping`),
                    UNIQUE KEY `ID_CHANNEL_ID_CONNECTION` (`id_channel`, `id_connection`),
                    KEY `ID_CHANNEL` (`id_channel`),
                    KEY `ID_CONNECTION` (`id_connection`)
                    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8')
            ;
    }

    /**
     * @return bool
     */
    public static function removeDbTable()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . self::$definition['table'] . '`');
    }

    /**
     * @param int $idConnection
     * @param int $idChannel
     * @return ChannelMapping|null
     */
    public static function getActiveByIdConnectionAndIdChannel(int $idConnection, int $idChannel)
    {
        $where = '`id_channel` = ' . $idChannel . ' AND `id_connection` = ' . $idConnection . ' AND `is_active` = 1';
        $list = self::getList($where);
        if (count($list) === 1) {
            return $list[0];
        }
        return null;
    }

    /**
     * @param ChannelMapping $channelMapping
     * @return bool
     */
    public static function existsByIdConnectionAndIdChannel(ChannelMapping $channelMapping)
    {
        $where = '`id_channel` = ' . $channelMapping->id_channel . ' AND `id_connection` = ' . $channelMapping->id_connection;
        if ($channelMapping->id !== null) {
            // Exclude record that the user is trying to update
            $where .= ' AND `id_channel_mapping` != ' . $channelMapping->id;
        }
        $list = self::getList($where);
        return count($list) > 0;
    }

    /**
     * @param int $idChannel
     * @return ChannelMapping[]
     */
    public static function getListByIdChannel(int $idChannel)
    {
        $where = '`id_channel` =' . $idChannel;
        return self::getList($where);
    }

    /**
     * @param int $idConnection
     * @return ChannelMapping[]
     */
    public static function getListByIdConnection(int $idConnection)
    {
        $where = '`id_connection` =' . $idConnection;
        return self::getList($where);
    }
}