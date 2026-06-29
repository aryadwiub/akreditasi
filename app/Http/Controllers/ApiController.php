<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function index()
    {
        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => 'https://akreditasi.bpm.unair.ac.id/index.php/api/all_national?token=bpmu4jamu!2021%24',
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'GET',
        //     CURLOPT_HTTPHEADER => array(
        //         'Cookie: ci_session=r8o90gij6nfr8kc787htbess9a0ovbaq'
        //     ),
        // ));

        // $response = curl_exec($curl);
        // // $response2 = json_decode($response, true);

        // curl_close($curl);
        // // echo $response2;
        // // var_dump(json_decode($response2));

        // foreach (json_decode($response, true) as $key) {
        //     echo $key['id_prodi'] . "<br>";
        // }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.satudata.unair.ac.id/login?username=bpm&password=s4tud4t4bpm!123',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            // CURLOPT_HTTPHEADER => array('Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJJRCI6IjkyMTdiZTZkLTQwMmItNGEwNS05MWEyLWZhYTJiN2UyOWRmNiIsImFkbWluIjpmYWxzZSwiZXhwIjoxNzY4MzY1ODczLCJuYW1lIjoiQmFkYW4gUGVuamFtaW5hbiBNdXR1IiwidXNlcm5hbWUiOiJicG0ifQ.EognYANKJw_wsPUAQEvBXnhAaGzWrD-eqJzYI3M5Uxs'),
        ));

        $response = curl_exec($curl);
        // $response1 = json_decode($response, true);
        // echo $response1;

        curl_close($curl);
        // echo $response;
        // echo $response1['token'];
        // $jwt = JWT::
        foreach (json_decode($response, true) as $key) {
             echo $key['token'] . "<br>";
             const token = "YOUR_JWT_TOKEN_HERE";
     const url = "https://api.contoh.com/data";
     
     fetch(url, {
       method: 'GET',
       headers: {
         'Authorization': `Bearer ${token}`,
         'Content-Type': 'application/json'
       }
     })
     .then(response => response.json())
     .then(data => console.log(data))
     .catch(error => console.error('Error:', error));
         }


        return view('API/api');
    }
}
