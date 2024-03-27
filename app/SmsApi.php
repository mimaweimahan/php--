<?php


/**
 *DESC:
 *Created on 2021/7/6 11:56 下午
 *Created by Victor
 */

namespace App;


class SmsApi
{
//客户账号：ZZGJ003
//登录密码：mbw3p8t3
//接口密码：em72pdab
//行业网址：c.ipyy.net

    const ACCOUNT = 'ZZGJ003';
    const PASSWORD = 'em72pdab';
    const USERID = '67908';
    const BASE_URL = 'https://dx.ipyy.net/sms.aspx';

    const SIGN_TEXT = '【华信】';

    public static function init()
    {
        return new static();
    }


    public function sendSms($mobile, $content)
    {
        $apiParams = [
            'userid' => self::USERID,
            'account' => self::ACCOUNT,
            'password' => strtoupper(md5(self::PASSWORD)),
            'mobile' => $mobile,
            'code' => 8,
            'content' => $this->unicodeEncode($content . self::SIGN_TEXT),
            'sendTime' => '',
            'action' => 'send',
            'extno' => '',
        ];
//        var_dump($content . self::SIGN_TEXT);
        $return = $this->curlPost(self::BASE_URL, $apiParams);
//        var_dump($return);
        if (!$return || $return['returnstatus'] !== 'Success') {
            return false;
        }

        return $return;
    }
    public function sendSmsI18N($mobile, $content)
    {
        $apiParams = [
            'userid' => self::USERID,
            'account' => self::ACCOUNT,
            'password' => strtoupper(md5(self::PASSWORD)),
            'mobile' => $mobile,
            'code' => 8,
            'content' => $this->unicodeEncode($content . self::SIGN_TEXT),
            'sendTime' => '',
            'action' => 'send',
            'extno' => '',
        ];
//        var_dump($content . self::SIGN_TEXT);
        $return = $this->curlPost(self::BASE_URL, $apiParams);
//        var_dump($return);
        if (!$return || $return['returnstatus'] !== 'Success') {
            return false;
        }

        return $return;
    }

    public function unicodeEncode($str)
    {
        $str = iconv('UTF-8', 'ISO-10646-UCS-2', $str);

        return bin2hex($str);
    }


    private function curlPost($url, $postfield, $time = 30)
    {
        foreach ($postfield as $key => $value) {
            if (!empty($value)) {
                $str[] = $key . "=" . $value;
            }
        }
        $postfields = join("&", $str);

        $ch = curl_init();
        //跳过https  SSL证书验证
        $params[CURLOPT_SSL_VERIFYPEER] = false;
        $params[CURLOPT_SSL_VERIFYHOST] = false;

        $params[CURLOPT_URL] = $url;
        $params[CURLOPT_HEADER] = false;
        $params[CURLOPT_RETURNTRANSFER] = true;
        $params[CURLOPT_FOLLOWLOCATION] = true;
        $params[CURLOPT_USERAGENT] = 'Mozilla/5.0 (Windows NT 5.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1';
        $params[CURLOPT_POST] = true;
        $params[CURLOPT_TIMEOUT] = $time;
        $params[CURLOPT_POSTFIELDS] = $postfields;
        $params[CURLOPT_HTTPHEADER] = array('Content-Type: application/x-www-form-urlencoded');

        curl_setopt_array($ch, $params);
        $content = curl_exec($ch);
        curl_close($ch);


        return $this->xml2Arr($content);
    }

    private function xml2Arr($xml)
    {
        return json_decode(json_encode(simplexml_load_string($xml)), true);
    }
}
