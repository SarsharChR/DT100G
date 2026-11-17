<?php
include("include/header.php");

$file = "../../../writeable/guestbook.txt";

$entries = [];
if (file_exists($file)) {
    $entries = unserialize(file_get_contents($file));
    if (!is_array($entries)) $entries = [];
}
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
            <strong><?php echo htmlspecialchars($entries[$i]["name"]); ?></strong><br>
            <?php echo nl2br(htmlspecialchars($entries[$i]["message"])); ?><br>
            <i><?php echo htmlspecialchars($entries[$i]["date_time"]); ?></i>
        </p>

        <form method="post" action="include/delete.php">
            <button type="submit" name="delEntry" value="<?php echo $i; ?>">Radera</button>
        </form>
    </article>

    <hr>
<?php endfor; ?>

</body>
</html>