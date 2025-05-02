<?php
session_start();

$username = 'z1942888';
$password = '2000Jul08';

try {
	// connect tot database
	$dsn = "mysql:host=courses;dbname=z1942888";
	$pdo = new PDO($dsn, $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$orderID = $_POST['orderID'];

	// get info for each item on order
	$sql = $pdo->prepare("SELECT products.prodID, products.image, products.name, orderItems.quantity FROM orderItems
				JOIN products ON orderItems.prodID = products.prodID
				WHERE orderItems.orderID = :orderID");
	$sql->execute(['orderID' => $orderID]);
	$orderItems = $sql->fetchall(PDO::FETCH_ASSOC);

	// get user info
	$sql= $pdo->prepare("SELECT users.email, users.phone, users.username FROM orders
			JOIN users ON orders.userID = users.userID WHERE orders.orderID = :orderID");
	$sql->execute(['orderID' => $orderID]);
	$userInfo = $sql->fetch(PDO::FETCH_ASSOC);


	// if employee hits the marked as shipped button
	if(isset($_POST['markAsShipped'])){

		// update order status
		$sql = $pdo->prepare("UPDATE orders SET status= 'shipped', notes = :notes WHERE orderID = :orderID");
		$sql->execute(['orderID' => $orderID, 'notes' => $_POST['notes']]);

		// and go back to list of active orders
		header("Location: active_orders.php");
		exit;
	}



} catch (PDOException $e){
	echo "Error: ".$e->getMessage();
	exit;
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Order Fulfillment</title>
</head>
<body>
	<h2><a href="<?php echo $_SESSION['usertype']?>_home.html">Home</a><br/>
	<a href="active_orders.php">Back</a></h2>
	<h1 style="text-align: center">Order Fulfillment</h1>
	<div style="width: fit-content; margin: auto; text-align:right">
		<table style="margin: auto" border = "1">
			<thead style="text-align: center">
				<th style="width: 75px">ProdID</th>
				<th style="width: 100px">Image</th>
				<th style="width: 100px">Name</th>
				<th style="width: 75px">Quantity</th>
			</thead>
			<tbody>
				<?php foreach ($orderItems as $item) { ?>
					<tr style="text-align: center">
						<td><?php echo $item['prodID'] ?></td>
						<td><img src="<?php echo $item['image'] ?>" width="50px" height="50px"></td>
						<td><?php echo $item['name'] ?></td>
						<td><?php echo $item['quantity'] ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<p style="text-align: left">Make sure you have the correct quantity of each item bagged 
		<br/>and ready before marking as shipped, <?php echo $_SESSION['username']?>. </p>

		<form action="fulfill.php" method="POST">
			<input type="hidden" name="orderID" value="<?php echo $orderID?>">
			<label for="notes" style="display: block; text-align: left">Notes:</label>
			<textarea name="notes" style="width: 100%"></textarea><br/>
			<p style="text-align: left">Customer Contact:<br/>
				Email: <?php echo $userInfo['email'];?><br/>
				<?php if($userInfo['phone'] != null) { ?>
				<?php echo "Phone".$userInfo['phone']; } ?>
			</p>

			<button type="submit" name="markAsShipped">Mark as Shipped</button>
		</form> 
	</div>
</body>
</html>
