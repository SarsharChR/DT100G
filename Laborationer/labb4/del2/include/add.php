<?php

spl_autoload_register(function ($class) {
    include '../classes/' . $class . '.class.php';
});

$name = $_POST["name"];
$message = $_POST["message"];


$gb = new Guestbook();

if(isset($_POST["addEntry"])){
    if($name != "" && $message != ""){
        $gb->addEntry($name, $message);
    }
    unset($_POST["addEntry"]);
    header("Location: ../index.php");
    exit();
}