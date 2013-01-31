<?php
session_start();
if($_SESSION['userId'] == null) { return false; }

require_once("inc/db.inc");
require_once("inc/commonFunction.inc");

$request_by_ajax = $_GET['request_by_ajax'];
$loader = $_GET['loader'];
$req_type = $_GET['req_type'];
if($request_by_ajax != 1) {
	$ary_js = array('jquery.qtip','commonFunction','friend');
	pageStart($ary_js);
	main();
	pageEnd();
} else {
    if($loader == 1) {
        $content = loader_schedue($req_type);
        echo $content;
        return;
    } 
}

function main() {
	global $sess;
	
	$sql = "select user_name from sys_user 
		where user_id <> ".USER_ID_SYSTEM."
		order by lower(user_name);";
	$record = $sess->getResult($sql);
	for($i=0; $i < count($record); $i++){
		$mod = $record[$i];
		
		$div_tmp = "
			<div class='span4'>
				<div class='thumbnail'>
					<div class='caption'>
						<h3 class='gt'>".$mod->user_name."</h3>
						<div class='AvatarBody' style='text-align:center'></div>
						<div class='GamerScore hero-unit' style='font-size:60px;text-align:center;'><img src='img/loading_green_circle.gif'></div>
						<div class='OnlineStatus'></div>
					</div>
				</div>
			</div>";
		if($i % 3 == 0) { $div .= "<div class='row'>"; }
		$div .= $div_tmp;
		if($i % 3 == 2) { $div .= "</div>"; }
	}
	
	
	$str = "
		<div class='container'>
		$div
		</div>";
	
	echo $str;
}

function loader_schedue($reqType) {
	switch($reqType) {
		case 'updateFriend':
			return json_encode(getXBL($_GET['gamerTag']));
	}
}
?>