<?php
	
	//session_start();

	//Redirects to Login page if someone tries to access some page without loggin in
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

	$result = ''; 
	$no_of_lines = 0;

	$qry = "select count(*) as Count from cancer_mutation_repository"; // echo $qry;
	$result = mysqli_fetch_array(mysqli_query($cn, $qry));
	$no_of_lines = $result['Count'];
	mysqli_close($cn);

?>	