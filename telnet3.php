<?php
require_once "inc/TelnetDog.class.php";

$myUser = 'kakamiRB';
$myPass = 'jasmine';
//$ary_mailList = array('kakami','zhenyuan','TETUO','johnny7157','emilchu98');
$ary_mailList = array('kakami');
$mailSubject = '[����]';
$mailContent = '>>>>>>����>>��>>��>>��';	// change line: >>

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
		if(strpos($ret,'�ΥH new ���U:') !== false) {
			$telnet->Execute($myUser);
			break;
		}
	}
	while($telnet->Receiving()) {
		$ret = $telnet->Get();
		//echo $ret;
		if(strpos($ret,'�z���K�X:') !== false) {
			$telnet->Execute($myPass);
			break;
		}
	}
	while($telnet->Receiving()) {
		$ret = $telnet->Get();
		//echo $ret;
		if(strpos($ret,'�Ы����N���~��') !== false) {
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
		if(strpos($ret,'�D�\���') !== false) {
			$telnet->Execute('m');
			break;
		}
	}
	while($telnet->Receiving()) {
		$ret = $telnet->Get();
	//	echo $ret;
		if(strpos($ret,'���ثH�c����') !== false) {
			$telnet->Execute('m');
			break;
		}
	}
	while($telnet->Receiving()) {
		$ret = $telnet->Get();
	//	echo $ret;
		if(strpos($ret,'�ޤJ��L�S�O�W��') !== false) {
			$telnet->Execute('a');
			break;
		}
	}
	while($telnet->Receiving()) {
		$ret = $telnet->Get();
	//	echo $ret;
		if(strpos($ret,'�п�J�n�W�[���N��') !== false) {
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
		if(strpos($ret,'�T�{�H�H�W��') !== false) {
			$telnet->Execute('m');
			break;
		}
	}
	while($telnet->Receiving()) {
		$ret = $telnet->Get();
		echo $ret;
		if(strpos($ret,'�D�D�G') !== false) {
			$telnet->Execute($mailSubject);
			break;
		}
	}
	while($telnet->Receiving()) {
		$ret = $telnet->Get();
//		echo $ret;
		if(strpos($ret,'�s��峹') !== false) {
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
		if(strpos($ret,'�T�w�n�x�s�ɮ׶�') !== false) {
			$telnet->Execute('s');
			break;
		}
	}
	while($telnet->Receiving()) {
		$ret = $telnet->Get();
		echo $ret;
		if(strpos($ret,'�w���Q�H�X�A�O�_�ۦs���Z') !== false) {
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