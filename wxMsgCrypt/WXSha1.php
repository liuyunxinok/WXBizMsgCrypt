<?php
/**
 * Created by PhpStorm.
 * User: juhaiweilan-liuyunxin
 * Date: 2019/12/17
 * Time: 2:42 PM
 */

namespace App\wxMsgCrypt;


class WXSha1
{
    /**
     * 用SHA1算法生成安全签名
     * @param string $token 票据
     * @param string $timestamp 时间戳
     * @param string $nonce 随机字符串
     * @param string $encrypt 密文消息
     * @return array
     */
    public function getSHA1($token, $timestamp, $nonce, $encrypt_msg)
    {
        //排序
        try {
            $array = array($encrypt_msg, $token, $timestamp, $nonce);
            sort($array, SORT_STRING);
            $str = implode($array);
            return array(WXErrorCode::$OK, sha1($str));
        } catch (\Exception $e) {
            //print $e . "\n";
            return array(WXErrorCode::$ComputeSignatureError, null);
        }
    }
}