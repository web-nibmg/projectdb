<!DOCTYPE html>
<html>
	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Adder</title>
		<link href="../App_Hide/New.css" rel="stylesheet" type="text/css" />
		<link href='../App_Hide/Table.css' rel="stylesheet" type="text/css" />
		
		<script language='javascript' type='text/javascript'>
			function cancel_submit(){

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

				//Adding Header File...
				if(!file_exists($_SESSION['header'])){
					header('Location: '.$_SESSION['login'].'?flag=6');
				}
				require($_SESSION['header']);
				//Header ends...

				//Adding Logout button...
				if(!file_exists($_SESSION['logout_button'])){
					header('Location: '.$_SESSION['login'].'?flag=6');
				}
				require($_SESSION['logout_button']);
				//Logout ends...

		  		//Common part for every page except the appender file...
				//Removes the uploaded file and resets session variable...
				if(($_SESSION['uploaded_file'] != '') && file_exists($_SESSION['uploaded_file'])){
					unlink($_SESSION['uploaded_file']);	//removes the file from the system
					$_SESSION['uploaded_file'] = '';
				}
				//Removal ends...

			?>
			<!--  -----------------------Header End---------------------- -->
			
			<!--  -----------------------Body Start---------------------- -->

			<div class='container'>
		
				<?php
					
					//Adding Side-bar...
					if(!file_exists($_SESSION['side_bar'])){
						header('Location: '.$_SESSION['login'].'?flag=6');
					}
					require($_SESSION['side_bar']);
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
				  		$target_dir = "/opt/lampp/htdocs/App_Store/uploads/".$mail_string[0];
				  		$target_file = '';
				  		$line = '';
				  		$fields = '';
				  		$input_file = '';
				  		$target_file = '';
				  		$sourceFile = '';
				  		$no_of_lines = 100;
						
						if(isset($_FILES['inputFile']['name'])){
							//setting actually uploaded file-name in this variable to be used later...
							$input_file = $_FILES['inputFile']['name'];
							
							//stores file extension...
							$file_ext = pathinfo($_FILES["inputFile"]["name"],PATHINFO_EXTENSION);
					  		
							//Declines upload if the file format is not of type 'txt'...
							if($file_ext != 'txt'){
								header('Location: '.$_SESSION['home'].'?flag=8');
								exit();
							}
							
							else{
								//creation of source file in the source directory...
								$target_file = $target_dir.'.'.$file_ext;
								
							    if(move_uploaded_file($_FILES["inputFile"]["tmp_name"], $target_file)){
							    	$_SESSION['uploaded_file'] = $target_file;	//Sets the session variable for target file
							    	echo "<br /><b><i>".$input_file."</i></b>.<br>";
									
									//The following codes prints first few lines of the input file as a preview...
									//Opens the source file in read mode, if file exists else redirects to Home page...
									if(file_exists($target_file)){
										if(0 == filesize($target_file)){
											if(($_SESSION['uploaded_file'] != '') && file_exists($_SESSION['uploaded_file'])){
												unlink($_SESSION['uploaded_file']);	//removes the file from the system
												$_SESSION['uploaded_file'] = '';
											}
											header('Location: '.$_SESSION['home'].'?flag=15');
											exit();
										}
										//Opens source file or redirects to the homepage...
										$sourceFile = fopen($target_file, 'r') or header('Location: '.$_SESSION['home'].'?flag=12');
									}
									else{
										header('Location: '.$_SESSION['home'].'?flag=10');
										exit();
									}

									$line = fgets($sourceFile);	//reads the first line which is in turn the header
									$fields = explode("\t", $line);	//splits the first line into fields

									//Start of the table...
									echo "<table class='mytables2' align='center'><tr>";	//start of header row
									for($j = 0; $j < sizeof($fields); $j++){
										echo "<th>".$fields[$j]."</th>";
									}
									echo "</tr>";	//end of header row

									//Start of data containing in the table...
									for($i = 0; ($i < $no_of_lines) && (($line = fgets($sourceFile)) != ''); $i++){
										$fields = explode("\t", $line);
										echo "<tr>";
										for($j = 0; $j < sizeof($fields); $j++){
											echo "<td>".$fields[$j]."</td>";
										}
										echo "</tr>";
									}
									echo "</table><i>First ".$i." Lines...</i>";
									//End of the table...
									
									fclose($sourceFile);
								}
								//Redirects to Home page if upload fails...
							    else{
							    	header('Location: '.$_SESSION['home'].'?flag=10');
							        exit();
							    }
							}
						}
						//Redirects to Home page if file is not set...
						else{
							header('Location: '.$_SESSION['home'].'?flag=20');
							exit();
						}

					?>

					</div>
					<br />
					<div class='display_buttons' align='center'>
						<br /><b>Do You Want To Submit The File?</b>
						<form action='../App_Hide/Appender.php' method='post'>
							<br>
							<!-- Passes the data -->
							<input type='hidden' name='target' id='target' value='<?php print_r($target_file) ?>'>
							<input type='hidden' name='input_file' id='input_file' value='<?php print_r($input_file) ?>'>
							<!-- Submits the data containing in the file -->
							<div align='center'>
								<input type='submit' value='Submit' class='submit_button'/>
							</div>
							<div align='center'>
								<a href='../App_Hide/Remove_upload_file.php'><input type='button' value='Cancel' class='cancel_button' /></a>
							</div>
						</form>
					</div>
				</div>
			</div>

			<?php
				//Adding Footer...
				if(!file_exists($_SESSION['footer'])){
					header('Location: '.$_SESSION['login'].'?flag=6');
					}
				require($_SESSION['footer']);
				//Footer ends...
			?>

		</div>

	</body>

</html>