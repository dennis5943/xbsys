<?php
session_start();
if($_SESSION['userId'] == null) { return false; }

require_once("inc/db.inc");
require_once("inc/commonFunction.inc");

$request_by_ajax = $_GET['request_by_ajax'];
$loader = $_GET['loader'];
$req_type = $_GET['req_type'];
if($request_by_ajax != 1) {
	$ary_js = array('commonFunction','chgpass');
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
	$str = "
		<div class='container'>
			<form class='form-horizontal'>
				<div class='control-group'>
					<div class='control-group'>
						<div class='controls'>
							<input type='password' id='txt_passNew' placeholder='new pass'>
						</div>
					</div>
					<div class='control-group'>
						<div class='controls'>
							<input type='password' id='txt_passNewAgain' placeholder='new pass again'>
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
			onclkSubmit(rawurldecode($_GET['txt_passNew']));
			return 0;
			break;
		default:
			break;
	}
}

function onclkSubmit($txt_passNew) {
	global $sess;
	
	$sql = "update sys_user set user_pass='$txt_passNew' where user_id=".$_SESSION['userId'].";";
	$record = $sess->getResult($sql); 
	
	return 0;
}
?>