<?php
session_start(); // ሴሽኑን መጀመሪያ መክፈት ያስፈልጋል

// ሁሉንም የሴሽን ቫሪያብሎች ማጥፋት
$_SESSION = array();

// ሴሽኑን ሙሉ በሙሉ ማፍረስ
session_destroy();

// ተጠቃሚውን ወደ መግቢያ (Login) ገጽ መመለስ
header("Location: login.php");
exit();
?>