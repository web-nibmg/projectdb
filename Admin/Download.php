<?php
	//Checks whether the user is logged in or not...
	session_start();
	if($_SESSION['id'] == ''){
		header('Location: '.$_SESSION['login'].'?flag=3');
		exit();
	}
	//Login Check ends...

	//Adding Connection String
	if(!file_exists($_SESSION['path'].$_SESSION['connection'])){
		header('Location: '.$_SESSION['login'].'?flag=6');
		exit();
	}
	require($_SESSION['path'].$_SESSION['connection']);
	//Adding Connection String ends
	
	//Checks whether the required values are null or not(server-side checking)...
	if(!isset($_GET['file_extension'])){
		header('Location: '. $_SESSION['home'] .'?flag=20');
		exit();
	}
	//null check ends...

	//receives inputs from the "File_Adder_Admin.php" page...
	$file_extension = filter_var($_GET['file_extension'], FILTER_SANITIZE_STRING,
           FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
	$file_first_name = 'Repository';
	
	$qry = '';

	//Absolute location of the Folder where the file will be saved...
	$download_file_path = '/opt/lampp/htdocs/Data_Portal/Backend/File_Store/downloads/';
	
	//Relative location w.r.t. /opt/lampp/mysql/projectdb of the Folder where the file will be saved...
	$mysql_path = '../../htdocs/Data_Portal/Backend/File_Store/downloads/';

	$file_full_path = '';
	$mysql_full_path = '';

	$file_full_path = $download_file_path . $_SESSION['id'] . '_' . $file_first_name . '.' . $file_extension;
	$mysql_full_path = $mysql_path . $_SESSION['id'] . '_' . $file_first_name . '.' . $file_extension;
	
	$qry = "select * into outfile '$mysql_full_path' fields terminated by '\t' lines terminated by '\n' from cancer_mutation_repository";
	$result = '';
	$table_header = '';

	//Removes any file that has been created in the server previously by the same user...
	if(file_exists($file_full_path)){
		unlink($file_full_path);
	}

	if(!mysqli_query($cn, $qry)){
		mysqli_close($cn);
		exit();
	}

	$result_inter = array();
	$fieldName = array();

	$qry = "select column_name from information_schema.columns where table_name = 'cancer_mutation_repository'";
	$result = mysqli_query($cn, $qry);
	
	while($result_inter = mysqli_fetch_assoc($result)){
		$fieldName[] = $result_inter['column_name'];
	}

	$table_header = implode("\t", $fieldName);

	//Calling the download() method to begin the download procedure...
	download($file_first_name, $file_extension, $file_full_path, $table_header);
	
	//Body of the download() method..
	function download($first_name, $extenstion, $file_full_path, $table_header){
		
		//Beginning download procedure...
		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			
		//For "txt" files...
		if($extenstion == 'txt'){
		    header('Content-Type: text/plain');
		    header("Content-Disposition: attachment; filename=\"$first_name.$extenstion\"");
		    header('Content-Length: '.filesize($file_full_path));
		    ob_clean();
		    flush();
		    $file_read_stream = fopen($file_full_path, "r") or die('Location: '.$_SESSION['home'].'?flag=15');
		    echo $table_header."\n";
			while (($line = fgets($file_read_stream))){
				$line = remove_newline($line);
				echo $line."\n";
			}
		    fclose($file_read_stream);
		    unlink($file_full_path);
		    exit();
		}

		//For "csv" files...
		elseif($extenstion == 'csv'){
			header('Content-Type: application/csv');
		    header("Content-Disposition: attachment; filename=\"$first_name.$extenstion\"");
		    header('Content-Length: '.filesize($file_full_path));
		    ob_clean();
		    flush();
		    $file_read_stream = fopen($file_full_path, "r") or die('Location: '.$_SESSION['home'].'?flag=15');
			//echo $table_header."\n";
			while (($line = fgets($file_read_stream))){
				echo $line;
			}
		    fclose($file_read_stream);
		    unlink($file_full_path);
		    exit();
		}

		//For "doc" files...
/*		elseif($extenstion == 'doc'){
			header('Content-Type: application/msword');
		    header("Content-Disposition: attachment; filename=\"$first_name.$extenstion\"");
		    header('Content-Length: '.filesize($file_full_path));
		    ob_clean();
		    flush();
		    $file_read_stream = fopen($file_full_path, "r") or die('Location: '.$_SESSION['home'].'?flag=15');
			echo $table_header."\n";
			while (($line = fgets($file_read_stream))){
				echo $line;
			}
			fclose($file_read_stream);
			unlink($file_full_path);
		    exit();
		}		
*/
		//Download procedure Ends...
		
		//Redirects to the home page if Repository doesn't exist...
		else{
			header('Location: '.$_SESSION['home'].'?flag=16');
			exit();
		}
	}

	function remove_newline($str){
		$words = explode("\t", $str);
		for($i = 0; $i < sizeof($words); $i++){
			if($words[$i] == "\N\n"){
				$words[$i] = "-";
			}
			elseif($words[$i] == "\N"){
				$words[$i] = "-";
			}
			else{}
		}
		return implode("\t", $words);
	}
	//End of download() method...
?>