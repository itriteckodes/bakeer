<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationHelper
{

    public static function send($object)
    {
        $notification['to'] = $object->firebase_token;
        $notification['notification']['click_action'] = "FLUTTER_NOTIFICATION_CLICK";
        $notification['notification']['title'] = $object->title;
        $notification['notification']['body'] = $object->body;
        
        $notification['data'] = $object->extra;
        $result = Http::withOptions(['json' => $notification])
            ->withHeaders(["authorization" => "key= AAAAqOu1ryQ:APA91bHYDQVBzCMWNIIYptEP8GRiIKhdvsBmHxNldaqOpSBXiHms0RPATWez7nV1MbDe2DpDUzjBPAnewh24yEZWU_mrbiV1GIjx7oeCcgACS2NBfS50wFU1Jo9vuTpqOhO_bZ4UE0m7"])
            ->post("https://fcm.googleapis.com/fcm/send");

        Log::alert($result);
    }
}
