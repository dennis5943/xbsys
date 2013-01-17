<?php
session_start();
require_once("inc/db.inc");
require_once("inc/commonFunction.inc");

$request_by_ajax = $_GET['request_by_ajax'];
$loader = $_GET['loader'];
$req_type = $_GET['req_type'];

if($request_by_ajax != 1) {
    main();
} else {
    if($loader == 1) {
        $content = loader_login($req_type);
        echo $content;
        return;
    } 
}

function main() {
	$str_systemMsg = getSystemSetting('sys_msg');
	$str_footer = getFooter();
	$str = "
	<!DOCTYPE html>
	<html lang='zh-TW'>
	<html>
		<head>
			<meta charset='utf-8' />
			<title>360成就團</title>
			<link href='css/bootstrap.css' rel='stylesheet'></head>
		<body>
			<script src='js/jquery.js'></script>
			<script src='js/bootstrap.js'></script>
			<script src='js/login.js'></script>
			<div class='container'>
				<form class='form-horizontal'>
					<div class='well'>$str_systemMsg</div>
					<div class='control-group'>
						<div class='controls'>
							<input type='text' id='txt_account' placeholder='You know'>
						</div>
					</div>
					<div class='control-group'>
						<div class='controls'>
							<input type='password' id='txt_pass' placeholder='what to do...'>
						</div>
					</div>
					<div class='control-group'>
						<div class='controls'>
							<button class='btn btn-primary' onclick='onclkLogin();return false;'><i class='icon-off icon-white'></i> Log me in!</button>
						</div>
					</div>
				</form>
			</div>
			$str_footer
		</body>
	</html>";
	
	echo $str;
}

function loader_login($reqType) {
	switch($reqType) {
		case 'onclkLogout':
			return onclkLogout();
			break;
		case 'onclkLogin':
			$txt_account = $_GET['txt_account'];
			$txt_pass = $_GET['txt_pass'];
			$req_type = $_GET['req_type'];
			return onclkLogin($txt_account,$txt_pass);
			break;
		case 'doLogin':
			$txt_account = $_GET['txt_account'];
			$txt_pass = $_GET['txt_pass'];
			return doLogin($txt_account,$txt_pass);
			break;
		default:
			break;
	}
}

function onclkLogin($txt_account,$txt_pass) {
	global $sess;

echo "[onclkLogin]".print_r($sess).']';	
	$sql = "select count(*) from sys_user where user_name='$txt_account' and user_pass='$txt_pass';";
	$vardb = $sess->getVar($sql);
	
	return $vardb;
}

function doLogin($txt_account,$txt_pass) {
	global $sess;
	
	$sql = "select user_id from sys_user where user_name='$txt_account';";
	$vardb = $sess->getVar($sql);
	$UINFO = getUserInfo($vardb);
	$_SESSION['userId'] = $UINFO['userId'];
	$_SESSION['userName'] = $UINFO['userName'];
	$_SESSION['userType'] = $UINFO['userType'];
	$_SESSION['xbl_AvatarTile'] = $UINFO['xbl_AvatarTile'];
	
	return $_SESSION['userId'];
}

function onclkLogout() {
	session_unset();
	session_destroy();
	
	return 0;
}
?>