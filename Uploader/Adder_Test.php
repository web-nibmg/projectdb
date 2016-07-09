<!DOCTYPE html>
<html>
	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Adder</title>
		<link href="/Data_Portal/Backend/css/New.css" rel="stylesheet" type="text/css" />
		<link href='/Data_Portal/Backend/css/Table.css' rel="stylesheet" type="text/css" />
		
		<script language='javascript' type='text/javascript'>
			function SetAction(value){
				if(value == "Submit"){
					document.getElementById('append').action = "/Data_Portal/Backend/Uploader/Appender_Test.php";
				}
				else if(value == "Cancel"){
					document.getElementById('append').action = "/Data_Portal/Backend/Uploader/Remove_upload_file.php";
				}
			}
		</script>

	</head>
	<body>

		<div id='wrapper'>
		
  	<!--  -----------------------Header Start---------------------- -->
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

				//Adding Header File...
				if(!file_exists($_SESSION['path'].$_SESSION['header'])){
					header('Location: '.$_SESSION['login'].'?flag=6');
				}
				require($_SESSION['path'].$_SESSION['header']);
				//Header ends...

				//Adding Logout button...
				if(!file_exists($_SESSION['path'].$_SESSION['logout_button'])){
					header('Location: '.$_SESSION['login'].'?flag=6');
				}
				require($_SESSION['path'].$_SESSION['logout_button']);
				//Logout ends...

		  		//Common part for every page except the appender file...
				//Removes the uploaded file and resets session variable...
				if(($_SESSION['uploaded_file'] != '') && file_exists($_SESSION['path'].$_SESSION['uploaded_file'])){
					unlink($_SESSION['uploaded_file']);	//removes the file from the system
					$_SESSION['uploaded_file'] = '';
				}
				//Removal ends...

				$qry = '';
				$result = '';

				$qry = "select count(*) as Count from dummy_cancer_mutation where User_Id='".$_SESSION['id']."'"; // echo $qry;
				$result = mysqli_fetch_array(mysqli_query($cn, $qry));
				$Total_Row = $result['Count'];
				if($Total_Row > 0){
					$qry = "delete from dummy_cancer_mutation where User_Id='".$_SESSION['id']."'"; // echo $qry;
					$result = mysqli_query($cn, $qry);
				}

			?>
			<!--  -----------------------Header End---------------------- -->
			
			<!--  -----------------------Body Start---------------------- -->

			<div class='container'>
		
				<?php
					
					//Adding Side-bar...
					if(!file_exists($_SESSION['path'].$_SESSION['side_bar'])){
						header('Location: '.$_SESSION['login'].'?flag=6');
					}
					require($_SESSION['path'].$_SESSION['side_bar']);
					//Side-bar ends...

				?>
			
				<div class='main_content' align='center'>

					<div class='Main_Content_Header'>
						<b>Preview</b><br>
					</div>

					<div class='table_wrapper' align='center'>
						
				  	<?php
						
						//Splitting up the mail id to use it in creating the target file name...
				  		$mail_string = explode("@", $_SESSION['id']);

						//Fixing source directory location in $target_dir...
				  		$target_dir = "/opt/lampp/htdocs/Data_Portal/Backend/File_Store/uploads/".$mail_string[0];
				  		$input_file = '';
				  		$file_ext = '';
				  		$target_file = '';				  		

						if(isset($_FILES['inputFile']['name'])){
							//setting actually uploaded file-name in this variable to be used later...
							$input_file = $_FILES['inputFile']['name'];
							
							//stores file extension...
							$file_ext = pathinfo($_FILES["inputFile"]["name"],PATHINFO_EXTENSION);
							//echo '<script type="text/javascript">alert("'.$file_ext.'");</script>';
					  		
							//Declines upload if the file format is not of type 'txt'...
							if($file_ext != 'csv' && $file_ext != 'txt'){							
								header('Location: '.$_SESSION['home'].'?flag=8');
								exit();
							}
							else{
								//creation of source file in the source directory...
								$target_file = $target_dir.'.'.$file_ext;
								
							    if(move_uploaded_file($_FILES["inputFile"]["tmp_name"], $target_file)){
							    	$_SESSION['uploaded_file'] = $target_file;	//Sets the session variable for target file
							    	echo '<br />';
							    	//echo "<br /><b><i>".$target_file."</i></b>.<br>";
									
									//The following codes prints first few lines of the input file as a preview...
									//Opens the source file in read mode, if file exists else redirects to Home page...
									if(file_exists($target_file)){
										check_blank_file($target_file);
										check_tab_delimiter($target_file, $cn);
										show_first_100_lines($target_file);	
										import_data_into_dummy_table($target_file, $cn);																
									}
									else{
										header('Location: '.$_SESSION['home'].'?flag=10');
										exit();
									}

								}
							}						
						}
						//Redirects to Home page if file is not set...
						else{
							header('Location: '.$_SESSION['home'].'?flag=20');
							exit();
						}

						//////////////////////////////////////////////////////////////////////////////////////////
						//Check wheather the source file has some content...
						function check_blank_file($target_file){
							if(0 == filesize($target_file)){
								if(($_SESSION['uploaded_file'] != '') && file_exists($_SESSION['path'].$_SESSION['uploaded_file'])){
									unlink($_SESSION['uploaded_file']);	//removes the file from the system
									$_SESSION['uploaded_file'] = '';
								}
								header('Location: '.$_SESSION['home'].'?flag=15');
								exit();
							}
							return;
						}

						///////////////////////////////////////////////////////////////////////////////////////
						//Check the source file tab separated or not...
						function check_tab_delimiter($target_file, $cn){
							$sourceFile = '';
							$data = '';
							$flag = FALSE;
				  			$colNumber = '';

							//Opens source file or redirects to the homepage...
							$sourceFile = fopen($target_file, "r") or header('Location: '.$_SESSION['home'].'?flag=12');

    						while (($data = fgetcsv($sourceFile, 10000, "\t")) !== FALSE) {   									
    							$num = count($data);
        						//echo "<p> $num fields in line 1: <br /></p>\n";
        						if($num > 1){
        							$flag = TRUE;
        						}
    						}
    						fclose($sourceFile);

    						//If the sorce file is tab separated then do all work...
    						if($flag == TRUE){
    							header_check($target_file, $cn);
    						}
    						//Redirects to Home page if the source file is not tab separated...
    						else{
    							header('Location: '.$_SESSION['home'].'?flag=26');
								exit();
    						}
    						return;  
						}

						/////////////////////////////////////////////////////////////////////////////////////
						//Checks whether the file has the same columns as the repository...
						function header_check($target_file, $cn){
							$source_File = '';
							$line = '';
				  			$fields = '';
				  			$qry1 = '';
				  			$result1 = '';
				  			$colNumber = '';

							$source_File = fopen($target_file, "r");
    						$line = fgets($source_File);	//reads the first line which is in turn the header									
							$fields = explode("\t", $line);	//splits the first line into fields 
							fclose($source_File); 
							//print_r($fields);

							$qry1 = "select * from dummy_cancer_mutation";
							$result1 = mysqli_query($cn, $qry1); 								
							$colNumber = mysqli_num_fields($result1);
							//print_r($colNumber);

							for($i=1; $i<=$colNumber-1; $i++){
								$fieldName[] = mysqli_fetch_field_direct($result1,$i)->name;
							}
							//print_r($fieldName);

							//Removes the uploaded file if there are less headers in the uploaded file
							if(sizeof($fields) < $colNumber - 2){
								unlink($_SESSION['uploaded_file']);
								$_SESSION['uploaded_file'] = '';
								header('Location: '.$_SESSION['home'].'?flag=14');
								exit();
							}
							else {}	

							//Removes the uploaded file if there are less number of fields in any row of the uploaded file						
							for($i=0; $i<$colNumber-2; $i++){
								if($fields[$i] == ''){
									unlink($_SESSION['uploaded_file']);
									$_SESSION['uploaded_file'] = '';
									header('Location: '.$_SESSION['home'].'?flag=19');
									exit();
								}
								elseif(trim($fields[$i]) != trim($fieldName[$i])){
									unlink($_SESSION['uploaded_file']);
									$_SESSION['uploaded_file'] = '';
									header('Location: '.$_SESSION['home'].'?flag=13');
									exit();
								}
								else{};
							}
							return;
						}

						////////////////////////////////////////////////////////////////////////////////////////
						//Show first 100 lines of the source file...
						function show_first_100_lines($target_file){
							$source_file = '';
							$line1 = '';
							$fields1 = '';
							$no_of_lines = 100;

							$source_file = fopen($target_file, "r");
							$line1 = fgets($source_file);	//reads the first line which is in turn the header
							$fields1 = explode("\t", $line1);	//splits the first line into fields

							//Start of the table...
							echo "<table class='mytables2' align='center'><tr>";	//start of header row
							for($j = 0; $j < sizeof($fields1); $j++){
								echo "<th>".$fields1[$j]."</th>";
							}
							echo "</tr>";	//end of header row

							//Start of data containing in the table...
							for($i = 0; ($i < $no_of_lines) && (($line1 = fgets($source_file)) != ''); $i++){
								$fields1 = explode("\t", $line1);
								echo "<tr>";
								for($j = 0; $j < sizeof($fields1); $j++){
									echo "<td>".$fields1[$j]."</td>";
								}
								echo "</tr>";
							}
							echo "</table><i>First ".$i." Lines...</i>";
							//End of the table...
									
							fclose($source_file);
							return;
						}

						///////////////////////////////////////////////////////////////////////////////////////
						//Import source file data into dummy_cancer_mutation table...
						function import_data_into_dummy_table($target_file, $cn){
							$qry2 = '';
							$result2 = '';

							$qry2 = "LOAD DATA LOCAL INFILE '$target_file' INTO TABLE projectdb.dummy_cancer_mutation FIELDS TERMINATED BY '\t' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n' IGNORE 1 LINES (`PMID`,`Cancer_Code`,`Chromosome`,`Start_Pos`,`Stop_Pos`,`Reference`,`Alternate`,`Patient_ID`) SET User_Id = '".$_SESSION['id']."';";
							$result2 = mysqli_query($cn, $qry2);

							if (!mysqli_error($cn)){
								unlink($_SESSION['uploaded_file']);
								$_SESSION['uploaded_file'] = '';
							}
							mysqli_close($cn);
							return;
						}
					?>
					</div>
					<br />
					<div class='display_buttons' align='center'>
						<br /><b>Do You Want To Submit The File?</b>
						<form action='' method='post' id="append">
							<br>
							<input type='hidden' name='input_file' id='input_file' value='<?php print_r($input_file) ?>'>
							<div align='center'>
								<input type='submit' value='Submit' class='submit_button' onclick="SetAction(this.value);"/>
							</div>
							<div align='center'>
								<input type='submit' value='Cancel' class='cancel_button' onclick="SetAction(this.value);"/>
							</div>
						</form>
					</div>
				</div>
			</div>

			<?php
				//Adding Footer...
				if(!file_exists($_SESSION['path'].$_SESSION['footer'])){
					header('Location: '.$_SESSION['login'].'?flag=6');
					}
				require($_SESSION['path'].$_SESSION['footer']);
				//Footer ends...
			?>

		</div>

	</body>

</html>