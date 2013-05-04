<?php
define('PATH',dirname(__FILE__));
define('DOMAIN',$_SERVER['SERVER_NAME']);
define('TR',true);
$request=$_SERVER['REQUEST_URI'];
$requestarr=explode("?",$request);
$url_f=$requestarr[0]."?";
$uurl=stristr($request,"url=");
$ucookie=strrchr($uurl,"&cookie");
$uurl=$ucookie?str_replace($ucookie,'',$uurl):$uurl;

$uurl=urldecode(trim($uurl,"url="));
$preg="/([^\n]+\.[a-zA-Z]{2,3})(\.[a-zA-Z]){0,3}\/([^\n]+)/";
preg_match($preg,$uurl,$uusrr);
$enurl=urlencode($uusrr[3]);
//print_r($uusrr[1].$uusrr[2]."/".$enurl);

$_GET['cookie']=urldecode(trim($ucookie,"&cookie="));

$urlarr['url']=$_GET['url'];
$urlarr['cookie']=$_GET['cookie'];


$ishtml=0;
$myurl ="http://". DOMAIN . $url_f."url=";
$con_tpey=array(
	 "application/x-javascript",
	 "text/html",
	 "text/css"
);
if(isset($_GET['url'])){
	$url=$_GET['url'];	

	if(empty($url))exit("请输入url");
	$headerarr=get_headers($url,1);
	if(is_array($headerarr)){
		foreach($headerarr as $k=>$v){
			if($k==="Location"){
			$urlarr['url']=$v;
			$query=http_build_query($urlarr);
				Header("Location:{$url_f}{$query}");
				break;
			}				
			Header("{$k}: {$v}");
			if(TR && strstr($v,'html')==true){
				$ishtml= 1;
			}
		}
		if(TR && in_array($headerarr['Content-Type'],$con_tpey)== true ){
			$ishtml= 1;
		}		
	}
}

$usercookie=isset($_GET['cookie'])?$_GET['cookie']:"";

echo $url;
if(isset($url)){
$file=getinfo($url,$usercookie,$myurl,$ishtml);
}
$baseurl=baseurl($url);
function getinfo($url,$usercookie=0,$myurl,$ishtml){	//获取内容
	$baseurl=baseurl($url);
//	echo $url."bs".$baseurl;
	$headers = array(
		"Connection: keep-alive",
		"cookie:{$usercookie}",
		"Cache-Control: max-age=0",
		"Accept: application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5",
		"User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/534.12 (KHTML, like Gecko) Chrome/9.0.576.0 Safari/534.12",
		"Avail-Dictionary: xdC_A6dv",
		"Accept-Language: zh-CN,zh;q=0.8",
		"Accept-Charset: GBK,utf-8;q=0.7,*;q=0.3"
	);
			
			$curlPost="user=admin";
			$cookief= dirname(__FILE__).'/cookie.txt'; 
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, $url);
			if($ishtml){
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			}
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,10); 
		//	curl_setopt($ch,CURLOPT_POST,1);
		//	curl_setopt($ch,CURLOPT_POSTFIELDS,$curlPost );
       		curl_setopt($ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt($ch,CURLOPT_COOKIEJAR,$cookief );
			curl_setopt($ch,CURLOPT_COOKIEFILE,$cookief );
			$newfile = curl_exec($ch);
			curl_close($ch);
			
			$preg=array("/(href=)(\"|\')(http.*?)(\"|\')?/i",
				"/(href=)(\"|\')([^http].*?)(\"|\')?/i",
				"/(src=)(\"|\')(http.*?)\.js(\"|\')?/i",
				"/(src=)(\"|\')([^http].*?)\.js(\"|\')?/i"
			);
			$replace=array(
				'$1$2'.$myurl."$3$4",
				'$1$2'.$myurl.$baseurl."$3$4",
				"$1$2".$myurl."$3.js$4",
				"$1$2".$myurl.$baseurl."$3.js$4"
			);
			
			if(TR){
				$data=preg_replace($preg,$replace,$newfile);
				echo $data;
				file_put_contents("111.html",$data);
			}
			return $newfile;
}

function baseurl($url){
	$baseurl=strrchr($url,"/");

	if($baseurl==false || strlen($baseurl)<5){
		return $url;
	}
	$baseurl=str_replace($baseurl,'',$url);
	return $baseurl."/";
}

?>
<script>
	function strdomain(str){
		var reg=/(href=)(\"|\')(http.*?)(\"|\')?/g;		
		var gain=str.replace(reg,'\n');
	}
	$(function(){
		$("a").mouseover(function(){
			var href=$(this).attr("href");
		//	href.
			if(href.indexOf("http")==0){
				$(this).attr("href","<?=$myurl?>"+href);
			}
		//	<?=$myurl?>
		//	
		// attr("href","test.jpg");
		})
	})
	
</script>
<? 
if(!empty($_GET['url']))exit;
?>
<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>curl</title>
</head>
<body>
<form>
url:&nbsp;&nbsp;&nbsp;<input type="" name="url" size="68" value=""/>(请输入完整网址  别忘了加http://)<br>
cookie:<textarea cols="60" rows="5" name="cookie"></textarea>
<input type="submit"  value="提交"/>
</form>
</body>
</html>