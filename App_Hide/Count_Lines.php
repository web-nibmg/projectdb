<?php
	
	//session_start();

	//Redirects to Login page if someone tries to access some page without loggin in
	if($_SESSION['id'] == ''){
		header('Location: '.$_SESSION['home'].'?flag=3');
		exit();
	}
	//Redirection Ends...

	//Adding Connection String
	if(!file_exists($_SESSION['connection'])){
		header('Location: '.$_SESSION['home'].'?flag=4');
		exit();
	}
	require($_SESSION['connection']);
	//Adding Connection String ends

	$result = ''; 
	$no_of_lines = 0;

	$qry = "select count(*) as Count from cancer_mutation_repository"; // echo $qry;
	$result = mysqli_fetch_array(mysqli_query($cn, $qry));
	$no_of_lines = $result['Count'];
	mysqli_close($cn);

	//$repository = '/opt/lampp/htdocs/App_Store/uploads/Repository.txt';
	//$no_of_lines = 0;

	//Either opens the repository in read mode or redirects to the home page with an error message...
	//$repository_read_stream = fopen($repository, 'r') or header('Location: '.$_SESSION['home'].'?flag=11');
	//fgets($repository_read_stream);	//Skips the 1st line of the repository which contains the headers

	//Counts the number of lines in the repository...
	//while(fgets($repository_read_stream)){
	    //$no_of_lines++;
	//}
	//fclose($repository_read_stream);
    //Counting Ends...
?>	