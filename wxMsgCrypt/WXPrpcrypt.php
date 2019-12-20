<?php
/**
 * Created by PhpStorm.
 * User: juhaiweilan-liuyunxin
 * Date: 2019/12/17
 * Time: 2:40 PM
 */

namespace App\wxMsgCrypt;


class WXPrpcrypt
{

    public $key;

    function __construct($k)
    {
        $this->key = base64_decode($k . "=");
    }

    /**
     * 对明文进行加密
     * @param string $text 需要加密的明文
     * @return array 加密后的密文
     */
    public function encrypt($text, $appid){
        //获得16位随机字符串，填充到明文之前
        $random = $this->getRandomStr();
        $text = $random . pack("N", strlen($text)) . $text . $appid;
        $iv = substr($this->key, 0, 16);
        //使用自定义的填充方式对明文进行补位填充
        $pkc_encoder = new WXPKCS7Encoder();
        $text = $pkc_encoder->encode($text);
        $data = openssl_encrypt($text,'AES-256-CBC',substr($this->key, 0, 32),OPENSSL_ZERO_PADDING,$iv);
        return array(0, $data);
    }

    /**
     * 对密文进行解密
     * @param string $encrypted 需要解密的密文
     * @return array|string 解密得到的明文
     */
    public function decrypt($encrypted, $appid){
        //使用BASE64对需要解密的字符串进行解码
//        $ciphertext_dec = base64_decode($encrypted);
        $iv = substr($this->key, 0, 16);
        $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC',substr($this->key, 0, 32), OPENSSL_ZERO_PADDING, $iv);
        try {
            //去除补位字符
            $pkc_encoder = new WXPKCS7Encoder();
            $result = $pkc_encoder->decode($decrypted);
            //去除16位随机字符串,网络字节序和AppId
            if (strlen($result) < 16)
                return "";
            $content = substr($result, 16, strlen($result));
            $len_list = unpack("N", substr($content, 0, 4));
            $xml_len = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
            $from_appid = substr($content, $xml_len + 4);
        } catch (\Exception $e) {
            //print $e;
            return array(WXErrorCode::$IllegalBuffer, null);
        }
        if ($from_appid != $appid)
            return array(WXErrorCode::$ValidateAppidError, null);
        return array(0, $xml_content);
    }


    /**
     * 随机生成16位字符串
     * @return string 生成的字符串
     */
    function getRandomStr()
    {

        $str = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }
}