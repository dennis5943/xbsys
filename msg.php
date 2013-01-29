<?php
session_start();
if($_SESSION['userId'] == null) { return false; }

require_once("inc/db.inc");
require_once("inc/commonFunction.inc");

define('MSG_PER_PAGE',20);

$request_by_ajax = $_GET['request_by_ajax'];
$loader = $_GET['loader'];
$req_type = $_GET['req_type'];
$page = strlen($_GET['page']) ? $_GET['page'] : 1;

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
	global $page;
	
	$str_msg = getAllMsg(MSG_PER_PAGE,$page);
	$modal = getModalAlert('Sending Message...');
	$modal_del = getModalAlert('Deleting...','modal_loading_del');
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
				$modal_del
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
			return getAllMsg(MSG_PER_PAGE,1);
			break;
		case 'onclkDeleteMsg':
			$msgId = $_GET['msgId'];
			onclkDeleteMsg($msgId);
			return getAllMsg(MSG_PER_PAGE,1);
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

function getAllMsg($limit=MSG_PER_PAGE, $page) {
	global $sess;
	global $GLOBAL_XBL;
	
	$allMsgCount = getAllMsgCount();
	$offset = $limit*($page - 1);
	
	$sql = "select su.user_name, lm.user_id, lm.msg_id, lm.time, lm.msg from log_msg lm
		left join sys_user su on lm.user_id=su.user_id
		where 1=1
		order by lm.time desc
		limit $limit
		offset $offset;";
	$record = $sess->getResult($sql); 
	for($i=0; $i < count($record); $i++){
		$mod = $record[$i];
		$class_system = '';

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
		
		if($mod->user_id == USER_ID_SYSTEM) {	// system msg
			$div_msg = "<div class='alert alert-success'><div style='margin-left: 88px;position: relative;'>".$btn_del."<i class='icon-info-sign'></i> ".$mod->msg."</div></div>";
		} else {	// normal msg
			$div_msg = "<pre><div style='margin-left: 88px;position: relative;'>".$btn_del.$mod->msg."</div></pre>";
		}
		
		$str .= "<blockquote id='bq_".$mod->msg_id."'>
			<div class='input-append'>
			<button class='btn btn-primary disabled' type='button'>".$mod->user_name."</button>
			<span class='add-on'>@</span>
			<button class='btn btn-info disabled' type='button'>$str_time</button>
			</div>
			
			<div style='float:left;' class='cls_avatar' userName='".$mod->user_name."'>$img_avatar</div>
			$div_msg
			</blockquote>";
	}
	
	$str_paging = getPagination($page,ceil($allMsgCount / $limit));

	return $str.$str_paging;
}

function updateAvatar($userName) {
	global $sess;
	
	$sql = "select user_id from sys_user where user_name='$userName';";
	$vardb = $sess->getVar($sql);
	$UINFO = getUserInfo($vardb);
	$img_avatar = "<img src='".$UINFO['xbl_AvatarTile']."' class='img-polaroid'>";
	
	return $img_avatar;
}

function getPagination($nowPage,$totalPage) {
	if($nowPage == 1) {
		$str_prev = "<li class='disabled'><a href='#'><i class='icon-chevron-left'></i> 更新的</a></li>";
	} else {
		$str_prev = "<li><a href='msg.php?page=".($nowPage - 1)."'><i class='icon-chevron-left'></i> 更新的</a></li>";
	}

	if($nowPage == $totalPage) {
		$str_next = "<li class='disabled'><a href='#'>更舊的 <i class='icon-chevron-right'></i></li>";
	} else {
		$str_next = "<li><a href='msg.php?page=".($nowPage + 1)."'>更舊的 <i class='icon-chevron-right'></i></a></li>";
	}	
	
	for($i=0; $i < $totalPage; $i++) {
		if(($i+1) == $nowPage) {
			$str_page .= "<li class='disabled'><a href='#'>".($i + 1)."</a></li>";
		} else {
			$str_page .= "<li><a href='msg.php?page=".($i + 1)."'>".($i + 1)."</a></li>";
		}
	}
	$ret = "
		<div class='pagination pagination-centered'>
			<ul>
				$str_prev
				$str_page
				$str_next
			</ul>
		</div>";
	
	return $ret;
}

function getAllMsgCount() {
	global $sess;
	
	$sql = "select count(*) from log_msg;";
	$vardb = $sess->getVar($sql);
	
	return $vardb;
}
?>