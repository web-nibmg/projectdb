<!DOCTYPE html>
<html>

	<head>

	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	    <title>Download Repository</title>
	    <link href='../App_Hide/New.css' rel="stylesheet" type="text/css" />
	    <link href='../App_Hide/Table.css' rel="stylesheet" type="text/css" />
	    <script language='javascript' type='text/javascript' src='../jquery-2.1.4-uc.js'></script>

	    <!--  -----------------------download_option() Starts---------------------- -->
	    <script language='javascript' type='text/javascript'>
	    
			function download_option(ext){
				
	    		if(confirm('Download ' + 'Repository' + '.' + ext + '?')){
	   				window.location = '../App_Hide/Download.php?file_extension=' + ext;
		    	}
		    	else{return false;}	    	
		    }		

		</script>
		<!--  -----------------------download_option() Ends---------------------- -->

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

				//Adding Connection String
				if(!file_exists($_SESSION['connection'])){
					header('Location: '.$_SESSION['home'].'?flag=4');
					exit();
				}
				require($_SESSION['connection']);
				//Adding Connection String ends

				//Common part for every page except the appender file...
				
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
						<b>Download Repository</b><br>
					</div>
					<br />
					<div class='link_table' style='overflow-x:auto;'>
						<table class='mytables'>
							<caption><em><b>Click On The Links To Download::</b></em></caption>
							<tr>
								<th>File Type</th>
								<th>Download Link</th>
							</tr>
							<tr>
								<td>txt</td>
								<td><a href='#' onclick="download_option('txt');">Download</a></td>
							</tr>
							<tr>
								<td>csv</td>
								<td><a href='#' onclick="download_option('csv');">Download</a></td>
							</tr>
							<tr>
								<td>doc</td>
								<td><a href='#' onclick="download_option('doc');">Download</a></td>
							</tr>
						</table>
					</div>
					<div id='display_error' style='overflow-x:auto;'>
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

	</body>

</html>