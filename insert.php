<?php
session_start();

// ειναι για το free hosting!
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $genre = $conn->real_escape_string($_POST['genre']);
    $status = $conn->real_escape_string($_POST['status']);
    $description = $conn->real_escape_string($_POST['description']);
    
    $image = "";
    if (!empty($_FILES['image']['name'])) {
        $image = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
    }

    $sql = "INSERT INTO movies (title, genre, status, description, image) VALUES ('$title', '$genre', '$status', '$description', '$image')";
    if ($conn->query($sql)) { $_SESSION['flash'] = 'Η ταινία προστέθηκε επιτυχώς!';
header("Location: index.php"); exit(); }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Προσθήκη | Οι ταινίες του Λιάκου</title>
    <style>
        :root { --main-color: #e50914; --dark-bg: #141414; --card-bg: #2f2f2f; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--dark-bg); color: white; display: flex; justify-content: center; padding: 40px; }
        .form-card { background: var(--card-bg); padding: 30px; border-radius: 15px; width: 100%; max-width: 500px; border: 1px solid #444; }
        h2 { color: var(--main-color); text-align: center; margin-bottom: 25px; text-transform: uppercase; }
        label { display: block; margin-bottom: 8px; font-size: 14px; color: #bbb; }
        input, textarea, select { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #444; border-radius: 8px; background: #1f1f1f; color: white; box-sizing: border-box; }
        input:focus { border-color: var(--main-color); outline: none; }
        .btn-group { display: flex; gap: 10px; }
        .btn { flex: 1; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; text-align: center; text-decoration: none; transition: 0.3s; }
        .btn-save { background: var(--main-color); color: white; }
        .btn-cancel { background: #444; color: white; }
        .btn:hover { opacity: 0.8; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="form-card">
        <h2>Νέα Ταινία</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Τίτλος</label>
            <input type="text" name="title" required>
            
            <div style="display:flex; gap:15px;">
                <div style="flex:1;">
                    <label>Είδος</label>
                    <input type="text" name="genre" placeholder="π.χ. Δράση">
                </div>
                <div style="flex:1;">
                    <label>Κατάσταση</label>
                    <select name="status">
                        <option value="Available">Διαθέσιμο</option>
                        <option value="Coming Soon">Προσεχώς</option>
                    </select>
                </div>
            </div>

            <label>Περιγραφή</label>
            <textarea name="description" rows="4"></textarea>

            <label>Εικόνα Poster</label>
            <input type="file" name="image" accept="image/*">

            <div class="btn-group">
                <button type="submit" class="btn btn-save">Προσθήκη</button>
                <a href="index.php" class="btn btn-cancel">Ακύρωση</a>
            </div>
        </form>
    </div>
</body>
</html>