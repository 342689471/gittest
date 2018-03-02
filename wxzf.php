<?php
/* ini_set('date.timezone','Asia/Shanghai');
// error_reporting(E_ERROR);
error_reporting(0);
require_once "WxPay.Api.php";
require_once 'log.php';
require_once("../../includes/mysql.class.php");
//初始化日志
// $logHandler= new CLogFileHandler("./logs/".date('Y-m-d').'.log');
// $log = Log::Init($logHandler, 15);
$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
//禁止引用外部xml实体
$successdata=xmlToArray($xml);
// file_put_contents('log.txt',json_encode($successdata));
function xmlToArray($xml)
{
//将XML转为array
$array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
return $array_data;
}
// function printf_info($data)
// {
// foreach($data as $key=>$value){
// echo "<font color='#f00;'>$key</font> : $value <br/>";
// }
// }
$out_trade_no=$successdata['out_trade_no'];
 
if(!empty($out_trade_no)){
 
$input = new WxPayOrderQuery();
$input->SetOut_trade_no($out_trade_no);
$result=WxPayApi::orderQuery($input);
// printf_info($result);
if(array_key_exists("return_code", $result)&& array_key_exists("result_code", $result)&& $result["return_code"] == "SUCCESS"&& $result["result_code"] == "SUCCESS")
{
//file_put_contents('log.txt','支付成功'.$out_trade_no.$pid.$tel.$numbers.$poss);
//支付成功
//改变订单状态
$sql="update pai_deposit set status='1',paytime='".date("Y-m-d H:i:s")."' where out_trade_no='{$out_trade_no}'";
// file_put_contents('log.txt',$sql);
$con=new connection();
$result=$con->Query($sql);
echo "success";
$openid=$successdata['openid'];
$total_fee=$successdata['total_fee']*0.01;
$attach=$successdata['attach'];
$sql="select * from pai_deposit where out_trade_no='{$out_trade_no}'";
$result_deposit=$con->QueryArray($sql);
 
$sql="select * from paipro where id='".$result_deposit[0]['pid']."'";
$result_pro=$con->QueryArray($sql);
 
sendmsg($openid,$total_fee,$result_pro[0]['title'],$out_trade_no,$result_deposit[0]['tel']);
exit();
}
 
echo 'fail';
exit();
}
else
{
//file_put_contents('log.txt','支付失败2'.$out_trade_no);
echo 'fail';
exit();
} */
function sendmsg($wxid,$total,$name,$tel)
{
 //"touser":"'.$wxid.'",这个是发给单人
$access_token=new_access_token();
$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
 
$w_title="报名提交通知";
// $w_url='http://'.$_SERVER['HTTP_HOST']."/mobile/user.php?act=fenxiao1&wxid=".$wxid;
$w_url='';
 
$rtime=date("Y-m-d H:i:s");
$w_description="所报任务id:{$total}\r\n报名者姓名:{$name}\r\n电话:{$tel}\r\n报名时间:{$rtime}";
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
 
if($ret->errmsg != 'ok')
{
 
$access_token = new_access_token();
$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
$ret_json = curl_grab_page($url, $post_msg);
$ret = json_decode($ret_json);
}
 
}
function curl_grab_page($url, $data, $proxy = '', $proxystatus = '', $ref_url = '') {
$ch = curl_init();
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
if ($proxystatus == 'true') {
curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
curl_setopt($ch, CURLOPT_PROXY, $proxy);
}
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_URL, $url);
if (!empty($ref_url)) {
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_REFERER, $ref_url);
}
if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
}
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
ob_start();
return curl_exec ($ch);
ob_end_clean();
curl_close ($ch);
unset($ch);
}
 
function new_access_token() {
 
 
/*$appid = 'wx52dc8c34087d';
$appsecret ='d70878f040f08fa55766bf6d';*/
$appid = '';
$appsecret ='';
 
$time = time();
$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
$ret_json = curl_get_contents($url);
$ret = json_decode($ret_json);
 
return $ret->access_token;
 
 
}
 
function curl_get_contents($url) {
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
curl_setopt($ch, CURLOPT_REFERER, _REFERER_);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
}
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$r = curl_exec($ch);
curl_close($ch);
return $r;
}
?>