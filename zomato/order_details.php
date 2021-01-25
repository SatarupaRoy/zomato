<!DOCTYPE html>
<?php

session_start();

$order_id=$_GET['order_id'];

$conn=mysqli_connect("localhost","root","","zomato");

$query="SELECT * FROM orders WHERE order_id LIKE '$order_id'";

$result=mysqli_query($conn,$query);

$result=mysqli_fetch_array($result);

$r_id=$result['r_id'];

$query1="SELECT * FROM restaurants WHERE r_id LIKE '$r_id'";

$result1=mysqli_query($conn,$query1);

$result1=mysqli_fetch_array($result1);

$r_name=$result1['r_name'];

?>

<?php

$query3="SELECT name,price from menu m JOIN order_details o ON o.menu_id=m.id WHERE o.order_id LIKE '$order_id'";

$result3=mysqli_query($conn,$query3);

$total=0;

while($row=mysqli_fetch_array($result3)){
	$total=$total + $row['price'];
}

?>
<html>
<head>
	<title>Order Details</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script></title>
</head>

<script type="text/javascript">
	$(document).ready(function(){

		var initial_total='<?php echo $total; ?>';

		$('#total').text(initial_total);
		$('#amount').text(initial_total);

		$('#apply_coupon').click(function(){
			var coupon_input=$('#coupon_input').val()
			//alert(coupon_input);

			//ajax call into the db
			$.ajax({
				url:'check_discount.php',
				type:'POST',
				data:{'user_input':coupon_input},
				success:function(data){
					console.log(data);
					data=JSON.parse(data);
					var percent=data.percent;
					//Make changes
					var total=$('#total').text();

					if(data.response==200)
					{
						

						var discount=(percent/100)*total;

						var amount=total - discount;

						$('#discount').text(discount);
						$('#amount').text(amount);
					}else{
						$('#discount').text(0);
						$('#amount').text(total);
					}
					

				},
				error:function(){
					alert("Some error occured");
				}
			})
		})

		$('#pay').click(function(){

			var order_id='<?php echo $order_id; ?>';

			var final_amount=$('#amount').text();

			window.location.href='confirm_order.php?order_id=' + order_id + '&amount=' + final_amount;  
		});
	})
</script>
<body>

	<nav class="navbar bg-danger">
		<h4 class="navbar-brand text-light">Zomato</h4>
		<h5 class="float-right text-light">Hi <?php echo $_SESSION['name'] ?></h5>
	</nav>

	<div class="container">
		<div class="row mt-3">
			<div class="col-md-9">
				<div class="card">
					<div class="card-body">
						<h4><?php echo $r_name; ?></h4>
						<table class="table">
							<?php

							$query2="SELECT name,price from menu m JOIN order_details o ON o.menu_id=m.id WHERE o.order_id LIKE '$order_id'";

							$result2=mysqli_query($conn,$query2);

							$counter=1;

							while($row=mysqli_fetch_array($result2))
							{
								echo '<tr>
										<td>'.$counter.'</td>
										<td>'.$row['name'].'</td>
										<td>2</td>
										<td>Rs '.$row['price'].'</td>
									</tr>';

								$counter++;
							}
							?>
							
						</table>
						<p>Have a coupon code? Apply Now!</p>
						<input type="text" name="" class="form-control" id="coupon_input">
						<button class="btn btn-danger mt-1" id="apply_coupon">Apply</button><br>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card">
					<div class="card-body">
						<table class="table">
							<tr>
								<td>Total</td>
								<td>Rs <span id="total">1300</span></td>
							</tr>
							<tr>
								<td>Discount</td>
								<td>Rs <span id="discount">0</span></td>
							<tr>
								<td>To be Paid</td>
								<td>Rs <span id="amount">1100</span></td>
							</tr>
						</table>
						<button class="btn btn-danger btn-block" id="pay">Pay Now</button>
					</div>
				</div>
			</div>
		</div>
	</div>

</body>
</html>