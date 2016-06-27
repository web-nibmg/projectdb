<?php
	
	session_start();

	//Redirects to Login page if someone tries to access some page without loggin in
	if($_SESSION['id'] == ''){
		header('Location: '.$_SESSION['home'].'?flag=3');
		exit();
	}
	//Redirection Ends...

	//Checks whether the source file is uploaded or not...
	$uploaded_file = '';
	if(isset($_POST['target'])){
		$uploaded_file = $_POST['target'];
	}
	else{
		header('Location: '.$_SESSION['home'].'?flag=12');
		exit();
	}

	//Checks whether the source file-name is passed or not...
	if(!isset($_POST['input_file'])){
		header('Location: '.$_SESSION['home'].'?flag=17');
		exit();
	}

	//Adding Connection String
	if(!file_exists($_SESSION['connection'])){
		header('Location: '.$_SESSION['home'].'?flag=4');
		exit();
	}
	require($_SESSION['connection']);
	//Adding Connection String ends

	// Variable declaration...
	$field = '';
	$line = '';
	$new_line = '';
	$upload_file_header = '';
	$destination_header = '';
	$limit = 0;	// Number of columns to be added
	$new_line = '';
	
	//==================== Common part between column_check() & header_check() starts ====================
	//Opens the source file in read mode if file exists else redirects to Home page...
	$uploaded_stream = fopen($uploaded_file, 'r') or header('Location: '.$_SESSION['home'].'?flag=12');
	
	// Collects the header information from the source file into the upload_file_header array...
	$line = fgets($uploaded_stream);
	$new_line = $line;
	$upload_file_header = explode("\t", $new_line);
	fclose($uploaded_stream);

	//Opens the destination file(repository) in read mode, if file exists else redirects to Home page...
	$qry = "SELECT column_name FROM information_schema.columns WHERE table_name = 'users' and table_schema = 'projectdb' ORDER  BY ordinal_position";
	if(($result = mysqli_query($cn, $qry)) != ''){
		$destination_header = mysqli_fetch_array($result);
	}
	else{
		header('Location: '.$_SESSION['home'].'?flag=25');
		exit();
	}

	$limit = sizeof($destination_header);	//sets the value of limit to the number of columns in the repository...

	//==================== Common part between column_check() & header_check() ends ====================

	
	//==================== Method calls start ====================
	
	header_check($limit, $uploaded_file, $upload_file_header, $destination_header);
	$no_of_lines_added = blanks_check($limit, $uploaded_file);
	unique_lines($uploaded_file);
	unique_PMID($uploaded_file, $destinaion_file);
	apppend_file($uploaded_file, $destinaion_file, $no_of_lines_added, $limit);
	
	//==================== Method calls end ====================


	//Start of all the method-bodies used in this program...

	//====================================================================================
	//Start of header_check():: Checks whether the file has the same columns as the repository...
	function header_check($limit, $uploaded_file, $upload_file_header, $destination_header){
		if(sizeof($upload_file_header) < $limit - 1){
			unlink($uploaded_file);
			header('Location: '.$_SESSION['home'].'?flag=14');
			exit();
		}
		else {}

		for($i=0; $i<$limit-1; $i++){
			if($upload_file_header[$i] == ''){
				unlink($uploaded_file);
				header('Location: '.$_SESSION['home'].'?flag=19');
				exit();
			}

			elseif(trim($upload_file_header[$i]) != trim($destination_header[$i])){
				unlink($uploaded_file);
				header('Location: '.$_SESSION['home'].'?flag=13');
				exit();
			}
			else{};
		}
		return;
	}
	//header_check() ends...
	//====================================================================================
	

	//====================================================================================
	//Start of blanks_check():: Checks whether the file contains null values except the last column...
	function blanks_check($limit, $uploaded_file){
		$field = [];
		$line = '';
		$no_of_lines_added = 0;
		//Opens the source file in read mode if file exists else redirects to Home page...
		$uploaded_stream = fopen($uploaded_file, 'r') or header('Location: '.$_SESSION['home'].'?flag=12');
		fgets($uploaded_stream);	//Skips the first line that contains the headers...

		$line_no = 2;
		while(($line = fgets($uploaded_stream)) != false){
			$field = explode("\t", $line);
			if(sizeof($field) > $limit){
				fclose($uploaded_stream);
				unlink($uploaded_file);
				header('Location: '.$_SESSION['home'].'?flag=22+'.$line_no);
				exit();
			}
			for($i=0; $i<($limit-1); $i++){
				if(trim($field[$i]) == ''){
					fclose($uploaded_stream);
					unlink($uploaded_file);
					header('Location: '.$_SESSION['home'].'?flag=2+'.$line_no);
					exit();
				}
			}
			//following section checks if there is any character present in the Patient_Id
			//other than alphanumeric or null...
			if(sizeof($field) == $limit){
				if((preg_match('/[^A-Za-z0-9]/', trim($field[$i])) == 1) && (trim($field[$i]) != '')){
					fclose($uploaded_stream);
					unlink($uploaded_file);
					header('Location: '.$_SESSION['home'].'?flag=23+'.$line_no);
					exit();
				}
			}
			$no_of_lines_added++;	//Counts the number of lines in the uploaded file...
			unset($field);
			$line_no++;
		}
		fclose($uploaded_stream);
		return $no_of_lines_added;
	}
	//blanks_check() ends...
	//====================================================================================
	
	
	//====================================================================================
	//Start of unique_lines():: Checks whether the file contains any duplicate lines...
	function unique_lines($uploaded_file){
		$field = [];
		$line = '';
		$uploaded_stream = fopen($uploaded_file, 'r') or header('Location: '.$_SESSION['home'].'?flag=12');

		$line_no = 1;
		while(($line = fgets($uploaded_stream)) != false){
			$position = ftell($uploaded_stream);
			$comparing_line_no = $line_no + 1;
			while(($line2 = fgets($uploaded_stream)) != false){
				if($line == $line2){
					fclose($uploaded_stream);
					unlink($uploaded_file);
					header('Location: '.$_SESSION['home'].'?flag=21+'.$line_no.'+'.$comparing_line_no);
					exit();
				}
				$comparing_line_no++;
			}
			fseek($uploaded_stream, $position);
			$line_no++;
		}
		fclose($uploaded_stream);
		return;
	}
	//unique_lines() ends...
	//====================================================================================
	

	//====================================================================================
	//Start of unique_PMID():: Checks whether the file has specified number of columns...
	function unique_PMID($uploaded_file, $destinaion_file){
		$field = [];
		$line = '';
		$uploaded_stream = fopen($uploaded_file, 'r') or header('Location: '.$_SESSION['home'].'?flag=12');
		
		fgets($uploaded_stream); //skips 1st line that contains the headers...

		$PMID_pool = [];
		while(($line = fgets($uploaded_stream)) != false){
			$field = explode("\t", $line);
			if(!in_array($field[0], $PMID_pool)){ 
				array_push($PMID_pool, $field[0]);
			}
			unset($field);
		}

		//echo '<br />';
		//print_r($PMID_pool);

		//Opens the destination(repository) in read mode or redirects to the home page...
		if(file_exists($destinaion_file) && (0 != filesize($destinaion_file))){
			$destination_stream = fopen($destinaion_file, 'r') or header('Location: '.$_SESSION['home'].'?flag=11');
			//Redirects to Home Page if fails to get an exclusive lock on the repository...
			if(!flock($destination_stream,LOCK_SH)){
				fclose($uploaded_stream);
				fclose($destination_stream);
				unlink($uploaded_file);
				header('Location: '.$_SESSION['home'].'?flag=24');
				exit();
			}
			fgets($uploaded_stream);	// skips first line of source file if repository already exists...
			while(($line = fgets($destination_stream)) != false){
				$field = explode("\t", $line);
				if(in_array($field[0], $PMID_pool)){ 
					fclose($uploaded_stream);
					fclose($destination_stream);
					unlink($uploaded_file);
					header('Location: '.$_SESSION['home'].'?flag=18');
					exit();
				}
				unset($field);
			}
			fclose($uploaded_stream);
			fclose($destination_stream);
		}
	}
	//unique_PMID() ends...
	//====================================================================================
	

	//====================================================================================
	//Start of apppend_file():: Checks whether the file has specified number of columns...
	function apppend_file($uploaded_file, $destinaion_file, $no_of_lines_added, $limit){
		$field = [];
		$line = '';
		//Opens the source file in read mode, if file exists else redirects to Home page...
		$uploaded_stream = fopen($uploaded_file, 'r') or header('Location: '.$_SESSION['home'].'?flag=12');

		//Opens the destination file in append mode, if file exists else redirects to Home page...
		if(file_exists($destinaion_file) && (0 != filesize($destinaion_file))){
			$destination_stream = fopen($destinaion_file, 'a') or header('Location: '.$_SESSION['home'].'?flag=11');
			//Redirects to Home Page if fails to get an exclusive lock on the repository...
			if(!flock($destination_stream,LOCK_EX)){
				fclose($uploaded_stream);
				fclose($destination_stream);
				unlink($uploaded_file);
				header('Location: '.$_SESSION['home'].'?flag=24');
				exit();
			}
			fgets($uploaded_stream);	// skips first line of source file if repository already exists...
			//fwrite($destination_stream, "\n");	// puts a newline in if the repository already exists...
		}

		//Opens the destination file in write mode if the file doesn't exists...
		else{
			$destination_stream = fopen($destinaion_file, 'w') or header('Location: '.$_SESSION['home'].'?flag=11');
			//Redirects to Home Page if fails to get an exclusive lock on the repository...
			if(!flock($destination_stream,LOCK_EX)){
				fclose($uploaded_stream);
				fclose($destination_stream);
				unlink($uploaded_file);
				header('Location: '.$_SESSION['home'].'?flag=24');
				exit();
			}
			//Inserts the header line in the destination file...
			fwrite($destination_stream, trim(fgets($uploaded_stream)));
		}

		//Adds lines from source file to the destination files...
		//There will be an extra blnak line at the end of the file which is not the part of the source file...
		while(($line = fgets($uploaded_stream)) != false){
			$field = explode("\t", $line);
			$str = "\n";
			for($i=0; $i<$limit-1; $i++){
				$str .= trim($field[$i]) . "\t";
			}
			if((sizeof($field) == $limit) && (trim($field[$limit-1]) != '')){
				//array_push($arr, $field[$limit-1]);
				$str .= trim($field[$limit-1]);
			}
			else{
				$str .= '-';
				//array_push($arr, '*');
			}
			//$str .= trim($field[$i])."\n";
			fwrite($destination_stream, $str);
			//file_put_contents($destination_stream, $str//);
		}

		//Closure of the opened files...
		fclose($destination_stream);
		fclose($uploaded_stream);

		//Deletion of the uploaded source file...
		unlink($uploaded_file);
		
		//Adding Connection String
		if(!file_exists($_SESSION['connection'])){
			header('Location: '.$_SESSION['home'].'?flag=4');
			exit();
		}
		require($_SESSION['connection']);
		//Adding Connection String ends
		
		//Insertion of record in upload_history database...
		//Insertion qury...
		$qry = "insert into upload_history values('".$_SESSION['id']."','".$_POST['input_file']."',now())";
		
		if(mysqli_query($cn, $qry)){
			//Inserts the number of uploads into the variable no_of_uploads...
			$qry = "select count(*) as no_of_uploads from upload_history where email = '".$_SESSION['id']."'";
			$result = mysqli_fetch_array(mysqli_query($cn, $qry)); print_r($result['no_of_uploads']);
			//If number of uploads is greater than 10, deletes the extra records from the upload_history...
			if($result['no_of_uploads'] > 10){
				$qry = "delete from upload_history where email = '".$_SESSION['id']."' order by upload_date limit ".($result['no_of_uploads'] - 10);
				mysqli_query($cn, $qry);
			}
			mysqli_close($cn);
			$_SESSION['no_of_lines'] += $no_of_lines_added;
			header('Location: ../App_Files/File_Adder.php?flag='.$no_of_lines_added);
			exit();
		}
		//If insertion fails, closes mysql connection and redirects to Home page...
		else{
			mysqli_close($cn);
			header('Location: '.$_SESSION['home'].'?flag=4');
			exit();
		}
	}
	//apppend_file() ends...
	//====================================================================================
	
	//End of the method-bodies...
?>