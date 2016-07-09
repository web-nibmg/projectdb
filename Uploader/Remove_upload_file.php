<?php

	session_start();

	//Redirects to Login page if someone tries to access some page without loggin in...
	if($_SESSION['id'] == ''){
		header('Location: '.$_SESSION['home'].'?flag=3');
		exit();
	}
	//Redirection Ends...

	//Adding Connection String
	if(!file_exists($_SESSION['path'].$_SESSION['connection'])){
		header('Location: '.$_SESSION['home'].'?flag=4');
		exit();
	}
	require($_SESSION['path'].$_SESSION['connection']);
	//Adding Connection String ends

	//Removes the uploaded file and resets session variable...
	if(($_SESSION['uploaded_file'] != '') && file_exists($_SESSION['path'].$_SESSION['uploaded_file'])){
		unlink($_SESSION['uploaded_file']);	//removes the file from the system
		$_SESSION['uploaded_file'] = '';
	}
	//Removal ends...

	$qry = '';
	$result = '';

	$qry = "delete from dummy_cancer_mutation where User_Id='".$_SESSION['id']."'"; //echo $qry;
	$result = mysqli_query($cn, $qry);
	mysqli_close($cn);

	header('Location: '.$_SESSION['home'].'?flag=16');
	exit();

?>