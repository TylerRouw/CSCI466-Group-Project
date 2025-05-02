<?php 
session_start();

$username = '';
$password = '';

try{
	// connect to database
	$dsn ="mysql:host=courses;dbname=";
	$pdo = new PDO($dsn, $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$userID = $_SESSION['userID']; 
	$placed = false;		

	if($_POST['action'] === 'placeOrder')
	{
		$pdo->beginTransaction();


		// grab the relevant info from the cart (name, images, prodIDs, quantities and prices)
		$sql = $pdo->prepare("SELECT p.name, p.image, ci.prodID, ci.quantity, p.price FROM carts c
					JOIN cartItems ci ON c.cartID = ci.cartID
					JOIN products p ON ci.prodID = p.prodID
					WHERE c.userID = :userID");
		$sql->execute(['userID' => $userID]);
		$orderItems = $sql->fetchall(PDO::FETCH_ASSOC);


		// calculate total for each item & total for order
		$orderTotal = 0;	
		foreach($orderItems as &$item) {
			$item['total'] = $item['quantity'] * $item['price'];
			$orderTotal += $item['total'];
		}
		unset($item);


		// insert userID and the calculated orderTotal into the orders table
		$sql=$pdo->prepare("INSERT INTO orders (userID, orderTotal) VALUES (:userID, :orderTotal)");
		$sql->execute(['userID' => $userID, 'orderTotal' => $orderTotal]);


		$orderID = $pdo->lastInsertId(); // grabs autoincremented orderID from orders table


		// sets tracking number to combo of T + date + 0..orderID
		$tracking = 'T'.date('Ymd')."-".str_pad($orderID, 4, 0, STR_PAD_LEFT); 


		// Update the tracking number for the new row in orders
		$sql= $pdo->prepare("UPDATE orders SET trackingNumber = :tracking WHERE orderID = :orderID");
		$sql->execute(['tracking' => $tracking, 'orderID' => $orderID]);

		

		// prepare sql to be ran to insert each item from cart into orderItems to be tracked
		$itemInsert = $pdo->prepare("INSERT INTO orderItems (orderID, prodID, quantity, price)
						VALUES (:orderID,:prodID,:quantity,:price)");


		// prepare sql to update the stock and stockInUse for each product on order
		$updateStock = $pdo->prepare("UPDATE products SET stock = stock - :quantity, 
						stockInUse = stockInUse - :quantity WHERE prodID = :prodID");


		// insert each item into orderItems and update their stock info
		foreach($orderItems as $item){
			$itemInsert->execute(['orderID' => $orderID, 'prodID' => $item['prodID'],
						'quantity' => $item['quantity'], 'price' => $item['price']]);

			$updateStock->execute(['quantity' => $item['quantity'], 'prodID' => $item['prodID']]);
		}


		// clear the cart contents
		$sql=$pdo->prepare("DELETE FROM cartItems WHERE cartID = (SELECT cartID FROM carts WHERE userID = :userID)");
		$sql->execute(['userID' => $userID]);


		$pdo->commit();


		// if theres no issues sets placed to true.
		$placed = true; 
	}

	// get the relevant product info as well as their respective quantities from the cart
	$sql = $pdo->prepare("SELECT p.prodID, p.name, p.price, p.image, ci.quantity FROM carts c
				JOIN cartItems ci ON c.cartID = ci.cartID
				JOIN products p ON ci.prodID = p.prodID
				WHERE c.userID = :userID");
	$sql->execute(['userID' => $userID]);
	$cartItems = $sql->fetchall(PDO::FETCH_ASSOC); 

	//calculates total of items in the cart
	$grandTotal = 0;
	foreach($cartItems as &$item)
	{
		$item['total'] = $item['price'] * $item['quantity'];	// calculate indiviual product totals
		$grandTotal += $item['total'];				// calculate the grand total of all products
	}
	unset($item);


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
	<?php if($placed === false) { ?>

		<h1 style="text-align: center">Checkout</h1>
		<br/><br/>

		<h2 style="margin-left: 12.5%">Shipping Info:</h2>

		<form action="checkout.php" method="POST">
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
			<input type="text" name="cardNum" minlength="16" maxlength="16"  style="margin-left: 12.5%" required>
			<input type="text" name="cardExp" minlength="4" maxlength="4" style="margin-left: 15px" size="1" required><br/><br/>
			<label for="seCode" style="margin-left: 12.5%" >CVV:</label>
			<input type="text" name="seCode" size="1" style="margin-left: 2px" minlength="3" maxlength="3" required>

			<label for="billzip" style="margin-left: 5px">Billing ZIP:</label>
			<input type="text" name="billzip" size="2" style="margin-left: 2px" minlength="5" maxlength="10" required>
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
					<?php foreach($cartItems as $item) { ?>
						<tr>
							<td style="text-align: center"><img src="<?php echo $item['image']?>" width="50" height="50"></td>
							<td style="text-align: center"><?php echo $item['name']?> </td>
							<td style="text-align: center"><?php echo '$'.$item['price']?> </td>
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

		<form action="<?php echo $_SESSION['usertype']?>_home.html" method="POST">
			<button type="submit" name="action" value="cancelOrder" style="margin-left: 24.9%">Cancel Order</button>
		</form>

	<?php } else { ?>	
		<h1 style="text-align: center">Placed Order Successfully!</h1>
		<div style="width: fit-content; margin: auto; text-align: right;">
			<table border="1">
				<thead>
					<th style="text-align: center">Image</th>
					<th style="text-align: center">Name</th>
					<th style="text-align: center">Price</th>
					<th style="text-align: center">Quantity</th>
					<th style="text-align: center">Total</th>
				</thead>
				<tbody>
					<?php foreach($orderItems as $item) { ?>
						<tr>
							<td style="text-align: center"><img src="<?php echo $item['image'] ?>" width="50px" height="50px"></td>
							<td style="text-align: center"><?php echo $item['name']?></td>
							<td style="text-align: center">$<?php echo $item['price']?></td>
							<td style="text-align: center"><?php echo $item['quantity']?></td>
							<td style="text-align: center">$<?php echo $item['total']?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			
			<h3>Order Total: $<?php echo $orderTotal ?></h3>

			<a href="order_status.php" style="text-align: right">View Order</a><br/>
			<a href="<?php echo $_SESSION['usertype']?>_home.html">Home</a><br/>
		</div>
	<?php } ?>
</body>

</html>
