<?php
$uploadPath = "./upload/";
if($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
	if(move_uploaded_file($_FILES["file"]["tmp_name"],$uploadPath.$_FILES["file"]["name"])) {
		echo "success<br>"
			.$_FILES["file"]["type"].'<br>'
			.($_FILES["file"]["size"]/1024).'kB';
	} else {
		echo "fail";
	}
} else {
	echo "fail".$_FILES["myFileID"]["error"];
}
?>