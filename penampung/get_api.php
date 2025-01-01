<?php

class get_api_sunfish{

public static function get_apis(){
    return (new self())->fetchAllData();
}

private function login()
    {   
        $curl = curl_init();
        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => 'https://hrisdev-pro.jamkrindo.co.id/sf7/?ofid=sfSystem.loginUser&originapp=hris_jamkrindo',
        //     // CURLOPT_URL => 'https://sf7dev-pro.dataon.com/sfpro/?ofid=sfSystem.loginUser&originapp=hris_jamkrindo',
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'POST',
        //     CURLOPT_POSTFIELDS => '{
        //     "USERPWD": "CCAAF7B45FAE48408EA946E8FFF29FB1C70DD04F",
        //     "USERNAME":"90711",
        //     "ACCNAME":"jamkrindo",
        //     "TIMESTAMP": "' . date('Y-m-d H:i:s') . ' +0700"
        //     }',
        //     CURLOPT_HTTPHEADER => array(
        //         'Accept-Language: en-US,en;q=0.9,id;q=0.8',
        //         'Language: en',
        //         'Origin: https://workplaze.dataon.com',
        //         'Referer: https://workplaze.dataon.com/auth',
        //         'Sec-Fetch-Dest: empty',
        //         'Sec-Fetch-Mode: cors',
        //         'User-Agent: Mozilla5.0 (X11; Linux x86_64) AppleWebKit537.36 (KHTML, like Gecko) Chrome125.0.0.0 Safari537.36',
        //         'sec-ch-ua: "Google Chrome";v="125", "Chromium";v="125", "Not.ABrand";v="24"',
        //         'sec-ch-ua-mobile: ?0',
        //         'sec-ch-ua-platform: "Linux"',
        //         'Content-Type: application/json',
        //         'Cookie: JSESSIONID=C18D3CB96739FBBB5DD807CBDEB0FA51; LANG=en; _BDC=LANG'
        //     ),
        // ));
        $data = array(
            "USERPWD" => "CCAAF7B45FAA946E8FFF29FB1C70DD04F",
            "USERNAME" => "90711",
            "ACCNAME" => "jamkrindo",
            "TIMESTAMP" => date('Y-m-d H:i:s') . ' +0700'
        );
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://hris-pro.jamkrindo.co.id/sf7/?ofid=sfSystem.loginUser&originapp=hris_jamkrindo',  
            CURLOPT_RETURNTRANSFER => true,
         
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data), // Menggunakan json_encode untuk format yang benar
            CURLOPT_HTTPHEADER => array(
     
               
      
                'Content-Type: application/json',
                
            ),
        ));
        // $response = curl_exec($curl);
        // $data = json_decode($response, true);

        // dd($data);
        // curl_close($curl);
        // // echo $response;

        // Eksekusi cURL
$response = curl_exec($curl);

// Cek error
if (curl_errno($curl)) {
    echo 'Error:' . curl_error($curl);
}

// Ambil status kode HTTP
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

// Tampilkan hasil
echo "HTTP Code: " . $httpCode . "\n"; // Tampilkan status kode
echo "Response: " . $response . "\n"; // Tampilkan respons mentah

if ($httpCode == 200) {
    // Jika berhasil, decode JSON
    $responseData = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "JSON Decode Error: " . json_last_error_msg();
    } else {
        var_dump($responseData); // Tampilkan data yang diterima
    }
} else {
    echo "HTTP Error: " . $httpCode . "\n";
}
die;

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
