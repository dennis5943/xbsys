<?php
session_start();
if($_SESSION['userId'] == null) { return false; }

require_once("inc/db.inc");
require_once("inc/commonFunction.inc");

$request_by_ajax = $_GET['request_by_ajax'];
$loader = $_GET['loader'];
$req_type = $_GET['req_type'];
if($request_by_ajax != 1) {
	$ary_js = array('commonFunction','admin');
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
	$str_systemMsg = str_replace("<br/>","\n",getSystemSetting('sys_msg'));
	$str = "
		<div class='container'>
			<form class='form-horizontal'>
				<div class='control-group'>
					<div class='control-group'>
						<div class='controls'>
							<textarea id='txt_myMsg' placeholder='' rows='10'>$str_systemMsg</textarea>
						</div>
					</div>
					<div class='control-group'>
						<div class='controls'>
							<button class='btn btn-primary' onclick='onclkSubmit();return false;'><i class='icon-ok icon-white'></i> 更新</button>
						</div>
					</div>
				</div>
			</form>
		</div>";
	
	echo $str;
}

function loader_msg($reqType) {
	switch($reqType) {
		case 'onclkSubmit':
			onclkSubmit(rawurldecode($_GET['txt_myMsg']));
			return 0;
			break;
		default:
			break;
	}
}

function onclkSubmit($txt_myMsg) {
	global $sess;
	
	$sql = "
		delete from sys_setting where setting_name='sys_msg';
		insert into sys_setting values ('sys_msg','$txt_myMsg');";
	$record = $sess->getResult($sql); 
	
	return 0;
}
?>