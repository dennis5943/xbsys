<?php
session_start();
if($_SESSION['userId'] == null) { return false; }

require_once("inc/db.inc");
require_once("inc/commonFunction.inc");

$request_by_ajax = $_GET['request_by_ajax'];
$loader = $_GET['loader'];
$req_type = $_GET['req_type'];
if($request_by_ajax != 1) {
	$ary_js = array('commonFunction','schedue');
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
	list($str_schedue,$count_schedue) = getMySchedue($_SESSION['userId'],USER_ARRANGE_STATUS_YES);
	list($str_schedueReverse,$count_schedueReverse) = getMySchedue($_SESSION['userId'],USER_ARRANGE_STATUS_YET);
	$str = "
		<div class='container'>
			<form class='form-horizontal'>
				<div id='div_schedue'>
					<blockquote>
					<div class='input'>
					<button class='btn btn-success disabled' type='button'><i class='icon-ok-circle'></i> 已排定的約戰</button>
					</div>
					<pre>"
					.$str_schedue."</pre>
					</blockquote>
				</div>
				<div id='div_schedueReverse'>
					<blockquote>
					<div class='input'>
					<button class='btn btn-warning disabled' type='button'><i class='icon-question-sign'></i> 未決定的約戰
					</button>
					<span class='badge badge-important'>$count_schedueReverse</span>
					</div>
					<pre>"
					.$str_schedueReverse."</pre>
					</blockquote>
				</div>
			</form>
		</div>";
	
	echo $str;
}

function loader_schedue($reqType) {
	switch($reqType) {
		case 'onclickChoose':
			onclickChoose($_GET['arrangeId'],$_GET['isJoin']);
			return 0;
			break;
	}
}

function getMySchedue($userId,$status) {
	global $sess;
	
	$sql = "select laa.arrange_id,la.game_id,ga.game_name,la.time,la.msg,usr.user_name from log_arrange_apply laa
		left join log_arrange la
			on (la.arrange_id=laa.arrange_id)
		left join sys_game ga
			on (ga.game_id=la.game_id)
		left join sys_user usr
			on (usr.user_id=la.user_id)
		where laa.user_id=$userId 
			and laa.status in ($status)
		order by la.time;";

	$record = $sess->getResult($sql); 
	for($i=0; $i < count($record); $i++){
		$mod = $record[$i];
		
		$avatar_joinMember = getArrangeMemberAvatar($mod->arrange_id,1);
		$avatar_notJoinMember = getArrangeMemberAvatar($mod->arrange_id,0);
		if(strlen($avatar_joinMember) == 0) { $style_divJoin = "style='display:none'"; }
		if(strlen($avatar_notJoinMember) == 0) { $style_divNotJoin = "style='display:none'"; }

		$str .= "<tr class='warning' trigger='1' inx='".($i+1)."' status='$status'>
			<td>".($i+1)."</td>
			<td>".date("Y/m/d H:i",strtotime($mod->time))."</td>
			<td>".$mod->game_name."</td>
			<td>".$mod->user_name."</td>
			<td>".$mod->msg."</td>
			</tr>";
		if($status == USER_ARRANGE_STATUS_YET) {
			$btnChoose = "
				<span class='btn-group' data-toggle='buttons-radio'>"
				."<button type='button' class='btn btn-success' inx='".$mod->arrange_id."'>參加</button>"
				."<button type='button' class='btn btn-danger' inx='".$mod->arrange_id."'>不參加</button>"
			."</span><button type='button' class='btn btn-primary' style='margin-left:20px' onclick='onclickChoose(".$mod->arrange_id.");'><i class='icon-ok icon-white'></i> 決定</button>";
		}
		$str .= "<tr class='warning' style='display:none' inx='".($i+1)."' status='$status'>
		<td colspan='5'>"
			."<div class='alert alert-info alert-block' $style_divJoin>$avatar_joinMember<span class='pull-right'>會來</span></div>"
			."<div class='alert alert-error alert-block' $style_divNotJoin>$avatar_notJoinMember<span class='pull-right'>不會來</span></div>"
			.$btnChoose
		."</td></tr>";
	}
	
	if(strlen($str) == 0) {
		$str = '你已經屎了';
	} else {
		$str = "<table class='table table-hover'>
			<thead>
			<th style='width:5%'>#</th>
			<th style='width:18%'>時間</th>
			<th style='width:22%'>Game</th>
			<th style='width:15%'>發起人</th>
			<th>說明</th>
			</thead>
			$str</table>";
	}
	
	return array($str,count($record));
}

function getArrangeMemberAvatar($arrangeId,$flag_join) {
	global $sess;
	
	$status = $flag_join ? USER_ARRANGE_STATUS_YES : USER_ARRANGE_STATUS_NO;
	$sql = "select usr.user_name,laa.user_id 
		from log_arrange_apply laa 
		left join sys_user usr
			on (usr.user_id=laa.user_id)
		where laa.arrange_id=$arrangeId 
			and laa.status=$status;";

	$record = $sess->getResult($sql); 
	for($i=0; $i < count($record); $i++){
		$mod = $record[$i];
		
		$UINFO = getUserInfo($mod->user_id);
		$ret .= "<img src='".$UINFO['xbl_AvatarTile']."' class='img-polaroid' rel='tooltip' title='".$UINFO['userName']."'>";
	}
	
	return $ret;
}

function onclickChoose($arrangeId,$isJoin) {
	global $sess;
	
	$status = USER_ARRANGE_STATUS_YET;
	if(intval($isJoin) == 1) {
		$status = USER_ARRANGE_STATUS_YES;
	} else {
		$status = USER_ARRANGE_STATUS_NO;
	}
	$sql = "update log_arrange_apply set status=$status
		where arrange_id=$arrangeId
			and user_id=".$_SESSION['userId'].";";
	$record = $sess->getResult($sql); 
	
	return 0;
}
?>