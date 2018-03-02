消息推送
一：微信基础配置
	function new_access_token() {
$appid = '公众号中获取'; 
$appsecret ='公众号中获取';

$time = time();
$url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
$ret_json = curl_get_contents($url);
$ret = json_decode($ret_json); 
return $ret->access_token; 
}
	注：微信公众号开发者中心中需要配置消息回调页面及下载网站验证文件（根据公众号的提示即可配置完成）

二：在sendmsg方法中设置需要显示的字段

function sendmsg($wxid,$total,$title,$order_id,$tel)
{
}
其中$wxid 即为消息接收者的openid , 如果需要将此消息同事发送给管理员，可以将此sendmsg方法在重写讲$wxid换成管理员的oppid即可。
	2.1  消息输出的方式
		$access_token=new_access_token();
$url='https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;

$w_title="付款成功通知";
// $w_url='http://'.$_SERVER['HTTP_HOST']."/mobile/user.php?act=fenxiao1&wxid=".$wxid;
$w_url='';$rtime=date("Y-m-d H:i:s");
$w_description="订单编号:{$order_id}\r\n保证金:￥{$total}元\r\n商品详情:{$title}\r\n联系电话:{$tel}\r\n付款时间:{$rtime}";

三：将消息拼接并转换位json
$post_msg = '{
"touser":"'.$wxid.'",
"msgtype":"news",
"news":{
"articles": [
{
"title":"'.$w_title.'",
"description":"'.$w_description.'",
"url":"'.$w_url.'",
"picurl":"'.$w_picurl.'"
}
]
}
}';
$ret_json = curl_grab_page($url, $post_msg);

$ret = json_decode($ret_json);

四：消息推送确认。如果失败则再次发送
	if($ret->errmsg != 'ok')
{
$access_token = new_access_token();
$url='https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
$ret_json = curl_grab_page($url, $post_msg);
$ret = json_decode($ret_json);
}
以上内容均属于sendmsg 方法中的内容 (本文件附代码)



代码部分：
//$wxid 用户openid
 
sendmsg($wxid,$total,$title,$order_id,$tel,$name,$hao)
 
functionsendmsg($wxid,$total,$title,$order_id,$tel,$name,$hao)
{
 
$access_token=new_access_token();
$url= 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
 
$w_title="付款成功通知";
// $w_url='http://'.$_SERVER['HTTP_HOST']."/mobile/user.php?act=fenxiao1&wxid=".$wxid;
$w_url='';
 
$rtime=date("Y-m-d H:i:s");
$w_description="订单编号:{$order_id}\r\n订单金额:￥{$total}元\r\n商品详情:{$title}\r\n联系电话:{$tel}\r\n抽奖号码:{$hao}\r\n付款时间:{$rtime}";
$post_msg= '{
"touser":"'.$wxid.'",
"msgtype":"news",
"news":{
"articles": [
{
"title":"'.$w_title.'",
"description":"'.$w_description.'",
"url":"'.$w_url.'",
"picurl":"'.$w_picurl.'"
}
]
}
}';
$ret_json= curl_grab_page($url, $post_msg);
 
$ret= json_decode($ret_json);
 
if($ret->errmsg != 'ok')
{
 
$access_token= new_access_token();
$url= 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
$ret_json= curl_grab_page($url, $post_msg);
$ret= json_decode($ret_json);
}
 
}
functioncurl_grab_page($url, $data, $proxy= '', $proxystatus= '', $ref_url= '') {
$ch= curl_init();
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
if($proxystatus== 'true') {
curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
curl_setopt($ch, CURLOPT_PROXY, $proxy);
}
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_URL, $url);
if(!empty($ref_url)) {
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_REFERER, $ref_url);
}
if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
}
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
ob_start();
returncurl_exec ($ch);
ob_end_clean();
curl_close ($ch);
unset($ch);
}
 
functionnew_access_token() {
 
 
$appid= '';
$appsecret='';
 
$time= time();
$url= "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
$ret_json= curl_get_contents($url);
$ret= json_decode($ret_json);
 
return$ret->access_token;
 
 
}
 
functioncurl_get_contents($url) {
$ch= curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
curl_setopt($ch, CURLOPT_REFERER, _REFERER_);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
}
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$r= curl_exec($ch);
curl_close($ch);
return$r;
}

