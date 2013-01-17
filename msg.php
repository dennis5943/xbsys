<?php
session_start();
if($_SESSION['userId'] == null) { return false; }

require_once("inc/db.inc");
require_once("inc/commonFunction.inc");

define('MSG_PER_PAGE',50);

$request_by_ajax = $_GET['request_by_ajax'];
$loader = $_GET['loader'];
$req_type = $_GET['req_type'];
if($request_by_ajax != 1) {
	$ary_js = array('commonFunction','msg');
	pageStart($ary_js);
	main();
	pageEnd();
} else {
    if($loader == 1) {
        $content = loader_msg($req_type);
        echo $content;
        return;
    } 
}

function main() {
	$str_msg = getAllMsg();
	$modal = getModalAlert('Sending Message...');
	$str = "
		<div class='container'>
			<form class='form-horizontal'>
				<div class='control-group'>
					<div class='controls'>
						<textarea id='txt_myMsg' placeholder='Smoke Bomb..' rows='2'></textarea>
						<button type='button' id='btn_submit' class='btn btn-primary btn-large' onclick='onclkSubmitMsg();'><i class='icon-ok icon-white'></i> 好了</button>
					</div>
				</div>
				<div id='div_msg'>
				$str_msg
				$modal
				</div>
			</form>
		</div>";
	
	echo $str;
}

function loader_msg($reqType) {
	switch($reqType) {
		case 'updateAvatar':
			$userName = $_GET['userName'];
			return updateAvatar($userName);
			break;
		case 'onclkSubmitMsg':
			$txt_myMsg = $_GET['txt_myMsg'];
			$msgId = onclkSubmitMsg($txt_myMsg);
			return getAllMsg(MSG_PER_PAGE,$msgId);
			break;
		case 'onclkDeleteMsg':
			$msgId = $_GET['msgId'];
			onclkDeleteMsg($msgId);
			return getAllMsg();
			break;
		default:
			break;
	}
}

function onclkSubmitMsg($msg) {
	global $sess;
	
	$msgId = getNewSeq('seq_msg_id');
	$sql = "insert into log_msg (msg_id,user_id,time,msg) values ($msgId,".$_SESSION['userId'].",now(),'$msg');";
	$vardb = $sess->getVar($sql);

	return $msgId;
}

function onclkDeleteMsg($msgId) {
	global $sess;
	
	$sql = "delete from log_msg where msg_id=$msgId;";
	$vardb = $sess->getVar($sql);

	return 0;
}

function getAllMsg($limit=MSG_PER_PAGE, $msgId=0) {
	global $sess;
	global $GLOBAL_XBL;
	
	if($msgId > 0) { $sql_cri = "and lm.msg_id=$msgId"; }
	
	$sql = "select su.user_name, lm.user_id, lm.msg_id, lm.time, lm.msg from log_msg lm
		left join sys_user su on lm.user_id=su.user_id
		where 1=1
		$sql_cri
		order by lm.time desc
		limit $limit;";
	$record = $sess->getResult($sql); 
	for($i=0; $i < count($record); $i++){
		$mod = $record[$i];

		$str_time = date('Y/m/d H:i',strtotime($mod->time));
		if($mod->user_id == $_SESSION['userId']) {
			$btn_del = "<button type='button' class='close' onclick='onclkDeleteMsg(".$mod->msg_id.");return false;'>×</button>";
		} else {
			$btn_del = '';
		}
		
		if(isset($GLOBAL_XBL[$mod->user_name])) {
			$tmpXBL = $GLOBAL_XBL[$mod->user_name];
			$img_avatar = "<img src='".$tmpXBL->AvatarTile."' class='img-polaroid'>";
		} else {
			if($msgId > 0) { 
				$img_avatar = "<img src='".$_SESSION['xbl_AvatarTile']."' class='img-polaroid'>";
			} else {
				$img_avatar = "<img src='img/ajax-loader.gif' class='img-polaroid' style='padding: 26px;'>";
			}
		}
		
		$str .= "<blockquote>
			<div class='input-append'>
			<button class='btn btn-primary disabled' type='button'>".$mod->user_name."</button>
			<span class='add-on'>@</span>
			<button class='btn btn-info disabled' type='button'>$str_time</button>
			</div>
			
			<div style='float:left;' class='cls_avatar' userName='".$mod->user_name."'>$img_avatar</div>
			<pre><div style='margin-left: 88px;position: relative;'>".$btn_del.$mod->msg."</div></pre>
			</blockquote>";
	}

	return $str;
}

function updateAvatar($userName) {
	global $sess;
	
	$sql = "select user_id from sys_user where user_name='$userName';";
	$vardb = $sess->getVar($sql);
	$UINFO = getUserInfo($vardb);
	$img_avatar = "<img src='".$UINFO['xbl_AvatarTile']."' class='img-polaroid'>";
	
	return $img_avatar;
}
?>