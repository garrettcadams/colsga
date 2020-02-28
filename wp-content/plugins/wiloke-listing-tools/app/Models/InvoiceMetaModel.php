<?php

namespace WilokeListingTools\Models;

use WilokeListingTools\AlterTable\AlterTableInvoiceMeta;

class InvoiceMetaModel
{
    protected static $tblName;

    public static function addPrefixToMetaName($metaKey)
    {
        return $metaKey = wilokeListingToolsRepository()->get('general:metaboxPrefix').$metaKey;
    }

    /**
     * @return void
     */
    public static function generateTableName($wpdb)
    {
        self::$tblName = $wpdb->prefix.AlterTableInvoiceMeta::$tblName;
    }

    /**
     * @param $invoiceID
     * @param $metaKey
     * @param $val
     *
     * @return false|int
     */
    public static function patch($invoiceID, $metaKey, $val)
    {
        global $wpdb;
        self::generateTableName($wpdb);

        return $wpdb->update(
            self::$tblName,
            [
                'meta_value' => maybe_serialize($val)
            ],
            [
                'invoiceID' => $invoiceID,
                'meta_key'  => $metaKey,
            ],
            [
                '%s'
            ],
            [
                '%d',
                '%s'
            ]
        );
    }

    /**
     * @param $invoiceID
     * @param $metaKey
     * @param $val
     *
     * @return false|int
     */
    public static function update($invoiceID, $metaKey, $val)
    {
        return self::patch($invoiceID, self::addPrefixToMetaName($metaKey), $val);
    }

    /**
     * @param $invoiceID
     * @param $metaKey
     *
     * @return bool|mixed
     */
    public static function get($invoiceID, $metaKey)
    {
        global $wpdb;
        self::generateTableName($wpdb);

        $aResult = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT meta_value FROM ".self::$tblName." WHERE invoiceID=%d AND meta_key=%s ORDER BY ID DESC",
                $invoiceID, self::addPrefixToMetaName($metaKey)
            )
        );

        if (empty($aResult)) {
            return false;
        }

        return maybe_unserialize($aResult);
    }

    /**
     * @param $invoiceID
     * @param $metaKey
     * @param $val
     *
     * @return false|int
     */
    public static function set($invoiceID, $metaKey, $val)
    {
        global $wpdb;

        self::generateTableName($wpdb);

        if (empty(self::get($invoiceID, $metaKey))) {
            return $wpdb->insert(
                self::$tblName,
                [
                    'invoiceID'  => $invoiceID,
                    'meta_key'   => self::addPrefixToMetaName($metaKey),
                    'meta_value' => maybe_serialize($val)
                ],
                [
                    '%d',
                    '%s',
                    '%s'
                ]
            );
        } else {
            return self::patch($invoiceID, self::addPrefixToMetaName($metaKey), $val);
        }
    }

    public static function setInvoiceToken($invoiceID, $token)
    {
        return self::set($invoiceID, 'token', $token);
    }

    public static function getInvoiceToken($invoiceID)
    {
        return self::get($invoiceID, 'token');
    }

    public static function getInvoiceIDByToken($token)
    {
        global $wpdb;
        self::generateTableName($wpdb);

        return $wpdb->get_var(
            $wpdb->prepare(
                "SELECT invoiceID from ".self::$tblName." WHERE meta_key=%s AND meta_value=%s",
                self::addPrefixToMetaName('token'), $token
            )
        );
    }
}
