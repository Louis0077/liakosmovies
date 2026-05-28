<?php
session_start(); // Ξεκινάμε τη σύνδεση με το υπάρχον session
session_unset(); // Αφαιρούμε όλες τις μεταβλητές session
session_destroy(); // Καταστρέφουμε το session εντελώς

// Ανακατεύθυνση στην αρχική σελίδα (ή στο login αν προτιμάς)
header("Location: index.php");
exit();
?>