<?php
	
	//receives inputs from the "File_Adder_Admin.php" page...
	$file_first_name = 'drc1@nibmg.ac.in_Repository';
	$file_extenstion = 'txt';
	//end of input...

	$qry = '';

	//Location to the Repository...
	//$download_file_path = '/opt/lampp/htdocs/App_Store/downloads/';
	$download_file_path = '/opt/lampp/htdocs/App_Store/';
	//$mysql_path = '/opt/lampp/htdocs/App_Store/';
	$mysql_path = '../../htdocs/App_Store/';

	$file_full_path = '';
	$mysql_full_path = '';


	$file_full_path = $download_file_path . $_SESSION['id'] . '_' . $file_first_name . '.' . $file_extenstion;
	$mysql_full_path = $mysql_path . $_SESSION['id'] . '_' . $file_first_name . '.' . $file_extenstion;
	
	$qry = "select * into outfile '$mysql_full_path' fields terminated by '\t' lines terminated by '\n' from cancer_mutation_repository";


	//Calling the download() method to begin the download procedure...
	download($download_file_path, $file_first_name, $file_extenstion, $file_full_path);
	
	//Body of the download() method..
	function download($file_path, $first_name, $extenstion, $file_full_path){
		
		echo "<script language='javascript' type='text/javascript'>
				alert('within the function download...');
			</script>";
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
		    readfile($file_full_path) or die('Location: '.$_SESSION['home'].'?Unable to read');
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
			while (($line = fgets($file_read_stream))){
				echo $line;
			}
		    fclose($file_read_stream);
		    exit();
		}

		//For "doc" files...
		elseif($extenstion == 'doc'){
			header('Content-Type: application/msword');
		    header("Content-Disposition: attachment; filename=\"$first_name.$extenstion\"");
		    header('Content-Length: '.filesize($file_full_path));
		    ob_clean();
		    flush();
		    $file_read_stream = fopen($file_full_path, "r") or die('Location: '.$_SESSION['home'].'?flag=15');
			while (($line = fgets($file_read_stream))){
				echo $line;
			}
			fclose($file_read_stream);
		    exit();
		}

		//Redirects to the home page if Repository doesn't exist...
		else{
			header('Location: '.$_SESSION['home'].'?flag=16');
			exit();
		}
	}
	//End of download() method...
?>