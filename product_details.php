<?php
session_start();

$username = '';
$password = '';

try{
	// connect to database
	$dsn = "mysql:host=courses;dbname=";
	$pdo = new PDO($dsn, $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// get all product info for corresponding product link clicked
	$sql = $pdo->prepare("SELECT prodID, name, description, price, image, stock FROM products WHERE prodID = :id");
	$sql->execute(['id' => $_GET['id']]);
	$product = $sql->fetch(PDO::FETCH_ASSOC);

}catch(PDOException $e)
{
	echo "Error: ".$e->getMessage();
	exit;
}

?>

<!DOCTYPE html>
<html>
<head>
	<title><?php echo $product['name']; ?></title>
</head>
	<h1><?php echo $product['name']; ?></h1>
<body>
	<img src="<?php echo $product['image'] ?>" width="500" height="500" border='3px solid'/><br/>
	<?php if($product['description'] != null)
	 	echo nl2br($product['description']); ?>

	<h1><?php echo '$'.$product['price'] ?></h1>

		<form action="product_details.php?id=<?php echo $product['prodID'] ?>" method="POST" />
		<input type="hidden" name="prodID" value=<?php echo $product['prodID']; ?>>

		<input type="number" name="quantity" id="quantity" min="1" max ="<?php echo $product['stock']; ?>" value="1" style="width: 50px" required />

		<input type="submit" name"cartAdd" value="Add to Cart" />
	</form>

	( Only <?php echo $product['stock'] ?> left in stock! )

	<br/><br/>
	<a href="cart.php">View Cart</a><br/>
	<a href="product.php">Back to Products</a>
</body>
</html>
