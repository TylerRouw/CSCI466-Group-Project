<?php 
session_start();

$username = '';
$password = '';

try{
	// connect to the database
	$dsn = "mysql:host=courses;dbname=";
	$pdo = new PDO($dsn, $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


	// create an array holding arrays of product details
	$productDetails =[];
	foreach ($_SESSION['cart'] as $prodID => $cartProduct) {
		$sql = $pdo->prepare("SELECT prodID, name, price, image FROM products WHERE prodID = :prodID");
		$sql->execute(['prodID' => $prodID]);
		$productDetails[$prodID] = $sql->fetch(PDO::FETCH_ASSOC);
	}


	// create an array of the items in cart with their quantity and total cost
	$cartItems = [];
	$totalPrice = 0;
	foreach($_SESSION['cart'] as $prodID => $cartProduct){
		$product = $productDetails[$prodID];
		$productTotal = $product['price'] * $cartProduct['quantity'];
		$cartItems[] = [
			'product' => $product,
			'quantity' => $cartProduct['quantity'],
			'total' => $productTotal
		];

		$totalPrice += $productTotal;
	}
} catch (PDOException $e) {
	echo "Error: ".$e->getMessage();
	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>View Cart</title>
</head>
<body>
	<h1 style="text-align: center">Your Cart</h1>

	<table border="1" width="75%" style="margin: 0 auto">
		<thead>
			<th>Image</th>
			<th>Name</th>
			<th>Price</th>
			<th>Quantity</th>
			<th>Remove?</th>
			<th>Total</th>
		</thead>
		<tbody>
			<?php foreach($cartItems as $item) { ?>
				<tr>
					<td style="text-align: center"><img src="<?php echo $item['product']['image']; ?>" width="100" height="100"/></td>
					<td style="text-align: center"><?php echo $item['product']['name']; ?></td>
					<td style="text-align: center"><?php echo '$'.$item['product']['price'] ?></td>
					<td style="text-align: center"><?php echo $item['quantity']; ?></td>

					<td style="text-align: center"><a href="cart.php">Remove.</a></td>
					<td style="text-align: center"><?php echo '$'.$item['total']; ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</body>
</html>
