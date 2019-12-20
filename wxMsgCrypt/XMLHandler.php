<?php
/**
 * Created by PhpStorm.
 * User: juhaiweilan-liuyunxin
 * Date: 2019/7/27
 * Time: 下午5:20
 */

namespace App\Models;

use \DOMDocumemt;
/**
 * XML
 */
class XMLHandler
{
    protected $fileExtension = 'xml';
    protected $mimeType = 'application/xml';

    public static function computeData($grid, $is_std = false)
    {
        $dom = new \DomDocument('xml');
        foreach($grid as $item_key => $item_value)
        {
            $key = $dom->createElement($item_key);
            $dom->appendchild($key);
            $text = $dom->createTextNode($item_value);
            $key->appendchild($text);
        }
        $xml_res = $dom->saveXML();
        if(!$is_std)
        {
            $xml_res = str_replace('<?xml version="xml"?>', '<xml>',$xml_res);
            $xml_res .= '</xml>';
        }
        return $xml_res;
    }

    public static function computeXML($xml_str)
    {
        $xml_res = str_replace('<xml>','<?xml version="xml"?>', $xml_str);
        $xml_res = str_replace('</xml>','', $xml_res);
        return $xml_res;
    }

    public static function xmlToArray($xml_nostd_str)
    {
//        $respone_data = array();
//        $parser = xml_parser_create();
//        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
//        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
//        xml_parse_into_struct($parser, $xml_nostd_str, $std_res, $index);
//        xml_parser_free($parser);
//        if(!is_array($std_res) || $std_res == null){
//            return $respone_data;
//        }
//        //将xml数组转化为response一维数组
//        foreach($std_res as $key => $item)
//        {
//            $respone_data[$item['tag']] = $item['value'];
//        }
//        return $respone_data;

        //将XML转为array
        //禁止引用外部xml实体
        logger($xml_nostd_str);
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml_nostd_str, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        logger(json_encode($data));
        return $data;
    }

    public static function callBackXml($array){
        $xml = "<xml>";
        foreach ($array as $key=>$val) {
            $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
        $xml.="</xml>";
        return $xml;
    }


    public static function callBackWxThirdMessage($array){
        $xml = "<xml>";
        foreach ($array as $key=>$val) {
            if ($key == 'TimeStamp'|| $key == 'CreateTime'){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }



}