<?php
	// Protection from illegal usage starts
	session_start();
	if($_SESSION['id'] == ''){
		header('Location: '.$_SESSION['login'].'?flag=3');
		exit();
	}
	// Protection from illegal usage ends

	//Adding Connection String
	if(!file_exists($_SESSION['connection'])){
		header('Location: '.$_SESSION['login'].'?flag=6');
		exit();
	}
	require($_SESSION['connection']);
	//Adding Connection String ends

	//Receiving variables passed from the calling page...
	$edit_user_search_n = filter_var(strip_tags(substr($_POST['search_name'],0, 50)), FILTER_SANITIZE_STRING);
	$edit_user_search_e = filter_var(strip_tags(substr($_POST['search_email'],0, 50)), FILTER_SANITIZE_STRING);
	$edit_user_search_t = filter_var(strip_tags(substr($_POST['search_type'],0, 1)), FILTER_SANITIZE_STRING);
	$edit_user_search_n = mysql_escape_string($edit_user_search_n); // Attack Prevention
	$edit_user_search_e = mysql_escape_string($edit_user_search_e); // Attack Prevention
	$edit_user_search_t = mysql_escape_string($edit_user_search_t); // Attack Prevention
	//End of reception...
	
	$qry1 = 'SELECT user_name, email, type FROM users WHERE stat = 1';
	$qry2 = 'SELECT user_name, email, type FROM users WHERE stat = 1 AND (';

	$i = 0;
	
	if($edit_user_search_n){
		$qry1 = $qry1 . ' AND ' . "user_name LIKE '%$edit_user_search_n%' ";
		$qry2 = $qry2 .  "user_name NOT LIKE '%$edit_user_search_n%' ";
		$i++;
	}
	
	if($edit_user_search_e){
		$qry1 = $qry1 . ' AND ' . "email LIKE '%$edit_user_search_e%' ";
		if($i > 0){
			$qry2 = $qry2 . ' OR ' . "email NOT LIKE '%$edit_user_search_e%' ";
		}
		else{
			$qry2 = $qry2 . "email NOT LIKE '%$edit_user_search_e%' ";
		}
		$i++;
		
	}

	if($edit_user_search_t){
		$qry1 = $qry1 . ' AND ' . "type = '$edit_user_search_t' ";
		if($i > 0){
			$qry2 = $qry2 . ' OR ' . "type <> '$edit_user_search_t' ";
		}
		else{
			$qry2 = $qry2 . "type <> '$edit_user_search_t' ";
		}
		$i++;
	}

	$qry1 = $qry1 . " ORDER BY user_name";
	$qry2 = $qry2 . ") ORDER BY user_name";

	//echo '<br />name:: '.$edit_user_search_n.'<br />eamil:: '.$edit_user_search_e.'<br />type:: '.$edit_user_search_t.'<br />';

	if(!$edit_user_search_n && !$edit_user_search_e && !$edit_user_search_t){
		$qry1 = "SELECT user_name, email, type FROM users WHERE stat = 1";
		$query = mysqli_query($cn, $qry1);
		
		if(mysqli_num_rows($query)){
			echo "<table class='mytables'>
			<caption><b>::List of Active Users::</b></caption>
			<tr>
				<th>User Name</th>
				<th>email</th>
				<th>Type</th>
				<th>Edit</th>
				<th>Delete</th>
				<th>Reset<br />Password</th>
			</tr>";
			while($row = mysqli_fetch_assoc($query)){
				echo "<tr><td id='edit".$i."0'><input type='text' name='user_name' disabled value='".$row['user_name']."'' /></td>
					<td id='edit".$i."1'><input type='text' name='email' disabled  value='".$row['email']."' /></td>
					<td id='edit".$i."2'><input type='text' name='type' disabled value='".$row['type']."' maxlength='1' size='1' /></td>
					<td id='edit".$i."3'><input type='button' value='Edit' class='edit_button' onclick='return edit_row(this.parentNode.id);' /></td>
					<td id='edit".$i."4'><input type='button' value='Delete' class='cancel_button' onclick='return delete_row(this.parentNode.id);' /></td>
					<td id='edit".$i."5'><input type='button' value='Reset' class='submit_button' onclick='return reset_password(this.parentNode.id);' /></td></tr>";
				$i++;
			}
			echo '</table>';
		}
		else{
			echo 'No Results To Show...';
		}
	}

	else{
		//echo "<br/> query::	$qry1<br />";
		$query = mysqli_query($cn, $qry1);

		$i = 0;
		echo "<table class='mytables'>
			<caption><b>::List of Active Users::</b></caption>
			<tr>
				<th>User Name</th>
				<th>email</th>
				<th>Type</th>
				<th>Edit</th>
				<th>Delete</th>
				<th>Reset<br />Password</th>
			</tr>";
		if(mysqli_num_rows($query)){
			
			while($row = mysqli_fetch_assoc($query)){
				echo "<tr><td id='edit".$i."0'><input type='text' name='user_name' disabled value='".$row['user_name']."'' /></td>
					<td id='edit".$i."1'><input type='text' name='email' disabled  value='".$row['email']."' /></td>
					<td id='edit".$i."2'><input type='text' name='type' disabled value='".$row['type']."' maxlength='1' size='1' /></td>
					<td id='edit".$i."3'><input type='button' value='Edit' class='edit_button' onclick='return edit_row(this.parentNode.id);' /></td>
					<td id='edit".$i."4'><input type='button' value='Delete' class='cancel_button' onclick='return delete_row(this.parentNode.id);' /></td>
					<td id='edit".$i."5'><input type='button' value='Reset' class='submit_button' onclick='return reset_password(this.parentNode.id);' /></td></tr>";
				$i++;
			}
		}
		else{
			echo "<tr><td>No</td><td>Matches</td><td>Found</td><td>For</td><td>The</td><td>Keyword(s)</td></tr>";
		}

		echo "<tr><td>=========================</td><td>=========================</td><td>==</td><td>====</td><td>=====</td><td>=====</td></tr>";

		//echo "<br/> query::	$qry2<br />";
		$query = mysqli_query($cn, $qry2);

		while($row = mysqli_fetch_assoc($query)){
			echo "<tr><td id='edit".$i."0'><input type='text' name='user_name' disabled value='".$row['user_name']."'' /></td>
				<td id='edit".$i."1'><input type='text' name='email' disabled  value='".$row['email']."' /></td>
				<td id='edit".$i."2'><input type='text' name='type' disabled value='".$row['type']."' maxlength='1' size='1' /></td>
				<td id='edit".$i."3'><input type='button' value='Edit' class='edit_button' onclick='return edit_row(this.parentNode.id);' /></td>
				<td id='edit".$i."4'><input type='button' value='Delete' class='cancel_button' onclick='return delete_row(this.parentNode.id);' /></td>
				<td id='edit".$i."5'><input type='button' value='Reset' class='submit_button' onclick='return reset_password(this.parentNode.id);' /></td></tr>";
			$i++;
		}

		echo '</table>';
	}
?>