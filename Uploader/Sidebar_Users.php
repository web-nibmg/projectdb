			<?php
				//Redirects to Login page if someone tries to access some page without loggin in...
				if($_SESSION['id'] == ''){
					header('Location: '.$_SESSION['login'].'?flag=3');
					exit();
				}
				//Login checking ends...
			?>

			<!--  -----------------------Sidebar Starts---------------------- -->
			<div class='side_bar'>
				<div class='side_bar_inner'>
					<div class='side_bar_inner_inner'>
						<ul>
							<li><a href='<?php echo $_SESSION['home'] ?>'>Home</a></li>
							<li><a href='/Data_Portal/Backend/Uploader/File_Adder.php'>File Uploader</a></li>
							<li><a href='/Data_Portal/Backend/Uploader/Upload_History.php'>Upload History</a></li>
							<li><a href='/Data_Portal/Backend/Uploader/Change_Password.php'>Change Password</a></li>
						</ul>
					</div>
				</div>
			</div>
			<!--  -----------------------Sidebar Ends---------------------- -->
