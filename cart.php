<?php 
session_start();

if(!isset($_SESSION['userID'])){
	header("Location: index.html");
	exit;
}
$userID = $_SESSION['userID']; 	// use to link a user to their cart

$username = 'z1942888';
$password = '2000Jul08';

try{
	// connect to the database
	$dsn = "mysql:host=courses;dbname=z1942888";
	$pdo = new PDO($dsn, $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	if(!isset($_SESSION['userID'])){
		header("Location: index.html");
		exit;
	}

	$userID = $_SESSION['userID']; 	// use to link a user to their cart

	$sql = $pdo->prepare("SELECT cartID FROM carts WHERE userID = :userID");
	$sql->execute(['userID' => $userID]);
	$cartID = $sql->fetchColumn();

	if($cartID === false)
	{
		$sql = $pdo->prepare("INSERT INTO carts (userID) VALUES (:userID)");
		$sql->execute(['userID' => $userID]);
		$cartID = $pdo->lastInsertId();
	}

	// use sql statement to get the product info from the products as well as their quantity 
	// in the cart where the userID is the one from the session
	$sql = $pdo->prepare("SELECT p.prodID, p.name, p.price, p.image, SUM(ci.quantity) AS quantity
				FROM cartItems ci JOIN products p ON p.prodID = ci.prodID WHERE ci.cartID = :cartID GROUP BY p.prodID, p.name, p.price, p.image");
	$sql->execute(['cartID' => $cartID]);
	$cartItems = $sql->fetchall(PDO::FETCH_ASSOC); 			//cartItems now holds info for each product in cart

	$grandTotal = 0;
	foreach($cartItems as &$item)
	{
		$item['total'] = $item['price'] * $item['quantity']; 	// calculate indvidual item totals
		$grandTotal += $item['total'];				// calculate grand total
	}
	unset($item);

} catch (PDOException $e) {
	echo "Error: ".$e->getMessage();
	exit;
}


// if one of the buttons was hit in the form 
if(isset($_POST['action'])){ 

	
	$prodID = $_POST['prodID']; // get the prodID of the product corresponding with the button hit	
	

	// if the remove button was clicked
	if($_POST['action'] === 'remove'){

		// use sql statement to get quantity of the item in the cart to track how much of stock is used
		$sql=$pdo->prepare("SELECT quantity FROM cartItems WHERE cartID = :cartID AND prodID = :prodID");	
		$sql->execute(['cartID' => $cartID,'prodID' => $prodID]);
		$quantity = $sql->fetchColumn();

		// removes the item from the cart
		$sql=$pdo->prepare("DELETE FROM cartItems WHERE prodID = :prodID AND cartID = :cartID");
		$sql->execute(['prodID' => $prodID, 'cartID' => $cartID]);

		// then update the stockInUse so that more products than whats in inventory cant be sold
		$sql=$pdo->prepare("UPDATE products SET stockInUse = stockInUse - :quantity 
					WHERE prodID = :prodID");
		$sql->execute(['quantity' => $quantity, 'prodID' => $prodID]);
	}


	// if '-' button was hit then subtract 1 from the quantity in the cart.
	// if quantity is 1 then remove the item from the cart.
	if($_POST['action'] === 'subItem') {

		$sql=$pdo->prepare("SELECT quantity FROM cartItems WHERE cartID = :cartID AND prodID = :prodID");
		$sql->execute(['cartID' => $cartID, 'prodID' => $prodID]);
		$currqty = $sql->fetchColumn();

		if($currqty > 1) {
			// update the quantity in the cart as long as the item in the carts quantity is greater than 1
			$sql = $pdo->prepare("UPDATE cartItems SET quantity = quantity - 1 WHERE cartID = :cartID
						AND prodID = :prodID");
			$sql->execute(['cartID' => $cartID, 'prodID' => $prodID]);
		} else {
			// delete the item from the cart if the quantity in the cart is 1 or less
			$sql = $pdo->prepare("DELETE FROM cartItems
						WHERE cartID = :cartID AND prodID = :prodID");
			$sql->execute(['cartID' => $cartID, 'prodID' => $prodID]);
		}

		// update the stockInUse to reflect 1 more stock being available
		$sql=$pdo->prepare("UPDATE products SET stockInUse = stockInUse - 1 WHERE prodID = :prodID");
		$sql->execute(['prodID' => $prodID]);
	}


	// adds 1 to cart if + button is hit
	if($_POST['action'] === 'addItem') {

		// update the quantity of the item in the cart 
		$sql=$pdo->prepare("UPDATE cartItems SET quantity = quantity + 1
					WHERE cartID = :cartID AND prodID = :prodID");
		$sql->execute(['cartID' => $cartID, 'prodID' => $prodID]);

		// update the stockInUse 
		$sql=$pdo->prepare("UPDATE products SET stockInUse = stockInUse + 1 WHERE prodID = :prodID");
		$sql->execute(['prodID' => $prodID]);
	}


	// if they clicked checkout they are redirected to a checkout page to process their order
	if($_POST['action'] === 'checkout')
	{
		header("Location: checkout.php");
		exit;
	}


	// reloads page
	header("Location: cart.php");
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
					<td style="text-align: center"><img src="<?php echo $item['image']; ?>" width="100" height="100"/></td>
					<td style="text-align: center"><?php echo $item['name']; ?></td>
					<td style="text-align: center"><?php echo '$'.$item['price'] ?></td>

					<td style="text-align: center"><form action="cart.php" method="POST">
						<input type="hidden" name="prodID" value="<?php echo $item['prodID']; ?>">
						<button type="submit" name="action" value="subItem">-</button>
					</form></td>

					<td style="text-align: center"><?php echo $item['quantity']; ?></td>

					<td style="text-align: center"><form action="cart.php" method="POST">
						<input type="hidden" name="prodID" value="<?php echo $item['prodID']; ?>">
						<button type="submit" name="action" value="addItem">+</button>
					</form></td>

					<td style="text-align: center"><form action="cart.php" method="POST">
						<input type="hidden" name="prodID" value="<?php echo $item['prodID']; ?>">
						<button type="submit" name="action" value="remove">Remove</button>
					</form></td>

					<td style="text-align: center"><?php echo '$'.$item['total']; ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<?php if($grandTotal != 0) { ?>
		<h2 style="text-align: right; margin-right: 12.5%">Grand Total: $<?php echo $grandTotal; ?></h2>
		<form action="cart.php" method="POST">
			<button style="float: right; margin-right: 12.5%" type="submit" name="action" value="checkout">Checkout!</button>
		</form>
	<?php } ?>
		
</body>
</html>
