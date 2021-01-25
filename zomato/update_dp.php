<?php

session_start();

$conn=mysqli_connect("localhost","root","","zomato");

print_r($_FILES['img_file']);

$filename=$_FILES['img_file']['name'];

$dp="http://localhost/zomato/images/".$filename;

$user_id=$_SESSION['user_id'];

$query="UPDATE users SET dp='$dp' WHERE user_id=$user_id";

try{
	mysqli_query($conn,$query);
	//echo '../images/'.$filename;
	move_uploaded_file($_FILES['img_file']['tmp_name'], 'images/'.$filename);
	header('Location:profile.php');
}catch(Exception $e){
	echo "Error occured";
}


?>