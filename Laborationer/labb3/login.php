<?php
session_start();
include "config.php";

if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] == true) {
    header("Location: main.php");
    exit();
}

if (isset($_POST["submit"])) {
    $u = "";
    $p = "";

    if (isset($_POST["uid"])) {
        $u = $_POST["uid"];
    }
    if (isset($_POST["pwd"])) {
        $p = $_POST["pwd"];
    }

    if ($u == "" || $p == "") {
        header("Location: login.php?error=emptyinput");
        exit();
    }

    if ($u == $VALID_USER && $p == $VALID_PASS) {
        $_SESSION["logged_in"] = true;
        $_SESSION["username"] = $u;

        header("Location: main.php");
        exit();
    }

    header("Location: login.php?error=wronglogin");
    exit();
}

$page_title = "Login";
include "header.php";
?>

<h2>Login</h2>

<div class="signup_form">
    <form action="login.php" method="post">
        <input type="text" name="uid" placeholder="Username..."><br><br>
        <input type="password" name="pwd" placeholder="Password..."><br><br>
        <button type="submit" name="submit">Login</button>
    </form>
</div>

<div class="sign_up_error_message">
    <?php
    if (isset($_GET["error"])) {
        if ($_GET["error"] == "emptyinput") {
            echo "<p>Please make sure that all the fields are filled in.</p>";
        } else if ($_GET["error"] == "wronglogin") {
            echo "<p>Incorrect login information!</p>";
        }
    }
    ?>
</div>

<?php
include "footer.php";
?>