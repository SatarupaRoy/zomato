<?php

$conn=mysqli_connect("localhost","root","","zomato");

session_start();

$name=$_POST['name'];
$email=$_POST['email'];
$password=$_POST['password'];

$query="INSERT INTO users (user_id,name,email,password,dp) VALUES (NULL, '$name','$email','$password','https://previews.123rf.com/images/metelsky/metelsky1809/metelsky180900233/109815470-man-avatar-profile-male-face-icon-vector-illustration-.jpg')";

try{
	//check user already exists or not
	mysqli_query($conn,$query);
	header('Location:login_form.php?message=1');

}catch(Exception $e){
	header('Location:login_form.php?message=0');
}

?>