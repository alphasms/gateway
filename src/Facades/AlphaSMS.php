<?php

namespace alphasms\gateway\Facades;

use Illuminate\Support\Facades\Facade;

/**
 *
 * @method static void setApiKey($apiKey)
 * @method static void setSmsSender($smsSender)
 * @method static void setViberSender($viberSender)
 * @method static void setViberLifetime($seconds)
 * @method static void setViberForceSms($int)
 *
 * @method static mixed getBalance()
 * @method static mixed getStatus($queue_id)
 * @method static mixed deleteQueue($queue_id)
 *
 * @method static mixed sendSms($sms_data = [])
 * @method static mixed sendViber($viber_data = [])
 *
 */
class AlphaSMS extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'alphasms';
    }
}