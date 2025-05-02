<?php 
session_Start();

$username='';
$password='';

try {
	// connect to database
	$dsn="mysql:host=courses;dbname=";
	$pdo = new PDO($dsn, $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// get all info from processing orders
	$sql = $pdo->prepare("SELECT orders.*, users.username FROM orders
	       			JOIN users ON orders.userID = users.userID
				WHERE status = 'processing' ORDER BY orderDate ASC");
	$sql->execute();
	$orders = $sql->fetchall(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
	echo "Error: ". $e->getMessage();
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Active Orders</title>
</head>
<body>
	<h2 style="text-align:left"><a href="<?php echo $_SESSION['usertype'] ?>_home.html">Home</a></h2>
	<h1 style="text-align: center">Active Orders</h1>

	<table style="margin: 0 auto" border="1">
		<thead>
			<th style="width: 75px">OrderID</th>
			<th style="width: 75px">Username</th>
			<th style="width: 150px">Date</th>
			<th style="width: 175px">Tracking #</th>
			<th style="width: 75px">Process?</th>
			<th style="width: 100px">Order Total</th>
		</thead>
		<tbody>
			<?php foreach($orders as $order) { ?>
				<tr style="text-align: center">
					<td><?php echo $order['orderID'] ?></td>
					<td><?php echo $order['username'] ?></td>
					<td><?php echo $order['orderDate'] ?></td>
					<td><?php echo $order['trackingNumber'] ?></td>
					<td><form action="fulfill.php" method="POST">
						<input type="hidden" name="orderID" value="<?php echo $order['orderID'] ?>">
						<button type="submit">Process</button>
					</form></td>
					<td>$<?php echo $order['orderTotal']?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>	
</body>
</html>

