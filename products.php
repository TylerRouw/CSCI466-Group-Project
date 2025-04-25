<?php

$username = '';
$password = '';

try{
	// connect to database
	$dsn = "mysql:host=courses;dbname=";
	$pdo = new PDO($dsn, $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// get products with inventory > 0
	$sql = $pdo->query("SELECT image, prodID, name, price FROM products WHERE stock > 0 ORDER BY prodID ASC");
	$products = $sql->fetchall(PDO::FETCH_ASSOC);

} catch(PDOException $e){
	echo "Error: ". $e->getMessage();
	exit;
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Products</title>
</head>
<body>
	<h1>Products</h1>

	<?php if(!empty($products)){ ?>
		<ul>
			<?php foreach($products as $product){ ?>
				<img src="<?php echo $product['image'] ?>" width="300" height="300" border='3px solid'/>
				<br/>
				<h3><a href="product_details.php?id=<?php echo $product['prodID']; ?>">
					<?php echo $product['name']; ?>
				</a> - $<?php echo $product['price']; ?>
				</h3><br/><br/> 

		 	<?php }; ?>
		</ul>
	<?php }else{ ?>
		<p>Sold Out!</p>
	<?php }; ?>
</body>
</html>