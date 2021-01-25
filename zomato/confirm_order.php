<?php

$conn=mysqli_connect("localhost","root","","zomato");

$order_id=$_GET['order_id'];
$amount=$_GET['amount'];

$query="UPDATE orders SET amount='$amount',status=1 WHERE order_id LIKE '$order_id'";

try{
	mysqli_query($conn,$query);
	header('Location:success.php?order_id='.$order_id);
}catch(Exeption $e){
	header('Location: order_details.php?order_id='.$order_id);
}






?>