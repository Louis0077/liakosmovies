<?php
session_start();
// είναι για το free hosting!
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM movies WHERE movie_id = $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $movie = $result->fetch_assoc();
    } else {
        die("Η ταινία δεν βρέθηκε.");
    }
} else {
    die("Δεν έχει οριστεί ID ταινίας.");
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <title>Λεπτομέρειες: <?php echo htmlspecialchars($movie['title']); ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
        }
        /* 1. Κάνουμε την κάρτα λίγο πιο στενή αν θέλουμε η εικόνα να φαίνεται πιο μαζεμένη */
.movie-card {
    background: white;
    max-width: 500px; /* Από 700px που ήταν, το κάνουμε 500px */
    margin: auto;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

/* 2. Εδώ ορίζουμε το μέγεθος της εικόνας */
.poster-container img {
    width: auto;        /* Μην την αναγκάζεις να πιάνει όλο το πλάτος */
    max-height: 400px;  /* Όρισε ένα μέγιστο ύψος που σου αρέσει (π.χ. 400px) */
    display: block;
    margin: 20px auto;  /* Κεντράρει την εικόνα και δίνει λίγο χώρο γύρω της */
    border-radius: 8px; /* Προαιρετικά, στρογγυλεμένες γωνίες στην ίδια την εικόνα */
}
        .poster-container {
            width: 100%;
            background-color: #fff; /* Λευκό background αν η εικόνα είναι μικρότερη */
            display: block;
        }
        .content {
            padding: 30px;
        }
        h1 {
            margin-top: 0;
            color: #222;
            border-bottom: 3px solid #4CAF50;
            display: inline-block;
            padding-bottom: 5px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }
        .label {
            font-weight: bold;
            color: #666;
            text-transform: uppercase;
            font-size: 0.85em;
            display: block;
        }
        .description-box {
            margin-top: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-left: 5px solid #4CAF50;
            color: #444;
        }
        .actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .btn {
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            margin-right: 10px;
        }
        .btn-back { background-color: #333; color: white; }
        .btn-edit { background-color: #007bff; color: white; }
    </style>
</head>
<body>

    <div class="movie-card">
        <div class="poster-container">
            <?php if (!empty($movie['image'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($movie['image']); ?>" alt="Poster">
            <?php else: ?>
                <div style="text-align:center; padding: 50px; background:#ddd;">Δεν υπάρχει αφίσα</div>
            <?php endif; ?>
        </div>

        <div class="content">
            <h1><?php echo htmlspecialchars($movie['title']); ?></h1>
            
            <div class="info-grid">
                <div class="info-item">
                    <span class="label">Είδος</span>
                    <?php echo htmlspecialchars($movie['genre']); ?>
                </div>
                <div class="info-item">
                    <span class="label">Κατάσταση</span>
                    <strong><?php echo htmlspecialchars($movie['status']); ?></strong>
                </div>
            </div>

            <div class="description-box">
                <span class="label">Περιγραφή</span>
                <?php echo nl2br(htmlspecialchars($movie['description'])); ?>
            </div>

            <div class="actions">
                <a href="index.php" class="btn btn-back">← Επιστροφή</a>
                <a href="edit.php?id=<?php echo $movie['movie_id']; ?>" class="btn btn-edit">Επεξεργασία</a>
            </div>
        </div>
    </div>

</body>
</html>
<?php $conn->close(); ?>