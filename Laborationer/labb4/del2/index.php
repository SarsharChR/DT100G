<?php
include("include/header.php");

spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.class.php';
});

$gb = new Guestbook();
$entries = $gb->loadEntries();
?>

<form method="post" action="include/add.php">
    <label for="name">Namn:</label>
    <input type="text" id="name" name="name"><br><br>

    <label for="message">Meddelande:</label><br>
    <textarea id="message" name="message" rows="4" cols="40"></textarea><br><br>

    <button type="submit" name="addEntry" value="1">Publicera</button>
</form>

<hr>

<?php for ($i = count($entries) - 1; $i >= 0; $i--): ?>
    <article>
        <p>
            <strong><?php echo htmlspecialchars($entries[$i]["Username"]); ?></strong><br>
            <?php echo nl2br(htmlspecialchars($entries[$i]["Post"])); ?><br>
            <i><?php echo htmlspecialchars($entries[$i]["PostDate"]); ?></i>
        </p>

        <form method="post" action="include/delete.php">
            <button type="submit" name="delEntry" value="<?php echo $entries[$i]["id"]; ?>">Radera</button>
        </form>
    </article>

    <hr>
<?php endfor; ?>

</body>
</html>