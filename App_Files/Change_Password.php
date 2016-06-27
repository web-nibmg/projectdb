<!DOCTYPE html>
<html>

	<head>

	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	    <title>Change Password</title>
	    <link href='../App_Hide/New.css' rel="stylesheet" type="text/css" />
	    <link href='../App_Hide/Table.css' rel="stylesheet" type="text/css" />
	    <script language='javascript' type='text/javascript' src='../App_Hide/urlreader.js'></script>

	<head>

	<body>

		<div id='wrapper'>

			<?php

				session_start();

				//Redirects to Login page if someone tries to access some page without loggin in
				if($_SESSION['id'] == ''){
		  			header('Location: '.$_SESSION['header'].'?flag=3');
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
						
						<p id='message0' style='display:none'>Change Your Password...</p>
						<p id='message1' style='display:none'>Incorrect Password...<br />Please Try Again.</p>
						<p id='message2' style='display:none'>You Can Not Leave Any Field Blank...</p>
						<p id='message3' style='display:none'>Password Has Been Updated Successfully...</p>
						<p id='message4' style='display:none'>Database Error...<br />Unable To Perform The Action.</p>
						<p id='message5' style='display:none'>You are using the Default Password...<br />It is strongly recommended to change it immidiately.</p>
						
					</div>
					<br />
					<div align='center'>
						<form action='../App_Hide/Change_pwd.php' method='post'>
							<table class='mytables'>
								<caption><b>Please Fill-in the required information::</b></caption>
								<tr>
									<td>Old Password::	</td>
									<td><input type='password' name='old_password' id='old_password' ></td>
								</tr>
								<tr>
									<td>New Password::	</td>
									<td><input type='password' name='new_password' id='new_password' ></td>
								</tr>
								<tr>
									<td>Confirm New Password::	</td>
									<td><input type='password' name='confirm_new_password' id='confirm_new_password' ></td>
								</tr>
							</table>
							<br />
							<input type='submit' value='Submit' class='submit_button' onclick='return verification();' />
							<br />
							&nbsp;
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

		<!--  -----------------------Body Ends---------------------- -->
		
		<!--  -----------------------displays the messages according to the flag value---------------------- -->
	    <script language='javascript' type='text/javascript'>

			if(flag == 0){document.getElementById('message0').style.display = 'block';}
			else if(flag == 1){document.getElementById('message1').style.display = 'block';}
			else if(flag == 2){document.getElementById('message2').style.display = 'block';}
			else if(flag == 3){document.getElementById('message3').style.display = 'block';}
			else if(flag == 4){document.getElementById('message4').style.display = 'block';}
			else if(flag == 5){document.getElementById('message5').style.display = 'block';}
			
		</script>
		<!--  -----------------------display message ends---------------------- -->

		<script language='javascript' type='text/javascript'>
			
			//validation functions...
			function verification(){
				//checks whether 'old password' is blank...
				if(!document.getElementById('old_password').value){
					alert('You can not leave \'old_password\' field blank');
					document.getElementById('old_password').focus();
					return false;
				}

				//checks whether 'new password' is blank...
				if(!document.getElementById('new_password').value){
					alert('You can not leave \'new_password\' field blank');
					document.getElementById('new_password').focus();
					return false;
				}

				//checks whether 'confirm new password' is blank...
				if(!document.getElementById('confirm_new_password').value){
					alert('You can not leave \'confirm_new_password\' field blank');
					document.getElementById('confirm_new_password').focus();
					return false;
				}

				//checks whether 'new password' and 'confirm new password' fields are matching or not...
				if(document.getElementById('new_password').value != document.getElementById('confirm_new_password').value){
					alert('Values in New Password fields are not matching.\nPlease try again...');
					document.getElementById('confirm_new_password').focus();
					return false;
				}
			}

		</script>
	
	</body>

</html>