<?php

session_start();

//1. Connect to DB

$conn=mysqli_connect("localhost","root","","zomato");

//2. Fetch data from HTML

//print_r($_POST);

$email=$_POST['email'];
$password=$_POST['password'];



//3. Check in database

$query="SELECT * FROM users WHERE email LIKE '$email' AND password LIKE '$password'";

$result=mysqli_query($conn,$query);

//$new_result=mysqli_fetch_array($result);

$rows=mysqli_num_rows($result);

echo $query;

//echo $rows;
//4. Tell result

if($rows==1){

	$_SESSION['is_user_loggedin']=1;

	$query1="SELECT * FROM users WHERE email LIKE '$email'";

	$result1=mysqli_query($conn,$query1);

	$result1=mysqli_fetch_array($result1);

	$_SESSION['user_id']=$result1['user_id'];

	$_SESSION['name']=$result1['name'];

	//echo $_SESSION['user_id'];
	//echo $_SESSION['name'];
	//echo "Welcome";
	header('Location: profile.php');
}else{
	//echo "Incorrect email/password";
	header('Location: login_form.php');
}


?>