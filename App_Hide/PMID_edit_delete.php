<?php
	
	//Checks whether the user is logged in or not...
	session_start();
	if($_SESSION['id'] == ''){
		header('Location: '.$_SESSION['login'].'?flag=3');
		exit();
	}
	//Login Check ends...

	//Adding Connection String
	if(!file_exists($_SESSION['connection'])){
		header('Location: '.$_SESSION['login'].'?flag=6');
		exit();
	}
	require($_SESSION['connection']);
	//Adding Connection String ends
	
	//Checks whether the required values are null or not(server-side checking)...
	if(!isset($_POST['request_type']) || !isset($_POST['PMID'])){
		header('Location: /App_Files/File_Adder_Admin.php?flag=20');
		exit();
	}
	//null check ends...

	//receives inputs from the "File_Adder_Admin.php" page...
	$request_type = htmlspecialchars($_POST['request_type']);
	$PMID = htmlspecialchars($_POST['PMID']);
	//end of input...

	$qry = '';
	
	//Setting up the flag...
	$flag_value = '';
	if($request_type == 'edit'){
		$flag_value =	11;
	}
	elseif($request_type == 'delete'){
		$flag_value =	12;
	}
	//Flag setting ends...

	//Editing the existing users...
	if($request_type == 'edit'){
		if(!isset($_POST['PMID_final'])){
			echo "<script language='javascript' type='text/javascript'>
					alert('PMID is not set.\nEdit Aborted...');
				</script>";
			//header('Location: '.$_SESSION['home'].'?flag=1');
			exit();
		}

		$PMID_final = htmlspecialchars($_POST['PMID_final']);
		$qry = "update cancer_mutation_repository set PMID = '$PMID_final' where PMID = '$PMID'";
	}
	//Editing ends...

	//Deletion of users...
	else if($request_type == 'delete'){
		$qry = "delete from cancer_mutation_repository where PMID = '$PMID'";
	}
	//Deletion ends...

	//Default: Redirection to the Home page...
	else{
		header('Location: '.$_SESSION['home'].'?flag=2');
	}
	//End of default...

	//echo $qry;

	

	//Execution of the query...
	if(mysqli_query($cn, $qry)){
		mysqli_close($cn);
		if($request_type != 'edit'){
			$PMID_final = '';
		}
		//send_infomail($request_type, $email, $PMID_final, $PMID, $subject, $body, $flag_value);
		//header('Location: '.$_SESSION['home'].'?flag='.$flag_value);
		echo "<script language='javascript' type='text/javascript'>
				alert('Operation has been Successful.\nGetting back to source page...');
			</script>";
		exit();
	}
	//Execution ends...

	//Failure will lead to the Home page...
	else{
		mysqli_close($cn);
		//header('Location: '.$_SESSION['home'].'?flag=4');
		echo "<script language='javascript' type='text/javascript'>
				alert('Operation has been Failed.\nGetting back to source page...');
			</script>";
		exit();
	}
	//Failure ends...

?>