<!DOCTYPE html>
<html>

	<head>

	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	    <title>Edit History</title>
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
					header('Location: ../App/Login.html?flag=3');
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

				//Sets the page number...
				$pageNum = 1;
				if(isset($_GET['pageNum'])){
  					$pageNum = $_GET['pageNum'];
  				}
  				//page number ends...

				//Number of rows on each page...
				$rows_per_page = 3;
				
				//Query to count total records from the database...
		  		$qry = "select count(*) FROM edit_history";

				$total_records = 0;
				$total_records = mysqli_fetch_array(mysqli_query($cn, $qry)) or header('Location: '.$_SESSION['home'].'?flag=4');
		  		
		  		//Total number of pages...
		  		$total_pages = ceil($total_records[0] / $rows_per_page);
		  		
		  		//This is the off-set from the start of the MySQL result...
		  		//Page 1 starts from the end of the table and the last page ends at the beginning of the table...
		  		$start = $total_records[0] - ($pageNum * $rows_per_page);
		  		if($start < 0){
		  			$rows_per_page = $rows_per_page + $start;
		  			$start = 0;
		  		}

		  		// print the link to access each page
				$self = $_SERVER['PHP_SELF'];
		  		
		  		//Query to fetch information from the database...
		  		$qry = "select user_name, edit_history.email, action, original_PMID, replaced_PMID, date(edit_date) as date_of_edit, time(edit_date) as time_of_edit, edit_date from users, edit_history where users.email = edit_history.email limit $start, $rows_per_page";
		  		
		  		$count = 0;
		  		//Execution of the qury...
		  		$result = mysqli_query($cn, $qry) or header('Location: '.$_SESSION['home'].'?flag=4');
		  		while(($row = mysqli_fetch_array($result)) != null){
		  			$new_row[$count++] = $row;
		  		}
		  		
				mysqli_close($cn);


				// creating previous and next link
				// plus the link to go straight to the first and last page

				if($pageNum > 1)
				{
					$page  = $pageNum - 1;
					$prev  = " <a href=\"$self?pageNum=$page\">[Prev]</a> ";

					$first = " <a href=\"$self?pageNum=1\">[First Page]</a> ";
				}
				else
				{
					$prev  = '&nbsp;'; // we're on page one, don't print previous link
					$first = '&nbsp;'; // nor the first page link
				}

				if($pageNum < $total_pages)
				{
					$page = $pageNum + 1;
					$next = " <a href=\"$self?pageNum=$page\">[Next]</a> ";

					$last = " <a href=\"$self?pageNum=$total_pages\">[Last Page]</a> ";
				}
				else
				{
					$next = '&nbsp;'; // we're on the last page, don't print next link
					$last = '&nbsp;'; // nor the last page link
				}

				// print the navigation link
				//echo $first . $prev . " Showing page $pageNum of $total_pages pages " . $next . $last;

				//and close the database connection
				//include '../library/closedb.php';


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
						<b>Edit History</b><br />
					</div>
					<div id='upload_history'>
						<br />
						<?php
							//Displays message and exits execution if there is no files to display...
							//echo '<br />'.$total_records[0].'<br />';
							if($count == 0){
								echo 'No Edit History to Show...';
								exit();
							}
							else{ ?>
								<div id='upload_history_table'>
									<table class='mytables'>
										<caption><em><b>::Editing Logs of Repository By The Users::</b></em></caption>
										<tr>
											<th>User Name</th>
											<th>email id</th>
											<th>Action</th>
											<th>Original PMID</th>
											<th>Replaced PMID</th>
											<th>Edit Date</th>
											<th>Edit Time</th>
										</tr>
							<?php
								$i = $count;
								while($i > 0){
									echo "<tr>
											<td>". $new_row[$i - 1][0]."</td>
											<td>". $new_row[$i - 1][1]."</td>
											<td>". $new_row[$i - 1][2]."</td>
											<td>". $new_row[$i - 1][3]."</td>
											<td>". $new_row[$i - 1][4]."</td>
											<td>". $new_row[$i - 1][5]."</td>
											<td>". $new_row[$i - 1][6]."</td>
										</tr>";
									$i--;
								}
								
								//Printing the Navigation buttons...
								echo "<tr>
										<td> === </td>
										<td> === </td>
										<td> === </td>
										<td> === </td>
										<td> === </td>
										<td> === </td>
										<td> === </td>
									</tr>
									<tr>
										<td>". $first."</td>
										<td>". $prev."</td>
										<td>-". ''."</td>
										<td>". " page $pageNum of $total_pages"."</td>
										<td>-". ''."</td>
										<td>". $next."</td>
										<td>". $last."</td>
									</tr>";
							?>
								</table>
								</div>
								<div class='button_holder'>
									<input type='button' value='Print' id='print_history' class='submit_button' onclick='print_history();' />
								</div>
							<?php } ?>
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