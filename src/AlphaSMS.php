<?php

namespace alphasms\gateway;

class AlphaSMS
{
    protected static $api_url = 'https://alphasms.ua/api/http.php?version=http&';

    protected static $conf = [
        'api_key' => '',
        'sms_sender' => 'Site-Code',
        'date_time' => null,
        'viber_sender' => '',
        'viber_lifetime' => 60,
        'viber_force_sms' => null,
    ];

    public function __set($param, $data = null)
    {
        if ($param == 'config' && !empty($data)) {
            static::$conf = array_merge(static::$conf, $data);
        }
    }

    public static function setApiKey($api_key)
    {
        static::$conf['api_key'] = $api_key;
    }

    public static function getApiKey()
    {
        return static::$conf['api_key'];
    }

    public static function setSmsSender($sms_sender)
    {
        static::$conf['sms_sender'] = $sms_sender;
    }

    public static function setViberSender($viber_sender)
    {
        static::$conf['viber_sender'] = $viber_sender;
    }

    public static function setViberLifetime($viber_lifetime)
    {
        static::$conf['viber_lifetime'] = (int)$viber_lifetime;
    }

    public static function setViberForceSms($viber_force_sms)
    {
        static::$conf['viber_force_sms'] = empty($viber_force_sms) ? null : 1;
    }

    private static function prepare_phone($phone)
    {
        return preg_replace('/\D/', '', $phone);
    }

    public static function getBalance()
    {
        $request = static::curl(['command' => 'balance']);

        if (empty($request)) {
            return 0;
        }

        if (preg_match('!^balance\:([0-9\.\-]+)$!si', trim($request), $balance_arr)) {
            return floatval($balance_arr['1']);
        }

        return $request;
    }

    public static function getStatus($queue_id)
    {
        if (empty($queue_id) || !is_numeric($queue_id)) {
            return false;
        }

        $request = static::curl(['command' => 'receive', 'id' => intval(trim($queue_id))]);

        if (empty($request)) {
            return false;
        }

        if (preg_match('!status\:(.*?)[\s]{1,}!si', trim($request), $status_arr)) {
            return $status_arr['1'];
        }

        return $request;
    }

    public static function deleteQueue($queue_id)
    {
        if (empty($queue_id) || !is_numeric($queue_id)) {
            return false;
        }

        $request = static::curl(['command' => 'delete', 'id' => intval(trim($queue_id))]);

        if (empty($request)) {
            return false;
        }

        if (preg_match('!status\:(.*?)[\s]{1,}!si', trim($request), $status_arr)) {
            return $status_arr['1'];
        }

        return $request;
    }

    public static function sendSms($sms_data)
    {
        if (!empty($sms_data['sms_sender'])) {
            static::$conf['sms_sender'] = $sms_data['sms_sender'];
        }

        if (empty($sms_data['recipient']) || empty($sms_data['message']) || empty(static::$conf['sms_sender'])) {
            return false;
        }

        $request_data = [
            'command' => 'send',
            'to' => static::prepare_phone($sms_data['recipient']),
            'message' => trim($sms_data['message']),
        ];

        if (!empty($sms_data['date_time']) && preg_match('!^[0-9]{4}\-[0-9]{2}\-[0-9]{2}T[0-9]{2}\:[0-9]{2}\:[0-9]{2}\+[0-9]{3,4}$!si', trim($sms_data['date_time']))) {
            $request_data['ask_date'] = $sms_data['date_time'];
        }

        $request = static::curl($request_data);

        if (empty($request)) {
            return false;
        }

        if (preg_match('!id\:([0-9]+)!si', trim($request), $request_arr)) {
            return intval($request_arr['1']);
        }

        return $request;
    }

    public static function sendViber($viber_data)
    {
        if (!empty($viber_data['viber_sender'])) {
            static::$conf['viber_sender'] = $viber_data['viber_sender'];
        }

        if (!empty($sms_data['sms_sender'])) {
            static::$conf['sms_sender'] = $sms_data['sms_sender'];
        }

        if (isset($viber_data['force_sms'])) {
            static::$conf['viber_force_sms'] = $viber_data['force_sms'];
        }

        if (empty($viber_data['recipient']) || empty($viber_data['message']) || empty(static::$conf['viber_sender'])) {
            return false;
        }

        $request_data = [
            'command' => 'send',
            'viber' => 1,
            'viber_type' => 'text',
            'to' => static::prepare_phone($viber_data['recipient']),
            'viber_from' => static::$conf['viber_sender'],
            'viber_message' => trim($viber_data['message']),
        ];

        if (!empty($viber_data['date_time']) && preg_match('!^[0-9]{4}\-[0-9]{2}\-[0-9]{2}T[0-9]{2}\:[0-9]{2}\:[0-9]{2}\+[0-9]{3,4}$!si', trim($viber_data['date_time']))) {
            $request_data['ask_date'] = $viber_data['date_time'];
        }

        if (!empty($viber_data['lifetime']) && is_numeric($viber_data['lifetime'])) {
            static::$conf['viber_lifetime'] = intval($viber_data['lifetime']);
        }

        if (!empty(static::$conf['viber_force_sms'])) {
            $request_data['from'] = static::$conf['sms_sender'];
            $request_data['message'] = trim($viber_data['message']);
            $request_data['viber_sms'] = 1;
        }

        if (!empty(static::$conf['viber_lifetime'])) {
            $request_data['viber_lifetime'] = static::$conf['viber_lifetime'];
        }

        if (!empty($viber_data['image'])) {
            $request_data['viber_image'] = $viber_data['image'];
            $request_data['viber_type'] = 'image';
        }

        if (!empty($viber_data['button'])) {
            $request_data['viber_button'] = $viber_data['button'];
            $request_data['viber_type'] = 'button';
        }

        if (!empty($viber_data['url'])) {
            $request_data['viber_url'] = $viber_data['url'];
        }

        $request = static::curl($request_data);

        if (empty($request)) {
            return false;
        }

        if (preg_match('!id\:([0-9]+)!si', trim($request), $request_arr)) {
            return intval($request_arr['1']);
        }

        return $request;
    }

    private static function curl($request)
    {
        if (empty($request)) {
            return false;
        }

        $request['key'] = static::$conf['api_key'];

        if (!empty($request['command']) && in_array($request['command'], ['price', 'send']) && empty($request['viber'])) {
            $request['from'] = static::$conf['sms_sender'];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, static::$api_url . http_build_query($request));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }
}
