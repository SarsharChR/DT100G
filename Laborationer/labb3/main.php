<?php
include("require_login.php");
session_start();

if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] != true) {
    header("Location: login.php");
    exit();
}

$page_title = "Hem";
include "header.php";
?>

<h2>Startsidan</h2>

<ol>
    <li><strong>Har du tidigare erfarenhet av utveckling med PHP?</strong><br>
        Nej, det här är min första erfarenhet av PHP.</li>

    <li><strong>Hur har du valt att strukturera upp dina filer och kataloger?</strong><br>
        Jag har organiserat mina filer och mappar.</li>

    <li><strong>Har du följt guiden, eller skapat på egen hand?</strong><br>
        Jag följde guiden.</li>

    <li><strong>Har du gjort några förbättringar eller vidareutvecklingar av guiden (om du följt denna)?</strong><br>
        Jag försökte vidareutveckla den till viss del.</li>

    <li><strong>Vilken utvecklingsmiljö har du använt för uppgiften (Editor, webbserver etcetera)?</strong><br>
        Jag använde Visual Studio (Code) för webbdesign och XAMPP som webbserver.</li>

    <li><strong>Har något varit svårt med denna uppgift?</strong><br>
        Det var lite förvirrande ibland, men inte så mycket svårt.</li>
</ol>

<?php
include "footer.php";
?>