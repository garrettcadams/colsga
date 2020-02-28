<?php

namespace WilokeListingTools\Framework\Helpers;

class Message
{
    public static function error($msg, $isReturn = false)
    {
        if (wp_doing_ajax()) {
            wp_send_json_error(
                [
                    'msg' => $msg
                ]
            );
        } else {
            if ($isReturn) {
                return [
                    'status' => 'error',
                    'msg'    => $msg
                ];
            }
            throw new \Exception($msg, 403);
        }
    }
}
