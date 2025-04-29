<?php 
session_start();

$username = '';
$password = '';

try{
	// connect to database
	$dsn ="mysql:host=courses;dbname=";
	$pdo = new PDO($dsn, $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// get array of info for each product
	$prodDetails = [];
	foreach($_SESSION['cart'] as $prodID => $cartProd) {
		$sql = $pdo->prepare("SELECT prodID, image, name, price FROM products WHERE prodID = :prodID");
		$sql->execute(['prodID' => $prodID]);
		$prodDetails[$prodID] = $sql->fetch(PDO::FETCH_ASSOC);
	}

	// get array of the items in the cart with their cart values
	$cartitems = [];
	$grandTotal = 0;
	foreach($_SESSION['cart'] as $prodID => $cartprod)
	{
		$prodTotal = $prodDetails[$prodID]['price'] * $cartprod['quantity'];
		$cartitems[] = [
			'product' => $prodDetails[$prodID],
			'quantity' =>$cartprod['quantity'],
			'total' => $prodTotal
		];

		$grandTotal += $prodTotal;
	}
} catch (PDOException $e) {
	echo "Databse Error: ".$e->getMessage();
	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Checkout</title>
</head>
<body>
	<h1 style="text-align: center">Checkout</h1>
	<br/><br/>

	<h2 style="margin-left: 12.5%">Shipping Info:</h2>
	<form action="order_status.php" method="POST">
		<label for="address" style="margin-left: 12.5%">Address: </label>
		<input type="text" name="address" required>
		<label for="apartment" style="margin-left: 2px">Apt? </label>
		<input type="text" size="1" name="apt"><br/><br/>
		<label for="city" style="margin-left: 12.5%">City: </label>
		<input type="text" name="city" size="5" required>
		<label for="state" style="margin-left: 2px" >State: </label>
		<input type="text" name="state" size="1" required>
		<label for="zip" style="margin-left: 2px">ZIP Code: </label>
		<input type="text" name="zip" size="3" required>
		<br/><br/>
		<h2 style="margin-left: 12.5%">Billing Info:</h2>

		<label for="cardNum" style="margin-left: 12.5%">Card Number:</label>
		<label for="cardExp" style="margin-left: 100px">Exp:</label><br/>
		<input type="text" name="cardNum" min="16" max="16"  style="margin-left: 12.5%" required>
		<input type="text" name="cardExp" min="4" max="4" style="margin-left: 15px" size="1" required><br/><br/>
		<label for="seCode" style="margin-left: 12.5%">CVV:</label>
		<input type="text" name="seCode" size="1" style="margin-left: 2px" required>
		<label for="billzip" style="margin-left: 5px">Billing ZIP:</label>
		<input type="text" name="billzip" size="2" style="margin-left: 2px" required>
		<br/><br/>
		<h2 style="margin-left: 17.5%">Cart Contents</h2>

		<table style="margin-left: 13%" border="1">
			<thead>
				<th>Image</th>
				<th>Name</th>
				<th>Price</th>
				<th>Quantity</th>
				<th>Total</th>
			</thead>
			<tbody>
				<?php foreach($cartitems as $item) { ?>
					<tr>
						<td style="text-align: center"><img src="<?php echo $item['product']['image']?>" width="50" height="50"></td>
						<td style="text-align: center"><?php echo $item['product']['name']?> </td>
						<td style="text-align: center"><?php echo '$'.$item['product']['price']?> </td>
						<td style="text-align: center"><?php echo $item['quantity']?> </td>
						<td style="text-align: center"><?php echo '$'.$item['total'] ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<p style="margin-left: 22%">Grand Total: $<?php echo $grandTotal?></p>

		<button type="submit" name="action" value="placeOrder" style="margin-left: 25%">Place Order!</button>
		<br/><br/>
	</form>
	<form action="<?php echo$_SESSION['usertype']?>_home.html?>" method="POST">
		<button type="submit" name="action" value="cancelOrder" style="margin-left: 24.9%">Cancel Order</button>
	</form>
	
</body>

</html>
