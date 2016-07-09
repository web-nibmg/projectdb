<?php
	// Page name::	PMID_search.php
	// Protection from illegal usage starts
	session_start();
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
	//Adding Connection String ends

	//Receiving variables passed from the calling page...
	$search_PMID = filter_var(strip_tags(substr($_POST['search_item'],0, 15)), FILTER_SANITIZE_STRING);
	$search_PMID = mysql_escape_string($search_PMID); // Attack Prevention
	//End of reception...
		
	$result = array();
	$i = 0;
	
	if($search_PMID > 0){
		$qry = "SELECT DISTINCT PMID FROM cancer_mutation_repository WHERE PMID LIKE '$search_PMID'";
	}
	
	//echo '<br />name:: '.$search_PMID.'<br />eamil:: '.$edit_user_search_e.'<br />type:: '.$edit_user_search_t.'<br />';

	if(!$search_PMID){
		echo 'No Results To Show...';		
	}

	else{
		//echo "<br/> query::	$qry<br />";
		$result = mysqli_query($cn, $qry);

		$i = 0;
		echo "<table class='mytables'>
			<caption><b>::Search Result(s)::</b></caption>
			<tr>
				<th>PMID</th>
				<th>Edit</th>
				<th>Delete</th>
			</tr>";
		if(mysqli_num_rows($result)){
			while($row = mysqli_fetch_assoc($result)){
				echo "<tr><td id='edit".$i."0'><input type='text' name='PMID' disabled value='".$row['PMID']."'' /></td>					
					<td id='edit".$i."1'><input type='button' value='Edit' class='edit_button' onclick='return edit_PMID_ajax(this.parentNode.id);' /></td>
					<td id='edit".$i."2'><input type='button' value='Delete' class='cancel_button' onclick='return delete_PMID(this.parentNode.id);' /></td>
					</tr>";
				$i++;
			}
		}
		else{
			echo "<tr><td>No</td><td>Matches</td><td>Found</td></tr>";
		}
		echo '</table>';
	}
?>