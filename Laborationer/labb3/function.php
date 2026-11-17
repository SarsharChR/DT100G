<?php
function showInfo()
{
    $now = date("Y-m-d H:i:s");

    $isFriday = (date("N") == 5);   // Friday = 5
    $fridayText = "";
    if ($isFriday) {
        $fridayText = " - Äntligen fredag!";
    }

    $ip = $_SERVER["REMOTE_ADDR"];
    $path = $_SERVER["PHP_SELF"]; 
    $agent = $_SERVER["HTTP_USER_AGENT"];

    echo "<h2>Information</h2>";
    echo "<ul>";
    echo "<li><strong>Datum/klockslag:</strong> " . $now . $fridayText . "</li>";
    echo "<li><strong>Din IP-adress:</strong> " . $ip . "</li>";
    echo "<li><strong>Sökväg/filnamn:</strong> " . $path . "</li>";
    echo "<li><strong>User agent-sträng:</strong> " . htmlspecialchars($agent) . "</li>";
    echo "</ul>";
}