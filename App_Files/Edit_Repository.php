<!DOCTYPE html>
<html>

	<head>

	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	    <title>Edit Repository</title>
	    <link href='../App_Hide/New.css' rel="stylesheet" type="text/css" />
	    <link href='../App_Hide/Table.css' rel="stylesheet" type="text/css" />
	    <script language='javascript' type='text/javascript' src='../App_Hide/urlreader.js'></script>
	    <script language='javascript' type='text/javascript' src='../jquery-2.1.4-uc.js'></script>

	    <!--  -----------------------clear_fields() starts---------------------- -->
		<script language='javascript' type='text/javascript'>

			//clear fields in the active users search table...
			function clear_fields(){
				$('#search_PMID').val('');
			}

		</script>
		<!--  -----------------------clear_fields() ends---------------------- -->

	<head>

	<body onload='load_body();'>

		<div id='wrapper'>

			<?php

				session_start();

				//Redirects to Login page if someone tries to access some page without loggin in
				if($_SESSION['id'] == ''){
					header('Location: '.$_SESSION['header'].'?flag=3');
					exit();
				}
				//Redirection Ends...

				//Common part for every page
				
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

				//Receives the PMID value if it is set...
				if(isset($_POST['PMID'])){
					$PMID = mysql_escape_string(filter_var(strip_tags(substr($_POST['PMID'],0, 15)), FILTER_SANITIZE_STRING));
				}
				else{
					$PMID = 'none';
				}
				
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
				
				<!--  -----------------------Main Content Start---------------------- -->
				<div class='main_content' align='center'>

					<!--  -----------------------Message Start---------------------- -->
					<div id='messages'>
						<fieldset>
							<p id='display_message' align='center'></p>
						</fieldset>
					</div>
					<br/>
					<!--  -----------------------Message End---------------------- -->
				
					<!--  -----------------------edit_by_pmid_box Starts---------------------- -->
					<div id='edit_by_pmid' align='center'>
						<div style='overflow-x:auto;'>
							<table class='mytables'>
								<caption><b>::Search PMID::</b></caption>
								<input type="hidden" name="action" size="10" value='search' />
								<tr>
									<th>PMID</th>
									<th>Action</th>
								</tr>
								<tr>
									<td><input type="text" name="search_PMID" id="search_PMID" placeholder="Type PMID" size="15"/></td>
									<td><input type="button" value="Search" id="search" class='submit_button' onclick='ajax_search();' /></td>
								</tr>
							</table>
							<p id='not_found_message' style='color:red;'></p>
							<br />
						</div>
						<!--  -----------------------edit_by_pmid_box Ends---------------------- -->						
						<!--  -----------------------edit_by_pmid_table Start---------------------- -->
						<div id='edit_by_pmid_table' style='overflow-x:auto; display:none'>							
						</div>
						<!--  -----------------------edit_by_pmid_table Ends---------------------- -->

						<!--  -----------------------edit_PMID_search_table Start---------------------- -->
						<div id='edit_PMID_search_table' align='center'>
						</div>
						<!--  -----------------------edit_PMID_search_table End---------------------- -->
						
						<!--  -----------------------All Request Submit Table Starts---------------------- -->
						<div id='all_request_submit_table' style='display:hidden' >
							<input type='hidden' name='PMID_transit' id='PMID_transit' val='' />
						</div>
					<!--  -----------------------All Request Submit Table Ends---------------------- -->

					</div>
					<!--  -----------------------Main Content Ends---------------------- -->
				</div>
				<!--  -----------------------Container Ends---------------------- -->
			</div>
			<!--  -----------------------Wrapper Ends---------------------- -->
			<?php
				//Adding Footer...
				if(!file_exists($_SESSION['footer'])){
					header('Location: '.$_SESSION['login'].'?flag=6');
				}
				require($_SESSION['footer']);
				//Footer ends...
			?>

		</div>

		<!--  -----------------------load_body() starts---------------------- -->
		<script language='javascript' type='text/javascript'>

			//clear fields in the active users search table...
			function load_body(){
				var PMID = '<?php echo $PMID; ?>';
				if(flag == '' && PMID == 'none'){
					document.getElementById('display_message').innerHTML = ':: Edit Repository ::';
				}
				else if(PMID == 'not_found'){
					document.getElementById('display_message').innerHTML = 'PMID Not Found in The Repository...';
				}
				else if(PMID == 'PMID_null'){
					document.getElementById('display_message').innerHTML = 'You Can Not Enter a Null PMID...';
				}
				else if(flag == 1){
					document.getElementById('display_message').innerHTML = 'No Action Has Been Set...';
				}
				else if(flag == 2){
					document.getElementById('display_message').innerHTML = 'No PMID Has Not Been Set...';
				}
				else if(flag == 3){
					document.getElementById('display_message').innerHTML = 'Original PMID Has Not Been Set...';
				}
				else if(flag == 4){
					document.getElementById('display_message').innerHTML = 'Search Aborted...<br />Unable to Open Repository.';
				}
				else if(flag == 5){
					document.getElementById('display_message').innerHTML = 'Repository Has Successfully Been Edited...';
				}
				else if(flag == 6){
					document.getElementById('display_message').innerHTML = 'Edit Aborted...<br />Unable to Open Repository.';
				}
				else if(flag == 7){
					document.getElementById('display_message').innerHTML = 'Edit Aborted...<br />Unable to Open Dump-File.';
				}
				else if(flag == 8){
					document.getElementById('display_message').innerHTML = 'Edit Aborted...<br />Unable to Back-Up Repository.';
				}
				else if(flag == 9){
					document.getElementById('display_message').innerHTML = 'Edit Aborted...<br />Unable to Update Repository.';
				}
				else if(flag == 10){
					document.getElementById('display_message').innerHTML = 'Edit Aborted...<br />Unable Lock The Repository.';
				}
				else if(flag == 11){
					document.getElementById('display_message').innerHTML = 'Edit Aborted...<br />Repository Does Not Exist.';
				}
				else if(flag == 12){
					document.getElementById('display_message').innerHTML = 'Edit Aborted...<br />Unable to Insert Into The Log.';
				}
				else if(flag == 13){
					document.getElementById('display_message').innerHTML = 'All Data Associated With The Given PMID,<br />Has Successfully Been Deleted...';
				}
				else if(flag == 14){
					document.getElementById('display_message').innerHTML = 'Delete Aborted...<br />Unable to Open Repository.';
				}
				else if(flag == 15){
					document.getElementById('display_message').innerHTML = 'Delete Aborted...<br />Unable to Open Dump-File.';
				}
				else if(flag == 16){
					document.getElementById('display_message').innerHTML = 'Delete Aborted...<br />Unable to Back-Up Repository.';
				}
				else if(flag == 17){
					document.getElementById('display_message').innerHTML = 'Delete Aborted...<br />Unable to Update Repository.';
				}
				else if(flag == 18){
					document.getElementById('display_message').innerHTML = 'Delete Aborted...<br />Unable Lock The Repository.';
				}
				else if(flag == 19){
					document.getElementById('display_message').innerHTML = 'Delete Aborted...<br />Repository Does Not Exist.';
				}
				else if(flag == 20){
					document.getElementById('display_message').innerHTML = 'Delete Aborted...<br />Unable to Insert Into The Log.';
				}
				else if(flag == 21){
					document.getElementById('display_message').innerHTML = 'Repository Has Successfully Been Deleted...';
				}
				else if(PMID != ''){
					document.getElementById('display_message').innerHTML = 'The Requested PMID Has Been Found...';
					document.getElementById('edit_by_pmid_table').style.display = 'block';
				}
			}

		</script>
		<!--  -----------------------load_body() ends---------------------- -->

		<!--  -----------------------search_PMID_validation starts---------------------- -->
		<script language='javascript' type='text/javascript'>

		function search_PMID_validation(PMID){
			if(!PMID){
				alert('You can not leave the search field blank.\nPlease try again...');
				return false;
			}

			else if(PMID.length > 15){
				alert('Maximum length of PMID can be 15 characters.\nPlease try again...');
				return false;
			}

			else{
				return true;
			}
		}

		</script>
		<!--  -----------------------ready function ends---------------------- -->

		<!--  -----------------------ajax_search() starts---------------------- -->
		<script language='javascript' type='text/javascript'>

			//collects the serach keywords from the active user search and passes on to the server...
			function ajax_search(){
				var search_PMID = $("#search_PMID").val();
				//search_PMID = filter_var($("#search_PMID").val(), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
				
				if(!search_PMID_validation(search_PMID)){
					return false;
				}
				
				$.post('../App_Hide/PMID_search.php', {search_item : search_PMID}, function(data){
					$("#edit_PMID_search_table").html(data);
				});
			}
		</script>
		<!--  -----------------------ajax_search() edns---------------------- -->

		<!--  -----------------------edit_delete_ajax() starts---------------------- -->
		<script language='javascript' type='text/javascript'>

			//collects the serach keywords from the active user search and passes on to the server...
			function edit_delete_ajax(){
				$.post('../App_Hide/PMID_edit_delete.php', {request_type : $('#action_type').val(), PMID : $('#PMID_transit').val(), PMID_final : $('#PMID_final_transit').val()}, function(){
					alert('Action Performed');
				});
			}
		</script>
		<!--  -----------------------edit_delete_ajax() edns---------------------- -->


		</script>
		<!--  -----------------------submit_search() ends---------------------- -->

		<!--  -----------------------edit_PMID() starts---------------------- -->
		<script language='javascript' type='text/javascript'>

	    	function edit_PMID_ajax(parent_id){
	    		var i = 0;
	    		var temp_PMID = '';
	    		var temp_PMID_final = '';
	    		var temp_ID = '';
				//enables the input fields and changes "edit" button into "submit"...
				if($('input', '#' + parent_id).val() == 'Edit'){
					temp_ID = parent_id.slice(0, parent_id.length-1);
					//$('#all_request_old_PMID').val($('input', temp_PMID + 0).val());
					$('input', '#' + temp_ID + 2).prop('disabled',true);
					temp_PMID = $('input', '#' + temp_ID + 0).val();
					$('input', '#' + temp_ID + 0).prop('disabled',false);
					$('input', '#' + temp_ID + 1).val('Submit');
					$('#PMID_transit').val(temp_PMID);
					return true;
				}

				//if the button value is already "submit", it validates the user input and submits them to the server on confirmation...
				else if($('input', '#' + parent_id).val() == 'Submit'){
					temp_ID = parent_id.substring(0,parent_id.length-1);
					temp_PMID_final = filter_var($('input', '#' + temp_ID + 0).val(), FILTER_SANITIZE_STRING,
           FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
					if(temp_PMID_final == ''){
						alert('You Can Not Leave PMID Field Blank');
						return false;
					}
					if(confirm('Do You Really Want To Submit?')){
						$.post('../App_Hide/PMID_edit_delete.php', {request_type : 'edit', PMID : $('#PMID_transit').val(), PMID_final : temp_PMID_final}, function(){
							alert('Edit Performed');
							$('input', '#' + temp_ID + 0).prop('disabled', true);
						});
					}
					else{
						alert('Edit Cancelled...');
						$('input', '#' + temp_ID + 0).val($('#PMID_transit').val());
						$('input', '#' + temp_ID + 0).prop('disabled', true);
						return false;
					}
				}
				else{
					alert('Unlisted Option...');
					return false;
				}				
	    	}
		</script>
		<!--  -----------------------edit_PMID() ends---------------------- -->

	    <!--  -----------------------delete_PMID() starts---------------------- -->
	    <script language='javascript' type='text/javascript'>

	    	//deletes the entries asssociated with the given PMID on confirmation...
	    	function delete_PMID(parent_id){
	    		var i = 0;
	    		var temp_PMID = '';
	    		var temp_ID = '';
				//enables the input fields and changes "edit" button into "submit"...
				if($('input', '#' + parent_id).val() == 'Delete'){
					temp_ID = parent_id.slice(0, parent_id.length-1);
					temp_PMID = $('input', '#' + temp_ID + 0).val();
					if(confirm('Do You Really Want To Submit?')){
						$.post('../App_Hide/PMID_edit_delete.php', {request_type : 'delete', PMID : temp_PMID}, function(){
							alert('Deletion Performed');
						});
						return true;
					}
					else{
						alert('Deletion Cancelled...')
						return false;
					}
				}

				else{
					alert('Unlisted Option...');
					return false;
				}
			}

	    </script>
	    <!--  -----------------------delete_PMID() ends---------------------- -->

		<!--  -----------------------Body Ends---------------------- -->

	</body>

</html>