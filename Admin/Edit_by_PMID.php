<?php

	$edit_repository_path = '/Data_Portal/Backend/Admin/Edit_Repository.php';
	// Protection from illegal usage starts
	session_start();
	if($_SESSION['id'] == ''){
		header('Location: '.$_SESSION['login'].'?flag=3');
		exit();
	}
	// Protection from illegal usage ends

	//Checks whether the request_type is set or not(server-side checking)...
	if(!isset($_POST['all_request_action'])){
		header("Location: /Data_Portal/Backend/Admin/$edit_repository_path?flag=1");
		exit();
	}
	//request_type set checking ends...

	//Checks whether the PMID is set or not(server-side checking)...
	if(!isset($_POST['all_request_PMID'])){
		header("Location: /Data_Portal/Backend/Admin/$edit_repository_path?flag=2");
		exit();
	}
	//PMID set checking ends...

	//Receiving variables passed from the calling page...
	$action = filter_var(strip_tags(substr($_POST['all_request_action'],0, 10)), FILTER_SANITIZE_STRING);
	$PMID = mysql_real_escape_string(filter_var(strip_tags(substr($_POST['all_request_PMID'],0, 15)), FILTER_SANITIZE_STRING));
	//End of reception...
	$search_result = '';
	$repository_path = '../App_Store/uploads/Repository.txt';
	$new_path = '../App_Store/uploads/Repository_'.substr($_SESSION['id'],0,2).'.txt';

	
	//Executing search action...
	if($action == 'search'){
		if(!$PMID){
			$search_result = 'PMID_null';
		}

		else{
			if(search_function($PMID, $repository_path)){
				$search_result = $PMID;
			}
			else{
				$search_result = 'not_found';
			}
		}
	}
	//End of search...

	//Executing edit action...
	elseif($action == 'edit'){
		//Checks whether the old_PMID is set or not(server-side checking)...
		if(!isset($_POST['all_request_old_PMID'])){
			header("Location: $edit_repository_path?flag=3");
			exit();
		}
		//old_PMID set checking ends...

		else{
			$old_PMID = mysql_real_escape_string(filter_var(strip_tags(substr($_POST['all_request_old_PMID'],0, 15)), FILTER_SANITIZE_STRING));
			if(edit_function($PMID, $old_PMID, $repository_path, $new_path)){
				header("Location: $edit_repository_path?flag=5");
				exit();
			}
		}
	}
	//Edit Ends...

	//Executing delete action...
	elseif($action == 'delete'){
		if(delete_function($PMID, $repository_path, $new_path)){
			header("Location: $edit_repository_path?flag=21");
			exit();
		}
	}
	//Delete Ends...

	//	================================================================================================
	//Start of search_function()
	function search_function($PMID, $repository_path){
		//Opens the source file in read mode if file exists else redirects to Home page...
		if(file_exists($repository_path) && (0 != filesize($repository_path))){
			$repository_stream = fopen($repository_path, 'r') or header('Location: '.$_SESSION['home'].'?flag=4');
			
			$line = '';
			$field = '';
			while(($line = fgets($repository_stream)) != false){
			    $field = explode("\t", $line);
				if($field[0] == $PMID){
					fclose($repository_stream);
					return true;
				}
			}
			fclose($repository_stream);
			return false;
		}
		else{
			header('Location: '.$_SESSION['home'].'?flag=repositorydoesnotexist');
		}
	}
	//End of search_function()
	//	================================================================================================


	//	================================================================================================
	//Start of edit_function()
	function edit_function($PMID, $old_PMID, $repository_path, $new_path){
		//Opens the source file in read mode if file exists else redirects to Home page...
		if(file_exists($repository_path) && (0 != filesize($repository_path))){
			//opens Repository in read mode...
			$repository_stream = fopen($repository_path, 'r') or header("Location: $edit_repository_path?flag=6");
			//Gets an exclusive lock on the repository...
			if(flock($repository_stream,LOCK_EX)){
				//Opens the dump file in write mode...
				$new_stream = fopen($new_path, 'w') or header("Location: $edit_repository_path?flag=7");
				
				$line = '';
				$field = '';
				while(($line = fgets($repository_stream)) != false){
				    $field = explode("\t", $line);
					if($field[0] == $old_PMID){
						$field[0] = $PMID;
						$line = '';
						for($i=0; $i<sizeof($field)-1; $i++){
							$line .= $field[$i] . "\t";
						}
						$line .= $field[$i];	//removes the tab at the end of the line...
					}
					fwrite($new_stream, $line);
					//writes data to the file...
				}
				fclose($repository_stream);
				fclose($new_stream);
				//Creates a back-up copy of the the repository before removing it...
				$file_name = explode(".txt", $repository_path);
				$backup_path = $file_name[0] . '_backup.txt';
				$repository_stream = fopen($repository_path, 'r') or header("Location: $edit_repository_path?flag=6");
				if(flock($repository_stream,LOCK_EX)){
					if(!copy($repository_path, $backup_path)){
						fclose($repository_stream);
						header("Location: $edit_repository_path?flag=8");
						exit();
					}
					if(!rename($new_path, $repository_path)){
						rename($backup_path, $repository_path);
						fclose($repository_stream);
						header("Location: $edit_repository_path?flag=9");
						exit();
					}
					fclose($repository_stream);
					unlink($backup_path);
					
					//Adding Connection String
					if(!file_exists($_SESSION['connection'])){
						header('Location: '.$_SESSION['home'].'?flag=4');
						exit();
					}
					require($_SESSION['connection']);
					//Adding Connection String ends
					
					$qry = "insert into edit_history values('".$_SESSION['id']."', 'edit', '$old_PMID', '$PMID', now())";
					if(!mysqli_query($cn, $qry)){
						mysqli_close($cn);
						header("Location: $edit_repository_path?flag=12");
						exit();
					}
					else{
						mysqli_close($cn);
						return true;
					}
				}
				else{
					header("Location: $edit_repository_path?flag=10");
					exit();
				}
			}
			else{
				header("Location: $edit_repository_path?flag=10");
				exit();
			}
		}
		else{
			header("Location: $edit_repository_path?flag=11");
			exit();
		}
	}
	//End of edit_function()
	//	================================================================================================

	
	//	================================================================================================
	//Start of delete_function()
	function delete_function($PMID, $repository_path, $new_path){
		//Opens the source file in read mode if file exists else redirects to Home page...
		if(file_exists($repository_path) && (0 != filesize($repository_path))){
			//opens Repository in read mode...
			$repository_stream = fopen($repository_path, 'r') or header("Location: $edit_repository_path?flag=14");
			//Gets an exclusive lock on the repository...
			if(flock($repository_stream,LOCK_EX)){
				//Opens the dump file in write mode...
				$new_stream = fopen($new_path, 'w') or header("Location: $edit_repository_path?flag=15");
				
				$line = '';
				$field = '';
				while(($line = fgets($repository_stream)) != false){
				    $field = explode("\t", $line);
					if($field[0] == $PMID){
						continue;
					}
					fwrite($new_stream, $line);
					//writes data to the file...
				}
				fclose($repository_stream);
				fclose($new_stream);
				//Creates a back-up copy of the the repository before removing it...
				$file_name = explode(".txt", $repository_path);
				$backup_path = $file_name[0] . '_backup.txt';
				$backup_path = $file_name[0] . '_backup'. $file_name[1];
				$repository_stream = fopen($repository_path, 'r') or header("Location: $edit_repository_path?flag=5");
				if(flock($repository_stream,LOCK_EX)){
					if(!copy($repository_path, $backup_path)){
						flock($repository_stream,LOCK_UN);
						header("Location: $edit_repository_path?flag=16");
						exit();
					}
					if(!rename($new_path, $repository_path)){
						rename($backup_path, $repository_path);
						flock($repository_stream,LOCK_UN);
						header("Location: $edit_repository_path?flag=17");
					}
					flock($repository_stream,LOCK_UN);
					unlink($backup_path);

					//Adding Connection String
					if(!file_exists($_SESSION['connection'])){
						header('Location: '.$_SESSION['home'].'?flag=4');
						exit();
					}
					require($_SESSION['connection']);
					//Adding Connection String ends
					
					$qry = "insert into edit_history values('".$_SESSION['id']."', 'delete', '$PMID', null, now())";
					if(!mysqli_query($cn, $qry)){
						mysqli_close($cn);
						header("Location: $edit_repository_path?flag=20");
						exit();
					}
					else{
						mysqli_close($cn);
						return true;
					}
				}
				else{
					header("Location: $edit_repository_path?flag=18");
					exit();
				}
			}
			else{
				header("Location: $edit_repository_path?flag=18");
				exit();
			}
		}
		else{
			header("Location: $edit_repository_path?flag=19");
			exit();
		}
	}
	//End of delete_function()
	//	================================================================================================

?>

<html>
	<!--  -----------------------All Request Submit Table Starts---------------------- -->
	<form action='../App_Files/Edit_Repository.php' method='post' id='PMID_submit_form'>
		<input type='hidden' name='PMID' id='PMID' value='<?php echo $search_result; ?>' />
	</form>
	<!--  -----------------------All Request Submit Table Ends---------------------- -->
</html>

<script language='javascript' type='text/javascript'>
	document.getElementById('PMID_submit_form').submit();
</script>