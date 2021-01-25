<?php

session_start();

if(empty($_SESSION))
{
	header('Location: login_form.php');
}

$conn=mysqli_connect("localhost","root","","zomato");

$r_id=$_GET['id'];

$query="SELECT * FROM restaurants WHERE r_id=$r_id";

$result=mysqli_query($conn,$query);

$result=mysqli_fetch_array($result);

if(empty($result))
{
	header('Location: error.php');
}
else
{
	$name=$result['r_name'];
	$bg=$result['r_logo'];
	$cuisine=$result['r_cuisine'];
}


?>

<!DOCTYPE html>
<html>
<head>
	<title>Restaurant Name</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</head>
<script type="text/javascript">
	$(document).ready(function(){

		var r_id="<?php echo $r_id; ?>";

		var flag=0;

		$('#order-box').hide();

		var order_id=0;

		$('.menu_item').click(function(){

			

			var item_id=$(this).data('id');

			var item_name=$('#menu_item_name' + item_id).text();

			var item_price=$('#menu_item_price' + item_id).text();
			// make an entry in the db
			$.ajax({
				url:"add_order.php",
				type:"POST",
				data:{"r_id":r_id,"menu_id":item_id,'flag':flag,'order_id':order_id},
				success:function(data){

					if(flag==0){
						flag++;
					}

					console.log(data);

					var data=JSON.parse(data);

					order_id=data.order_id;

					$('#order-box').show();

					$('#show_items').append('<p>' + item_name +'<span class="float-right">Rs' + item_price +'</span></p>');

				},
				error:function(){
					alert("Hello");
				}
			});
		})

		$('#place').click(function(){
			window.location.href="http://localhost/zomato/order_details.php?order_id=" + order_id;
		})
	})
</script>
<body>

	<nav class="navbar bg-danger">
		<h4 class="navbar-brand text-light">Zomato</h4>
		<h5 class="float-right text-light">Hi <?php echo $_SESSION['name'] ?></h5>
	</nav>

	<div class="container">
		<div class="row">
			<div class="col-md-12 mt-2">
				<img src="<?php echo $bg; ?>" style="width:100%;height:350px">
			</div>
		</div>

		<div class="row mt-3">
			<div class="col-md-9">

				<div class="row">
					<div class="col-md-12">
						<div class="card">
							<div class="card-body">
								<h1><?php echo $name; ?></h1>
								<h5><?php echo $cuisine; ?></h5>
							</div>
						</div>
					</div>
				</div>

				<div class="row mt-2">
					<div class="col-md-3">
						<div class="row">
							<div class="col-md-12">
								<div class="card">
									<div class="card-body">
										<h4>Filter</h4>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-9">
						<div class="row">
							<?php

							$query2="SELECT * FROM menu WHERE r_id=$r_id AND status=1";

							$result=mysqli_query($conn,$query2);

							while($row=mysqli_fetch_array($result))
							{
								echo '<div class="col-md-12 mt-2">
								<div class="card">
									<div class="card-body">
										<div class="row">';

										if($row['type']==1)
										{
											echo '<div class="col-md-1" style="padding:12px">
												<div style="width:15px;height: 15px;background-color: green;border-radius: 15px"></div>';

										}
										else
										{
											echo '<div class="col-md-1" style="padding:12px">
												<div style="width:15px;height: 15px;background-color: red;border-radius: 15px"></div>';

										}

											
											echo '</div>
											<div class="col-md-9">
												<h5 id="menu_item_name'.$row['id'].'">'.$row['name'].'</h5>
												<p>Rs <span id="menu_item_price'.$row['id'].'">'.$row['price'].'</span><br>
												<small>'.$row['desc'].'</small></p>
											</div>
											<div class="col-md-2">
												<button data-id='.$row['id'].' class="btn btn-danger btn-sm menu_item">Add</button>
											</div>
										</div>
									</div>
								</div>
							</div>';
							}

							?>
							
						</div>
					</div>
				</div>


			</div>
			<div class="col-md-3">
				<div class="row">

					<div class="col-md-12">
						<div id="order-box" class="card bg-danger text-light">
							<div class="card-body">
								<h5>Order Details</h5>
								<div id="show_items">
									
								</div>

								<button id="place" class="btn btn-light btn-block">Place Order</button>
							</div>
						</div>
					</div>

					<div class="col-md-12 mt-2">
						<div class="card">
							<div class="card-body">
								<h3>Other Details</h3>
							</div>
						</div>
					</div>
					<div class="col-md-12 mt-2">
						<div class="card">
							<div class="card-body">
								<h3>Reviews</h3>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</body>
</html>