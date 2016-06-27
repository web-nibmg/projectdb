<!DOCTYPE html>
<html>

	<head>

	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	    <title>File Adder</title>
	    <link href='../App_Hide/New.css' rel="stylesheet" type="text/css" />
	    <link href='../App_Hide/Table.css' rel="stylesheet" type="text/css" />
	    <script language='javascript' type='text/javascript' src='../App_Hide/urlreader.js'></script>
	    <script language='javascript' type='text/javascript' src='../jquery-2.1.4-uc.js'></script>
	    <script language='javascript' type='text/javascript'>
	    	
	    	function clickbutton(){
		    	alert('Within the button');
		    	$('input','#clickme').prop('disabled',true);
		    }

		    function download_option(){
	    		if(confirm('Download Repository.txt?')){
	    			window.location = '../App_Hide/Download.php';
	    		}
	    		else {return false;}
	    	}

	    </script>
	    
				
	</head>

	<body onload='initial_display();'>

		<?php

			session_start();
			
			//Loacating Previous page start
			$url = '';
			if(isset($_SERVER['HTTP_REFERER'])){
				$url = $_SERVER['HTTP_REFERER'];
			}
			//Loacating Previous page end

			//Adding Connection String
			require('../App_Hide/Connect.php');
			//Adding Connection String ends

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
					
			//Redirects to user page if the user is not Administrator
					if($type == 'U'){
						header('Location: ../App_Files/File_Adder.php?flag=5');
					}

					$qry = "select user_name, pswd, stat from users where email = '$email' and type='A'"; // echo $qry;
					
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
						header('Location: ../App/Login.html?flag=1');
						mysqli_close($cn);
						exit();
					}

			//Redirects to Login page in case of Deleted User
					else if($status == '0'){
							header('Location: ../App/Login.html?flag=6');
							mysqli_close($cn);
							exit();
					}

			//Sets the session variables if login is successful
					else{
						$_SESSION['id'] = $email;
						$_SESSION['name'] = $result['user_name'];
					}
				}
			}

			//Redirects to Login page if someone tries to access some page without loggin in
			else{
				if($_SESSION['id'] == ''){
					header('Location: ../App/Login.html?flag=3');
					exit();
				}
			}

			$count = 0;
			$count_du = 0;
			$qry = "select user_name,email,type from users where stat = 1 and email <> '".$_SESSION['id']."' order by user_name";
			$qry_du = "select user_name,email,type from users where stat = 0";
	  		$result = mysqli_query($cn, $qry);
	  		$result_du = mysqli_query($cn, $qry_du);
			
			while(($row[$count] = mysqli_fetch_array($result)) !=  null){
	  			$count++;
			}
			while(($row_du[$count_du] = mysqli_fetch_array($result_du)) !=  null){
	  			$count_du++;
			}
			mysqli_close($cn);

		?>
	
		<!--  -----------------------Header Start---------------------- -->
		<div id='header'>
			<h1>File-Appender v1.0</h1>
		</div>
		<!--  -----------------------Header End---------------------- -->
		
		<?php require('../App_Hide/Logout_button.html'); ?>
		
		<!--  -----------------------Body Start---------------------- -->
		<div class='container'>
			<!--  -----------------------Sidebar Start---------------------- -->
			<div class='side_bar'>
				<ul>
					<li><a id='d_link' href='#' onclick='return download_option();'>Download Repository</a></li>
				</ul>
			</div>
			<!--  -----------------------Sidebar End---------------------- -->

			<div class='main_content'>
				<!--  -----------------------Main Content Start---------------------- -->
				<!--  -----------------------Message Start---------------------- -->
				<div id='messages' style="background-color: black">
					<p id='messageNull' style='display:none' align='center'>Field values are not filled up...</p>
					<p id='message0' style='display:none' align='center'>User Control Panel...</p>
					<p id='message1' style='display:none' align='center'>The original id is missing...</p>
					<p id='message2' style='display:none' align='center'>Didn't find any action to perform...</p>
					<p id='message3' style='display:none' align='center'>Action Not Specified...</p>
					<p id='message4' style='display:none' align='center'>Database Error...<br>Unable to execute the query.</p>
					<p id='message5' style='display:none' align='center'>You Need To Login As Administrator To Access The Page...</p>
					<p id='message6' style='display:none' align='center'>Deleted User.<br />Please contact the Administrator for further help...</p>
					<p id='message7' style='display:none' align='center'>Duplicate Entry.<br />Unable to Process...</p>
					<p id='message9' style='display:none' align='center'>Action Failed...</p>
					<input id='url' style='display:none' value="<?php echo $url; ?>" />
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
						<tr>
							<th>User Name</th>
							<th>email</th>
							<th>Type</th>
							<th>Add</th>
						</tr>
						<tr><form action='../App_Hide/Edit_Delete.php?' method='post'>
							<input type='hidden' value='insert' name='request_type' id='ins'>
							<td><input type='text' id='add0' name='user_name' /></td>
							<td><input type='email' id='add1' name='email' /></td>
							<td><select id='add<?php echo $i ?>2' name='type'><option value='U'>U</option><option value='A'>A</option></select></td>
							<td><input type='submit' value='Add' onclick="return insert_row();" /></td>
							</form>
						</tr>
					</table>
				</div>
				<!--  -----------------------Insertion Table End---------------------- -->
				<br/>
				<!--  -----------------------Updation Table Start---------------------- -->
				<div style='display: none' id='edit_user' align='center'>

				<!--  -----------------------edit_user search-box Starts---------------------- -->
					<div style='overflow-x:auto;'>
						<form id="edit_user_search_form" method="post">
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
									<td><input type="button" value="clear" id="clear" onclick='clear_fields();' /></td>
								</tr>
							</table>
						</form>
					</div>
				<!--  -----------------------edit_user search Ends---------------------- -->
					<br />				
				<!--  -----------------------edit_user search Table Start---------------------- -->
					<div id='edit_user_search_table' style='overflow-x:auto;'>
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
									<td><input type="button" value="clear" id="clear" onclick='du_clear_fields();' /></td>
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
					</form>
				</div>
			<!--  -----------------------All Request Submit Table Ends---------------------- -->

			<!--  -----------------------Main Content End---------------------- -->
		</div>
		<!--  -----------------------Body End---------------------- -->

		<!--  -----------------------initial_display() starts---------------------- -->
		<script language='javascript' type='text/javascript'>

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
				//$("#edit_user_search_table").slideUp();
				/*$("#button_find").click(function(event){
					event.preventDefault();
					search_ajax_way();
				});*/

				$("#search_user_name").keyup(function(event){
					event.preventDefault();
					ajax_search();
				});

				$("#search_email").keyup(function(event){
					event.preventDefault();
					ajax_search();
				});

				$("#search_type").on('change',function(event){
					event.preventDefault();
					ajax_search();
				});

				$("#du_search_user_name").keyup(function(event){
					event.preventDefault();
					du_ajax_search();
				});

				$("#du_search_email").keyup(function(event){
					event.preventDefault();
					du_ajax_search();
				});

				$("#du_search_type").on('change',function(event){
					event.preventDefault();
					du_ajax_search();
				});

			});

		</script>
		<!--  -----------------------ready function ends---------------------- -->

		<!--  -----------------------ajax_search() starts---------------------- -->
		<script language='javascript' type='text/javascript'>

			function ajax_search(){
				//$("#edit_user_search_table").show();
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

			function du_ajax_search(){
				//$("#edit_user_search_table").show();
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

			function insert_row(){
				if(!$('#add0').val() || !$('#add1').val()){
					alert('You can not leave any fields blank...');
					return false;
				}
				else{
					if(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test($('#add1').val())){
						return true;
					}
					else{
						alert('email is not in format...');
						return false;
					}
				}
			}
		</script>
		<!--  -----------------------insert_row() ends---------------------- -->

		<!--  -----------------------clear_fields() starts---------------------- -->
		<script language='javascript' type='text/javascript'>

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
	    		var submit_i = '';
	    		var temp_id = '';
				if($('input','#' + parent_id).val() == 'Edit'){
					while(i <= <?php echo $count; ?>){
						temp_id = '#edit' + i;
						$('input',temp_id + 4).prop('disabled',true);
						$('input',temp_id + 5).prop('disabled',true);
						if('#' + parent_id != temp_id + 3){
							$('input',temp_id + 3).prop('disabled',true);
						}
						else{
							$('#old_id_' + i).val($('input',temp_id + 1).val());//alert($('#old_id_' + i).val());
							$('input',temp_id + 0).prop('disabled',false);
							$('input',temp_id + 1).prop('disabled',false);
							$('input',temp_id + 2).prop('disabled',false);
							$('#ed_del_' + i).val('edit');//alert('new i= '+i);
							$('#old_id_' + i).val($('input',temp_id + 1).val());//alert($('#old_id_' + i).val());
							$('input',temp_id + 3).val('Submit');
							submit_i = i;
						}
						i++;
					}
				}
				else if($('input','#' + parent_id).val() == 'Submit'){
					var base_id = parent_id.substring(0,parent_id.length-1);
					if(($('input','#' + base_id + '0').val() == '') || ($('input','#' + base_id + '1').val() == '')){
						return false;
					}
					if(!(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test($('input','#' + base_id + '1').val()))){
						return false;
					}
					if($('input','#' + base_id + '2').val() != 'A' && $('input','#' + base_id + '2').val() != 'U'){
						alert('Please enter "Type" either \'U\' (for user) or \'A\' (for administrator)');
						//alert($('input','#' + base_id + '2').val());
						return false;
					}
					if(!confirm('Do You Really Want To Submit?')){
						return false;
					}
					
					$('input','#' + parent_id).prop('type','submit');
				}
				else{}
	    	}
	    </script>
	    <!--  -----------------------edit_row() ends---------------------- -->

	    <!--  -----------------------delete_row() starts---------------------- -->
	    <script language='javascript' type='text/javascript'>

	    	function delete_row(parent_id){//alert('parent_id:: ' + parent_id);
	    		var submit_i = parent_id.slice(4, parent_id.length-1);
	    		var temp_id = parent_id.slice(0, parent_id.length-1);
				if(confirm('Do You Really Want To Delete This User?')){
					$('#ed_del_' + submit_i).val('delete');
					$('input','#' + temp_id + 0).prop('disabled',false);
					$('input','#' + temp_id + 1).prop('disabled',false);
					$('input','#' + temp_id + 2).prop('disabled',false);
					$('input','#' + parent_id).prop('type','submit');
				}
				else{
					return false;
				}
	    	}
	    </script>
	    <!--  -----------------------delete_row() ends---------------------- -->

	    <!--  -----------------------reset_password() starts---------------------- -->
	    <script language='javascript' type='text/javascript'>

	    	function reset_password(parent_id){
	    		var submit_i = parent_id.slice(4, parent_id.length-1);
	    		var temp_id = parent_id.slice(0, parent_id.length-1);
				if(confirm('Do You Really Want To Reset Password for This User?')){
					$('#ed_del_' + submit_i).val('Reset');
					$('input','#' + temp_id + 0).prop('disabled',false);
					$('input','#' + temp_id + 1).prop('disabled',false);
					$('input','#' + temp_id + 2).prop('disabled',false);
					$('input','#' + parent_id).prop('type','submit');
				}
				else{
					return false;
				}
	    	}
	    </script>
	    <!--  -----------------------reset_password() ends---------------------- -->

	    <!--  -----------------------deleted_user_row() starts---------------------- -->
	    <script language='javascript' type='text/javascript'>

	    	function deleted_user_row(parent_id){//alert(parent_id);
	    		//var submit_i = parent_id.slice(2, parent_id.length-1);//alert(submit_i);
		    	var temp_id = parent_id.slice(0, parent_id.length-1);//alert(temp_id);
				if(confirm('Do You Really Want To Reset Password for This User?')){
					$('#all_req_type').val($('#' + temp_id + 4).val());
					$('#all_user_name').val($('input', '#' + temp_id + 0).val());
					$('#all_email').val($('input', '#' + temp_id + 1).val());
					$('#all_type').val($('input', '#' + temp_id + 2).val());
					//alert($('#all_user_name').val());
					//alert($('#all_email').val());
					//alert($('#all_type').val());
					//$('#all_type').prop('type', 'submit');
					//$('#all_type').click();
					$('#all_submit_form').submit();
				}
				else{
					return false;
				}
			}

	    </script>
	    <!--  -----------------------deleted_user_row() ends---------------------- -->

	    <script language='javascript' type='text/javascript'>

	    	function checkbutton(parent_id){
	    		var base_id = parent_id.substring(0,parent_id.length-1);
		    	alert('#'+base_id + 0);
		    	alert($('input','#' + base_id + '0').val());
		    	alert($('input','#' + base_id + '1').val());
		    }
			
	    </script>

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
	    	else{document.getElementById('message9').style.display = 'block';}
	
	    </script>
	
	</body>
</html>