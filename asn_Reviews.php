
<?php

session_start();
$referer = $_SERVER['HTTP_REFERER'] ?? '';
if (!isset($_SESSION['loggedin'])) {
    header('Location:asn_admin_loging.php');
    exit();
}
if (empty($referer)) {
    header('Location:asn_admin_loging.php');
    exit();
}

$referer = $_SERVER['HTTP_REFERER'] ?? '';

// 1) CONNECT
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: asn_admin_loging.php');
    exit;
}
$conn = new mysqli("localhost","root","","asantravels_og");
if($conn->connect_error) {
    die("DB Connection failed: ".$conn->connect_error);
}

$statusMsg = "";
$searchRec = null;
$reportQ   = null;

// 2) SEARCH
if(isset($_POST['search'])) {
    $id   = (int)$_POST['id'];
    $res  = $conn->query("SELECT * FROM reviews WHERE id=$id");
    if($res && $res->num_rows) {
        $searchRec = $res->fetch_assoc();
    } else {
        $statusMsg = "⚠️ No review found with ID $id.";
    }
}

// 3) UPDATE STATUS
if(isset($_POST['update'])) {
    $id     = (int)$_POST['id'];
    $status = $conn->real_escape_string($_POST['status']);

    $upd = $conn->prepare("UPDATE reviews SET status=? WHERE id=?");
    $upd->bind_param("si", $status, $id);
    if($upd->execute()) {
        $statusMsg = "✅ Updated status for review #$id to “$status.”";
    } else {
        $statusMsg = "❌ Update failed: ".$upd->error;
    }
    $upd->close();
}

// 4) DELETE
if(isset($_POST['delete'])) {
    $id  = (int)$_POST['id'];
    $res = $conn->query("SELECT id FROM reviews WHERE id=$id");
    if($res && $res->num_rows) {
        $conn->query("DELETE FROM reviews WHERE id=$id");
        $statusMsg = "🗑️ Review #$id deleted.";
    // Renumber IDs to be sequential
    $conn->query("SET @num := 0");
    $conn->query("UPDATE reviews SET id = (@num := @num + 1) ORDER BY id");
    $conn->query("ALTER TABLE reviews AUTO_INCREMENT = 1");
    } else {
        $statusMsg = "⚠️ No review #$id to delete.";
    }
}

// 5) REPORT
if(isset($_POST['report'])) {
    $reportQ = $conn->query("SELECT * FROM reviews");
    if(!$reportQ || !$reportQ->num_rows) {
        $statusMsg = "ℹ️ No reviews to display.";
        $reportQ   = null;
    }
}

// 6) LOGOUT
if(isset($_POST['logout'])) {
    header("Location: logout.php");
    exit;
}

// Configuration: central public assets host (set to your InfinityFree domain)
// Make sure this domain is added to your hosting account and files are uploaded to /uploads
$assetsBase = 'https://asantravels.xo.je/uploads/';

// Placeholder path (relative to web root) — create this file at htdocs/assets/img/placeholder-80.png
$placeholderPath = '/assets/img/placeholder-80.png';

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>Admin Panel • Reviews</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <style>
        :root {
            --bg: #121212;
            --surf: #1E1E1E;
            --text: #E0E0E0;
            --primary: #BB86FC;
            --accent: #03DAC6;
            --grad: linear-gradient(45deg,var(--primary),var(--accent));
            --rad: 8px;
            --dur: .3s;
        }
        * { box-sizing:border-box; margin:0; padding:0; }
        body {
            font-family:'Roboto',sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height:100vh;
            overflow-x:hidden;
        }
        /* — Top Bar — */
        header {
            position:fixed; top:0; left:0; right:0; height:56px;
            background:var(--surf);
            display:flex; align-items:center; padding:0 16px;
            box-shadow:0 2px 4px rgba(0,0,0,0.6);
            z-index:10;
        }
        header .material-icons {
            cursor:pointer; color:var(--accent); margin-right:16px; font-size:28px;
            transition:transform var(--dur);
        }
        header .material-icons:hover {
            transform:scale(1.2) rotate(15deg);
        }
        header h1 {
            font-size:1.2rem; font-weight:500;
        }
        /* — Sidebar — */
        nav {
            position:fixed; top:56px; left:-240px; bottom:0; width:240px;
            background:var(--surf);
            box-shadow:2px 0 4px rgba(0,0,0,0.6);
            transition:left var(--dur);
            padding-top:16px; z-index:9;
        }
        nav.open {
            left:0;
        }
        nav a {
            display:flex; align-items:center;
            padding:12px 24px; margin:4px 8px;
            color:var(--text); text-decoration:none;
            border-radius:var(--rad);
            transition:background var(--dur),transform var(--dur);
        }
        nav a:hover {
            background:rgba(255,255,255,0.1);
            transform:translateX(8px);
        }
        nav a .material-icons {
            margin-right:16px; color:var(--accent);
        }
        /* — Main Content — */
        main {
            padding:80px 24px 24px;
            transition:margin-left var(--dur);
        }
        nav.open ~ main {
            margin-left:240px;
        }
        /* — Glass Card — */
        .card {
            background:rgba(255,255,255,0.05);
            backdrop-filter:blur(12px);
            border-radius:var(--rad);
            padding:24px; margin-bottom:24px;
            box-shadow:0 4px 20px rgba(0,0,0,0.6);
            transition:transform var(--dur),box-shadow var(--dur);
        }
        .card:hover {
            transform:translateY(-6px);
            box-shadow:0 8px 30px rgba(0,0,0,0.8);
        }
        .card h2 {
            margin-bottom:16px;
            font-size:1.25rem; font-weight:500;
        }
        /* — Forms — */
        .form-group { margin-bottom:16px; }
        .form-group label {
            display:block; margin-bottom:6px; font-size:.9rem;
        }
        .md-input {
            width:100%; padding:12px;
            border:none; border-radius:4px;
            background:rgba(255,255,255,0.1);
            color:#fff; transition:background var(--dur);
        }
        .md-select {
            width:100%; padding:12px;
            border:none; border-radius:4px;
            background-color: #000 !important;
            color:#fff; transition:background var(--dur);
        }
        .md-input:focus, .md-select:focus {
            background:rgba(255,255,255,0.2);
        }
        /* — Buttons & Ripple — */
        .btn {
            position:relative; display:inline-block;
            padding:10px 20px; margin:8px 4px 0 0;
            background:var(--grad); color:var(--bg);
            font-weight:500; border:none; border-radius:var(--rad);
            cursor:pointer; overflow:hidden;
            transition:transform var(--dur),box-shadow var(--dur);
        }
        .btn:hover {
            transform:scale(1.05);
            box-shadow:0 6px 20px rgba(0,0,0,0.6);
        }
        .ripple {
            position:absolute; border-radius:50%; transform:scale(0);
            background:rgba(255,255,255,0.7);
            animation:ripple-effect .6s linear; pointer-events:none;
        }
        @keyframes ripple-effect {
            to { transform:scale(4); opacity:0; }
        }
        /* — Status & Table — */
        .status {
            padding:12px; margin-bottom:16px;
            border-radius:4px; background:rgba(255,255,255,0.1);
        }
        .report-table {
            width:100%; border-collapse:collapse; margin-top:16px;
        }
        .report-table th, .report-table td {
            padding:12px; text-align:left;
            border-bottom:1px solid rgba(255,255,255,0.15);
        }
        .report-table th {
            background:rgba(255,255,255,0.1);
        }
    </style>
</head>
<body>

    <!-- Top Bar -->
    <header>
        <span class="material-icons" id="menuBtn">menu</span>
        <h1>Admin_Panel_Reviews</h1>
    </header>

    <!-- Sidebar -->
    <nav id="drawer">
    <a href="admin.php"><span class="material-icons">dashboard</span>Dashboard</a>
    <a href="asn_Gallery.php"><span class="material-icons">photo_library</span>Gallery</a>
    <a href="asn_custom_admin.php"><span class="material-icons">explore</span>Custom Tours</a>
    <a href="asn_Bookings.php"><span class="material-icons">event</span>Bookings</a>
    <a href="asn_Contact.php"><span class="material-icons">mail</span>Contacts</a>
    <a href="asn_Reviews.php"><span class="material-icons">star</span>Reviews</a>
    <a href="asn_subscribers.php"><span class="material-icons">people</span>Subscribers</a>
        <form method="post" style="text-align:center; margin-top:24px;">
            <button name="logout" class="btn">Logout</button>
        </form>
    </nav>

    <!-- Main Area -->
    <main>
        <?php if($statusMsg): ?>
            <div class="status"><?= htmlspecialchars($statusMsg) ?></div>
        <?php endif; ?>

        <!-- Manage Review Status -->
        <section class="card">
            <h2>Manage Reviews</h2>
            <form method="post">
                <div class="form-group">
                    <label>Review ID</label>
                    <input type="text" name="id" class="md-input" placeholder="Enter review ID…" />
                </div>
                <button type="submit" name="search" class="btn">Search</button>
                <button type="submit" name="update" class="btn">Update Status</button>
                <button type="submit" name="delete" class="btn">Delete</button>
                <button type="submit" name="report" class="btn">Report</button>
            </form>
        </section>

        <!-- Search Result -->
        <?php if($searchRec): ?>
            <section class="card">
                <h2>Search Result</h2>
                <?php foreach($searchRec as $key => $val): ?>
                    <p>
                        <strong><?= ucfirst(str_replace('_',' ',$key)) ?>:</strong>
                        <?= htmlspecialchars($val) ?>
                    </p>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>

        <!-- Report Table -->
        <?php if($reportQ): ?>
            <section class="card">
                <h2>All Reviews</h2>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Rating</th>
                            <th>Comments</th>
                            <th>Picture</th>
                            <th>Status</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($r = $reportQ->fetch_assoc()): ?>
                                                        <tr>
                                                                <form method="post">
                                                                        <td><input type="hidden" name="id" value="<?= $r['id'] ?>" /><?= $r['id'] ?></td>
                                                                        <td><input type="text" name="name" value="<?= htmlspecialchars($r['name']) ?>" class="md-input" /></td>
                                                                        <td><input type="email" name="email" value="<?= htmlspecialchars($r['email']) ?>" class="md-input" /></td>
                                                                        <td><input type="number" name="rating" value="<?= htmlspecialchars($r['rating']) ?>" class="md-input" min="1" max="5" /></td>
                                                                        <td><textarea name="comments" class="md-input"><?= htmlspecialchars($r['comments']) ?></textarea></td>
                                                                        <td>
                                                                            <?php if(!empty($r['filename'])): ?>
                                                                                <?php
                                                                                $filename = $r['filename'];
                                                                                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                                                                $served = false;
                                                                                // 1) try central assets host if configured
                                                                                if (!empty($assetsBase)) {
                                                                                    $remoteUrl = rtrim($assetsBase, '/') . '/' . rawurlencode($filename);
                                                                                    $headers = @get_headers($remoteUrl);
                                                                                    if ($headers && strpos($headers[0], '200') !== false) {
                                                                                        if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                                                                                            echo '<img src="' . htmlspecialchars($remoteUrl) . '" alt="Review Image" style="max-width:80px;max-height:80px;border-radius:6px;" />';
                                                                                        } elseif ($ext === 'pdf') {
                                                                                            echo '<a href="' . htmlspecialchars($remoteUrl) . '" target="_blank">PDF</a>';
                                                                                        } else {
                                                                                            echo '<a href="' . htmlspecialchars($remoteUrl) . '" target="_blank">File</a>';
                                                                                        }
                                                                                        $served = true;
                                                                                    }
                                                                                }
                                                                                // 2) fallback to local uploads folder
                                                                                if (!$served) {
                                                                                    $localPath = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $filename;
                                                                                    if (file_exists($localPath)) {
                                                                                        $localUrl = 'uploads/' . rawurlencode($filename);
                                                                                        if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                                                                                            echo '<img src="' . htmlspecialchars($localUrl) . '" alt="Review Image" style="max-width:80px;max-height:80px;border-radius:6px;" />';
                                                                                        } elseif ($ext === 'pdf') {
                                                                                            echo '<a href="' . htmlspecialchars($localUrl) . '" target="_blank">PDF</a>';
                                                                                        } else {
                                                                                            echo '<a href="' . htmlspecialchars($localUrl) . '" target="_blank">File</a>';
                                                                                        }
                                                                                        $served = true;
                                                                                    }
                                                                                }
                                                                                // 3) final fallback: placeholder + debug note
                                                                                if (!$served) {
                                                                                    echo '<img src="' . htmlspecialchars($placeholderPath) . '" alt="Missing" style="max-width:80px;max-height:80px;border-radius:6px;opacity:.6;" />';
                                                                                    echo '<div style="color:#ffd54f;font-size:11px;margin-top:6px;">Missing file on server: ' . htmlspecialchars($filename) . '</div>';
                                                                                }
                                                                                ?>
                                                                            <?php else: ?>
                                                                                —
                                                                            <?php endif; ?>
                                                                        </td>
                                                                        <td>
                                                                                <select name="status" class="md-select">
                                                                                        <option value="Pending"<?= (isset($r['status']) && $r['status']==='Pending')?' selected':''; ?>>Pending</option>
                                                                                        <option value="Approved"<?= (isset($r['status']) && $r['status']==='Approved')?' selected':''; ?>>Approved</option>
                                                                                        <option value="Rejected"<?= (isset($r['status']) && $r['status']==='Rejected')?' selected':''; ?>>Rejected</option>
                                                                                </select>
                                                                        </td>
                                                                        <td><button type="submit" name="update" class="btn">Update</button></td>
                                                                </form>
                                                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
        <?php endif; ?>

    </main>

    <script>
        // Sidebar toggle
        document.getElementById('menuBtn')
            .addEventListener('click', () => {
                document.getElementById('drawer').classList.toggle('open');
            });

        // Ripple effect
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', e => {
                const c = document.createElement('span');
                const d = Math.max(btn.clientWidth, btn.clientHeight);
                c.style.width  = c.style.height = d + 'px';
                c.style.left   = e.clientX - btn.offsetLeft  - d/2 + 'px';
                c.style.top    = e.clientY - btn.offsetTop   - d/2 + 'px';
                c.className    = 'ripple';
                const oldRipple = btn.querySelector('.ripple');
                if(oldRipple) oldRipple.remove();
                btn.appendChild(c);
            });
        });
    </script>
</body>
</html>