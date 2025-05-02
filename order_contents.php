<?php
session_start();

$username = 'z1942888';
$password = '2000Jul08';

try {
	// connect to the database
	$dsn = "mysql:host=courses;dbname=z1942888";
	$pdo = new PDO($dsn, $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$orderID = $_GET['orderID'];

	// get info for each orderItem
	$sql = $pdo->prepare("SELECT products.prodID, products.image, products.name, orderItems.quantity FROM orderItems
				JOIN products ON orderItems.prodID = products.prodID
				WHERE orderItems.orderID = :orderID");
	$sql->execute(['orderID' => $orderID]);
	$orderItems = $sql->fetchall(PDO::FETCH_ASSOC);


} catch (PDOException $e){
	echo "Error: ".$e->getMessage();
	exit;
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Order Contents</title>
</head>
<body>
	<h2><a href="<?php echo $_SESSION['usertype']?>_home.html">Home</a><br/>
	<a href="order_status.php">Back</a></h2>
	<h1 style="text-align: center">Order Contents</h1>
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
	</div>
</body>
</html>
