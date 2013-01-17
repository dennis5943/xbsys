<?php
//session_start();
//if($_SESSION['userId'] == null) { return false; }
//
//require_once("inc/db.inc");
//require_once("inc/commonFunction.inc");
//$request_by_ajax = $_GET['request_by_ajax'];
//$loader = $_GET['loader'];
//$req_type = $_GET['req_type'];
//if($request_by_ajax != 1) {
//	$ary_js = array('commonFunction','msg');
//	pageStart($ary_js);
	main();
//	pageEnd();
//} else {
//    if($loader == 1) {
//        $content = loader_msg($req_type);
//        echo $content;
//        return;
//    } 
//}

function main() {
	$str = "
<html>
<body>

<form action='upload.php' method='post' enctype='multipart/form-data'>
檔案名稱:<input type='file' name='file' id='file' /><br />
<input type='submit' name='submit' value='上傳檔案' />
</form>

</body>
</html>";
	
	echo $str;
}

//function loader_msg($reqType) {
//	switch($reqType) {
//		case 'updateAvatar':
//			$userName = $_GET['userName'];
//			return updateAvatar($userName);
//			break;
//		case 'onclkSubmitMsg':
//			$txt_myMsg = $_GET['txt_myMsg'];
//			onclkSubmitMsg($txt_myMsg);
//			return getAllMsg();
//			break;
//		case 'onclkDeleteMsg':
//			$msgId = $_GET['msgId'];
//			onclkDeleteMsg($msgId);
//			return getAllMsg();
//			break;
//		default:
//			break;
//	}
//}


?>