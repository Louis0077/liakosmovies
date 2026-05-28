<?php
session_start();


// ειναι για το free hosting!
include 'db_connect.php';

if ($conn->connect_error) {
    die("Σφάλμα σύνδεσης: " . $conn->connect_error);
}

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

$search = "";
$where_clause = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where_clause = " WHERE title LIKE '%$search%' OR genre LIKE '%$search%' OR description LIKE '%$search%'";
}

$filter_genre = "";
if (isset($_GET['genre']) && !empty($_GET['genre'])) {
    $filter_genre = $conn->real_escape_string($_GET['genre']);
    if (empty($where_clause)) {
        $where_clause = " WHERE genre LIKE '%$filter_genre%'";
    } else {
        $where_clause .= " AND genre LIKE '%$filter_genre%'";
    }
}

$flash = isset($_SESSION['flash']) ? $_SESSION['flash'] : '';
unset($_SESSION['flash']);

$sql = "SELECT * FROM movies" . $where_clause . " ORDER BY title ASC";
$result = $conn->query($sql);
$genres_result = $conn->query("SELECT DISTINCT genre FROM movies ORDER BY genre");
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Οι Ταινίες του Λιάκου</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #141414; color: #fff; font-family: 'Segoe UI', Arial, sans-serif; }
        .header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            border-bottom: 3px solid #e50914;
            padding: 15px 30px;
            display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 2px 20px rgba(229,9,20,0.3);
        }
        .header h1 { color: #e50914; font-size: 1.8rem; letter-spacing: 2px; text-transform: uppercase; }
        .header-right { display: flex; align-items: center; gap: 15px; font-size: 0.9rem; color: #aaa; }
        .header-right a { color: #e50914; text-decoration: none; font-weight: bold; }
        .header-right a:hover { color: #ff4444; }
        .container { max-width: 1400px; margin: 0 auto; padding: 20px 30px; }
        .flash-message {
            background: linear-gradient(135deg, #155724, #28a745);
            color: #fff; padding: 12px 20px; border-radius: 8px;
            margin-bottom: 20px; font-weight: bold; text-align: center;
            animation: fadeOut 4s forwards; box-shadow: 0 4px 15px rgba(40,167,69,0.3);
        }
        @keyframes fadeOut { 0%,70%{opacity:1} 100%{opacity:0} }
        .search-section { margin: 25px 0 20px; }
        .search-form { display: flex; gap: 10px; max-width: 600px; margin: 0 auto; }
        .search-form input {
            flex: 1; padding: 12px 18px; border-radius: 30px;
            border: 2px solid #333; background: #222; color: #fff;
            font-size: 1rem; outline: none; transition: border-color 0.3s;
        }
        .search-form input:focus { border-color: #e50914; }
        .search-form button {
            padding: 12px 25px; border-radius: 30px; border: none;
            background: #e50914; color: #fff; font-weight: bold;
            font-size: 1rem; cursor: pointer; transition: background 0.3s;
        }
        .search-form button:hover { background: #c40811; }
        .filters-section {
            display: flex; flex-wrap: wrap; gap: 10px;
            align-items: center; justify-content: center; margin: 20px 0;
        }
        .filter-label { color: #aaa; font-size: 0.9rem; }
        .filter-btn {
            padding: 7px 18px; border-radius: 20px; border: 2px solid #444;
            background: transparent; color: #ccc; font-size: 0.85rem;
            cursor: pointer; text-decoration: none; transition: all 0.25s; display: inline-block;
        }
        .filter-btn:hover, .filter-btn.active { border-color: #e50914; color: #fff; background: #e50914; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .results-count { color: #aaa; font-size: 0.9rem; }
        .btn-add {
            padding: 10px 22px; background: #28a745; color: #fff; border-radius: 8px;
            text-decoration: none; font-weight: bold; font-size: 0.95rem;
            transition: background 0.3s, transform 0.2s; display: inline-block;
        }
        .btn-add:hover { background: #218838; transform: translateY(-2px); }
        .movies-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 25px; }
        .movie-card {
            background: #1e1e1e; border-radius: 12px; overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s; cursor: pointer;
            border: 1px solid #2a2a2a;
        }
        .movie-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 40px rgba(229,9,20,0.35); border-color: #e50914;
        }
        .movie-card a.card-link { text-decoration: none; color: inherit; display: block; }
        .movie-poster { width: 100%; height: 300px; object-fit: cover; display: block; }
        .movie-info { padding: 14px; }
        .movie-title {
            font-size: 1rem; font-weight: bold; margin-bottom: 8px; color: #fff;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .badges { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 8px; }
        .badge-genre { background: #333; color: #ccc; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; }
        .badge-status { padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; }
        .badge-status.released { background: #28a745; color: #fff; }
        .badge-status.planning { background: #fd7e14; color: #fff; }
        .badge-status.post { background: #6f42c1; color: #fff; }
        .badge-status.other { background: #555; color: #ddd; }
        .movie-desc {
            font-size: 0.82rem; color: #999; line-height: 1.4;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .rating { display: flex; gap: 2px; margin: 8px 0; }
        .star { color: #ffd700; font-size: 0.9rem; }
        .star.empty { color: #444; }
        .admin-tools { display: flex; gap: 8px; padding: 10px 14px; background: #161616; border-top: 1px solid #2a2a2a; }
        .btn-edit, .btn-delete {
            flex: 1; text-align: center; padding: 6px 0; border-radius: 6px;
            font-size: 0.8rem; font-weight: bold; text-decoration: none; transition: background 0.2s;
        }
        .btn-edit { background: #1a6040; color: #4caf50; }
        .btn-edit:hover { background: #28a745; color: #fff; }
        .btn-delete { background: #5a1a1a; color: #e57373; }
        .btn-delete:hover { background: #e50914; color: #fff; }
        .no-results { text-align: center; color: #888; font-size: 1.1rem; padding: 60px 20px; }
        .no-results span { font-size: 3rem; display: block; margin-bottom: 15px; }
        .modal-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.75); z-index: 1000;
            justify-content: center; align-items: center;
        }
        .modal-overlay.active { display: flex; }
        .modal-box {
            background: #1e1e1e; border: 1px solid #e50914; border-radius: 12px;
            padding: 30px; max-width: 400px; width: 90%; text-align: center;
        }
        .modal-box h3 { font-size: 1.3rem; margin-bottom: 10px; color: #fff; }
        .modal-box p { color: #aaa; margin-bottom: 25px; font-size: 0.95rem; }
        .modal-btns { display: flex; gap: 12px; justify-content: center; }
        .modal-cancel { padding: 10px 25px; border-radius: 8px; background: #333; color: #fff; border: none; cursor: pointer; }
        .modal-confirm { padding: 10px 25px; border-radius: 8px; background: #e50914; color: #fff; border: none; cursor: pointer; font-weight: bold; }
        .modal-confirm:hover { background: #c40811; }
        @media (max-width: 768px) {
            .header { flex-direction: column; gap: 10px; text-align: center; }
            .header h1 { font-size: 1.3rem; }
            .container { padding: 15px; }
            .movies-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 15px; }
            .movie-poster { height: 220px; }
            .top-bar { flex-direction: column; gap: 10px; align-items: flex-start; }
        }
        @media (max-width: 480px) {
            .movies-grid { grid-template-columns: repeat(2, 1fr); }
            .search-form { flex-direction: column; }
            .search-form input, .search-form button { border-radius: 8px; }
        }
    </style>
</head>
<body>
<div class="header">
    <h1>&#127916; Οι Ταινίες του Λιάκου</h1>
    <div class="header-right">
        <?php if (isset($_SESSION['username'])): ?>
            Σύνδεση: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Σύνδεση</a>
        <?php endif; ?>
    </div>
</div>
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <h3>&#9888; Διαγραφή Ταινίας</h3>
        <p>Είσαι σίγουρος ότι θέλεις να διαγράψεις αυτή την ταινία; Η ενέργεια δεν αναιρείται.</p>
        <div class="modal-btns">
            <button class="modal-cancel" onclick="closeDeleteModal()">Ακύρωση</button>
            <a id="confirmDeleteBtn" href="#" class="modal-confirm">Διαγραφή</a>
        </div>
    </div>
</div>
<div class="container">
    <?php if (!empty($flash)): ?>
        <div class="flash-message">&#10003; <?php echo htmlspecialchars($flash); ?></div>
    <?php endif; ?>
    <div class="search-section">
        <form class="search-form" method="GET" action="index.php">
            <input type="text" name="search" placeholder="Αναζήτηση ταινίας, είδους ή περιγραφής..." value="<?php echo htmlspecialchars($search); ?>">
            <?php if (!empty($filter_genre)): ?>
                <input type="hidden" name="genre" value="<?php echo htmlspecialchars($filter_genre); ?>">
            <?php endif; ?>
            <button type="submit">Αναζήτηση</button>
        </form>
    </div>
    <div class="filters-section">
        <span class="filter-label">Φίλτρο:</span>
        <a href="index.php<?php echo !empty($search) ? '?search='.urlencode($search) : ''; ?>" class="filter-btn <?php echo empty($filter_genre) ? 'active' : ''; ?>">Όλες</a>
        <?php if ($genres_result && $genres_result->num_rows > 0): ?>
            <?php while ($g = $genres_result->fetch_assoc()): ?>
                <a href="index.php?genre=<?php echo urlencode($g['genre']); ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?>"
                   class="filter-btn <?php echo ($filter_genre === $g['genre']) ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($g['genre']); ?>
                </a>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
    <div class="top-bar">
        <div class="results-count">
            <?php
            $count = $result ? $result->num_rows : 0;
            echo $count . " ταιν" . ($count === 1 ? "ία" : "ίες") . " βρέθηκαν";
            if (!empty($search)) echo " για &quot;" . htmlspecialchars($search) . "&quot;";
            if (!empty($filter_genre)) echo " στο είδος &quot;" . htmlspecialchars($filter_genre) . "&quot;";
            ?>
        </div>
        <?php if ($is_admin): ?>
            <a href="insert.php" class="btn-add">+ Προσθήκη Ταινίας</a>
        <?php endif; ?>
    </div>
    <?php if ($result && $result->num_rows > 0): ?>
        <div class="movies-grid">
        <?php while ($row = $result->fetch_assoc()):
            $m_id = $row['movie_id'];
            $status = strtolower(trim($row['status'] ?? ''));
            $status_class = in_array($status, ['released','planning','post']) ? $status : 'other';
            $status_label = strtoupper($status);
            $rating = intval($row['rating'] ?? 0);
            $poster = !empty($row['image']) ? $row['image'] : '';
            if (!empty($poster) && strpos($poster, 'http') !== 0) {
                $poster = 'uploads/' . $poster;
            }
        ?>
            <div class="movie-card">
                <a class="card-link" href="details.php?id=<?php echo $m_id; ?>">
                    <img class="movie-poster"
                         src="<?php echo !empty($poster) ? htmlspecialchars($poster) : 'https://placehold.co/220x300/1e1e1e/666?text=No+Image'; ?>"
                         alt="<?php echo htmlspecialchars($row['title']); ?>"
                         onerror="this.src='https://placehold.co/220x300/1e1e1e/666?text=No+Image'">
                    <div class="movie-info">
                        <div class="movie-title" title="<?php echo htmlspecialchars($row['title']); ?>">
                            <?php echo htmlspecialchars($row['title']); ?>
                        </div>
                        <div class="badges">
                            <span class="badge-genre"><?php echo htmlspecialchars($row['genre']); ?></span>
                            <?php if (!empty($status_label)): ?>
                                <span class="badge-status <?php echo $status_class; ?>"><?php echo $status_label; ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($rating > 0): ?>
                        <div class="rating">
                            <?php for ($i=1; $i<=5; $i++): ?>
                                <span class="star <?php echo $i <= $rating ? '' : 'empty'; ?>">&#9733;</span>
                            <?php endfor; ?>
                        </div>
                        <?php endif; ?>
                        <p class="movie-desc"><?php echo htmlspecialchars($row['description'] ?? ''); ?></p>
                    </div>
                </a>
                <?php if ($is_admin): ?>
                <div class="admin-tools">
                    <a href="edit.php?id=<?php echo $m_id; ?>" class="btn-edit">&#9999; Επεξεργασία</a>
                    <a href="#" class="btn-delete" onclick="openDeleteModal('delete.php?id=<?php echo $m_id; ?>'); return false;">&#128465; Διαγραφή</a>
                </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="no-results">
            <span>&#127916;</span>
            Δεν βρέθηκαν ταινίες<?php echo !empty($search) ? " για &quot;" . htmlspecialchars($search) . "&quot;" : ""; ?>.
        </div>
    <?php endif; ?>
</div>
<script>
function openDeleteModal(url) {
    document.getElementById('confirmDeleteBtn').href = url;
    document.getElementById('deleteModal').classList.add('active');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
}
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
</body>
</html>