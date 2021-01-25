<?php

$conn=mysqli_connect("localhost","root","","zomato");

$order_id=$_POST['order_id'];
$rating=$_POST['rating_number'];
$review=$_POST['review'];

$query="UPDATE orders SET rating=$rating,review='$review' WHERE order_id LIKE '$order_id'";

try{
	mysqli_query($conn,$query);
	$response=array('code'=>1,'message'=>'Rating submitted');
}catch(Exception $e){
	$response=array('code'=>0,'message'=>'Some error');
}

$response=json_encode($response);

print_r($response);


?>