<?php
session_start();
if($_SESSION['userId'] == null) { return false; }

require_once("inc/db.inc");
require_once("inc/commonFunction.inc");

$request_by_ajax = $_GET['request_by_ajax'];
$loader = $_GET['loader'];
$req_type = $_GET['req_type'];
if($request_by_ajax != 1) {
	$ary_js = array('fullcalendar','commonFunction','main');
	$ary_css = array('fullcalendar');
	pageStart($ary_js,$ary_css);
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
	$modal = getModalAlert('Loading Game...');
	$str = "
		<div id='div_cal'></div>
		$modal";
	
	echo $str;
}
?>