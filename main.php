<?php
session_start();
if($_SESSION['userId'] == null) { return false; }

require_once("inc/db.inc");
require_once("inc/commonFunction.inc");

$request_by_ajax = $_GET['request_by_ajax'];
$loader = $_GET['loader'];
$req_type = $_GET['req_type'];
if($request_by_ajax != 1) {
	$ary_js = array('jquery.qtip','fullcalendar','commonFunction','main');
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
		$modal
		<!-- Modal -->
		<div id='modal_event' class='modal hide fade' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
			<div class='modal-header' id='div_modalEventHeader'></div>
			<div class='modal-body'>
				<h1></h1></div>
			<div class='modal-footer'>
				<button class='btn btn-primary btn-large' onclick='$(\"#modal_event\").modal(\"toggle\");return false;'><i class='icon-ok icon-white'></i> OK</button></div>
		</div>";
	
	echo $str;
}
?>