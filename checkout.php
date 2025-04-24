#=========================
#z1960727 Justin Carney  |
#z2051554 Aasim Ghani    |
#Tyler Rouw 21942888     |
#Liam Belh z2047328      |
#Trevor Jannsen z2036452 |
#=========================

<?php
include 'includes/header.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Insert order
    $stmt = $pdo->prepare("INSERT INTO `Order` (...) VALUES (...)");
    $stmt->execute([...]);
    $order_id = $pdo->lastInsertId();

    // Insert order details and update stock
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $pdo->prepare("INSERT INTO OrderDetail (...) VALUES (...)")->execute([...]);
        $pdo->prepare("UPDATE Product SET stock_quantity = stock_quantity - ? WHERE product_id = ?")->execute([$quantity, $product_id]);
    }
    $_SESSION['cart'] = [];
}
include 'includes/footer.php';
?>