<?php

class get_api_sunfish{

public static function get_apis(){
    return self::fetchAllData();
}

private function login()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://sf7dev-pro.dataon.com/sfpro/?ofid=sfSystem.loginUser&originapp=hris_jamkrindo',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
            "USERPWD": "777FF8B018AB23EEE048D13978E0D1FCFF94D326",
            "USERNAME":"jamkrindo",
            "ACCNAME":"jamkrindo",
            "TIMESTAMP": "' . date('Y-m-d H:i:s') . ' +0700"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        $data = json_decode($response, true);

      
        curl_close($curl);
        // echo $response;

        return $data;
    }
    private function fetchData($page)
    {

        $token = $this->login();

        $token = $token['DATA']['JWT_TOKEN'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://sf7dev-pro.dataon.com/sfpro/?qlid=HrisUser.getEmployee',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                    "page_number" : "' . $page . '"
                }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
                'Cookie: JSESSIONID=1D874707C6E28326AA74FA53FF48D8CE; LANG=en; _BDC=JSESSIONID,LANG'
            ),
        ));

        $response = curl_exec($curl);
        $data = json_decode($response, true);
        // $data = $data['DATA'];
        // dd($data);

        curl_close($curl);
        // echo $response;

        return $data;
    }
    // Fungsi untuk mengambil semua data dari setiap halaman
    private function fetchAllData()
    {
        $allData = [];
        $currentPage = 1;
        $perPage = 10;
        $totalPages = 1;

        do {
            // Ambil data dari API
            $response = $this->fetchData($currentPage);

            if (!$response) {
                return false; // Jika ada error, return false
            }

            // Gabungkan data dari halaman saat ini ke allData
            $allData = array_merge($allData, $response['DATA']['DATA']);

            // Hitung total halaman
            $totalPages = ceil($response['DATA']['TOTAL'] / $perPage);

            $currentPage++;
        } while ($currentPage <= $totalPages);

        return $allData;
    }
}

?>
