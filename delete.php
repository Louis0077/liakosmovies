<?php
session_start();

// είναι για το free hosting!
include 'db_connect.php';

// Έλεγχος αν είναι συνδεδεμένος ΚΑΙ αν είναι admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Παίρνουμε το ID από το URL (π.χ. delete.php?id=5)
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM movies WHERE movie_id = $id";

    if ($conn->query($sql) === TRUE) {
        // Επιστροφή στην αρχική σελίδα μετά τη διαγραφή
        $_SESSION['flash'] = 'Η ταινία διαγράφηκε επιτυχώς!';
        header("Location: index.php");
    } else {
        echo "Σφάλμα κατά τη διαγραφή: " . $conn->error;
    }
}

$conn->close();