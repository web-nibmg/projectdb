<?php
	
	session_start();

	//Redirects to Login page if someone tries to access some page without loggin in
	if($_SESSION['id'] == ''){
		header('Location: '.$_SESSION['home'].'?flag=3');
		exit();
	}
	//Redirection Ends...

	//Checks whether the source file-name is passed or not...
	if(!isset($_POST['input_file'])){
		header('Location: '.$_SESSION['home'].'?flag=17');
		exit();
	}

	//Adding Connection String
	if(!file_exists($_SESSION['path'].$_SESSION['connection'])){
		header('Location: '.$_SESSION['home'].'?flag=4');
		exit();
	}
	require($_SESSION['path'].$_SESSION['connection']);
	//Adding Connection String ends

	$qry = '';
	$result = '';
	$count = 0;
	$no_of_lines_added = 0;

	$qry = "SELECT COUNT(*) as COUNT FROM dummy_cancer_mutation WHERE User_Id = '".$_SESSION['id']."';";
	$result = mysqli_fetch_array(mysqli_query($cn, $qry));
	$count = $result['COUNT'];

	if($count > 0){
		create_insert_each_email_table($cn);
		check_null_and_blank_value($cn);
		check_duplicate_lines($cn);
		duplicate_PMID($cn);
		$no_of_lines_added = append_data($cn);
		update_repository_history($cn, $no_of_lines_added);
	}
	else{
		header('Location: '.$_SESSION['home'].'?flag=12');
		exit();
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//CReate a temporary table for each mail id and insert dummy_cancer_mutation data into the table...
	function create_insert_each_email_table($cn){
		$qry = "drop table if exists `".$_SESSION['id']."`;";
		$result = mysqli_query($cn, $qry);

		$qry = "create table `".$_SESSION['id']."` (RID BIGINT(255) not null Primary Key AUTO_INCREMENT,User_Id varchar(20) null,PMID varchar(15) null,Cancer_Code varchar(15) null,Chromosome varchar(5) null,Start_Pos int(10) null,Stop_Pos int(10) null,Reference char(100) null,Alternate char(100) null,Patient_ID varchar(15));";
		$result = mysqli_query($cn, $qry);

		$qry = "insert into `".$_SESSION['id']."` (User_Id,PMID,Cancer_Code,Chromosome,Start_Pos,Stop_Pos,Reference,Alternate,Patient_ID) select User_Id,PMID,Cancer_Code,Chromosome,Start_Pos,Stop_Pos,Reference,Alternate,Patient_ID from dummy_cancer_mutation where User_Id='".$_SESSION['id']."';";
		$result = mysqli_query($cn, $qry); 
		return;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//Checks whether the file contains null values except the last column...
	function check_null_and_blank_value($cn){
		$qry = "SELECT RID FROM `".$_SESSION['id']."` WHERE (PMID = '' OR PMID IS NULL) OR (Cancer_Code = '' OR Cancer_Code IS NULL) OR (Chromosome = '' OR Chromosome IS NULL) OR (Start_Pos = '' OR Start_Pos IS NULL) OR (Stop_Pos = '' OR Stop_Pos IS NULL) OR (Reference = '' OR Reference IS NULL) OR (Alternate = '' OR Alternate IS NULL);";
		$result=mysqli_query($cn, $qry);
		while(($row = mysqli_fetch_array($result)) != null){
			$tags[] =htmlspecialchars( $row['RID'], ENT_NOQUOTES, 'UTF-8' );
		}
		if (!empty($tags)){
			$qry = "drop table if exists `".$_SESSION['id']."`;";
			$result = mysqli_query($cn, $qry);

			$qry = "delete from dummy_cancer_mutation WHERE User_Id='".$_SESSION['id']."';";
			$result = mysqli_query($cn, $qry);

			$line_no = implode(',', $tags);
			header('Location: '.$_SESSION['home'].'?flag=2+'.$line_no);
			exit();
			//print_r($line_no);
		}
		return;
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//Checks whether the file contains any duplicate lines...
	function check_duplicate_lines($cn){
		$qry = "SELECT AA.RID as RID, BB.Count as Count  FROM `".$_SESSION['id']."` as AA inner JOIN 
(select PMID,Cancer_Code,Chromosome,Start_Pos,Stop_Pos,Reference,Alternate,Patient_ID,COUNT(*) as Count from `".$_SESSION['id']."` group by PMID,Cancer_Code,Chromosome,Start_Pos,Stop_Pos,Reference,Alternate,Patient_ID having Count > 1) BB on AA.PMID=BB.PMID and AA.Cancer_Code=BB.Cancer_Code and AA.Chromosome=BB.Chromosome and AA.Start_Pos=BB.Start_Pos and AA.Stop_Pos=BB.Start_Pos and AA.Reference=BB.Reference and AA.Alternate=BB.Alternate and AA.Patient_ID<=>BB.Patient_ID order by BB.PMID,BB.Cancer_Code,BB.Chromosome,BB.Start_Pos,BB.Stop_Pos,BB.Reference,BB.Alternate,BB.Patient_ID;";
		$result=mysqli_query($cn, $qry);
		while(($row = mysqli_fetch_array($result)) != null){
			$RID[] = $row['RID'];
			$Count[] = $row['Count'];
		}
		if (!empty($RID)){
			$qry = "drop table if exists `".$_SESSION['id']."`;";
			$result = mysqli_query($cn, $qry);

			$qry = "delete from dummy_cancer_mutation WHERE User_Id='".$_SESSION['id']."';";
			$result = mysqli_query($cn, $qry);

			$output = array_slice($RID, 0, $Count[0]);
			sort($output);
			$line_no = implode(',', $output);
			header('Location: '.$_SESSION['home'].'?flag=21+'.$line_no);
			exit();
			//print_r($line_no);
		}
		return;
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//Checks whether the file has specified number of columns...
	function duplicate_PMID($cn){
		$Count = 0;

		$qry = "select count(*) as Count from `cancer_mutation_repository` where PMID in (select DISTINCT PMID from `".$_SESSION['id']."`);";
		$result = mysqli_fetch_array(mysqli_query($cn, $qry));
		$Count = $result['Count'];

		if($Count > 0){
			$qry = "drop table if exists `".$_SESSION['id']."`;";
			$result = mysqli_query($cn, $qry);

			$qry = "delete from dummy_cancer_mutation WHERE User_Id='".$_SESSION['id']."';";
			$result = mysqli_query($cn, $qry);
			
			header('Location: '.$_SESSION['home'].'?flag=18');
			exit();
		}
		return;

	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//Checks whether the file has specified number of columns...
	function append_data($cn){
		$qry = "insert into cancer_mutation_repository (PMID,Cancer_Code,Chromosome,Start_Pos,Stop_Pos,Reference,Alternate,Patient_ID) select PMID,Cancer_Code,Chromosome,Start_Pos,Stop_Pos,Reference,Alternate,Patient_ID from dummy_cancer_mutation where User_Id='".$_SESSION['id']."';";
		$result = mysqli_query($cn, $qry);
		if (!empty($result)){
			$qry = "select COUNT(*) as Count from `".$_SESSION['id']."`;";
			$result = mysqli_fetch_array(mysqli_query($cn, $qry));
			$no_of_lines_added = $result['Count'];
			
			$qry = "drop table if exists `".$_SESSION['id']."`;";
			$result = mysqli_query($cn, $qry);

			$qry = "delete from dummy_cancer_mutation WHERE User_Id='".$_SESSION['id']."';";
			$result = mysqli_query($cn, $qry);
		}
		return $no_of_lines_added;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function update_repository_history($cn, $no_of_lines_added){
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
			header('Location: /Data_Portal/Backend/Uploader/File_Adder.php?flag='.$no_of_lines_added);
			exit();
		}
		//If insertion fails, closes mysql connection and redirects to Home page...
		else{
			mysqli_close($cn);
			header('Location: '.$_SESSION['home'].'?flag=4');
			exit();
		}
	}	
	
	//End of the method-bodies...
?>