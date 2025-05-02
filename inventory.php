<?php 

$username = '';
$password = '';

try{
	// connect to the database
	$dsn = "mysql:host=courses;dbname=";
	$pdo = new PDO($dsn, $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// get values from database

	$sql = $pdo->query("SELECT prodID, name, price, stock, stockInUse FROM products ORDER BY prodID ASC");
	$stocks = $sql->fetchall(PDO::FETCH_ASSOC);

}catch(PDOException $e){
	echo "Error: ".$e->getMessage();
	exit;
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Inventory</title>
</head>
<body>
	<h2><a href="owner_home.html">Home</a></h2>
	<h1 style="text-align: center;">Inventory</h1>
		<table border="1" width="50%" style="margin: 0 auto">
			<thead>
				<tr>
					<th>Product ID</th>
					<th>Name</th>
					<th>Price</th>
					<th>Quantity in stock</th>
					<th>Stock in use</th>
				</tr>
			</thead>
			<tbody>				
				<?php foreach($stocks as $stock){ ?>
					<tr>
						<td style="text-align: center;"><?php echo $stock['prodID']; ?></td>
						<td style="text-align: center;"><?php echo $stock['name']; ?></td>
						<td style="text-align: center;"><?php echo '$'.$stock['price']; ?></td>
						<td style="text-align: center;"><?php echo $stock['stock']; ?></td>
						<td style="text-align: center;"><?php echo $stock['stockInUse']; ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
</body>
</html>
