<?php 
session_start();

// if one of the buttons was hit 
if(isset($_POST['action'])){ 
	
	// removes item if they hit the remove button
	if($_POST['action'] === 'remove'){	
		unset($_SESSION['cart'][$_POST['prodID']]);
	}


	// subtracts 1 from cart if - button is hit as long as theres more than 1
	// otherwise removes item from cart
	if($_POST['action'] === 'subItem') {
		if($_SESSION['cart'][$_POST['prodID']]['quantity'] > 1){
			$_SESSION['cart'][$_POST['prodID']]['quantity'] -= 1;
		} else {
			unset($_SESSION['cart'][$_POST['prodID']]);
		}
	}


	// adds 1 to cart if + button is hit
	if($_POST['action'] === 'addItem') {
		$_SESSION['cart'][$_POST['prodID']]['quantity'] += 1;
	}


	if($_POST['action'] === 'checkout')
	{
		header("Location: checkout.php");
		exit;
	}


	// reloads page
	header("Location: cart.php");
	exit;
}

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

	<?php if($_SESSION['usertype'] === null){ ?>
		<h2><a href="index.html">Home</a><br/>
	<?php } else { ?>
		<h2><a href="<?php echo $_SESSION['usertype']."_home.html" ?>">Home</a><br/>
	<?php } ?>	
	<a href="product.php">Products</a></h2>
	<h1 style="text-align: center">Your Cart</h1>

	<table border="1" width="75%" style="margin: 0 auto">
		<thead>
			<th>Image</th>
			<th>Name</th>
			<th>Price</th>
			<th>-</th>
			<th>Quantity</th>
			<th>+</th>
			<th>Remove?</th>
			<th>Total</th>
		</thead>
		<tbody>
			<?php foreach($cartItems as $item) { ?>
				<tr>
					<td style="text-align: center"><img src="<?php echo $item['product']['image']; ?>" width="100" height="100"/></td>
					<td style="text-align: center"><?php echo $item['product']['name']; ?></td>
					<td style="text-align: center"><?php echo '$'.$item['product']['price'] ?></td>

					<td style="text-align: center"><form action="cart.php" method="POST">
						<input type="hidden" name="prodID" value="<?php echo $item['product']['prodID']; ?>">
						<button type="submit" name="action" value="subItem">-</button>
					</form></td>

					<td style="text-align: center"><?php echo $item['quantity']; ?></td>

					<td style="text-align: center"><form action="cart.php" method="POST">
						<input type="hidden" name="prodID" value="<?php echo $item['product']['prodID']; ?>">
						<button type="submit" name="action" value="addItem">+</button>
					</form></td>

					<td style="text-align: center"><form action="cart.php" method="POST">
						<input type="hidden" name="prodID" value="<?php echo $item['product']['prodID']; ?>">
						<button type="submit" name="action" value="remove">Remove</button>
					</form></td>

					<td style="text-align: center"><?php echo '$'.$item['total']; ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<?php if($totalPrice != 0) { ?>
		<h2 style="text-align: right; margin-right: 12.5%">Grand Total: $<?php echo $totalPrice; ?></h2>
		<form action="cart.php" method="POST">
			<button style="float: right; margin-right: 12.5%" type="submit" name="action" value="checkout">Checkout!</button>
		</form>
	<?php } ?>
	
	
</body>
</html>
