<?php 
session_Start();

$username='z1942888';
$password='2000Jul08';

try {
	// connect to database 
	$dsn="mysql:host=courses;dbname=z1942888";
	$pdo = new PDO($dsn, $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$userID = $_SESSION['userID'];


	// get all order info for logged in user
	$sql = $pdo->prepare("SELECT * FROM orders WHERE userID = :userID 
				ORDER BY CASE WHEN status = 'shipped' THEN 0 WHEN status = 'processing' THEN 1 ELSE 2 END,
				orderID ASC");
	$sql->execute(['userID' => $userID]);
	$orders = $sql->fetchall(PDO::FETCH_ASSOC);

	// calculate the total of orders 
	$grandTotal = 0;
	foreach($orders as $order){
		$grandTotal += $order['orderTotal'];
	}
	unset($order);

} catch (PDOException $e) {
	echo "Error: ". $e->getMessage();
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Orders Status</title>
</head>
<body>
	<h2 style="text-align: left"><a href="<?php echo $_SESSION['usertype'] ?>_home.html">Home</a></h2>
	<h1 style="text-align: center">Orders Status</h1>
	<div style="width: fit-content; margin: auto; text-align: right">
		<table style="margin: 0 auto" border="1">
			<thead style="text-align: center">
				<th style="width: 75px">OrderID</th>
				<th style="width: 150px">Date</th>
				<th style="width: 175px">Tracking #</th>
				<th style="width: 100px">Status</th>
				<th style="width: 100px">OrderTotal</th>
			</thead>
			<tbody>
				<?php foreach($orders as $order) { ?>
					<tr style="text-align: center">
						<td><a href="order_contents.php?orderID=<?php echo $order['orderID'] ?>"><?php echo $order['orderID'] ?></a></td>
						<td><?php echo $order['orderDate'] ?></td>
						<td><?php echo $order['trackingNumber'] ?></td>
						<td><?php echo $order['status'] ?></td>
						<td>$<?php echo $order['orderTotal']?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	<br/>
	<?php if($grandTotal > 0) { ?>
		<h3 style="margin-right: 2.5%">Total for all Orders: $<?php echo $grandTotal ?></h3>
	<?php } ?>
	</div>	
</body>
</html>

