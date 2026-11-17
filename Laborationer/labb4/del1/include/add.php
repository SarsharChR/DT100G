<?php

spl_autoload_register(function ($class) {
    include '../classes/' . $class . '.class.php';
});

$name = $_POST["name"];
$message = $_POST["message"];
$dt = date("Y-m-d H:i:s");

$gb = new Guestbook();

if(isset($_POST["addEntry"])){
    if($name != "" && $message != ""){
        $gb->addEntry($name, $message);
    }
    unset($_POST["addEntry"]);
    header("Location: ../index.php");
    exit();
}