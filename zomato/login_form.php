<?php

session_start();

if(!empty($_SESSION))
{
	header('Location: profile.php');
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Login Form</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css">
	<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</head>
<body class="bg-danger">

	<nav class="navbar">
		<h3 class="navbar-brand text-light">Zomato</h3>
	</nav>

	<div class="container">

		<div class="row mt-50">
			<div class="col-md-8">
				<h1 class="display-2 text-light text-md-center">Craving for food? Look nowhere else. Explore Now! erbergwgw</h1>
			</div>
			<div class="col-md-4">
				<div class="card">
					<div class="card-body">
						<?php

						if(!empty($_GET))
						{
							$message=$_GET['message'];
							if($message==1)
							{
								echo '<p style="color:green">Account Created. Login to proceed</p>';
							}
							else
							{
								echo '<p style="color:red">Some error occured. Try again</p>';
							}
						}

						

						?>
						<form action="login_validation.php" method="POST">
							<label>Email:</label><br>
							<input type="email" name="email" class="form-control"><br><br>

							<label>Password:</label><br>
							<input type="password" name="password" class="form-control"><br><br>

							<input type="submit" name="" value="Login" class="btn bg-danger btn-block btn-lg text-light">
						</form>

						<p>Not a member? <a href="#" data-toggle="modal" data-target="#exampleModal">Sign Up here</a></p>
					</div>
				</div>
			</div>
		</div>
		
	</div>

	<div class="modal" id="exampleModal" tabindex="-1" role="dialog">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Register Here</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <form action="reg_validation.php" method="POST">
	        	<label>Name:</label><br>
	        	<input type="text" name="name" class="form-control"><br><br>

	        	<label>Email:</label><br>
	        	<input type="email" name="email" class="form-control"><br><br>

	        	<label>Password:</label><br>
	        	<input type="password" name="password" class="form-control"><br><br>

	        	<input type="submit" name="" value="Sign Up" class="btn btn-danger">
	        </form>
	      </div>
	    </div>
	  </div>
	</div>

</body>
</html>