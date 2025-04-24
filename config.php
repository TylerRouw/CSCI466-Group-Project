#=========================
#z1960727 Justin Carney  |
#z2051554 Aasim Ghani    |
#Tyler Rouw 21942888     |
#Liam Belh z2047328      |
#Trevor Jannsen z2036452 |
#=========================

<?php
session_start();
$pdo = new PDO('mysql:host=turing.une.edu;dbname=your_db', 'username', 'password');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>