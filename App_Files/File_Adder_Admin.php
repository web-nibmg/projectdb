<!DOCTYPE html>
<html>

	<head>

	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	    <title>File Adder</title>
	    <link href='../App_Hide/New.css' rel="stylesheet" type="text/css" />
	    <link href='../App_Hide/Table.css' rel="stylesheet" type="text/css" />
	    <script language='javascript' type='text/javascript' src='../App_Hide/urlreader.js'></script>
	    <script language='javascript' type='text/javascript' src='../jquery-2.1.4-uc.js'></script>
	    
	</head>

	<body onload='initial_display();'>

		<div id='wrapper'>

			<?php

				session_start();
				
				//Loacating Previous page starts
				$url = '';
				if(isset($_SERVER['HTTP_REFERER'])){
					$url = $_SERVER['HTTP_REFERER'];
				}
				//Loacating Previous page ends

				//Adding Connection String
				if(!file_exists('../App_Hide/Connect.php')){
					header('Location: ../App/Login.html?flag=6');
					exit();
				}
				require('../App_Hide/Connect.php');
				//Adding Connection String ends

				//Logging into the system...
				if(strpos($url, 'Login.html')){
				//Redirects to Login page if values are not passed or in case of improper attempt...
					if(($_POST['email'] == '') || ($_POST['pwd'] == '') || ($_POST['type'] == '')){
						header('Location: ../App/Login.html?flag=2');
						exit();
					}
					else{
				//Receive user inputs from "Login.html"...
						$email = htmlspecialchars($_POST['email']);
						$pwd = htmlspecialchars($_POST['pwd']);
						$type = htmlspecialchars($_POST['type']);
						
				//Redirects to user page if the user is not Administrator...
						if($type == 'U'){
							header('Location: ../App_Files/Home_Users.php?flag=5');
							exit();
						}

				//Query to collect user information from database...
						$qry = "select user_name, pswd, stat, type from users where email = '$email'";
						
						$result = '';
						$status = '2';
				//execution of the query...
						$result = mysqli_fetch_array(mysqli_query($cn, $qry));

				//redirects to "Login.html" if the query fails to execute...
						if(!$result){
							header('Location: ../App/Login.html?flag=5');
							exit();
						}
						
						$str = $result['pswd'];
						$status = $result['stat'];

				//Redirects to Login page in case of user-type other than administrator...
						if($type != 'A'){
							header('Location: ../App/Login.html?flag=5');
							mysqli_close($cn);
							exit();
						}

				//Redirects to Login page in case of password mismatch...
						if($pwd != $str){ 
							header('Location: ../App/Login.html?flag=1');
							mysqli_close($cn);
							exit();
						}

				//Redirects to Login page in case of Deleted User...
						else if($status == '0'){
								header('Location: ../App/Login.html?flag=6');
								mysqli_close($cn);
								exit();
						}

				//Sets the session variables if login is successful...
						else{
							$_SESSION['id'] = $email;
							$_SESSION['name'] = $result['user_name'];
							$_SESSION['header'] = '../App_Files/Header.php';
							$_SESSION['footer'] = '../App_Files/Footer.php';
							$_SESSION['logout_button'] = '../App_Hide/Logout_button.php';
							$_SESSION['login'] = '../App/Login.html';
							$_SESSION['home'] = '../App_Files/File_Adder_Admin.php';
							$_SESSION['side_bar'] = '../App_Hide/Sidebar_Admin.php';
							$_SESSION['connection'] = '../App_Hide/Connect.php';
						}
					}
				}

				//Redirects to Login page if someone tries to access some page without loggin in...
				else{
					if($_SESSION['id'] == ''){
						header('Location: '.$_SESSION['login'].'?flag=3');
						exit();
					}
				}

				$count = 0;
				$count_du = 0;

				//selects all active users except the logged in administrator...
				$qry = "select user_name,email,type from users where stat = 1 and email <> '".$_SESSION['id']."' order by user_name";
				
				//select all deleted users...
				$qry_du = "select user_name,email,type from users where stat = 0";
		  		
		  		$result = mysqli_query($cn, $qry);
		  		$result_du = mysqli_query($cn, $qry_du);
				
				//counts all active users except own id...
				while(($row[$count] = mysqli_fetch_array($result)) !=  null){
		  			$count++;
				}
				//counts all deleted users...
				while(($row_du[$count_du] = mysqli_fetch_array($result_du)) !=  null){
		  			$count_du++;
				}
				mysqli_close($cn);

				//Adding Header File...
				if(!file_exists($_SESSION['header'])){
					header('Location: '.$_SESSION['login'].'?flag=6');
					exit();
				}
				require($_SESSION['header']);
				//Header ends...

				//Adding Logout button...
				if(!file_exists($_SESSION['logout_button'])){
					header('Location: '.$_SESSION['login'].'?flag=6');
					exit();
				}
				require($_SESSION['logout_button']);
				//Logout ends...
			?>
		
			<!--  -----------------------Body Start---------------------- -->
			<div class='container'>
				<!--  -----------------------Sidebar Start---------------------- -->
				<?php
					if(!file_exists($_SESSION['side_bar'])){
						header('Location: '.$_SESSION['login'].'?flag=6');
						exit();
					}
					require($_SESSION['side_bar']);
				?>
				<!--  -----------------------Sidebar End---------------------- -->

				<!--  -----------------------Main Content Start---------------------- -->
				<div class='main_content'>
					<!--  -----------------------Message Start---------------------- -->
					<div id='messages'>
						<fieldset>
							<p id='message0' style='display:none' align='center'>User Control Panel...</p>
							<p id='message1' style='display:none' align='center'>The original id is missing...</p>
							<p id='message2' style='display:none' align='center'>Didn't find any action to perform...</p>
							<p id='message3' style='display:none' align='center'>Action Not Specified...</p>
							<p id='message4' style='display:none' align='center'>Database Error...<br>Unable to execute the query.</p>
							<p id='message5' style='display:none' align='center'>You Need To Have Administrator Privileges To Access The Page...</p>
							<p id='message6' style='display:none' align='center'>Deleted User.<br />Please contact the Administrator for further help...</p>
							<p id='message7' style='display:none' align='center'>Duplicate Entry.<br />Unable to Process...</p>
							<p id='message8' style='display:none' align='center'>Field values are not filled up...</p>
							<p id='message9' style='display:none' align='center'>Action Failed...</p>
							<p id='message10' style='display:none' align='center'>User Has Successfully Been Inserted...</p>
							<p id='message11' style='display:none' align='center'>User Information Has Successfully Been Edited...</p>
							<p id='message12' style='display:none' align='center'>User Has Successfully Been Deleted...</p>
							<p id='message13' style='display:none' align='center'>User-Password Has Successfully Been Reset...</p>
							<p id='message14' style='display:none' align='center'>User Has Successfully Been Reactivated...</p>
							<p id='message15' style='display:none' align='center'>Download Aborted.<br />Unable To Open The Repository...</p>
							<p id='message16' style='display:none' align='center'>Download Aborted.<br />Repository Doesn't Exist...</p>
							<p id='flag_2_message'></p>
						</fieldset>
					</div>
					<br/>
					<!--  -----------------------Message End---------------------- -->
					<!--  -----------------------Choice Radio Button Start---------------------- -->
					<div align='center'>
						<input type='radio' name='choice' id='choice_ie' value='Insert/Edit' />Insert/Edit/Delete
						<input type='radio' name='choice' id='choice_du' value='Deleted Users' />Deleted Users
					</div>
					<!--  -----------------------Choice Radio Button End---------------------- -->
					<br />
					<!--  -----------------------Insertion Table Start---------------------- -->
					<div style='display: none; overflow-x:auto;' id='add_user' align='center'>
						<table class='mytables'>
							<caption><b>::Insert New Users::</b></caption>
							<tr>
								<th>User Name</th>
								<th>email</th>
								<th>Type</th>
								<th>Add</th>
							</tr>
							<tr><td><input type='text' id='add0' name='user_name' /></td>
								<td><input type='email' id='add1' name='email' /></td>
								<td><select id='add2' name='type'><option value='U'>U</option><option value='A'>A</option></select></td>
								<td><input type='submit' value='Add' class='submit_button' onclick="return insert_row();" /></td>
							</tr>
						</table>
					</div>
					<!--  -----------------------Insertion Table End---------------------- -->
					<br/>
					<!--  -----------------------Updation Table Start---------------------- -->
					<div style='display: none' id='edit_user' align='center'>

					<!--  -----------------------edit_user search-box Starts---------------------- -->
						<div style='overflow-x:auto;'>
							<form id='edit_user_search_form' method='post'>
								<table class='mytables'>
									<caption><b>::Search Active Users::</b></caption>
									<tr>
										<th>User Name</th>
										<th>email</th>
										<th>Type</th>
										<th>Action</th>
									</tr>
									<tr>
										<td><input type="text" name="search_user_name" id="search_user_name" placeholder="Type Name" size="25"/></td>
										<td><input type="text" name="search_email" id="search_email" placeholder="email" size="25"/></td>
										<td><select name="search_type" id="search_type">
											<option value=''>select</option>
											<option value='U'>U</option>
											<option value='A'>A</option>
										</select></td>
										<td><input type="button" value="clear" id="clear" class='submit_button' onclick='clear_fields();' /></td>
									</tr>
								</table>
							</form>
							<br />
						</div>
					<!--  -----------------------edit_user search-box Ends---------------------- -->
						<br />				
					<!--  -----------------------edit_user search Table Start---------------------- -->
						<div id='edit_user_search_table' align='right'>
						</div>
					<!--  -----------------------edit_user search Table End---------------------- -->
					
					</div>				
					<!--  -----------------------Updation Table End---------------------- -->

					<!--  -----------------------Deleted User Table Start---------------------- -->
					<div style='display: none; overflow-x:auto;' id='deleted_users' align='center'>
					<!--  -----------------------deleted_user search-box Starts---------------------- -->
						<div style='overflow-x:auto;'>
							<form id="deleted_user_search_form" method="post">
								<table class='mytables'>
									<caption><b>::Search Deleted Users::</b></caption>
									<tr>
										<th>User Name</th>
										<th>email</th>
										<th>Type</th>
										<th>Action</th>
									</tr>
									<tr>
										<td><input type="text" name="du_search_user_name" id="du_search_user_name" placeholder="Type Name" size="25"/></td>
										<td><input type="text" name="du_search_email" id="du_search_email" placeholder="email" size="25"/></td>
										<td><select name="du_search_type" id="du_search_type">
											<option value=''>select</option>
											<option value='U'>U</option>
											<option value='A'>A</option>
										</select></td>
										<td><input type="button" value="clear" class='submit_button' id="clear" onclick='du_clear_fields();' /></td>
									</tr>
								</table>
							</form>
						</div>
					<!--  -----------------------deleted_user search Ends---------------------- -->
					
						<br />
					
					<!--  -----------------------deleted_user search Table Start---------------------- -->
						<div id='deleted_user_search_table' style='overflow-x:auto;'>
						</div>
					<!--  -----------------------deleted_user search Table End---------------------- -->
					
						<br />
					</div>
					<!--  -----------------------Deleted User Table End---------------------- -->
				</div>
				<!--  -----------------------All Request Submit Table Starts---------------------- -->
					<div id='deleted_user_submit_table' style='overflow-x:auto;'>
						<form action='../App_Hide/Edit_Delete.php' method='post' id='all_submit_form'>
							<input type='hidden' name='user_name' id='all_user_name' />
							<input type='hidden' name='email' id='all_email' />
							<input type='hidden' name='type' id='all_type' maxlength='1' size='1' />
							<input type='hidden' name='request_type' id='all_req_type' />
							<input type='hidden' name='old_id' id='all_old_id' />
						</form>
					</div>
				<!--  -----------------------All Request Submit Table Ends---------------------- -->

			</div>
			<!--  -----------------------Main Content End---------------------- -->
			
			<?php
				//Adding Footer...
				if(!file_exists($_SESSION['footer'])){
					header('Location: '.$_SESSION['login'].'?flag=6');
				}
				require($_SESSION['footer']);
				//Footer ends...
			?>

		</div>
		<!--  -----------------------Body End---------------------- -->

		<!--  -----------------------initial_display() starts---------------------- -->
		<script language='javascript' type='text/javascript'>

			//this function is called when the body is loaded...
			function initial_display(){
				$('#choice_ie').prop('checked', true);
				$('#add_user').show();
				$('#edit_user').show();
				$('#deleted_users').hide();
				ajax_search();
				du_ajax_search();
			}

		</script>
		<!--  -----------------------initial_display() ends---------------------- -->

		<!--  -----------------------Choice Button on click event starts---------------------- -->
		<script language='javascript' type='text/javascript'>

			//shows the selected one(i.e.- insert/edit or deleted users) only and hides the other...
			$("input[name$='choice']").on('change',function(){
				if(this.value == 'Insert/Edit'){
					$('#add_user').show();
					$('#edit_user').show();
					$('#deleted_users').hide();
				}
				else if(this.value == 'Deleted Users'){
					$('#add_user').hide();
					$('#edit_user').hide();
					$('#deleted_users').show();
				}
			});

		</script>
		<!--  -----------------------Choice Button on click event ends---------------------- -->

		<!--  -----------------------ready function starts---------------------- -->
		<script language='javascript' type='text/javascript'>

			$(document).ready(function(){

				//searches active using name...
				$("#search_user_name").keyup(function(event){
					event.preventDefault();
					ajax_search();
				});

				//searches active using email...
				$("#search_email").keyup(function(event){
					event.preventDefault();
					ajax_search();
				});

				//searches active using type...
				$("#search_type").on('change',function(event){
					event.preventDefault();
					ajax_search();
				});

				//searches deleted users using name...
				$("#du_search_user_name").keyup(function(event){
					event.preventDefault();
					du_ajax_search();
				});

				//searches deleted users using email...
				$("#du_search_email").keyup(function(event){
					event.preventDefault();
					du_ajax_search();
				});

				//searches deleted users using type...
				$("#du_search_type").on('change',function(event){
					event.preventDefault();
					du_ajax_search();
				});

			});

		</script>
		<!--  -----------------------ready function ends---------------------- -->

		<!--  -----------------------ajax_search() starts---------------------- -->
		<script language='javascript' type='text/javascript'>

			//collects the serach keywords from the active user search and passes on to the server...
			function ajax_search(){
				var edit_user_search_n = $("#search_user_name").val();
				var edit_user_search_e = $("#search_email").val();
				var edit_user_search_t = $("#search_type").val();

				$.post('../App_Hide/Edit_user_search.php', {search_name : edit_user_search_n, search_email : edit_user_search_e, search_type : edit_user_search_t}, function(data){
					$("#edit_user_search_table").html(data);
				});
			}
		</script>
		<!--  -----------------------ajax_search() edns---------------------- -->

		<!--  -----------------------du_ajax_search() starts---------------------- -->
		<script language='javascript' type='text/javascript'>

			//collects the serach keywords from the deleted user search and passes on to the server...
			function du_ajax_search(){
				var edit_user_search_n = $("#du_search_user_name").val();
				var edit_user_search_e = $("#du_search_email").val();
				var edit_user_search_t = $("#du_search_type").val();

				$.post('../App_Hide/Deleted_user_search.php', {search_name : edit_user_search_n, search_email : edit_user_search_e, search_type : edit_user_search_t}, function(data){
					$("#deleted_user_search_table").html(data);
				});
			}
		</script>
		<!--  -----------------------du_ajax_search() edns---------------------- -->

		<!--  -----------------------insert_row() starts---------------------- -->
		<script language='javascript' type='text/javascript'>

			//validates user inputs and passes on to the server if the user action is confirmed...
			function insert_row(){
				if(!$('#add0').val() || !$('#add1').val()){
					alert('You can not leave any fields blank...');
					return false;
				}
				else if(!(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test($('#add1').val()))){
					alert('email is not in format...');
					return false;
				}
				else{
					if(confirm('Do You Really Want To Add This User?')){
						$('#all_req_type').val('insert');
						$('#all_user_name').val($('#add0').val());
						$('#all_email').val($('#add1').val());
						$('#all_type').val($('#add2').val());
						$('#all_submit_form').submit();
					}
					else{
						return false;
					}
				}
			}
		</script>
		<!--  -----------------------insert_row() ends---------------------- -->

		<!--  -----------------------clear_fields() starts---------------------- -->
		<script language='javascript' type='text/javascript'>

			//clear fields in the active users search table...
			function clear_fields(){
				$('#search_user_name').val('');
				$('#search_email').val('');
				$("option:contains('select')",'#search_type').prop('selected',true);
				ajax_search();
			}

		</script>
		<!--  -----------------------clear_fields() ends---------------------- -->

		<!--  -----------------------du_clear_fields() starts---------------------- -->
		<script language='javascript' type='text/javascript'>

			//clear fields in the deleted users search table...
			function du_clear_fields(){
				$('#du_search_user_name').val('');
				$('#du_search_email').val('');
				$("option:contains('select')",'#du_search_type').prop('selected',true);
				du_ajax_search();
			}

		</script>
		<!--  -----------------------du_clear_fields() ends---------------------- -->

	    <!--  -----------------------edit_row() starts---------------------- -->
	    <script language='javascript' type='text/javascript'>

	    	function edit_row(parent_id){
	    		var i = 0;
	    		var temp_id = '';
				//enables the input fields and changes "edit" button into "submit"...
				if($('input','#' + parent_id).val() == 'Edit'){
					while(i <= <?php echo $count; ?>){
						temp_id = '#edit' + i;
						$('input', temp_id + 4).prop('disabled',true);
						$('input', temp_id + 5).prop('disabled',true);
						if('#' + parent_id != temp_id + 3){
							$('input', temp_id + 3).prop('disabled',true);
						}
						else{
							$('#all_old_id').val($('input', temp_id + 1).val());
							$('input', temp_id + 0).prop('disabled',false);
							$('input', temp_id + 1).prop('disabled',false);
							$('input', temp_id + 2).prop('disabled',false);
							$('input', temp_id + 3).val('Submit');
						}
						i++;
					}
				}

				//if the button value is already "submit", it validates the user input and submits them to the server on confirmation...
				else if($('input','#' + parent_id).val() == 'Submit'){
					var temp_id = parent_id.substring(0,parent_id.length-1);
					if(($('input','#' + temp_id + '0').val() == '') || ($('input','#' + temp_id + '1').val() == '')){
						alert('You Can Not Leave Any Fields Blank');
						return false;
					}
					if(!(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test($('input','#' + temp_id + '1').val()))){
						alert("Please Insert email id in proper format...");
						return false;
					}
					if($('input','#' + temp_id + '2').val() != 'A' && $('input','#' + temp_id + '2').val() != 'U'){
						alert('Please enter "Type" either \'U\' (for user) or \'A\' (for administrator)');
						return false;
					}
					if(confirm('Do You Really Want To Submit?')){
						$('#all_req_type').val('edit');
						$('#all_user_name').val($('input', '#' + temp_id + 0).val());
						$('#all_email').val($('input', '#' + temp_id + 1).val());
						$('#all_type').val($('input', '#' + temp_id + 2).val());
						$('#all_submit_form').submit();
					}
					else{
						return false;
					}
				}
				else{
					alert("Unlisted Option...");
				}
	    	}
	    </script>
	    <!--  -----------------------edit_row() ends---------------------- -->

	    <!--  -----------------------delete_row() starts---------------------- -->
	    <script language='javascript' type='text/javascript'>

	    	//deletes the user on confirmation...
	    	function delete_row(parent_id){
	    		var temp_id = parent_id.slice(0, parent_id.length-1);
				if(confirm('Do You Really Want To Delete This User?')){
					$('#all_req_type').val('delete');
					$('#all_user_name').val($('input', '#' + temp_id + 0).val());
					$('#all_email').val($('input', '#' + temp_id + 1).val());
					$('#all_type').val($('input', '#' + temp_id + 2).val());
					$('#all_submit_form').submit();
				}
				else{
					return false;
				}
			}

	    </script>
	    <!--  -----------------------delete_row() ends---------------------- -->

	    <!--  -----------------------reset_password() starts---------------------- -->
	    <script language='javascript' type='text/javascript'>

	    	//resets the password to the default on confirmation...
	    	function reset_password(parent_id){
	    		var temp_id = parent_id.slice(0, parent_id.length-1);
				if(confirm('Do You Really Want To Reset Password for This User?')){
					$('#all_req_type').val('reset');
					$('#all_user_name').val($('input', '#' + temp_id + 0).val());
					$('#all_email').val($('input', '#' + temp_id + 1).val());
					$('#all_type').val($('input', '#' + temp_id + 2).val());
					$('#all_submit_form').submit();
				}
				else{
					return false;
				}
			}
	    </script>
	    <!--  -----------------------reset_password() ends---------------------- -->

	    <!--  -----------------------deleted_user_row() starts---------------------- -->
	    <script language='javascript' type='text/javascript'>

	    	//restores a deleted user on confirmation...
	    	function deleted_user_row(parent_id){
	    		var temp_id = parent_id.slice(0, parent_id.length-1);
				if(confirm('Do You Really Want To Restore This User?')){
					$('#all_req_type').val('reactivate');
					$('#all_user_name').val($('input', '#' + temp_id + 0).val());
					$('#all_email').val($('input', '#' + temp_id + 1).val());
					$('#all_type').val($('input', '#' + temp_id + 2).val());
					$('#all_submit_form').submit();
				}
				else{
					return false;
				}
			}

	    </script>
	    <!--  -----------------------deleted_user_row() ends---------------------- -->

	    <!--  -----------------------displays the messages according to the flag value---------------------- -->
	    <script language='javascript' type='text/javascript'>

	    	document.getElementById('message' + flag).style.display = 'block';
			
			if(flag == 10){
	    		alert('User has successfully been inserted...');
	    	}
	    	else if(flag == 11){
	    		alert('User information has successfully been edited...');
	    	}
	    	else if(flag == 12){
	    		alert('User has successfully been deleted...');
	    	}
	    	else if(flag == 13){
	    		alert('User-Password has successfully been reset...');
	    	}
	    	else if(flag == 14){
	    		alert('User has successfully been reactivated...');
	    	}
	    	else if(flag == 15){
	    		alert('Download Aborted.\nUnable To Open The File...');
	    	}
	    	else if(flag == 16){
	    		alert('Download Aborted.\nUnable To Open The File...');
	    	}
			else{
				if(flag < -1 && flag > 16){
					document.getElementById('message9').style.display = 'block';
				}
			}

		</script>

		<script language='javascript' type='text/javascript'>
			if(flag_2 != '' && flag_2 > 0 && flag_2 < 5){
				document.getElementById('flag_2_message').style.display = 'block';

				if(flag_2 == 1){
					document.getElementById('flag_2_message').innerHTML = 'Failed to Send email...';
				}
			}
		</script>

	    <!--  -----------------------display message ends---------------------- -->
	
	</body>
</html>