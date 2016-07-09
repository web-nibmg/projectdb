<?php
	session_start();

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
	
	//Checks whether the required values are null or not(server-side checking)...
	if(!isset($_POST['old_password']) || !isset($_POST['new_password']) || !isset($_POST['confirm_new_password'])){
		header('Location: /Data_Portal/Backend/Uploader/Change_Password.php?flag=2');
		exit();
	}
	//null check ends...

	//receives inputs from the "Change_Password.php" page...
	$old_password = htmlspecialchars($_POST['old_password']);
	$new_password = htmlspecialchars($_POST['new_password']);
	$confirm_new_password = htmlspecialchars($_POST['confirm_new_password']);
	//end of input...

	//query to collect user information using the session id...
	$qry = "select user_name, pswd, stat, type from users where email = '".$_SESSION['id']."'";
					
	$result = '';
	$status = '2';
	//stores the result of the query...
	$result = mysqli_fetch_array(mysqli_query($cn, $qry));
	$str = $result['pswd'];

	//Redirects to Login page in case of password mismatch
	if($old_password != $str){ 
		header('Location: /Data_Portal/Backend/Uploader/Change_Password.php?flag=1');
		mysqli_close($cn);
		exit();
	}
	//end of password check...

	//checks whether new passwords are matching...
	if($new_password != $confirm_new_password){ 
		header('Location: /Data_Portal/Backend/Uploader/Change_Password.php?flag=2');
		mysqli_close($cn);
		exit();
	}
	//end of new password check...

	//query to update password...
	$qry = "update users set pswd = '".$new_password."' where email = '".$_SESSION['id']."'";
	
	//redirects to Home page if the change is successful...
	if(mysqli_query($cn, $qry)){
		mysqli_close($cn);
		header('Location: '.$_SESSION['home'].'?flag=7');
		exit();
	}

	//redirects to "Change_Password.php" if the query is failed to execute...
	else{
		mysqli_close($cn);
		header('Location: /Data_Portal/Backend/Uploader/Change_Password.php?flag=4');
		exit();
	}

?>