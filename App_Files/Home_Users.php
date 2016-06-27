<!DOCTYPE html>
<html>

	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Home</title>
		<link href='../App_Hide/New.css' rel="stylesheet" type="text/css" />
		<script language='javascript' type='text/javascript' src='../App_Hide/urlreader.js'></script>
		
	</head>

	<body>

		<div id='wrapper'>

			<?php

				session_start();
				
				//Loacating Previous page start
				$url = '';
				if(isset($_SERVER['HTTP_REFERER'])){
					$url = $_SERVER['HTTP_REFERER'];
				}

				//Loacating Previous page end

				/////////strpos() testing//////////////
				//$abc='';
				//$abc=strpos('10.11 10', '10', 0);
				//echo '<script type="text/javascript">alert("'.$url.'"+"'.$abc.'");</script>';


				//Adding Connection String
				if(!file_exists('../App_Hide/Connect.php')){
					header('Location: ../App/Login.html?flag=6');
					exit();
				}
				require('../App_Hide/Connect.php');
				//Connection String ends

				//Logging into the system
				if(strpos($url, 'Login.html')){
				//Redirects to Login page if values are not passed or in case of improper attempt
					if(($_POST['email'] == '') || ($_POST['pwd'] == '') || ($_POST['type'] == '')){
						header('Location: ../App/Login.html?flag=2');
						exit();
					}
					else{
						$email = htmlspecialchars($_POST['email']);
						$pwd = htmlspecialchars($_POST['pwd']);
						$type = htmlspecialchars($_POST['type']);
						
						$qry = "select user_name, pswd, stat, type from users where email = '$email'"; // echo $qry;
						
						$result = '';
						$status = '2';
						$result = mysqli_fetch_array(mysqli_query($cn, $qry));

						if(!$result){
							header('Location: ../App/Login.html?flag=5');
							exit();
						}
						
						$str = $result['pswd'];
						$status = $result['stat'];

				//Redirects to Login page in case of password mismatch
						if($pwd != $str){
							mysqli_close($cn);
							header('Location: ../App/Login.html?flag=1');
							exit();
						}

				//Redirects to Login page in case of Deleted User
						else if($status == '0'){
							mysqli_close($cn);
							header('Location: ../App/Login.html?flag=6');
							exit();
						}

				
				//Sets the session variables if login is successful
						else{
							$_SESSION['id'] = $email;
							$_SESSION['name'] = $result['user_name'];
							$_SESSION['type'] = $result['type'];
							$_SESSION['header'] = '../App_Files/Header.php';
							$_SESSION['footer'] = '../App_Files/Footer.php';
							$_SESSION['logout_button'] = '../App_Hide/Logout_button.php';
							$_SESSION['login'] = '../App/Login.html';
							$_SESSION['home'] = '../App_Files/Home_Users.php';
							$_SESSION['side_bar'] = '../App_Hide/Sidebar_Users.php';
							$_SESSION['connection'] = '../App_Hide/Connect.php';
							$_SESSION['uploaded_file'] = '';
							$_SESSION['no_of_lines'] = '';

							//Counts the number of lines in the Repository and stores in $no_of_lines...
							//if(!file_exists('../App_Hide/Count_Lines.php')){
								//header('Location: ../App/Login.html?flag=6');
								//exit();
							//}
							//require('../App_Hide/Count_Lines.php');
							//Counting Ends...

							//$_SESSION['no_of_lines'] = $no_of_lines;
							
				//Redirects to Change Password page in case of default password 
							if($str == 'welcome@123'){
								mysqli_close($cn);
								header('Location: ../App_Files/Change_Password.php?flag=5');
								exit();
							}
						}
					}
				}

				//Redirects to Login page if someone tries to access some page without loggin in
				elseif($_SESSION['id'] == ''){
					mysqli_close($cn);
					header('Location: '.$_SESSION['login'].'?flag=3');
					exit();
				}

				//If the user has come from some other page...
				else{
					//$count = 0;
					$qry = "select user_name,email,type from users where stat = 1 and email <> '".$_SESSION['id']."'";
			  		$result = mysqli_query($cn, $qry);
					mysqli_close($cn);

					//Common part for every page except the appender file...
					//Removes the uploaded file and resets session variable...
					if(($_SESSION['uploaded_file'] != '') && file_exists($_SESSION['uploaded_file'])){
						unlink($_SESSION['uploaded_file']);	//removes the file from the system
						$_SESSION['uploaded_file'] = '';
					}
					//Removal ends...
				}

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
			?>

			<div class='container'>

				<?php
					//Adding Side-bar...
					if(!file_exists($_SESSION['side_bar'])){
						header('Location: '.$_SESSION['login'].'?flag=6');
					}
					require($_SESSION['side_bar']);
					//Side-bar ends...
				?>

				<div class='main_content'>
					
					<div id='messages'>
						<fieldset>
							<p id='message0' style='display:none' align='center'>Welcome To Somatic Mutation Data Uploader...</p>
							<p id='message1' style='display:none' align='center'>The File has been Successfully Appended...</p>
							<p id='message2' style='display:none' align='center'>Uploading Aborted.<br />File has NULL Values in Essential Places (At Line no.: <script>document.write(flag_2);</script>)...</p>
							<p id='message3' style='display:none' align='center'>File Does Not Exists.<br>You Need To Upload Some Data To Create It...</p>
							<p id='message4' style='display:none' align='center'>Database Error.<br>Unable to execute the query...</p>
							<p id='message5' style='display:none' align='center'>You Need To Login As Administrator To Access The Page...</p>
							<p id='message6' style='display:none' align='center'>Deleted User.<br />Please contact the Administrator for further help...</p>
							<p id='message7' style='display:none' align='center'>Password Has Been Changed Successfully...</p>
							<p id='message8' style='display:none' align='center'>Upload Aborted.<br />Only "csv" or "txt" Files Are Allowed...</p>
							<p id='message9' style='display:none' align='center'>Action Failed...</p>
							<p id='message10' style='display:none' align='center'>Some Err Occurred.<br />Unable To Upload The File...</p>
							<p id='message11' style='display:none' align='center'>Action Failed.<br />Unable To Open The Repository...</p>
							<p id='message12' style='display:none' align='center'>File Not Found<br />Upload Aborted...</p>
							<p id='message13' style='display:none' align='center'>Uploading Aborted.<br />Either The Uploaded File's Headers Are Different OR Not In Proper Order...</p>
							<p id='message14' style='display:none' align='center'>Uploading Aborted.<br />The Uploaded File Has Less Number of Columns...</p>
							<p id='message15' style='display:none' align='center'>Uploading Aborted.<br />The Uploaded File Has No Content...</p>
							<p id='message16' style='display:none' align='center'>Uploading Aborted By The User...</p>
							<p id='message17' style='display:none' align='center'>Uploading Aborted.<br />The Uploaded Filename Has Not Been Passed...</p>
							<p id='message18' style='display:none' align='center'>Uploading Aborted.<br />The Uploaded File Is Duplicate...</p>
							<p id='message19' style='display:none' align='center'>Uploading Aborted.<br />The Uploaded File Has Header(s) Missing...</p>
							<p id='message20' style='display:none' align='center'>Unable to Append the File...</p>
							<p id='message21' style='display:none' align='center'>Uploading Aborted.<br />The Uploaded File Has Duplicate Line(s) (at Line no.: <script>document.write(flag_2);</script>)...</p>
							<p id='message22' style='display:none' align='center'>Uploading Aborted.<br />File More Than 8 Columns (At Line no.: <script>document.write(flag_2);</script>).<br />Please Check For Any Extra Tab...</p>
							<p id='message23' style='display:none' align='center'>Uploading Aborted.<br />Patient_Id in Can Either Be Blank Or Alphanumeric (At Line no.: <script>document.write(flag_2);</script>)...</p>
							<p id='message24' style='display:none' align='center'>Uploading Aborted.<br />Unable To Get A Lock On The Uploaded File...</p>
							<p id='message25' style='display:none' align='center'>Uploading Aborted.<br />Database Error...</p>
							<p id='message26' style='display:none' align='center'>Upload Aborted.<br />Only TAB ("\t") separated Files Are Allowed...</p>
						</fieldset>
					</div>
					<br />
					<div id='horizontal_list' align='center'>
						<div>
							<fieldset>
								<h3><u><i>File Uploader</i></u></h3>
								To Upload Somataic Mutation File<br />
								<a href='../App_Files/File_Adder.php'><input type='button' value='Go' class='go_button' /></a>
							</fieldset>
						</div>
						<div>
							<fieldset>
								<h3><u><i>Upload History</i></u></h3>
								To View Your Previous Upload Records<br />
								<a href='../App_Files/Upload_History.php'><input type='button' value='Go' class='go_button' /></a>
							</fieldset>
						</div>
						<div>
							<fieldset>
								<h3><u><i>Change Password</i></u></h3>
								To Change Your Password<br />
								<a href='../App_Files/Change_Password.php'><input type='button' value='Go' class='go_button' /></a>
							</fieldset>
						</div>

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

		<script language='javascript' type='text/javascript'>
			
			if(-2 < flag && flag < 27){
				document.getElementById('message' + flag).style.display = 'block';
			}
			
			else{document.getElementById('message9').style.display = 'block';}

	    </script>

	</body>
</html>