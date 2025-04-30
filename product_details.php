<?php
session_start();

$username = 'z1942888';
$password = '2000Jul08';

try{
	// connect to database
	$dsn = "mysql:host=courses;dbname=z1942888";
	$pdo = new PDO($dsn, $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// get all product info for corresponding product link clicked
	$sql = $pdo->prepare("SELECT prodID, name, description, price, image, stock, stockInUse FROM products WHERE prodID = :id");
	$sql->execute(['id' => $_GET['id']]);
	$prod = $sql->fetch(PDO::FETCH_ASSOC);


}catch(PDOException $e)
{
	echo "Error: ".$e->getMessage();
	exit;
}

// check if a user is logged in
$bool = true;
if(!isset($_SESSION['usertype'])){
		$bool = false;
}

// if a user is logged in and they hit the Add to Cart button
if($bool != false && isset($_POST['cartAdd'])){

	$userID = $_SESSION['userID'];

	// get cart corresponding with users id 
	$findCart = $pdo->prepare("SELECT cartID FROM carts WHERE userID = :userID");
	$findCart->execute(['userID' => $userID]);
	$cartID = $findCart->fetchColumn();

	if($cartID === false)
	{
		$newCart = $pdo->prepare("INSERT INTO carts (userID) VALUES (:userID)");
		$newCart->execute(['userID' => $userID]);
		$cartID = $pdo->lastInsertId();
	}


	$prodID = $_POST['prodID'];	// get the prodID of the product theyre adding
	$quantity = $_POST['quantity'];	// as well as the quantity 

	$tempStock = $prod['stock'] - $prod['stockInUse'];	// calculate available stock

	// as long as the quantity theyre adding is less than or equal to the available quantity 
	if ($quantity <= $tempStock)
	{
		// update the amount of stockInUse
		$update = $pdo->prepare("UPDATE products SET stockInUse = stockInUse + :quantity WHERE prodID = :prodID");
		$update->execute(['quantity' => $quantity, 'prodID' => $prodID]);

		// get the quantity of the item currently in their cart
		$sql=$pdo->prepare("INSERT INTO cartItems (cartID, prodID, quantity)
					VALUES(:cartID, :prodID, :quantity)
					ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)");
		$sql->execute(['cartID' => $cartID, 'prodID' => $prodID, 'quantity' => $quantity]);	

		$sql = $pdo->prepare("SELECT stockInUse FROM products WHERE prodID = :prodID");
		$sql->execute(['prodID' => $prodID]);
		$prod['stockInUse'] = $sql->fetchColumn();

	} else {
		echo "Sorry only have $tempStock left";
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title><?php echo $prod['name']; ?></title>
</head>
	<h1><?php echo $prod['name']; ?></h1>
<body>
	<img src="<?php echo $prod['image'] ?>" width="500" height="500" border='3px solid'/><br/>
	<?php if($prod['description'] != null)
	 	echo nl2br($prod['description']); ?>	
	
	<h1><?php echo '$'.$prod['price'] ?></h1>

	<?php if($bool === false) { ?>
		<a href="index.html">Login or Register</a>
		<?php echo " to add to cart!" ?>
	<?php } ?>

	<form action="product_details.php?id=<?php echo $prod['prodID'] ?>" method="POST">
		<input type="hidden" name="prodID" value="<?php echo $prod['prodID']; ?>">

		<input type="number" name="quantity" id="quantity" min="1" max ="<?php echo ($prod['stock'] - $prod['stockInUse']); ?>" value="1" style="width: 50px" required />

		<input type="submit" name="cartAdd" value="Add to Cart" />
	</form>

	<?php echo "(Only ".($prod['stock']-$prod['stockInUse'])." left in stock!)"; ?>

	<br/><br/>
	<?php if($_SESSION['usertype'] != null) { ?>
		<a href="cart.php">View Cart</a><br/>
	<?php } ?>
	<a href="product.php">Back to Products</a>
</body>
</html>

