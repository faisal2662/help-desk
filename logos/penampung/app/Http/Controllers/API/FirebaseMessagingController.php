<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\API\PegawaiToken;
use Log;

class FirebaseMessagingController extends Controller
{
    private static $token = 'AAAA3kz8vjQ:APA91bE6GnsyT-Cm30xHRUdmK7j7IORok6qJ9e4wDpfCGG5fxev1PqEuU2yKTaG9lfzfjkemJajpAzGBvCckFNTYWR0zsyOmzztvZl29BYa-TUJ0v6XMx_kLmADtyzbqFv7ATm-nLKN-';

    public function send()
    {
        return self::sendMessage(
            'fG4m5uWvRJ-gQ4KfieOmlh:APA91bHviyLhbr-WxfXropJ6pX_jeD_Nl1W_ai9vUshWvRu8HwUP6UxFJ3yhUqh7yW-BjeF2hgzZBHTNGtQCo2ilN7bo64iV8gffdYnxKKOP54cCM-Ks_EBrwH8Pi0wm9BS-EBiNJvNh',
            'Pengaduan Baru',
            'Ada pengaduan baru yang dibuat oleh pelanggan.',
            [
                'screen' => '/agent-complaint-detail',
                'id' => 1
            ]
        );
    }

    public static function sendMessageToEmployee($id, $title, $body, $data = array())
    {
        $employeeTokens = PegawaiToken::where([
                'id_pegawai' => $id,
                'delete_pegawai_token' => 'N'
            ])
            ->groupBy('token_device')
            ->get();

        foreach ($employeeTokens as $token) {
            self::sendMessage($token->token_device, $title, $body, $data);
        }
    }

    public static function sendMessageToManyEmployee($ids, $title, $body, $data = array())
    {
        $employeeDeviceTokens = PegawaiToken::where('delete_pegawai_token', 'N')
            ->whereIn('id_pegawai', $ids)
            ->groupBy('token_device')
            ->pluck('token_device');

        foreach ($employeeDeviceTokens->chunk(900) as $chunkEmployeeDeviceTokens) {
            self::sendMessageToMany($chunkEmployeeDeviceTokens, $title, $body, $data);
        }
    }

    public static function sendMessage($to, $title, $body, $data = null)
    {
        $curl = curl_init();

        $payload = json_encode([
            'notification' => [
                'title' => $title,
                'body' => $body,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ],
            'data' => $data,
            'to' => $to
        ]);

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: key='.self::$token,
            ],
            CURLOPT_POSTFIELDS => $payload,
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public static function sendMessageToMany($registrationIds, $title, $body, $data = null)
    {
        $curl = curl_init();

        $dataPayload = [
            'notification' => [
                'title' => $title,
                'body' => $body,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ],
            'data' => $data
        ];

        if (count($registrationIds) > 1) {
            $dataPayload['registration_ids'] = $registrationIds;
        } else {
            $dataPayload['to'] = $registrationIds[0];
        }

        $payload = json_encode($dataPayload);

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: key='.self::$token,
            ],
            CURLOPT_POSTFIELDS => $payload,
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public static function sendBroadcast($topic, $title, $body, $data = null)
    {
        $curl = curl_init();

        $payload = json_encode([
            'notification' => [
                'title' => $title,
                'body' => $body,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ],
            'data' => $data,
            'to' => '/topics/'.$topic
        ]);

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: key='.self::$token,
            ],
            CURLOPT_POSTFIELDS => $payload,
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}
