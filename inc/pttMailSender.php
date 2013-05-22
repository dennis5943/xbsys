<?
/*
 * PTT MAIL SENDER
 * https://github.com/fisherTW/pttMailSender
 *
 * Copyright (c) 2013 Fisher Liao
 * fisher.liao@gmail.com
 *
 * Dual licensed under the MIT and GPL licenses.
 *
 * Date: 2013/5/21
 */

//	parameters
//	$ary_mailList	set here

$myUser			= 'kakamiRB';
$myPass			= 'jasmine';
$mailSubject	= "[約戰] 有人在約戰啦~";
$mailContent	= "\r\r快上約戰網看看吧\rhttp://www.zhenyuan.idv.tw:8888/";

$ret = '';
$json2 = array();
$json4 = array();

$host = "ptt.cc";
$port = 443;
$ary_mailList = array('kakami','zhenyuan','TETUO','johnny7157','emilchu98');

$json2[] = array('receive'=>'或以 new 註冊:','send'=> $myUser);
$json2[] = array('receive'=>'您的密碼:','send'=> $myPass,'sleepTime'=> 5);
$json2[] = array('receive'=>'系統負荷量大','send'=> '','sleepTime'=> 5);
$json2[] = array('receive'=>'刪除其他重複登入','send'=> 'n','sleepTime'=> 5);
$json2[] = array('receive'=>'編輯器自動復原','send'=> 'q');
$json2[] = array('receive'=>'請按任意鍵繼續','send'=> '');
$json2[] = array('receive'=>'主功能表','send'=>'m');
$json2[] = array('receive'=>'電子郵件','send'=>'m');
$json2[] = array('receive'=>'群組寄信名單','send'=>'a');
$json2 = json_encode($json2);

$json4[] = array('receive'=>'確認寄信名單','send'=>'m','sleepTime'=> 5);
$json4[] = array('receive'=>'主題','send'=> $mailSubject,'userFunc'=> "write");
$json4[] = array('receive'=>'[通告]','send'=> chr(20).$mailContent.chr(24));	// ctrl-t: to end of file // ctrl-x
$json4[] = array('receive'=>'檔案處理','send'=> 's');
//$json4[] = array('receive'=>'是否自存底稿','send'=> '');
$json4[] = array('receive'=>'電子郵件','send'=> 'e','sendCR'=> false);
$json4[] = array('receive'=>'主功能表','send'=> 'g');
$json4[] = array('receive'=>'您確定要離開','send'=> 'y');
$json4 = json_encode($json4);

main();

echo "<hr>$host:$port<hr>";

//create
$socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP); 
$connection = socket_connect($socket,$host,$port); //connect

// phase 1: login
$ret = write(2,2048);
$ret = write(2,4096);


// phase 2: go to mail menu
runJson($json2);

// phase 3: mail list
if(strpos($ret,'請輸入要增加的代號') !== false) {
	foreach($ary_mailList as $k => $v) {
		$ret = write($v);
	}
}
$ret = write('');

// phase 4: compose & send & quit
runJson($json4);

if(strpos($ret,'程式發展') !== false) {
	echo "
<script>window.close();</script>";
}


function main() {
	$str = "
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
<title>
PHP PTT Mail Sender
</title>
<script type='text/javascript' charset='utf-8' src='../js/jquery.js'></script>
<script>
var count = 1;
//$(window).load(function() {
//	setInterval(show,500);
//});
function pageLoad() {
	setInterval(show,500);
}
function show() {
	count++;
	//$('html, body').animate({ scrollTop: $(document).height() }, 1000);
	$('html, body').animate({ scrollTop: (100*count) }, 1000);
}
</script>
</head>

<body>
</body>
</html>";

	echo $str;
}

function write($str,$size=4096,$sleepTime=1,$sendCR=true) {
	global $socket;
	
	$len = strlen($str);
	
	echo "<br>sent: [$str]";
	socket_write($socket,$str,$len);
	usleep(5);
	if($sendCR) {
		socket_write($socket,"\r",1);
	}
	sleep($sleepTime);
	
	$ret = iconv("big5","UTF-8",socket_read($socket,$size,PHP_BINARY_READ));
	//$ret = socket_read($socket,$size);
	//$ret = str_replace('[K','<br>[K',$ret);

	echo "<p>$ret";
	ob_flush();
	flush();
	return $ret;
}

function runJson($json) {
	global $ret;
	
	$obj = json_decode($json);
	for($i=0; $i < count($obj); $i++) {
		$thisObj = $obj[$i];
		if(strpos($ret,$thisObj->receive) !== false) {
			$thisObj = $obj[$i];
			$size = isset($thisObj->size) ? $thisObj->size : 4096;
			$sleepTime = isset($thisObj->sleepTime) ? $thisObj->sleepTime : 1;
			$sendCR = isset($thisObj->sendCR) ? $thisObj->sendCR : true;
			$ret = write(iconv('UTF-8','big5',$thisObj->send),$size,$sleepTime,$sendCR);
			if(isset($thisObj->userFunc)) {
				call_user_func($thisObj->userFunc,'');
			}
		}
	}
}

?>