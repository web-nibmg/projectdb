<!DOCTYPE html>
<html>

	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>File Uploader</title>
		<link href='/Data_Portal/Backend/css/New.css' rel="stylesheet" type="text/css" />
		<link href='/Data_Portal/Backend/css/Table.css' rel="stylesheet" type="text/css" />
		<script language='javascript' type='text/javascript' src='/Data_Portal/Backend/js/urlreader.js'></script>

	</head>

	<body>

		<div id='wrapper'>

			<?php

				session_start();
				
				//Redirects to Login page if someone tries to access some page without loggin in
				if($_SESSION['id'] == ''){
		  			header('Location: '.$_SESSION['home'].'?flag=3');
		  			exit();
		  		}
		  		//Redirection Ends...

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

				//Counts the number of lines in the Repository and stores in $no_of_lines...
				if(!file_exists($_SESSION['path'].'/Data_Portal/Backend/Uploader/Count_Lines.php')){
					header('Location: '.$_SESSION['login'].'?flag=6');
					exit();
				}
				require($_SESSION['path'].'/Data_Portal/Backend/Uploader/Count_Lines.php');
				//Counting Ends...

				$_SESSION['no_of_lines'] = $no_of_lines;

				//Common part for every page except the appender file...
				//Removes the uploaded file and resets session variable...
				if(($_SESSION['uploaded_file'] != '') && file_exists($_SESSION['path'].$_SESSION['uploaded_file'])){
					unlink($_SESSION['uploaded_file']);	//removes the file from the system
					$_SESSION['uploaded_file'] = '';
				}
				//Removal ends...

				$flag = 0;			
				if(isset($_GET['flag'])){
					$flag = $_GET['flag'];
				}
				//echo '<script type="text/javascript">alert("'.$flag.'");</script>';

			?>

			<div class='container'>

				<?php
					
					//Adding Side-bar...
					if(!file_exists($_SESSION['path'].$_SESSION['side_bar'])){
						header('Location: '.$_SESSION['login'].'?flag=6');
					}
					require($_SESSION['path'].$_SESSION['side_bar']);
					//Side-bar ends...

				?>

				<div class='main_content'>
					
					<div id='messages'>
						<fieldset>
							<p>File Uploader</p>
						</fieldset>
					</div>

					<div class='messages'>
						<fieldset>
							<div align='center'>
								<table>
									<caption>::<u>Instructions</u>::</Caption>
									<tr>
										<td>1.</td>
										<td>Files should be in ".csv" or ".txt" format.</td>
									</tr>
									<tr>
										<td>2.</td>
										<td>All the fields should be "tab" delimited.</td>
									</tr>
									<tr>
										<td>3.</td>
										<td>None of the values should be within quotes(" " or ' ').</td>
									</tr>
										<td>4.</td>
										<td>The columns should be in proper order<br />(see the "<u>Reference Headers</u>").</tr>
									</tr>
									</tr>
										<td>5.</td>
										<td>Header for every column is mandatory.</tr>
									</tr>
								</table>
							</div>
							<br />
							<div class='reference_table' align='center'>
								<table class='mytables'>
									<caption>Reference Headers</caption>
									<tr>
										<th>PMID</th>
										<th>Cancer_Code</th>
										<th>Chromosome</th>
										<th>Start_Pos</th>
										<th>Stop_Pos</th>
										<th>Reference</th>
										<th>Alternate</th>
										<th>Patient_ID</th>
									</tr>
								</table>
							</div>
						</fieldset>
					</div>	
					<div align='center'>
						<fieldset>
							<div class='holder' align='center'>
								<div id='upload_msg' align='center' style='display:none'>
									<h3>The File Has Successfully Been Uploaded...</h3>
								</div>
								<div id='left_align'>
									No. of Lines in Repository::	<?php echo $_SESSION['no_of_lines'].'*'; ?>
								</div>
								<div id='right_align' style='display:none'>
									No. of Lines Added::	<?php echo $flag.'*'; ?>
								</div>
								<br /><br />* Number of lines doesn't include the 1st line(containing the headers).
							</div>
						</fieldset>
						<br />
						<fieldset>
							<h3><u><i>File Uploader</i></u></h3>
							Upload Your Somatic Mutation File Here...
							<form action='/Data_Portal/Backend/Uploader/Adder_Test.php' method='post' enctype='multipart/form-data'>
								<br />
								<input type='file' id='inputFile' name='inputFile' />
								<input type='submit' value='Submit' class='submit_button' id='fileSubmit' />
							</form>
						</fieldset>
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

		<script language='javascript' type='text/javascript'>

			if(flag != 0){
				document.getElementById('right_align').style.display = 'block';
				document.getElementById('upload_msg').style.display = 'block';
			}

		</script>

	</body>
</html>