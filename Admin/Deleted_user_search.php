<?php
	
	session_start();
	
	// Protection from illegal usage starts
	if($_SESSION['id'] == ''){
		header('Location: '.$_SESSION['login'].'?flag=3');
		exit();
	}
	// Protection from illegal usage ends

	//Adding Connection String
	if(!file_exists($_SESSION['path'].$_SESSION['connection'])){
		header('Location: '.$_SESSION['login'].'?flag=6');
		exit();
	}
	require($_SESSION['path'].$_SESSION['connection']);
	//Connection String ends

	//Receiving variables passed from the calling page...
	$deleted_user_search_n = filter_var(strip_tags(substr($_POST['search_name'],0, 50)), FILTER_SANITIZE_STRING);
	$deleted_user_search_e = filter_var(strip_tags(substr($_POST['search_email'],0, 50)), FILTER_SANITIZE_STRING);
	$deleted_user_search_t = filter_var(strip_tags(substr($_POST['search_type'],0, 1)), FILTER_SANITIZE_STRING);
	$deleted_user_search_n = mysql_escape_string($deleted_user_search_n); // Attack Prevention
	$deleted_user_search_e = mysql_escape_string($deleted_user_search_e); // Attack Prevention
	$deleted_user_search_t = mysql_escape_string($deleted_user_search_t); // Attack Prevention
	//End of reception...
	
	//Start of the search string...
	$qry1 = 'SELECT user_name, email, type FROM users WHERE stat = 0';
	//Start of the List-display string...
	$qry2 = 'SELECT user_name, email, type FROM users WHERE stat = 0 AND (';

	$i = 0;
	
	//Searching with name...
	if($deleted_user_search_n){
		$qry1 = $qry1 . ' AND ' . "user_name LIKE '%$deleted_user_search_n%' ";
		$qry2 = $qry2 .  "user_name NOT LIKE '%$deleted_user_search_n%' ";
		$i++;
	}
	
	//Searching with email...
	if($deleted_user_search_e){
		$qry1 = $qry1 . ' AND ' . "email LIKE '%$deleted_user_search_e%' ";
		if($i > 0){
			$qry2 = $qry2 . ' OR ' . "email NOT LIKE '%$deleted_user_search_e%' ";
		}
		else{
			$qry2 = $qry2 . "email NOT LIKE '%$deleted_user_search_e%' ";
		}
		$i++;
		
	}

	//Searching with type...
	if($deleted_user_search_t){
		$qry1 = $qry1 . ' AND ' . "type = '$deleted_user_search_t' ";
		if($i > 0){
			$qry2 = $qry2 . ' OR ' . "type <> '$deleted_user_search_t' ";
		}
		else{
			$qry2 = $qry2 . "type <> '$deleted_user_search_t' ";
		}
		$i++;
	}

	//End of Search string...
	$qry1 = $qry1 . " ORDER BY user_name";
	
	//End of List-display string...
	$qry2 = $qry2 . ") ORDER BY user_name";

	//echo '<br />name:: '.$deleted_user_search_n.'<br />eamil:: '.$deleted_user_search_e.'<br />type:: '.$deleted_user_search_t.'<br />';

	if(!$deleted_user_search_n && !$deleted_user_search_e && !$deleted_user_search_t){
		//selects all if no keywords are inserted...
		$qry1 = "SELECT user_name, email, type FROM users WHERE stat = 0";
		$query = mysqli_query($cn, $qry1);
		
		//creation of table...
		if(mysqli_num_rows($query)){
			echo "<table class='mytables'>
					<caption><b>::List of Deleted Users::</b></caption>
					<tr>
						<th>User Name</th>
						<th>email</th>
						<th>Type</th>
						<th>Action</th>
					</tr>";
			while($row = mysqli_fetch_assoc($query)){
				echo "<tr><td id='du".$i."0'><input type='text' name='user_name' disabled value='".$row['user_name']."' /></td>
					<td id='du".$i."1'><input type='text' name='email' disabled  value='".$row['email']."' /></td>
					<td id='du".$i."2'><input type='text' name='type' disabled value='".$row['type']."' maxlength='1' size='1' /></td>
					<td id='du".$i."3'><input type='button' value='Reactivate' class='cancel_button' onclick='return deleted_user_row(this.parentNode.id);' /></td>";
				$i++;
			}
			echo '</table>';
		}
		else{
			echo 'No Results To Show...';
		}
	}

	else{
		//Query for the case where at least one of the keywords are entered...
		$query = mysqli_query($cn, $qry1);

		$string = '';
		$i = 0;
		//creation of table...
		echo "<table class='mytables'>
				<caption><b>::List of Deleted Users::</b></caption>
				<tr>
					<th>User Name</th>
					<th>email</th>
					<th>Type</th>
					<th>Action</th>
				</tr>";
		if(mysqli_num_rows($query)){
			//creates rows if results are found...
			while($row = mysqli_fetch_assoc($query)){
				echo "<tr><td id='du".$i."0'><input type='text' name='user_name' disabled value='".$row['user_name']."' /></td>
					<td id='du".$i."1'><input type='text' name='email' disabled  value='".$row['email']."' /></td>
					<td id='du".$i."2'><input type='text' name='type' disabled value='".$row['type']."' maxlength='1' size='1' /></td>
					<td id='du".$i."3'><input type='button' value='Reactivate' class='cancel_button' onclick='return deleted_user_row(this.parentNode.id);' /></td>";
				$i++;
			}
		}
		else{
			//shows "No matches found" while no results could be found...
			$string = "No matches found!";
			echo "<tr><td>No</td><td>Suitable</td><td>Matches</td><td>Found</td></tr>";
		}

		echo "<tr><td>=========================</td><td>=========================</td><td>==</td><td>====</td></tr>";


		//query to get the rest of the table...
		$query = mysqli_query($cn, $qry2);

		while($row = mysqli_fetch_assoc($query)){
			echo "<tr><td id='du".$i."0'><input type='text' name='user_name' disabled value='".$row['user_name']."' /></td>
				<td id='du".$i."1'><input type='text' name='email' disabled  value='".$row['email']."' /></td>
				<td id='du".$i."2'><input type='text' name='type' disabled value='".$row['type']."' maxlength='1' size='1' /></td>
				<td id='du".$i."3'><input type='button' value='Reactivate' class='cancel_button' onclick='return deleted_user_row(this.parentNode.id);' /></td>";
			$i++;
		}

		echo '</table>';
	}
?>