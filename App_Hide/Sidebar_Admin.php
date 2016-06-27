			<?php
				//Redirects to Login page if someone tries to access some page without loggin in...
				if($_SESSION['id'] == ''){
					header('Location: '.$_SESSION['home'].'?flag=3');
					exit();
				}
				//Login checking ends...
			?>

			<!--  -----------------------Administrator Sidebar Starts---------------------- -->
			<div class='side_bar'>
				<div class='side_bar_inner'>
					<div class='side_bar_inner_inner'>
						<ul>
							<li><a href='<?php echo $_SESSION['home']; ?>'>Home</a></li>
							<li><a id='d_link' href='../App_Files/Download_Repository.php'>Download Repository</a></li>
							<li><a href='../App_Files/Edit_Repository.php'>Edit Repository</a></li>
							<li><a href='../App_Files/Edit_History.php'>Edit History</a></li>
						</ul>
					</div>
				</div>
			</div>
			<!--  -----------------------Administrator Sidebar Ends---------------------- -->
