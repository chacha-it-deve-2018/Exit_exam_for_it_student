<?php
$host = "sql109.infinityfree.com";
$user = "if0_40749029";
$pass = "chacha12121212";
$dbname = "if0_40749029_Exit_exam_for_it_student"; // ሙሉ ስሙን ተጠቀም

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>