<?php

session_start();

$conn=mysqli_connect("localhost","root","","zomato");

//$query4="SELECT * from restaurants r JOIN admin a WHERE r.r_id=a.admin_id ";


$query4="SELECT r_id from restaurants";

$result4=mysqli_query($conn,$query4);

$row4=mysqli_fetch_array($result4);
$r_id=$row4['r_id'];

$query="SELECT * FROM orders WHERE r_id LIKE '$r_id' AND status=1";

$result=mysqli_query($conn,$query);
//echo $query;

$pending=0;
$delivered=0;
$total=0;

$amount=0;
$counter=0;
while($row=mysqli_fetch_array($result))
{
	$amount=$amount + $row['amount'];
	 

	if($row['delivery_status']==0)
	{
		$pending++;
	}else{
		$total=$total + $row['rating'];
		$counter++;
		$delivered++;
	}

}

$rating=$total/$counter;


if(empty($_SESSION))
{
	header('Location: login_admin.php');
}

$admin_id=$_SESSION['admin_id'];

$query3="SELECT * FROM admin WHERE admin_id='$admin_id'";

$result3=mysqli_query($conn,$query3);

$result3=mysqli_fetch_array($result3);


?>

<!DOCTYPE html>
<html>
<head>
	<title>Hi Admin</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</head>
<style type="text/css">
	input[type="checkbox"]{
  width: 20px; /*Desired width*/
  height: 20px; /*Desired height*/
}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$('.order_id').click(function(){
			var order_id=$(this).text();
			//ajax call into order_details 
			$.ajax({
				url:'fetch_order_details.php',
				type:'POST',
				data:{'order_id':order_id},
				success:function(data){
					data=JSON.parse(data);

					$('#display_menu').html('');

					$('#display_menu').append('<table class="table"><tr><th>Sno</th><th>Name</th><th>Quantity</th></tr>');

					$('#display_menu').append('<tbody>');

					$.each(data, function(i, item) {



						$('#display_menu').append('<tr><td>1</td><td>' + item +'</td><td>2</td></tr>')

					    //console.log(item);
					});

					$('#display_menu').append('</tbody></table>');

				},
				error:function(){

				}
			})
			$('#orderModal').modal('show');
		})
	})
</script>
<body>

	<nav class="navbar bg-danger">
		<h4 class="navbar-brand text-light">Zomato Admin</h4>
		<h5 class="float-right text-light">Hi <?php echo $_SESSION['name'] ?></h5>
	</nav>

	<div class="container">
		<div class="row mt-3">
			<div class="col-md-3">
				<div class="card bg-success text-white">
					<div class="card-body">
						<h4>Amount</h4>
						<h1 class="float-right">Rs <span><?php echo $amount; ?></span></h1>
					</div>
				</div>	
			</div>
			<div class="col-md-3">
				<div class="card bg-warning text-white">
					<div class="card-body">
						<h4>Pending Orders</h4>
						<h1><span class="float-right"><?php echo $pending; 
						?></span></h1>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card bg-primary text-white">
					<div class="card-body">
						<h4>Delivered</h4>
						<h1><span class="float-right"><?php echo $delivered; 
						?></span></h1>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card bg-danger text-white">
					<div class="card-body">
						<h4>Rating</h4>
						<h1><span class="float-right"><?php echo $rating; ?></span></h1>
					</div>
				</div>
			</div>
		</div>

		<div class="row mt-3">
			<div class="col-md-6">
				<div class="card">
					<div class="card-body">
						<h2>Menu<button class="float-right btn btn-danger btn-sm">Add Menu</button></h2>

						<table class="table">
							<tr>
								<th>S No</th>
								<th>Name</th>
								<th>Status</th>
							</tr>
							<?php

							$query1="SELECT * FROM menu WHERE r_id=1";

							$result1=mysqli_query($conn,$query1);
							$counter1=1;
							while($row1=mysqli_fetch_array($result1))
							{
								if($row1['status']==1)
								{
									echo '<tr>
										<td>'.$counter1.'</td>
										<td>'.$row1['name'].'</td>
										<td>
		  									<input type="checkbox" checked>
										</td>
									</tr>';
								}
								else
								{
									echo '<tr class="text-muted">
										<td>'.$counter1.'</td>
										<td>'.$row1['name'].'</td>
										<td>
		  									<input type="checkbox">
										</td>
									</tr>';
								}
								
								$counter1++;
							}


							?>
							
						</table>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card">
					<div class="card-body">
						<h2>Recent Orders</h2>
						<table class="table">
							<tr>
								<th>S No</th>
								<th>Order id</th>
								<th>Customer</th>
								<th>Amount</th>
							</tr>
							<?php

							$query2="SELECT * FROM orders o JOIN users u ON u.user_id=o.user_id WHERE r_id=1 AND delivery_status=0";

							$result2=mysqli_query($conn,$query2);
							$counter2=1;
							while($row2=mysqli_fetch_array($result2))
							{
								echo '<tr>
										<td>'.$counter2.'</td>
										<td class="order_id"><a href="#">'.$row2['order_id'].'</a></td>
										<td>'.$row2['name'].'</td>
										<td>Rs '.$row2['amount'].'</td>
									</tr>';
								$counter2++;
							}
							?>
							
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<b><a href="logout_admin.php">Logout</a></b>

	<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="exampleModalLabel">Order Details</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        	<div id="display_menu"></div>
	      </div>
	    </div>
	  </div>
	</div>

</body>
</html>