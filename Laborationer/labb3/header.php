<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include ("config.php");

if (!isset($page_title)) {
    $page_title = "";
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title><?php echo $site_title . $divider . $page_title; ?></title>
    <link rel="stylesheet" href="css/stilmall.css" type="text/css">
</head>
<body>
<div id="container">
    <header id="mainheader">
        <h1>Labb3</h1>
        <nav id="mainmenu">
            <ul>
                <li><a href="main.php">Hem</a></li>
                <li><a href="omsidan.php">Om sidan</a></li>
                <li><a href="kontakt.php">Kontakt</a></li>
                <li><a href="info.php">Info</a></li>

                <?php
                if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] == true) {
                    echo '<li><a href="logout.php">Logga ut</a></li>';
                } else {
                    echo '<li><a href="login.php">Login</a></li>';
                }
                ?>
            </ul>
        </nav>
    </header>

    <section id="leftcontent">