<!DOCTYPE html>
<html>

	<head>

	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	    <title>Upload History</title>
	    <link href='../App_Hide/New.css' rel="stylesheet" type="text/css" />
	    <link href='../App_Hide/Table.css' rel="stylesheet" type="text/css" />
	    
	    <script language='javascript' type='text/javascript'>
	    	function print_history(){

	    		var history_page = window.open('../App_Files/Print_History.php','_blank','toolbar=no',"width=auto, height=auto");
	    		var window_content = "<div id='upload_history' align='center' style='overflow-y: auto; margin: 0% 0% 2% 0%;'>" +
	    			document.getElementById('upload_history').innerHTML + "</div>";
	    		history_page.onload = function(){this.document.body.innerHTML += window_content; this.print(); this.close();};
	    		//history_page.close();
	    	}
	    </script>

	<head>

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

				//Adding Connection String
				if(!file_exists($_SESSION['connection'])){
					header('Location: '.$_SESSION['home'].'?flag=4');
					exit();
				}
				require($_SESSION['connection']);
				//Adding Connection String ends

				//Common part for every page except the appender file...
				//Removes the uploaded file and resets session variable...
				if(($_SESSION['uploaded_file'] != '') && file_exists($_SESSION['uploaded_file'])){
					unlink($_SESSION['uploaded_file']);	//removes the file from the system
					$_SESSION['uploaded_file'] = '';
				}
				//Removal ends...
		  		
		  		//Query to select upload data from upload history...
		  		$qry = "select file_name, date(upload_date), time(upload_date) from upload_history where email = '".$_SESSION['id']."'";
		  			  		
		  		$count = 0;
		  		//Execution of the qury...
		  		$result = mysqli_query($cn, $qry);
		  		while(($row = mysqli_fetch_array($result)) != null){
		  			$new_row[$count++] = $row;
		  		}
		  		
				mysqli_close($cn);

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
						<?php
							//Displays message and exits execution if there is no files to display...
							if($count == 0){
								echo 'No Uploaded Files to Show...';
								exit();
							}
						?>
						<b>Upload History</b><br>
					</div>
					<br />
					<div id='upload_history'>
						<table class='mytables'>
							<caption><em><b>::Last <?php echo $count>10?10:$count; ?> files uploaded by you::</b></em></caption>
							<tr>
								<th>File Name</th>
								<th>Upload Date</th>
								<th>Upload Time</th>
							</tr>
							<?php while($count > 0){ ?>
							<tr>
								<td><?php echo $new_row[$count - 1][0] ?></td>
								<td><?php echo $new_row[$count - 1][1] ?></td>
								<td><?php echo $new_row[$count - 1][2] ?></td>
							</tr>
							<?php $count--; } ?>
						</table>
					</div>
					<div class='button_holder'>
						<input type='button' value='Print' id='print_history' class='submit_button' onclick='print_history();' />
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