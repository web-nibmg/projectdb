<!DOCTYPE html>
<html>

	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>File Uploader</title>
		<link href='../App_Hide/New.css' rel="stylesheet" type="text/css" />
		<script language='javascript' type='text/javascript' src='../App_Hide/urlreader.js'></script>
		
	</head>

	<body>

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
									<td>Files should be in ".txt" format.</td>
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
									<td>The columns should be in proper order.</tr>
								</tr>
								</tr>
									<td>5.</td>
									<td>Headers for every column is mandatory.</tr>
								</tr>
							</table>
						</div>
					</fieldset>
				</div>
				
				<br /><br />
				
				<div align='center'>
					<fieldset>
						<h3><u><i>File Uploader</i></u></h3>
						Upload Your Somatic Mutation File Here...
						<form action='/App_Hide/Adder.php' method='post' enctype='multipart/form-data'>
							<br />
							<input type='file' id='inputFile' name='inputFile'>
							<input type='submit' value='Submit' id='fileSubmit'>
							<br /><br />
						</form>
					</fieldset>
				</div>
				
			</div>
		</div>

		<script language='javascript' type='text/javascript'>
			if(flag == -1){document.getElementById('messageNull').style.display = 'block';}
			else if(flag == 0){document.getElementById('message0').style.display = 'block';}
	    	else if(flag == 1){document.getElementById('message1').style.display = 'block';}
	    	else if(flag == 2){document.getElementById('message2').style.display = 'block';}
	    	else if(flag == 3){document.getElementById('message3').style.display = 'block';}
	    	else if(flag == 4){document.getElementById('message4').style.display = 'block';}
	    	else if(flag == 5){document.getElementById('message5').style.display = 'block';}
	    	else if(flag == 6){document.getElementById('message6').style.display = 'block';}
	    	else if(flag == 7){document.getElementById('message7').style.display = 'block';}
	    	else if(flag == 8){document.getElementById('message8').style.display = 'block';}
	    	else if(flag == 10){document.getElementById('message10').style.display = 'block';}
	    	else if(flag == 11){document.getElementById('message11').style.display = 'block';}
	    	else if(flag == 12){document.getElementById('message12').style.display = 'block';}
	    	else if(flag == 13){document.getElementById('message13').style.display = 'block';}
	    	else if(flag == 14){document.getElementById('message14').style.display = 'block';}
	    	else{document.getElementById('message9').style.display = 'block';}
	    </script>

	</body>
</html>