<?php

session_start();

$username='';	
$password='';

try{

	// connect to database
	$dsn = "mysql:host=courses;dbname=";	
	$pdo = new PDO($dsn, $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


	// retrieve data entered on registration form
	$user = $_POST['username'];
	$pass = $_POST['password'];
	$type = $_POST['type'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];

	
	// randomizes password for security
	$hashpass = password_hash($pass, PASSWORD_DEFAULT);


	// prepare sql command to insert new user into database
	$addUser = $pdo->prepare(
		"INSERT INTO users (username, password, user_type, email, phone)
		VALUES ( :user, :pass, :type, :email, :phone)"
	);


	// execute the prepared statement using the values submitted in the register.html form
	$addUser->execute([
		':user' => $user,
		':pass' => $hashpass,
		':type' => $type,
		':email' => $email,
		':phone' => $phone
	]);


	$_SESSION['usertype'] = $type;
	$_SESSION['username'] = $user;

	// redirect back to homepage corresponding with user type once registered
	header("Location: ".$_SESSION['usertype']."_home.html");
	exit();

}
catch(PDOException $e) {
	if($e->errorInfo[1] == 1062) 
		echo "User exists";
	else
		echo "DB error";
}
catch(Exception $e){
	echo "Error: " . $e->getMessage();
}
	
?>
