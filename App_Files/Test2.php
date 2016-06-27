<?php
	$repository_path = '../App_Store/Repository_backup.txt';
	$file_name = explode(".txt", $repository_path);
	echo $file_name[0].'<br />';
	echo $file_name[1].'<br />';
	$backup_path = $file_name[0] . '_backup.txt';
	echo $backup_path;

	$repository_stream = fopen($repository_path, 'r');
	$line = fgets($repository_stream); //skips the header line...

	/*
	while(($line = fgets($repository_stream)) != false){
	    $field = explode("\t", $line);
	    if($field[0] == $old_PMID){
			continue;
		}
		echo "<br />".$line;
	}*/

	/*
	while(($line = fgets($repository_stream)) != false){
	    $position = ftell($repository_stream);
	    while(($line2 = fgets($repository_stream)) != false){
		    if($line == $line2){
				echo "<br />Duplicate::		".$line;
			}
		}
		fseek($repository_stream, $position);
	}

	fclose($repository_stream);
	*/

	/*$uploaded_file = '../App_Store/Repository_backup.txt';
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

	echo '<br />';
	print_r($PMID_pool);

	//Opens the destination(repository) in read mode or redirects to the home page...
	if(file_exists($destinaion) && (0 != filesize($destinaion))){
		$destination_stream = fopen($destinaion, 'r') or header('Location: '.$_SESSION['home'].'?flag=11');
		fgets($uploaded_stream);	// skips first line of source file if repository already exists...
	}
	
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
	fclose($destination_stream);*/
/*
	//====================================================================================
	//Start of unique_lines():: Checks whether the file has specified number of columns...
	$uploaded_file = '../App_Store/Final_Data2.txt';
	$field = [];
	$line = '';
	$line_no = 1;
	$uploaded_stream = fopen($uploaded_file, 'r') or header('Location: '.$_SESSION['home'].'?flag=12');

	while(($line = fgets($uploaded_stream)) != false){
	    $position = ftell($uploaded_stream);
	    $line_no_dup = $line_no + 1;
		while(($line2 = fgets($uploaded_stream)) != false){
	    	if($line == $line2){
		    	echo '<br />'.$line_no.'original::	'.$line.'<br />'.$line_no_dup.'Duplicate::	'.$line2;
		    	fclose($uploaded_stream);
		    	exit();
			}
			$line_no_dup++;
		}
		fseek($uploaded_stream, $position);
		$line_no++;
	}
	fclose($uploaded_stream);
	
	//unique_lines() ends...
	//====================================================================================
*/
/*
	//====================================================================================
	//Start of unique_lines():: Checks whether the file has specified number of columns...
	$uploaded_file = '../App_Store/Final_Data2.txt';
	$dummy_file = '../App_Store/Dummy.txt';
	$field = [];
	$line = '';
	$duplicate_lines = [];
	$uploaded_stream = fopen($uploaded_file, 'r');
	$dummy_stream = fopen($dummy_file, 'w');

	$line_no = 1;
	set_time_limit(1200);
	while(($line = fgets($uploaded_stream)) != false){
		$position = ftell($uploaded_stream);
	    $line_no_dup = $line_no + 1;
		while(!in_array($line_no, $duplicate_lines) && ($line2 = fgets($uploaded_stream)) != false){
	    	if($line == $line2){
		    	array_push($duplicate_lines, $line_no_dup);
		    	continue;
			}
			$line_no_dup++;
		}
		if(!in_array($line_no, $duplicate_lines)){
			fwrite($dummy_stream, $line);	
		}
		fseek($uploaded_stream, $position);
		$line_no++;
	}
	fclose($uploaded_stream);
	fclose($dummy_stream);
	
	//unique_lines() ends...
	//====================================================================================

	$string = 'one,two,three,four,five,six,seven';//,eight,nine,ten';
	$field = explode(",", $string);
	$i = 1;
	while($field[$i]){
		echo '<br />'.$i.'::	'.$field[$i];
		$i++;
	}
*/

		$uploaded_file = '/opt/lampp/htdocs/App_Store/new_data_2.txt';

		$destinaion_file = '/opt/lampp/htdocs/App_Store/Repository.txt';

		$limit = 8;
		$field = [];
		$line = '';
		//Opens the source file in read mode, if file exists else redirects to Home page...
		$uploaded_stream = fopen($uploaded_file, 'r') or die("unable to open destination file 1...");

		if(file_exists($destinaion_file) && (0 != filesize($destinaion_file))){
			$destination_stream = fopen($destinaion_file, 'a') or die("unable to open destination file 2...");
			//Redirects to Home Page if fails to get an exclusive lock on the repository...
			if(!flock($destination_stream,LOCK_EX)){
				echo "lock not set 1...";
				die();
			}
			fgets($uploaded_stream);	// skips first line of source file if repository already exists...
			//fwrite($destination_stream, "\n");	// puts a newline in if the repository already exists...
		}

		else{
			$destination_stream = fopen($destinaion_file, 'w') or die("unable to open destination file 3...");
			//Redirects to Home Page if fails to get an exclusive lock on the repository...
			if(!flock($destination_stream,LOCK_EX)){
				echo "lock not set ...";
				die();
			}
			if(($line = fgets($uploaded_stream)) != false){
				fwrite($destination_stream, $line);
			}
		}
/*
		echo "<table class='mytables'>
				<caption><em><b>::Editing Logs of Repository By The Users::</b></em></caption>
				<tr>
					<th>User Name</th>
					<th>email id</th>
					<th>Action</th>
					<th>Original PMID</th>
					<th>Replaced PMID</th>
					<th>Edit Date</th>
					<th>Edit Time</th>
				</tr>";
				*/

		//Adds lines from source file to the destination files...
		//There will be an extra blnak line at the end of the file which is not the part of the source file...
		while(($line = fgets($uploaded_stream)) != false){
	    	$field = explode("	", $line);
	    	//unset($arr);
	    	$arr = array();
	    	$str = $field[0];
	    	array_push($arr, $field[0]);
	    	//for($i=0; $i<(sizeof($field) < $limit ? sizeof($field) : $limit)-2; $i++){
	    	for($i=1; $i<$limit-1; $i++){
	   			array_push($arr, $field[$i]);
				$str .= "\t".$field[$i];
	    	}
	    	if((sizeof($field) == $limit) && ($field[$limit-1] != '')){
	   			array_push($arr, $field[$limit-1]);
				$str .= "\t".$field[$limit-1];
	    	}
	    	else{
	    		$str .= "\t".'*';
	    		array_push($arr, '*');
	    	}
	    	/*echo "<tr>
					<td>". $arr[0]."</td>
					<td>". $arr[1]."</td>
					<td>". $arr[2]."</td>
					<td>". $arr[3]."</td>
					<td>". $arr[4]."</td>
					<td>". $arr[5]."</td>
					<td>". $arr[6]."</td>
					<td>". $arr[7]."</td>
				</tr>";*/

			//echo "<br />".$str;
	    	//file_put_contents($destination_stream, $str//);
			fwrite($destination_stream, implode("\t", $arr));
	    }

	    //Closure of the opened files...
	    fclose($destination_stream);
	    fclose($uploaded_stream);
?>