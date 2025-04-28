<?php

session_start();

$username = '';
$password = '';

try{
	//connect to datamase
	$dsn = "mysql:host=courses;dbname=";
	$pdo = new PDO($dsn, $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// retrieve info entered into user login form
	$userInput = $_POST['username'];
	$passInput = $_POST['password'];

	// check database for username that was input
	$userCheck = $pdo->prepare("SELECT * FROM users WHERE username = :user");
	$userCheck->execute([':user' => $userInput]);

	// array holding the returned user info
	$user = $userCheck->fetch(PDO::FETCH_ASSOC);

	// as long as a user is found and their input password matches 
	// their hashed password stored within the database 
	if($user && password_verify($passInput, $user['password'])){

		$_SESSION['usertype'] = $user['user_type'];	// use session variable to track user's type
		$_SESSION['username'] = $user['username'];	// as well as their username

		// redirects them depending on their user type
		header("Location: ".$_SESSION['usertype']."_home.html");
		exit();
			
	} else {

		echo "wrong username or password";
	}
} catch (PDOException $e){
	echo "Database error: ".$e->getMessage();
}	
?>
