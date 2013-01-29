<?php
session_start();
if($_SESSION['userId'] == null) { return false; }

require_once("inc/db.inc");
require_once("inc/commonFunction.inc");

$request_by_ajax = $_GET['request_by_ajax'];
$loader = $_GET['loader'];
$req_type = $_GET['req_type'];
if($request_by_ajax != 1) {
	$ary_js = array('bootstrap-datepicker','bootstrap-timepicker','commonFunction','arrange');
	$ary_css = array('datepicker','timepicker');
	pageStart($ary_js,$ary_css);
	main();
	pageEnd();
} else {
    if($loader == 1) {
        $content = loader_arrange($req_type);
        echo $content;
        return;
    } 
}

function main() {
	$list_game = getGameList();
	$modal = getModalAlert('Sending...');
	$modal2 = getModalAlertBtn('新增完成');
	
	$str = "
		<form class='form-horizontal'>
			<div>
				$modal
				$modal2</div>
			<div class='control-group'>
				<div class='controls'>
					<div class='input'>
					<button class='btn disabled' type='button'>Game</button>
					<select id='sel_game' style='width:365px'>$list_game</select>
					</div>
				</div>
			</div>
			<div class='control-group'>
				<div class='controls'>
					<button class='btn disabled' type='button'>Date</button>
					<div class='input-append date' data-date-format='yyyy/mm/dd' data-date=''>
					<input type='text' id='txt_date' placeholder='' disabled>
					<span class='add-on'><i class='icon-calendar'></i></span>
					</div>
				</div>
			</div>
			<div class='control-group'>
				<div class='controls'>
					<button class='btn disabled' type='button'>Time</button>
					<div class='input-append bootstrap-timepicker-component'>
					<input type='text' id='txt_time' placeholder='' class='input timepicker-default' disabled>
					<span class='add-on'><i class='icon-time'></i></span>
					</div>
				</div>
			</div>
			<div class='control-group'>
				<div class='controls'>
					<button class='btn disabled' type='button'>備註</button>
					<input type='text' id='txt_myMsg' placeholder='' class='input'>
				</div>
			</div>
			<div class='control-group'>
				<div class='controls'>
					<button class='btn btn-primary btn-large' onclick='onclkSubmitArrange();return false;'><i class='icon-ok icon-white'></i> 揪</button>
				</div>
			</div>
		</form>";
	
	echo $str;
}

function loader_arrange($reqType) {
	switch($reqType) {
		case 'onclkSubmitArrange':
			$sel_game = $_GET['sel_game'];
			$txt_date = $_GET['txt_date'];
			$txt_time = $_GET['txt_time'];
			$txt_myMsg = $_GET['txt_myMsg'];
			onclkSubmitArrange($sel_game,$txt_date,$txt_time,$txt_myMsg);
			return 0;
			break;
		default:
			break;
	}
	
}

function getGameList() {
	global $sess;
	
	$sql = "select * from sys_game
		order by lower(game_name);";
	$record = $sess->getResult($sql); 
	for($i=0; $i < count($record); $i++){
		$mod = $record[$i];
		$list_game .= "<option value='".$mod->game_id."'>".$mod->game_name."</option>";
	}
	
	return $list_game;
}

function onclkSubmitArrange($sel_game,$txt_date,$txt_time,$txt_myMsg) {
	global $sess;
	
	$dateTime = $txt_date.' '.$txt_time;
	$arrange_id = getNewSeq('seq_arrange_id');
	$msgId = getNewSeq('seq_msg_id');
	$GINFO = getGameInfo($sel_game);
	
	$msg = $_SESSION['userName']." 揪了 ".$GINFO['gameName'];
	$sql = "
		insert into log_arrange (arrange_id,user_id,game_id,time,msg) 
		values ($arrange_id,".$_SESSION['userId'].",$sel_game,'$dateTime','$txt_myMsg');
		insert into log_msg (msg_id,user_id,time,msg) 
		values ($msgId,".USER_ID_SYSTEM.",now(),'$msg');";
	$record = $sess->getResult($sql);
		
	// get all user id
	$sql = "select user_id from sys_user;";
	$record = $sess->getResult($sql);
	for($i=0; $i < count($record); $i++){
		$mod = $record[$i];
		
		$status = ($mod->user_id == $_SESSION['userId']) ? USER_ARRANGE_STATUS_YES : USER_ARRANGE_STATUS_YET;
		$sql_insert .= "insert into log_arrange_apply (user_id,arrange_id,status)
			values (".$mod->user_id.",$arrange_id,$status);";
	}
	$record = $sess->getResult($sql_insert);

	return 0;	
}
?>