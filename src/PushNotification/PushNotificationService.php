<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2020-03-07
 * Time: 10:09
 */

namespace App\PushNotification;


class PushNotificationService
{
    public function __construct()
    {

    }

    public function pushNotification(string $deviceToken)
    {
        $keyfile = 'AuthKey_TLM9ZRAU52.p8';               # <- Your AuthKey file
        $keyid = 'TLM9ZRAU52';                            # <- Your Key ID
        $teamid = '7QJKRU58DK';                           # <- Your Team ID (see Developer Portal)
        $bundleid = 'com.versusteam.versussmrdel';                # <- Your Bundle ID
        $url = 'https://api.development.push.apple.com';  # <- development url, or use http://api.push.apple.com for production environment
        $token = '0cd9d2d44bb28ae7cf6c1337b3169ddb9f472dd5b6e40e00b65acd8470631edc';              # <- Device Token

        $message = '{"aps":{"alert":"Hi there!","sound":"default"}}';

        $key = openssl_pkey_get_private('file://'.'/Users/jtsmrdel/Desktop/'.$keyfile);

        $header = ['alg'=>'ES256','kid'=>$keyid];
        $claims = ['iss'=>$teamid,'iat'=>time()];

        $sHeader = serialize($header);
        $sClaims = serialize($claims);

        $header_encoded = base64_encode($sHeader);
        $claims_encoded = base64_encode($sClaims);

        $signature = '';
        openssl_sign($header_encoded . '.' . $claims_encoded, $signature, $key, 'sha256');
        $jwt = $header_encoded . '.' . $claims_encoded . '.' . base64_encode($signature);

        // only needed for PHP prior to 5.5.24
        if (!defined('CURL_HTTP_VERSION_2_0')) {
            define('CURL_HTTP_VERSION_2_0', 3);
        }

        $http2ch = curl_init();
        curl_setopt_array($http2ch, array(
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_URL => "$url/3/device/$token",
            CURLOPT_PORT => 443,
            CURLOPT_HTTPHEADER => array(
                "apns-topic: {$bundleid}",
                "authorization: bearer $jwt"
            ),
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => $message,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HEADER => 1
        ));

        $result = curl_exec($http2ch);

        if ($result === FALSE) {
            throw new Exception("Curl failed: ".curl_error($http2ch));
        }

        $status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);
        echo $status;

        function base64($data) {
            return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
        }
    }
}