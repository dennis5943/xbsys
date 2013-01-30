<?php
define("USER_ARRANGE_STATUS_YES", 1);
define("USER_ARRANGE_STATUS_YET", 2);
define("USER_ARRANGE_STATUS_NO", 3);
define("USER_TYPE_LV_1", 1);
define("USER_TYPE_LV_2", 2);
define("USER_TYPE_LV_4", 4);
define("USER_ID_SYSTEM", 5566);

$GLOBAL_XBL = array();

function getNavbar($userName) {
	$ary_tmp = explode('/',$_SERVER['SCRIPT_NAME']);
	$scriptName = $ary_tmp[count($ary_tmp)-1];
	$scriptName = str_replace('.php','',$scriptName);
	switch($scriptName) {
		case 'msg':
			$class_msg = "class='active'";break;
		case 'schedue':
			$class_schedue = "class='active'";break;
		case 'arrange':
			$class_arrange = "class='active'";break;
		case 'friend':
			$class_friend = "class='active'";break;
		case 'main':
		default:
			$class_main = "class='active'";break;
	}

	if((intval($_SESSION['userType']) & USER_TYPE_LV_4) == USER_TYPE_LV_4) {
		$nav_admin = "<li $class_admin><a href='admin.php'><i class='icon-cog'></i> admin</a></li>";
	}
	$str = "
	<div class='navbar'> 
	    <div class='navbar-inner'> 
	        <div class='container'> <!-- brand class is from bootstrap.css --> 
	            <div class='nav-collapse'> 
	                <ul class='nav'>
	                	<li><img src='".$_SESSION['xbl_AvatarTile']."' class='img-polaroid'></li>
	                    <li class='dropdown'><a href='#' class='dropdown-toggle' data-toggle='dropdown'><i class='icon-user'></i> $userName<b class='caret'></b></a> 
	                        <ul class='dropdown-menu'> 
	                            <li><a href='chgpass.php'>改密碼</a></li>
	                            <li><a href='#' onclick='return false;'>更新頭像</a></li>
	                            <li class='divider'></li> 
	                            <li><a href='#' onclick='onclkLogout();return false;'>登出</a></li>
	                        </ul> 
	                    </li> 
	                    <li $class_main><a href='main.php'><i class='icon-calendar'></i> 日曆</a></li> 
	                    <li $class_msg><a href='msg.php'><i class='icon-tasks'></i> 新留言</a></li>
	                    <li $class_schedue><a href='schedue.php'><i class='icon-time'></i> 我的行程</a></li>
	                    <li $class_arrange><a href='arrange.php'><i class='icon-wrench'></i> 安排約戰</a></li>
	                    <li $class_friend><a href='friend.php'><i class='icon-thumbs-up'></i> 好朋友們</a></li>
	                    $nav_admin
	                </ul> 
	            </div><!-- /.nav-collapse --> 
	        </div> 
	    </div><!-- /navbar-inner --> 
	</div><!-- /navbar -->";

	return $test.$str;
}

function getUserInfo($userId) {
	global $sess;
	
	$UINFO = array();
	$xbl_exp_days = getSystemSetting('xbl_exp_days');

	$sql = "select * from sys_user where user_id=$userId;";
	$record = $sess->getResult($sql); 
	for($i=0; $i < count($record); $i++){
		$mod = $record[$i];
		$UINFO['userId'] = $mod->user_id;
		$UINFO['userName'] = $mod->user_name;
		$UINFO['userType'] = $mod->user_type;
		// update XBL attribute or not
		if((time() - strtotime($mod->xbl_update_time)) > $xbl_exp_days*24*60*60 
			|| strlen($mod->xbl_avatartile) == 0) {
			updateXBL($mod->user_id,$mod->user_name);
		}
		if($UINFO['userId'] == USER_ID_SYSTEM) {
			$UINFO['xbl_AvatarTile'] = "img/tile_system.jpg";
		} else {
			$UINFO['xbl_AvatarTile'] = $mod->xbl_avatartile;
		}
	}

	return $UINFO;
}
function pageStart($ary_js=null,$ary_css=null) {
	$html_navbar = getNavbar($_SESSION['userName']);
	
	for($i=0; $i < count($ary_js); $i++) {
		$list_js .= "<script src='js/".$ary_js[$i].".js'></script>";
	}
	for($i=0; $i < count($ary_css); $i++) {
		$list_css .= "<link href='css/".$ary_css[$i].".css' rel='stylesheet'>";
	}
	
	$str = "
	<!DOCTYPE html>
	<html lang='zh-TW'>
		<head>
			<meta charset='utf-8' />
			<title>360成就團</title>
			<link href='css/bootstrap.css' rel='stylesheet'>
			$list_css</head>
		<body>
			<script src='js/jquery.js'></script>
			<script src='js/jquery_ui.js'></script>
			<script src='js/bootstrap.js'></script>
			$list_js
			<div class='container-fluid'>
				<div class='row-fluid'>
					<div class='span12'>
						$html_navbar
						<div class='container'>";

	echo $str;
}

function pageEnd() {
	$str_footer = getFooter();
	$str = "
						</div>
					</div>
				</div>
			</div>
			$str_footer
		</body>
	</html>";

	echo $str;
}

function getFooter() {
	$str = "
			<footer class='footer'>
			<div class='container'>
			<pre>
			Host by zhenyuanJP
			Designed and built with all the love in the world by kakamiTW
			Glyphicons Free licensed under CC BY 3.0.
			All rights reserved by all means and yes, I mean all</pre>
			</div>		
			</footer>";
	return $str;
}

function getNewSeq($seqName) {
	global $sess;
	
	$sql = "select nextval('$seqName'::text);";
	$vardb = $sess->getVar($sql);
	
	return $vardb;
}

function getSystemSetting($settingName) {
	global $sess;
	
	$sql = "select setting_value from sys_setting where setting_name='$settingName';";
	$vardb = $sess->getVar($sql);
	
	return $vardb;
}

//{
//  "Data": {
//    "Tier": "gold",
//    "IsValid": 1,
//    "IsCheater": 0,
//    "IsOnline": 0,
//    "OnlineStatus": "Last seen 16 hours ago playing Xbox.com",
//    "XBLLaunchTeam": 0,
//    "NXELaunchTeam": 0,
//    "KinectLaunchTeam": 0,
//    "AvatarTile": "https:\/\/avatar-ssl.xboxlive.com\/global\/t.fffe07d1\/tile\/0\/21032",
//    "AvatarSmall": "http:\/\/avatar.xboxlive.com\/avatar\/kakamiTW\/avatarpic-s.png",
//    "AvatarLarge": "http:\/\/avatar.xboxlive.com\/avatar\/kakamiTW\/avatarpic-l.png",
//    "AvatarBody": "http:\/\/avatar.xboxlive.com\/avatar\/kakamiTW\/avatar-body.png",
//    "Gamertag": "kakamiTW",
//    "GamerScore": 29115,
//    "Reputation": 20,
//    "Name": "\u30ab\u30a4\u30b8",
//    "Motto": "\u3056\u308f\u3056\u308f",
//    "Location": "\u53f0\u6e7e",
//    "Bio": "2012\/04\/23 10 100% complete.\r\n2012\/05\/15 20k"
//  },
//  "Stat": "ok",
//  "In": 1.273,
//  "Authed": "false",
//  "AuthedAs": null
//}
function getXBL($gamertag) {
	if(strpos($gamertag,' ') !== false)  {
		$gamertag = str_replace(' ','%20',$gamertag);
	}
	// Settings
	$profileUrl = 'http://www.xboxleaders.com/api/profile/'.$gamertag.'.json';
	
	// Get information about me
	$info = file_get_contents($profileUrl);
	
	// To JSON
	$json = json_decode($info);
	$user = $json->Data;
	
	return $user;
}

function updateXBL($userId,$userName) {
	global $sess;
	
	$XBL = getXBL($userName);
	
	$sql = "
		update sys_user set xbl_AvatarTile='".$XBL->AvatarTile."' where user_id=$userId;
		update sys_user set xbl_update_time=now() where user_id=$userId;";
	$vardb = $sess->getVar($sql);
	
	return 0;
}

function writeLog($msg,$filename="logFile") {
	$time = date("Y-m-d H:i:s");
	$log_data = "===== [$time] =====\n$msg\n\n";
	$file_name = "log/$filename";
	$log_file = @fopen($file_name, "a");
	if ($log_file) {
	   fputs($log_file,$log_data);
	   fclose($log_file);
	}
}

// out: div
function getModalAlert($msg,$divId='modal_loading') {
	$ret = "
		<!-- Modal -->
		<div id='$divId' class='modal hide fade' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
			<!-- <div class='modal-header'></div> -->
			<div class='modal-body'>
				<h1>$msg</h1>
				<div style='text-align:center'><img src='img/loading_heavy_machine_gun.gif'></div></div>
			<!-- <div class='modal-footer'></div> -->
		</div>";
		
	return $ret;
}

// out: div
function getModalAlertBtn($msg) {
	$ret = "
		<!-- Modal -->
		<div id='modal_alertBtn' class='modal hide fade' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
			<!-- <div class='modal-header'></div> -->
			<div class='modal-body'>
				<h1>$msg</h1>
				<div style='text-align:center'><img src='img/rebel_gossip.gif'></div></div>
			<div class='modal-footer'>
				<button class='btn btn-primary btn-large' onclick='$(\"#modal_alertBtn\").modal(\"toggle\");return false;'><i class='icon-ok icon-white'></i> OK</button></div>
		</div>";
		
	return $ret;
}

function getGameInfo($gameId) {
	global $sess;
	
	$GINFO = array();

	$sql = "select * from sys_game where game_id=$gameId;";
	$record = $sess->getResult($sql); 
	for($i=0; $i < count($record); $i++){
		$mod = $record[$i];

		$GINFO['gameId'] = $mod->game_id;
		$GINFO['gameName'] = $mod->game_name;
		$GINFO['iconUrl'] = $mod->icon_url;
	}
	
	return $GINFO;
}

?>