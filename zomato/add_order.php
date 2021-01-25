<?php

session_start();

$conn=mysqli_connect("localhost","root","","zomato");

$r_id=$_POST['r_id'];

if($_POST['flag']==0){

	$order_id=uniqid();

	$user_id=$_SESSION['user_id'];

	$query="INSERT INTO orders (order_id,user_id,r_id,order_time,status) VALUES ('$order_id',$user_id,$r_id,current_timestamp(),0)";

	try{
		mysqli_query($conn,$query);

		//insert into order details table
		//id/order_id/menu_id

		$menu_id=$_POST['menu_id'];

		$query1="INSERT INTO order_details (id,order_id,menu_id) VALUES (NULL,'$order_id',$menu_id)";

		try{
			mysqli_query($conn,$query1);
			$response=array('response'=>1,'order_id'=>$order_id);

		}catch(Exeption $e){
			$response=array('response'=>-1);
		}

	}catch(Exeption $e){
		$response=array('response'=>0);

	}

}else{

	$order_id=$_POST['order_id'];
	$menu_id=$_POST['menu_id'];

	$query1="INSERT INTO order_details (id,order_id,menu_id) VALUES (NULL,'$order_id',$menu_id)";

		try{
			mysqli_query($conn,$query1);
			$response=array('response'=>1,'order_id'=>$order_id);

		}catch(Exeption $e){
			$response=array('response'=>-1);
		}

}

$response=json_encode($response);

print_r($response);





?>