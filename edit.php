<?php
session_start();

// ειναι για το free hosting!
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }


if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = $conn->query("SELECT * FROM movies WHERE movie_id = $id");
    $movie = $res->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $title = $conn->real_escape_string($_POST['title']);
    $genre = $conn->real_escape_string($_POST['genre']);
    $status = $conn->real_escape_string($_POST['status']);
    $description = $conn->real_escape_string($_POST['description']);
    
    $img_sql = "";
    if (!empty($_FILES['image']['name'])) {
        $img_name = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $img_name);
        $img_sql = ", image='$img_name'";
    }

    $sql = "UPDATE movies SET title='$title', genre='$genre', status='$status', description='$description' $img_sql WHERE movie_id=$id";
    if ($conn->query($sql)) { $_SESSION['flash'] = 'Η ταινία ενημερώθηκε επιτυχώς!';
header("Location: index.php"); exit(); }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Επεξεργασία | Οι ταινίες του Λιάκου</title>
    <style>
        :root { --main-color: #e50914; --dark-bg: #141414; --card-bg: #2f2f2f; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--dark-bg); color: white; display: flex; justify-content: center; padding: 40px; }
        .form-card { background: var(--card-bg); padding: 30px; border-radius: 15px; width: 100%; max-width: 500px; border: 1px solid #444; }
        h2 { color: #ffc107; text-align: center; margin-bottom: 25px; text-transform: uppercase; }
        label { display: block; margin-bottom: 8px; font-size: 14px; color: #bbb; }
        input, textarea, select { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #444; border-radius: 8px; background: #1f1f1f; color: white; box-sizing: border-box; }
        .current-img { width: 60px; height: 80px; object-fit: cover; border-radius: 5px; margin-bottom: 10px; border: 1px solid var(--main-color); }
        .btn-group { display: flex; gap: 10px; }
        .btn { flex: 1; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; text-align: center; text-decoration: none; transition: 0.3s; }
        .btn-save { background: #ffc107; color: black; }
        .btn-cancel { background: #444; color: white; }
        .btn:hover { opacity: 0.8; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="form-card">
        <h2>Επεξεργασία</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $movie['movie_id']; ?>">
            
            <label>Τίτλος</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($movie['title']); ?>" required>
            
            <div style="display:flex; gap:15px;">
                <div style="flex:1;">
                    <label>Είδος</label>
                    <input type="text" name="genre" value="<?php echo htmlspecialchars($movie['genre'] ?? ''); ?>">
                </div>
                <div style="flex:1;">
                    <label>Κατάσταση</label>
                    <select name="status">
                        <option value="Available" <?php if($movie['status'] == 'Available') echo 'selected'; ?>>Διαθέσιμο</option>
                        <option value="Coming Soon" <?php if($movie['status'] == 'Coming Soon') echo 'selected'; ?>>Προσεχώς</option>
                    </select>
                </div>
            </div>

            <label>Περιγραφή</label>
            <textarea name="description" rows="4"><?php echo htmlspecialchars($movie['description']); ?></textarea>

            <label>Αλλαγή Εικόνας</label>
            <?php if($movie['image']): ?>
                <img src="uploads/<?php echo $movie['image']; ?>" class="current-img">
            <?php endif; ?>
            <input type="file" name="image" accept="image/*">

            <div class="btn-group">
                <button type="submit" class="btn btn-save">Ενημέρωση</button>
                <a href="index.php" class="btn btn-cancel">Ακύρωση</a>
            </div>
        </form>
    </div>
</body>
</html>