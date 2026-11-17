<?php

spl_autoload_register(function ($class) {
    include '../classes/' . $class . '.class.php';
});


$gb = new Guestbook();

if(isset($_POST["delEntry"])){
	$gb->delEntry($_POST["delEntry"]);
	unset($_POST["delEntry"]); // Disable button press
	header("Location: ../index.php"); // Reload
	exit();
}