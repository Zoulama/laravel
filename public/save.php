<?php
	$uploaddir='uploads/';
	// récupère l'URL de l'envoit de photos
	$uploadfile=$_GET['n'];
	 
	if (is_uploaded_file($_FILES['photo']['tmp_name']))	{
		move_uploaded_file($_FILES['photo']['tmp_name'],$uploaddir,$uploadfile);
	}
?>
