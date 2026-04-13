


<?php
$statusMsg = null;

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


// 1) DATABASE CONNECTION
$conn = new mysqli("localhost","root","","asantravels_og");
if ($conn->connect_error) {
  die("Connection failed: ".$conn->connect_error);
}

// 2) UPLOAD IMAGE
if (isset($_POST["Upload"])) {
  $targetDir    = "as_gallery/";
  $fileName     = basename($_FILES["image"]["name"]);
  $targetFile   = $targetDir.$fileName;
  $title        = $_POST['title'];

  if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
    $stmt = $conn->prepare("INSERT INTO gallery (image,title) VALUES (?,?)");
    $stmt->bind_param("ss",$fileName,$title);
    $stmt->execute();
    $stmt->close();
    $statusMsg = "✅ Image uploaded!";
  } else {
    $statusMsg = "❌ Upload failed.";
  }
}

// 3) SEARCH IMAGE
if (isset($_POST["Search"])) {
  $id    = $conn->real_escape_string($_POST['id']);
  $query = $conn->query("SELECT * FROM gallery WHERE id='$id'");
  if ($query->num_rows) {
    $searchRec = $query->fetch_assoc();
  } else {
    $statusMsg = "⚠️ No image found for ID $id.";
  }
}

// 4) DELETE IMAGE
if (isset($_POST["Delete"])) {
  $id    = $conn->real_escape_string($_POST['id']);
  $query = $conn->query("SELECT * FROM gallery WHERE id='$id'");
  if ($query->num_rows) {
    $rec = $query->fetch_assoc();
    @unlink("as_gallery/".$rec['image']);
    $conn->query("DELETE FROM gallery WHERE id='$id'");
    $statusMsg = "🗑️ Deleted image ID $id.";
  // Renumber IDs to be sequential
  $conn->query("SET @num := 0");
  $conn->query("UPDATE gallery SET id = (@num := @num + 1) ORDER BY id");
  $conn->query("ALTER TABLE gallery AUTO_INCREMENT = 1");
  } else {
    $statusMsg = "⚠️ No image to delete for ID $id.";
  }
}

// 5) REPORT
if (isset($_POST["Report"])) {
  $reportQ = $conn->query("SELECT * FROM gallery");
}

// 6) LOGOUT
if (isset($_POST["Logout"])) {
  header("Location: logout.php");
  exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Gallery Admin • AsanTravels</title>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
  <style>
    :root {
      --bg:        #121212;
      --surface:   #1E1E1E;
      --on-surface:#E0E0E0;
      --primary:   #BB86FC;
      --accent:    #03DAC6;
      --gradient:  linear-gradient(45deg,var(--primary),var(--accent));
      --radius:    8px;
      --trans:     .3s ease;
    }
    * { box-sizing:border-box; margin:0; padding:0; }
    body {
      background: var(--bg);
      color: var(--on-surface);
      font-family:'Roboto',sans-serif;
      min-height:100vh;
      overflow-x:hidden;
    }
    header {
      position:fixed; top:0; left:0; right:0;
      height:56px; background:var(--surface);
      display:flex; align-items:center;
      padding:0 16px;
      box-shadow:0 2px 4px rgba(0,0,0,0.6);
      z-index:10;
    }
    header .material-icons {
      cursor:pointer;
      color:var(--accent);
      margin-right:16px;
      font-size:28px;
      transition:transform var(--trans);
    }
    header .material-icons:hover {
      transform:scale(1.2) rotate(15deg);
    }
    header h1 {
      font-size:1.2rem; font-weight:500;
    }
    nav {
      position:fixed; top:56px; left:-260px;
      width:260px; height:calc(100vh - 56px);
      background:var(--surface);
      box-shadow:2px 0 4px rgba(0,0,0,0.6);
      transition:left var(--trans);
      padding-top:16px; z-index:99;
      overflow-y:auto;
    }
    nav.open { left:0; }
    nav a {
      display:flex; align-items:center;
      padding:12px 24px; margin:4px 8px;
      color:var(--on-surface);
      text-decoration:none;
      border-radius:var(--radius);
      transition:background var(--trans),transform var(--trans);
    }
    nav a:hover {
      background:rgba(255,255,255,0.1);
      transform:translateX(8px);
    }
    nav a .material-icons {
      margin-right:16px;
      color:var(--accent);
    }
    main {
      transition:margin-left var(--trans), filter 0.3s;
      padding:80px 24px 24px;
    }
    nav.open ~ main {
  margin-left:260px;
    }
    .card {
      background:rgba(255,255,255,0.05);
      backdrop-filter:blur(12px);
      border-radius:var(--radius);
      padding:24px;
      margin-bottom:24px;
      box-shadow:0 4px 20px rgba(0,0,0,0.6);
      transition:transform var(--trans),box-shadow var(--trans);
    }
    .card:hover {
      transform:translateY(-6px);
      box-shadow:0 8px 30px rgba(0,0,0,0.8);
    }
    .card h2 {
      margin-bottom:16px;
      font-size:1.25rem;
      font-weight:500;
    }
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
    .btn {
      position:relative; display:inline-block;
      padding:10px 20px; margin:8px 4px 0 0;
      background:var(--gradient);
      color:var(--bg); font-weight:500;
      border:none; border-radius:var(--radius);
      cursor:pointer; overflow:hidden;
      transition:transform var(--trans),box-shadow var(--trans);
    }
    .btn:hover {
      transform:scale(1.05);
      box-shadow:0 6px 20px rgba(0,0,0,0.6);
    }
    .ripple {
      position:absolute; border-radius:50%;
      transform:scale(0);
      background:rgba(255,255,255,0.7);
      animation:ripple-effect .6s linear;
      pointer-events:none;
    }
    @keyframes ripple-effect { to { transform:scale(4); opacity:0; } }
    .report-table {
      width:100%; border-collapse:collapse; margin-top:16px;
    }
    .report-table th, .report-table td {
      padding:12px; text-align:left;
      border-bottom:1px solid rgba(255,255,255,0.15);
      color:#fff;
    }
    .report-table th { background:rgba(255,255,255,0.1); }
    .report-table img {
      max-width:80px; border-radius:4px;
      box-shadow:0 2px 6px rgba(0,0,0,0.5);
    }
    .status {
      margin-bottom:16px;
      padding:12px;
      border-radius:4px;
      background:rgba(255,255,255,0.1);
    }
  </style>
</head>
<body>
  <header>
    <span class="material-icons" id="menuBtn">menu</span>
    <h1>Admin_Panel_Gallery</h1>
  </header>
  <nav id="drawer">
    <a href="admin.php"><span class="material-icons">dashboard</span>Dashboard</a>
    <a href="asn_Gallery.php"><span class="material-icons">photo_library</span>Gallery</a>
    <a href="asn_custom_admin.php"><span class="material-icons">explore</span>Custom Tours</a>
    <a href="asn_Bookings.php"><span class="material-icons">event</span>Bookings</a>
    <a href="asn_Contact.php"><span class="material-icons">mail</span>Contacts</a>
    <a href="asn_Reviews.php"><span class="material-icons">star</span>Reviews</a>
    <a href="asn_subscribers.php"><span class="material-icons">people</span>Subscribers</a>
    <form method="post" style="margin-top:24px; text-align:center;">
      <button name="Logout" class="btn">Logout</button>
    </form>
  </nav>
  <main>
    <?php if (!empty($statusMsg)): ?>
      <div class="status"><?= htmlspecialchars($statusMsg) ?></div>
    <?php endif; ?>
    <!-- MANAGEMENT FORM -->
    <section class="card">
      <h2>Manage Gallery</h2>
      <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
          <label for="title">Title</label>
          <select name="title" id="title" class="md-select" >
            <option value="">-- Select --</option>
            <option>Southern_Coast</option>
            <option>Eastern_Coast</option>
            <option>Northern_Coast</option>
            <option>Western_Coast</option>
            <option>Lankas_Wild_Kingdom</option>
          </select>
        </div>
        <div class="form-group">
          <label for="image">Image File</label>
          <input type="file" name="image" id="image" class="md-input" />
        </div>
        <div class="form-group">
          <label for="id">ID (Search/Delete)</label>
          <input type="text" name="id" id="id" class="md-input" placeholder="Enter ID"/>
        </div>
        <button type="submit" name="Upload" class="btn">Upload</button>
        <button type="submit" name="Search" class="btn">Search</button>
        <button type="submit" name="Delete" class="btn">Delete</button>
        <button type="submit" name="Report" class="btn">Report</button>
      </form>
    </section>
    <!-- SEARCH RESULT -->
    <?php if (!empty($searchRec)): ?>
      <section class="card">
        <h2>Search Result</h2>
        <p><strong>ID:</strong> <?= $searchRec['id'] ?></p>
        <p><strong>Title:</strong> <?= htmlspecialchars($searchRec['title']) ?></p>
        <img src="as_gallery/<?= htmlspecialchars($searchRec['image']) ?>" width="200"/>
      </section>
    <?php endif; ?>
    <!-- REPORT TABLE -->
    <?php if (!empty($reportQ) && $reportQ->num_rows): ?>
      <section class="card">
        <h2>Gallery Report</h2>
        <table class="report-table">
          <thead>
            <tr><th>ID</th><th>Title</th><th>Preview</th></tr>
          </thead>
          <tbody>
            <?php while ($r = $reportQ->fetch_assoc()): ?>
              <tr>
                <td><?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['title']) ?></td>
                <td>
                  <img src="as_gallery/<?= htmlspecialchars($r['image']) ?>"/>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </section>
    <?php endif; ?>
  </main>
  <script>
    // SIDEBAR TOGGLE
    const menuBtn = document.getElementById('menuBtn');
    const drawer = document.getElementById('drawer');
    menuBtn.addEventListener('click', () => {
      drawer.classList.toggle('open');
    });
    // BUTTON RIPPLE
    document.querySelectorAll('.btn').forEach(btn => {
      btn.addEventListener('click', e => {
        const circle = document.createElement('span');
        const d = Math.max(btn.clientWidth, btn.clientHeight);
        circle.style.width = circle.style.height = d+'px';
        circle.style.left = e.clientX - btn.offsetLeft - d/2+'px';
        circle.style.top  = e.clientY - btn.offsetTop  - d/2+'px';
        circle.classList.add('ripple');
        const r = btn.getElementsByClassName('ripple')[0];
        if (r) r.remove();
        btn.appendChild(circle);
      })
    });
  </script>
  <script>
    window.addEventListener("pageshow", function (event) {
        if (event.persisted || window.performance.navigation.type === 2) {
            // Reload to trigger PHP session check
            window.location.reload();
        }
    });
</script>
</body>
</html>
