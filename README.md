# WXBizMsgCrypt
微信公众号加解密消息工具类
最近在搞微信第三方平台, 微信要求消息的推送和回复必须使用安全模式, 即aes加解密, 开发程序使用的是php. 
于是, 去查看微信文档, 微信倒也是周到, 提供的有各种后台语言的, 加解密源码提供下载使用.
刚引入, 就发现demo使用的是 mcrypt进行加解密, 可以php7 以后已经废弃了,  于是想到了openssl , 把demo中加解密的函数替换成了
$data = openssl_encrypt($text,'AES-256-CBC', $this->key,OPENSSL_RAW_DATA,$iv);
$data = openssl_decrypt($encrypted, 'AES-256-CBC',$this->key, OPENSSL_RAW_DATA, $iv);
接下来, 收到微信的推送, 解密成功了, 满心欢喜,  由于一直未使用加密的方法,  感觉应该没什么问题, 
接下来的第二天, 做回复消息业务时, 发现, 回复的消息, 微信总是解密失败.
苦恼的查询了半天,  得到最终的正确的版本
$data = openssl_encrypt($text,'AES-256-CBC',substr($this->key, 0, 32),OPENSSL_ZERO_PADDING,$iv);
后来测试的过程中, 发现扫描带参二维码, 关注的的事件推送没有解密成功, 发现刚开始以为正确的解密方法也存在问题, 随即修改如下
$data = openssl_decrypt($encrypted, 'AES-256-CBC',substr($this->key, 0, 32), OPENSSL_ZERO_PADDING, $iv);
后在开发中经过多次多场景测试, 没出现问题, 开心
微信的这个大坑, 在此记录, 分享给遇到问题的同志们!



