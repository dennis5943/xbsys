<?php
session_start();
if($_SESSION['userId'] == null) { return false; }

require_once("inc/commonFunction.inc");
require_once("inc/db.inc");

main();

function main() {
	global $sess;

	$ary_ret = array();
	$sql  = "select sg.game_name,la.game_id,la.time,la.msg,su.user_name
		from log_arrange la
		left join sys_game sg on (sg.game_id=la.game_id)
		left join sys_user su on (su.user_id=la.user_id);";
	$record = $sess->getResult($sql); 
	for($i=0; $i < count($record); $i++){
		$ary_tmp = array();
		$mod = $record[$i];
		
		$ary_tmp['title'] = $mod->game_name;
		$ary_tmp['start'] = $mod->time;
		$ary_tmp['description'] = (strlen($mod->msg) == 0) ? '無' : $mod->msg;
		$ary_tmp['userName'] = $mod->user_name;
		$ary_tmp['allDay'] = false;
		
		array_push($ary_ret,$ary_tmp);
	}
	
	echo json_encode($ary_ret);
}

function loader_schedue($reqType) {
	switch($reqType) {
		case 'onclickChoose':
			onclickChoose($_GET['arrangeId'],$_GET['isJoin']);
			return 0;
			break;
	}
}

?>