<?php
require_once "inc/TelnetDog.class.php";

$myUser = 'kakamiRB';
$myPass = 'jasmine';
//$ary_mailList = array('kakami','zhenyuan','TETUO','johnny7157','emilchu98');
$ary_mailList = array('kakami');
$mailSubject = '[約戰]';
$mailContent = '>>>>>>換行>>測>>試>>中';	// change line: >>

$telnet = new TelnetDog('ptt.cc',23);

$result = login($myUser,$myPass);
if($result) {
	$result = mailman($ary_mailList,$mailSubject,$mailContent);
}
logout();

function login($myUser,$myPass) {
	global $telnet;
	
	$flag = false;
	
	while($telnet->Receiving()) {
		$ret = $telnet->Get();
		//echo $ret;
		if(strpos($ret,'或以 new 註冊:') !== false) {
			$telnet->Execute($myUser);
			break;
		}
	}
	while($telnet->Receiving()) {
		$ret = $telnet->Get();
		//echo $ret;
		if(strpos($ret,'您的密碼:') !== false) {
			$telnet->Execute($myPass);
			break;
		}
	}
	while($telnet->Receiving()) {
		$ret = $telnet->Get();
		//echo $ret;
		if(strpos($ret,'請按任意鍵繼續') !== false) {
			$telnet->Execute('');
			$flag = true;
			break;
		}
	}
	
	return $flag;
}

function mailman($ary_mailList,$mailSubject,$mailContent) {
	global $telnet;
	
	$flag = false;
	
	while($telnet->Receiving()) {
		$ret = $telnet->Get();
		//echo $ret;
		if(strpos($ret,'主功能表') !== false) {
			$telnet->Execute('m');
			break;
		}
	}
	while($telnet->Receiving()) {
		$ret = $telnet->Get();
	//	echo $ret;
		if(strpos($ret,'重建信箱索引') !== false) {
			$telnet->Execute('m');
			break;
		}
	}
	while($telnet->Receiving()) {
		$ret = $telnet->Get();
	//	echo $ret;
		if(strpos($ret,'引入其他特別名單') !== false) {
			$telnet->Execute('a');
			break;
		}
	}
	while($telnet->Receiving()) {
		$ret = $telnet->Get();
	//	echo $ret;
		if(strpos($ret,'請輸入要增加的代號') !== false) {
			for($i=0; $i < count($ary_mailList); $i++) {
				$telnet->Execute($ary_mailList[$i]);	
			}
			$telnet->Execute('');
			break;
		}
	}
	while($telnet->Receiving()) {
		$ret = $telnet->Get();
		echo $ret;
		if(strpos($ret,'確認寄信名單') !== false) {
			$telnet->Execute('m');
			break;
		}
	}
	while($telnet->Receiving()) {
		$ret = $telnet->Get();
		echo $ret;
		if(strpos($ret,'主題：') !== false) {
			$telnet->Execute($mailSubject);
			break;
		}
	}
	while($telnet->Receiving()) {
		$ret = $telnet->Get();
//		echo $ret;
		if(strpos($ret,'編輯文章') !== false) {
			$telnet->Execute(chr(20),false);	// ctrl-t: to end of file
			
			$ary_mailContent = explode('>>',$mailContent);
			for($i=0; $i < count($ary_mailContent); $i++) {
				$telnet->Execute($ary_mailContent[$i]);
			}

			$telnet->Execute(chr(24),false);	// ctrl-x
			break;
		}
	}
	
	while($telnet->Receiving()) {
		$ret = $telnet->Get();
//		echo $ret;
		if(strpos($ret,'確定要儲存檔案嗎') !== false) {
			$telnet->Execute('s');
			break;
		}
	}
	while($telnet->Receiving()) {
		$ret = $telnet->Get();
		echo $ret;
		if(strpos($ret,'已順利寄出，是否自存底稿') !== false) {
			$telnet->Execute('');
			$flag = true;
			break;
		}
	}
	
	return $flag;
}

function logout() {
	global $telnet;
	
	$telnet->Close();
}
?>