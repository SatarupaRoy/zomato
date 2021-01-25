<?php

session_start();

$conn=mysqli_connect("localhost","root","","zomato");

$name=$_POST['name'];
$password=$_POST['password'];
$user_id=$_SESSION['user_id'];

$query="UPDATE users SET name='$name',password='$password' WHERE user_id='$user_id'";

try{
	mysqli_query($conn,$query);
	$_SESSION['name']=$name;
	header('Location:profile.php?msg=1');
}catch(Exception $e){
	header('Location:profile.php?msg=0');
}



?>