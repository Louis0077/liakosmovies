<?php
// Αντίγραφο οδηγός για το db_connect.php
// Αντέγραψε αυτό, μετονόμασέ το σε db_connect.php
// και βάλε τα δικά σου credentials
$servername = "your_host";
$username   = "your_username";
$password   = "your_password";
$dbname     = "your_database";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");
?>