<<<<<<< Updated upstream
<!DOCTYPE html>
<html>
<head>
  <title>Cart Page</title>
</head>
<body>

  This is the cart body

</body>
=======
#=========================
#z1960727 Justin Carney  |
#z2051554 Aasim Ghani    |
#Tyler Rouw 21942888     |
#Liam Belh z2047328      |
#Trevor Jannsen z2036452 |
#=========================

<?php
include 'includes/header.php';
// Add to cart logic
if ($_POST['add_to_cart']) {
    $product_id = $_POST['product_id'];
    $_SESSION['cart'][$product_id] = $_POST['quantity'];
}
// Display cart items
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $product = $pdo->query("SELECT * FROM Product WHERE product_id = $product_id")->fetch();
    echo "{$product['name']} (Qty: $quantity)";
}
include 'includes/footer.php';
?>
>>>>>>> Stashed changes
