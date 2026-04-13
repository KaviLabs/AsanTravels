<?php
// Connect to database
$conn = new mysqli("localhost", "root", "", "asantravels_og");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Create locations table if it doesn't exist
$conn->query("CREATE TABLE IF NOT EXISTS locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    latitude DECIMAL(10, 6) DEFAULT 0,
    longitude DECIMAL(10, 6) DEFAULT 0,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Auto-populate locations from existing custom_tours data
$existing_locations = $conn->query("SELECT DISTINCT location FROM custom_tours WHERE location IS NOT NULL AND location != ''");
if ($existing_locations && $existing_locations->num_rows > 0) {
    while ($loc_row = $existing_locations->fetch_assoc()) {
        $loc_name = $loc_row['location'];
        // Check if location already exists
        $check = $conn->query("SELECT id FROM locations WHERE name = '" . $conn->real_escape_string($loc_name) . "'");
        if ($check && $check->num_rows == 0) {
            // Insert new location
            $conn->query("INSERT INTO locations (name, latitude, longitude, description) VALUES ('" . $conn->real_escape_string($loc_name) . "', 0, 0, '')");
        }
    }
};

// Handle actions
$action = $_GET['action'] ?? '';
$message = '';
$error = '';
$edit_location = null;
$edit_activity = null;

// Update Location
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_location'])) {
    $location_id = intval($_POST['location_id']);
    $location_name = trim($_POST['location_name']);
    $description = trim($_POST['description']);
    
    if ($location_id && $location_name) {
        $stmt = $conn->prepare("UPDATE locations SET name = ?, description = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("ssi", $location_name, $description, $location_id);
            if ($stmt->execute()) {
                $message = "✓ Location updated successfully!";
                // Refresh to show update
                sleep(1);
                header("Refresh: 1");
            } else {
                if ($stmt->errno == 1062) {
                    $error = "✗ Location name already exists!";
                } else {
                    $error = "✗ Error updating location: " . $stmt->error;
                }
            }
            $stmt->close();
        } else {
            $error = "✗ Database error: " . $conn->error;
        }
    } else {
        $error = "✗ Please fill in all required fields";
    }
}

// Update Activity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_activity'])) {
    $activity_id = intval($_POST['activity_id']);
    $activity_name = trim($_POST['activity_name']);
    $category = trim($_POST['category']);
    $location_id = intval($_POST['location_id']);
    $description = trim($_POST['description']);
    $foreign_adult_usd = floatval($_POST['foreign_adult_usd']);
    $foreign_child_usd = floatval($_POST['foreign_child_usd']);
    
    // Get location name
    $loc_stmt = $conn->prepare("SELECT name FROM locations WHERE id = ?");
    if ($loc_stmt) {
        $loc_stmt->bind_param("i", $location_id);
        $loc_stmt->execute();
        $loc_result = $loc_stmt->get_result();
        $location_row = $loc_result->fetch_assoc();
        $location_name = $location_row ? $location_row['name'] : '';
        $loc_stmt->close();
    } else {
        $location_name = '';
    }
    
    if ($activity_id && $activity_name && $category && $location_id > 0 && $foreign_adult_usd >= 0 && $foreign_child_usd >= 0) {
        $stmt = $conn->prepare("UPDATE custom_tours SET activity = ?, category = ?, location = ?, description = ?, foreign_adult_usd = ?, foreign_child_usd = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("ssssddi", $activity_name, $category, $location_name, $description, $foreign_adult_usd, $foreign_child_usd, $activity_id);
            if ($stmt->execute()) {
                $message = "✓ Activity updated successfully!";
                // Refresh to show update
                sleep(1);
                header("Refresh: 1");
            } else {
                $error = "✗ Error updating activity: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "✗ Database error: " . $conn->error;
        }
    } else {
        $error = "✗ Please fill in all required fields with valid values";
    }
}

// Add Location
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_location'])) {
    $location_name = trim($_POST['location_name']);
    $description = trim($_POST['description']);
    $latitude = 0.0; // Default latitude
    $longitude = 0.0; // Default longitude
    
    if ($location_name) {
        $stmt = $conn->prepare("INSERT INTO locations (name, latitude, longitude, description) VALUES (?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("sdds", $location_name, $latitude, $longitude, $description);
            if ($stmt->execute()) {
                $message = "Location added successfully!";
            } else {
                if ($stmt->errno == 1062) {
                    $error = "Location already exists!";
                } else {
                    $error = "Error adding location: " . $stmt->error;
                }
            }
            $stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    } else {
        $error = "Please fill in all required fields";
    }
}

// Delete Location
if ($action === 'delete_location' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM locations WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "Location deleted successfully!";
        } else {
            $error = "Error deleting location";
        }
        $stmt->close();
    } else {
        $error = "Database error: " . $conn->error;
    }
    header("Location: custom_admin.php");
    exit;
}

// Add Activity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_activity'])) {
    $activity_name = trim($_POST['activity_name']);
    $category = trim($_POST['category']);
    $location_id = intval($_POST['location_id']);
    $description = trim($_POST['description']);
    $foreign_adult_usd = floatval($_POST['foreign_adult_usd']);
    $foreign_child_usd = floatval($_POST['foreign_child_usd']);
    
    // Get location name
    $loc_stmt = $conn->prepare("SELECT name FROM locations WHERE id = ?");
    if ($loc_stmt) {
        $loc_stmt->bind_param("i", $location_id);
        $loc_stmt->execute();
        $loc_result = $loc_stmt->get_result();
        $location_row = $loc_result->fetch_assoc();
        $location_name = $location_row ? $location_row['name'] : '';
        $loc_stmt->close();
    } else {
        $location_name = '';
    }
    
    if ($activity_name && $category && $location_id > 0 && $foreign_adult_usd >= 0 && $foreign_child_usd >= 0) {
        // Add to custom_tours table
        $stmt = $conn->prepare("INSERT INTO custom_tours (activity, category, location, description, foreign_adult_usd, foreign_child_usd) VALUES (?, ?, ?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("ssssdd", $activity_name, $category, $location_name, $description, $foreign_adult_usd, $foreign_child_usd);
            
            if ($stmt->execute()) {
                $message = "Activity added successfully!";
            } else {
                $error = "Error adding activity: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    } else {
        $error = "Please fill in all required fields with valid values";
    }
}

// Delete Activity
if ($action === 'delete_activity' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM custom_tours WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "Activity deleted successfully!";
        } else {
            $error = "Error deleting activity";
        }
        $stmt->close();
    } else {
        $error = "Database error: " . $conn->error;
    }
    header("Location: custom_admin.php?tab=activities");
    exit;
}

// Get all locations
$locations_result = $conn->query("SELECT * FROM locations ORDER BY name");
$locations = $locations_result && $locations_result->num_rows > 0 ? $locations_result->fetch_all(MYSQLI_ASSOC) : [];

// Get all activities from custom_tours (use actual column names)
$activities_result = $conn->query("SELECT id, activity, category, location, description, foreign_adult_usd, foreign_child_usd, created_at FROM custom_tours ORDER BY activity");
$activities = $activities_result && $activities_result->num_rows > 0 ? $activities_result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Custom Tours Admin • AsanTravels</title>
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
    .md-input, .md-textarea {
      width:100%; padding:12px;
      border:none; border-radius:4px;
      background:rgba(255,255,255,0.1);
      color:#fff; transition:background var(--trans);
    }
    .md-select {
      width:100%; padding:12px;
      border:none; border-radius:4px;
      background-color: #000 !important;
      color:#fff; transition:background var(--trans);
    }
    .md-textarea { resize:vertical; min-height:60px; }
    .md-input:focus, .md-textarea:focus, .md-select:focus {
      background:rgba(255,255,255,0.2);
      outline:none;
    }
    .btn {
      position:relative; display:inline-block;
      padding:10px 20px; margin:8px 4px 0 0;
      background:var(--gradient);
      color:var(--bg); font-weight:500;
      border:none; border-radius:var(--radius);
      cursor:pointer; overflow:hidden;
      transition:transform var(--trans),box-shadow var(--trans);
      text-decoration:none;
    }
    .btn:hover {
      transform:scale(1.05);
      box-shadow:0 6px 20px rgba(0,0,0,0.6);
      color:var(--bg);
    }
    .btn-delete {
      background:linear-gradient(45deg,#CF6679,#B00020);
      color:#fff;
    }
    .btn-delete:hover { color:#fff; }
    .btn-small {
      padding:6px 14px; margin:0; font-size:0.85rem;
    }
    .ripple {
      position:absolute; border-radius:50%;
      transform:scale(0);
      background:rgba(255,255,255,0.7);
      animation:ripple-effect .6s linear;
      pointer-events:none;
    }
    @keyframes ripple-effect { to { transform:scale(4); opacity:0; } }
    .status {
      margin-bottom:16px;
      padding:12px;
      border-radius:4px;
      background:rgba(255,255,255,0.1);
    }
    .status-error {
      background:rgba(176,0,32,0.3);
    }
    .tab-buttons {
      display:flex; gap:8px; margin-bottom:24px; flex-wrap:wrap;
    }
    .tab-btn {
      padding:10px 24px;
      background:rgba(255,255,255,0.08);
      color:var(--on-surface);
      border:2px solid transparent;
      border-radius:var(--radius);
      cursor:pointer;
      font-size:1rem; font-weight:500;
      transition:all var(--trans);
      display:flex; align-items:center; gap:8px;
    }
    .tab-btn:hover { background:rgba(255,255,255,0.15); }
    .tab-btn.active {
      border-color:var(--accent);
      background:rgba(3,218,198,0.15);
      color:var(--accent);
    }
    .tab-pane { display:none; }
    .tab-pane.active { display:block; }
    .grid-2 {
      display:grid;
      grid-template-columns:1fr 1fr;
      gap:24px;
    }
    @media (max-width:768px) {
      .grid-2 { grid-template-columns:1fr; }
    }
    .list-row {
      display:flex; justify-content:space-between; align-items:center;
      padding:12px;
      border-bottom:1px solid rgba(255,255,255,0.1);
      transition:background var(--trans);
      gap:12px;
    }
    .list-row:hover { background:rgba(255,255,255,0.05); }
    .list-row > div:first-child { flex:1; min-width:0; overflow:hidden; }
    .list-row > div:last-child { display:flex; align-items:center; gap:6px; flex-shrink:0; white-space:nowrap; }
    .list-name { font-weight:500; color:var(--primary); }
    .list-desc { font-size:.85rem; color:rgba(255,255,255,0.5); }
    .report-table {
      width:100%; border-collapse:collapse; margin-top:16px;
    }
    .report-table th, .report-table td {
      padding:12px; text-align:left;
      border-bottom:1px solid rgba(255,255,255,0.15);
      color:#fff;
    }
    .report-table th { background:rgba(255,255,255,0.1); }
    /* Modal overlay */
    .modal-overlay {
      display:none; position:fixed; top:0; left:0; right:0; bottom:0;
      background:rgba(0,0,0,0.7); z-index:200;
      justify-content:center; align-items:center;
    }
    .modal-overlay.show { display:flex; }
    .modal-box {
      background:var(--surface);
      border-radius:var(--radius);
      padding:32px;
      width:90%; max-width:550px;
      box-shadow:0 8px 40px rgba(0,0,0,0.8);
      max-height:90vh; overflow-y:auto;
    }
    .modal-box h2 { margin-bottom:20px; color:var(--accent); }
    .modal-close {
      float:right; background:none; border:none;
      color:var(--on-surface); font-size:1.5rem; cursor:pointer;
    }
  </style>
</head>
<body>
  <header>
    <span class="material-icons" id="menuBtn">menu</span>
    <h1>Admin_Panel_Custom_Tours</h1>
  </header>
  <nav id="drawer">
    <a href="admin.php"><span class="material-icons">dashboard</span>Dashboard</a>
    <a href="asn_Gallery.php"><span class="material-icons">photo_library</span>Gallery</a>
    <a href="asn_custom_admin.php"><span class="material-icons">explore</span>Custom Tours</a>
    <a href="asn_Bookings.php"><span class="material-icons">event</span>Bookings</a>
    <a href="asn_Contact.php"><span class="material-icons">mail</span>Contacts</a>
    <a href="asn_Reviews.php"><span class="material-icons">star</span>Reviews</a>
    <a href="asn_subscribers.php"><span class="material-icons">people</span>Subscribers</a>
    <form method="post" action="logout.php" style="margin-top:24px; text-align:center;">
      <button class="btn">Logout</button>
    </form>
  </nav>
  <main>
    <?php if ($message): ?>
      <div class="status"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="status status-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- TAB BUTTONS -->
    <div class="tab-buttons">
      <button class="tab-btn active" onclick="switchTab('locations', this)">
        <span class="material-icons">place</span> Locations
      </button>
      <button class="tab-btn" onclick="switchTab('activities', this)">
        <span class="material-icons">explore</span> Activities
      </button>
    </div>

    <!-- ==================== LOCATIONS TAB ==================== -->
    <div class="tab-pane active" id="tab-locations">
      <div class="grid-2">
        <!-- Add Location Form -->
        <section class="card">
          <h2>Add New Location</h2>
          <form method="POST">
            <div class="form-group">
              <label>Location Name *</label>
              <input type="text" name="location_name" class="md-input" required placeholder="e.g., Colombo, Kandy"/>
            </div>
            <div class="form-group">
              <label>Description</label>
              <textarea name="description" class="md-textarea" placeholder="Brief description of the location"></textarea>
            </div>
            <button type="submit" name="add_location" class="btn">Add Location</button>
          </form>
        </section>

        <!-- Locations List -->
        <section class="card">
          <h2>Existing Locations (<?= count($locations) ?>)</h2>
          <?php if (count($locations) > 0): ?>
            <?php foreach ($locations as $loc): ?>
              <div class="list-row">
                <div>
                  <span class="list-name"><?= htmlspecialchars($loc['name']) ?></span>
                  <?php if (!empty($loc['description'])): ?>
                    <div class="list-desc"><?= htmlspecialchars($loc['description']) ?></div>
                  <?php endif; ?>
                </div>
                <div>
                  <button class="btn btn-small" onclick="openEditLocation(<?= htmlspecialchars(json_encode($loc)) ?>)">Edit</button>
                  <a href="?action=delete_location&id=<?= $loc['id'] ?>" class="btn btn-delete btn-small" onclick="return confirm('Delete this location?');">Delete</a>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p style="color:rgba(255,255,255,0.5);">No locations added yet.</p>
          <?php endif; ?>
        </section>
      </div>
    </div>

    <!-- ==================== ACTIVITIES TAB ==================== -->
    <div class="tab-pane" id="tab-activities">
      <div class="grid-2">
        <!-- Add Activity Form -->
        <section class="card">
          <h2>Add New Activity</h2>
          <form method="POST">
            <div class="form-group">
              <label>Activity Name *</label>
              <input type="text" name="activity_name" class="md-input" required placeholder="e.g., Temple Tour, Hiking"/>
            </div>
            <div class="form-group">
              <label>Category *</label>
              <input type="text" name="category" class="md-input" required placeholder="e.g., Cultural, Adventure, Beach"/>
            </div>
            <div class="form-group">
              <label>Location *</label>
              <select name="location_id" class="md-select" required>
                <option value="">-- Select Location --</option>
                <?php foreach ($locations as $loc): ?>
                  <option value="<?= $loc['id'] ?>"><?= htmlspecialchars($loc['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Description</label>
              <textarea name="description" class="md-textarea" placeholder="Activity description"></textarea>
            </div>
            <div class="form-group">
              <label>Foreign Adult Price (USD) *</label>
              <input type="number" name="foreign_adult_usd" class="md-input" step="0.01" min="0" required placeholder="50"/>
            </div>
            <div class="form-group">
              <label>Foreign Child Price (USD) *</label>
              <input type="number" name="foreign_child_usd" class="md-input" step="0.01" min="0" required placeholder="25"/>
            </div>
            <button type="submit" name="add_activity" class="btn">Add Activity</button>
          </form>
        </section>

        <!-- Activities List -->
        <section class="card">
          <h2>Existing Activities (<?= count($activities) ?>)</h2>
          <?php if (count($activities) > 0): ?>
            <table class="report-table">
              <thead>
                <tr><th>Activity</th><th>Category</th><th>Location</th><th>Adult</th><th>Child</th><th>Action</th></tr>
              </thead>
              <tbody>
                <?php foreach ($activities as $act): ?>
                  <tr>
                    <td><strong><?= htmlspecialchars($act['activity']) ?></strong></td>
                    <td><?= htmlspecialchars($act['category']) ?></td>
                    <td><?= htmlspecialchars($act['location']) ?></td>
                    <td>$<?= number_format($act['foreign_adult_usd'], 2) ?></td>
                    <td>$<?= number_format($act['foreign_child_usd'], 2) ?></td>
                    <td style="white-space:nowrap;">
                      <button class="btn btn-small" onclick="openEditActivity(<?= htmlspecialchars(json_encode($act)) ?>)">Edit</button>
                      <a href="?action=delete_activity&id=<?= $act['id'] ?>" class="btn btn-delete btn-small" onclick="return confirm('Delete this activity?');">Delete</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p style="color:rgba(255,255,255,0.5);">No activities added yet.</p>
          <?php endif; ?>
        </section>
      </div>
    </div>
  </main>

  <!-- Edit Location Modal -->
  <div class="modal-overlay" id="editLocationModal">
    <div class="modal-box">
      <button class="modal-close" onclick="closeModal('editLocationModal')">&times;</button>
      <h2>Edit Location</h2>
      <form method="POST">
        <input type="hidden" name="location_id" id="edit_location_id">
        <div class="form-group">
          <label>Location Name *</label>
          <input type="text" name="location_name" id="edit_location_name" class="md-input" required>
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea name="description" id="edit_location_description" class="md-textarea"></textarea>
        </div>
        <button type="submit" name="update_location" class="btn">Update Location</button>
        <button type="button" class="btn btn-delete" onclick="closeModal('editLocationModal')">Cancel</button>
      </form>
    </div>
  </div>

  <!-- Edit Activity Modal -->
  <div class="modal-overlay" id="editActivityModal">
    <div class="modal-box">
      <button class="modal-close" onclick="closeModal('editActivityModal')">&times;</button>
      <h2>Edit Activity</h2>
      <form method="POST">
        <input type="hidden" name="activity_id" id="edit_activity_id">
        <div class="form-group">
          <label>Activity Name *</label>
          <input type="text" name="activity_name" id="edit_activity_name" class="md-input" required>
        </div>
        <div class="form-group">
          <label>Category *</label>
          <input type="text" name="category" id="edit_activity_category" class="md-input" required>
        </div>
        <div class="form-group">
          <label>Location *</label>
          <select name="location_id" id="edit_activity_location" class="md-select" required>
            <option value="">-- Select Location --</option>
            <?php foreach ($locations as $loc): ?>
              <option value="<?= $loc['id'] ?>"><?= htmlspecialchars($loc['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea name="description" id="edit_activity_description" class="md-textarea"></textarea>
        </div>
        <div class="form-group">
          <label>Foreign Adult Price (USD) *</label>
          <input type="number" name="foreign_adult_usd" id="edit_activity_adult" class="md-input" step="0.01" min="0" required>
        </div>
        <div class="form-group">
          <label>Foreign Child Price (USD) *</label>
          <input type="number" name="foreign_child_usd" id="edit_activity_child" class="md-input" step="0.01" min="0" required>
        </div>
        <button type="submit" name="update_activity" class="btn">Update Activity</button>
        <button type="button" class="btn btn-delete" onclick="closeModal('editActivityModal')">Cancel</button>
      </form>
    </div>
  </div>

  <script>
    // SIDEBAR TOGGLE
    const menuBtn = document.getElementById('menuBtn');
    const drawer = document.getElementById('drawer');
    menuBtn.addEventListener('click', () => {
      drawer.classList.toggle('open');
    });

    // TAB SWITCHING
    function switchTab(tab, btn) {
      document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
      document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
      document.getElementById('tab-' + tab).classList.add('active');
      btn.classList.add('active');
    }

    // MODALS
    function openEditLocation(loc) {
      document.getElementById('edit_location_id').value = loc.id;
      document.getElementById('edit_location_name').value = loc.name;
      document.getElementById('edit_location_description').value = loc.description || '';
      document.getElementById('editLocationModal').classList.add('show');
    }
    function openEditActivity(act) {
      document.getElementById('edit_activity_id').value = act.id;
      document.getElementById('edit_activity_name').value = act.activity;
      document.getElementById('edit_activity_category').value = act.category;
      document.getElementById('edit_activity_description').value = act.description || '';
      document.getElementById('edit_activity_adult').value = act.foreign_adult_usd;
      document.getElementById('edit_activity_child').value = act.foreign_child_usd;
      // Set location based on activity's location name
      const locationSelect = document.getElementById('edit_activity_location');
      for (let option of locationSelect.options) {
        if (option.text.trim() === act.location.trim()) {
          option.selected = true;
          break;
        }
      }
      document.getElementById('editActivityModal').classList.add('show');
    }
    function closeModal(id) {
      document.getElementById(id).classList.remove('show');
    }

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
            window.location.reload();
        }
    });
  </script>

<?php
$conn->close();
?>
