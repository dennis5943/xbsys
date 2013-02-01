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
				$modal2
				<!-- Modal -->
				<div id='modal_addNewGame' class='modal hide fade' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
					<div class='modal-header'><h1>加新遊戲</h1></div>
					<div class='modal-body'>
						<div class='control-group'>
							<button class='btn disabled' type='button'>遊戲名稱</button>
							<input type='text' id='txt_gameName' placeholder='Blue Dragon(藍龍) or Blue Dragon' class='input' style='width:300px'>
						</div>
						<div class='alert'>
						<strong><i class='icon-flag'></i> 注意 </strong>命名規則=英文名稱(中文名稱) 中文可省略
						</div>
						<div class='alert alert-error'>
						<strong><i class='icon-warning-sign'></i> 警告 </strong>不要加到重複的遊戲
						</div></div>
					<div class='modal-footer'>
						<button class='btn btn-large' onclick='$(\"#modal_addNewGame\").modal(\"toggle\");return false;'><i class='icon-remove'></i> 取消</button>
						<button class='btn btn-primary btn-large' onclick='onclkSubmitNewGame();return false;'><i class='icon-ok icon-white'></i> 加</button></div>
				</div></div>
			<div class='control-group'>
				<div class='controls'>
					<div class='input'>
					<button class='btn disabled' type='button'>Game</button>
					<select id='sel_game' style='width:365px'>$list_game</select>
					<button class='btn btn-primary' type='button' onclick='$(\"#modal_addNewGame\").modal(\"toggle\");return false;'>加新遊戲</button>
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
		case 'onclkSubmitNewGame':
			$txt_gameName = rawurldecode($_GET['txt_gameName']);
			onclkSubmitNewGame($txt_gameName);
			return 0;			
		case 'onclkSubmitArrange':
			$sel_game = $_GET['sel_game'];
			$txt_date = $_GET['txt_date'];
			$txt_time = $_GET['txt_time'];
			$txt_myMsg = $_GET['txt_myMsg'];
			onclkSubmitArrange($sel_game,$txt_date,$txt_time,$txt_myMsg);
			return 0;
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

function onclkSubmitNewGame($txt_gameName) {
	global $sess;
	
	$game_id = getNewSeq('seq_game_id');
	$sql = "insert into sys_game (game_id,game_name)
	values ($game_id,'$txt_gameName')";
	$record = $sess->getResult($sql);
	
	return 0;
}
?>