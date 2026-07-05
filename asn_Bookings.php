<?php
// ── asn_Bookings.php ────────────────────────────────────────────────────────
// Admin Bookings Dashboard — View, search/filter, edit, delete, generate Word doc

session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: asn_admin_loging.php');
    exit;
}

$conn = new mysqli("sql205.infinityfree.com", "if0_42342516", "cpzbjidK5h1", "if0_42342516_asantravels_og");
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// Ensure new columns exist
$conn->query("ALTER TABLE booking ADD COLUMN IF NOT EXISTS package_name VARCHAR(500) DEFAULT ''");
$conn->query("ALTER TABLE booking ADD COLUMN IF NOT EXISTS num_adults INT DEFAULT 0");
$conn->query("ALTER TABLE booking ADD COLUMN IF NOT EXISTS num_children INT DEFAULT 0");
$conn->query("ALTER TABLE booking ADD COLUMN IF NOT EXISTS booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP");

$statusMsg = '';
$msgType   = 'success'; // 'success' | 'error'

// ── DELETE ────────────────────────────────────────────────────────────────
if (isset($_POST['delete']) && !empty($_POST['id'])) {
    $id = intval($_POST['id']);
    if ($conn->query("DELETE FROM booking WHERE id = $id")) {
        $statusMsg = "🗑️ Booking #$id deleted successfully.";
    } else {
        $statusMsg = "❌ Delete failed: " . $conn->error;
        $msgType   = 'error';
    }
}

// ── UPDATE (full edit) ────────────────────────────────────────────────────
if (isset($_POST['update']) && !empty($_POST['id'])) {
    $id      = intval($_POST['id']);
    $uname   = $conn->real_escape_string(trim($_POST['name']         ?? ''));
    $uemail  = $conn->real_escape_string(trim($_POST['email']        ?? ''));
    $ustart  = $conn->real_escape_string(trim($_POST['start_date']   ?? ''));
    $uend    = $conn->real_escape_string(trim($_POST['end_date']     ?? ''));
    $upax    = intval($_POST['passengers']    ?? 0);
    $uadults = intval($_POST['num_adults']    ?? 0);
    $uchild  = intval($_POST['num_children']  ?? 0);
    $uroom   = $conn->real_escape_string(trim($_POST['room_option']  ?? ''));
    $utotal  = floatval($_POST['total']       ?? 0);
    $ustatus = $conn->real_escape_string(trim($_POST['status']       ?? 'Pending'));
    $ureq    = $conn->real_escape_string(trim($_POST['special_request'] ?? ''));

    $pay_arrive = round($utotal / 2, 2);

    $sql = "UPDATE booking SET 
        name='$uname', email='$uemail', start_date='$ustart', end_date='$uend',
        passengers=$upax, num_adults=$uadults, num_children=$uchild,
        room_option='$uroom', total=$utotal, pay_on_arrival=$pay_arrive,
        status='$ustatus', special_request='$ureq'
        WHERE id=$id";

    if ($conn->query($sql)) {
        $statusMsg = "✅ Booking #$id updated successfully.";
    } else {
        $statusMsg = "❌ Update failed: " . $conn->error;
        $msgType   = 'error';
    }
}

// ── LOGOUT ────────────────────────────────────────────────────────────────
if (isset($_POST['logout'])) {
    header("Location: logout.php");
    exit;
}

// ── FETCH AND FILTER BOOKINGS ─────────────────────────────────────────────
$search     = $conn->real_escape_string(trim($_GET['q']      ?? ''));
$filterStatus = $conn->real_escape_string(trim($_GET['status'] ?? ''));
$filterDate = $conn->real_escape_string(trim($_GET['date']   ?? ''));

$where = "WHERE 1=1";
if ($search)       $where .= " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR Package LIKE '%$search%' OR id LIKE '%$search%')";
if ($filterStatus) $where .= " AND status = '$filterStatus'";
if ($filterDate)   $where .= " AND DATE(start_date) = '$filterDate'";

$bookings_result = $conn->query("SELECT * FROM booking $where ORDER BY id DESC");
$bookings = [];
if ($bookings_result) {
    while ($row = $bookings_result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

$total_count     = count($bookings);
$pending_count   = count(array_filter($bookings, fn($b) => ($b['status'] ?? '') === 'Pending'));
$confirmed_count = count(array_filter($bookings, fn($b) => ($b['status'] ?? '') === 'Confirmed'));
$total_revenue   = array_sum(array_column($bookings, 'total'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Admin — Bookings Dashboard • AsanTravels</title>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
  <style>
    :root {
      --bg:      #0F0F1A;
      --surface: #1A1A2E;
      --card:    #16213E;
      --text:    #E0E0F0;
      --muted:   #888899;
      --primary: #6C63FF;
      --accent:  #00D4AA;
      --danger:  #FF4757;
      --warning: #FFA502;
      --success: #2ED573;
      --radius:  12px;
      --dur:     .25s;
    }
    * { box-sizing:border-box; margin:0; padding:0; }
    body { font-family:'Segoe UI',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; overflow-x:hidden; }

    /* ─ Header ─ */
    header {
      position:fixed; top:0; left:0; right:0; height:58px;
      background:var(--surface);
      display:flex; align-items:center; padding:0 20px;
      box-shadow:0 2px 20px rgba(0,0,0,0.5);
      z-index:1000; gap:14px;
    }
    header .menu-icon { cursor:pointer; color:var(--accent); font-size:26px; transition:transform var(--dur); }
    header .menu-icon:hover { transform:scale(1.15) rotate(10deg); }
    header h1 { font-size:1.1rem; font-weight:600; color:var(--text); flex:1; }
    header .brand { font-size:0.85rem; color:var(--muted); }

    /* ─ Sidebar ─ */
    nav {
      position:fixed; top:58px; left:-265px; bottom:0; width:265px;
      background:var(--surface);
      box-shadow:3px 0 20px rgba(0,0,0,0.4);
      transition:left var(--dur); padding:20px 0; z-index:999; overflow-y:auto;
    }
    nav.open { left:0; }
    nav a {
      display:flex; align-items:center; gap:14px;
      padding:13px 24px; color:var(--text);
      text-decoration:none; font-size:0.95rem;
      border-radius:var(--radius); margin:4px 10px;
      transition:background var(--dur), transform var(--dur), color var(--dur);
    }
    nav a:hover { background:rgba(108,99,255,0.15); transform:translateX(6px); color:var(--accent); }
    nav a.active { background:rgba(0,212,170,0.15); color:var(--accent); }
    nav a .material-icons { color:var(--accent); font-size:20px; }
    nav .nav-divider { border:none; border-top:1px solid rgba(255,255,255,0.08); margin:12px 20px; }

    /* ─ Main ─ */
    main { padding:78px 24px 40px; transition:margin-left var(--dur); max-width:1400px; }
    nav.open ~ main { margin-left:265px; }

    /* ─ Toast / Status ─ */
    .toast {
      padding:14px 20px; border-radius:var(--radius); margin-bottom:20px;
      font-size:0.92rem; display:flex; align-items:center; gap:10px;
      animation:slideDown .35s ease;
    }
    @keyframes slideDown { from{opacity:0;transform:translateY(-12px)} to{opacity:1;transform:translateY(0)} }
    .toast.success { background:rgba(46,213,115,0.15); border:1px solid rgba(46,213,115,0.3); color:var(--success); }
    .toast.error   { background:rgba(255,71,87,0.15);  border:1px solid rgba(255,71,87,0.3);  color:var(--danger);  }

    /* ─ Stat Cards ─ */
    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px; }
    .stat-card {
      background:var(--card); border-radius:var(--radius); padding:20px;
      border:1px solid rgba(255,255,255,0.06);
      transition:transform var(--dur), box-shadow var(--dur);
    }
    .stat-card:hover { transform:translateY(-4px); box-shadow:0 8px 30px rgba(0,0,0,0.4); }
    .stat-label { font-size:0.8rem; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px; }
    .stat-value { font-size:2rem; font-weight:700; }
    .stat-card.blue   .stat-value { color:var(--primary); }
    .stat-card.green  .stat-value { color:var(--success); }
    .stat-card.orange .stat-value { color:var(--warning); }
    .stat-card.teal   .stat-value { color:var(--accent); }

    /* ─ Filter Bar ─ */
    .filter-bar {
      background:var(--card); border-radius:var(--radius); padding:16px 20px;
      display:flex; flex-wrap:wrap; gap:12px; align-items:center;
      margin-bottom:20px; border:1px solid rgba(255,255,255,0.06);
    }
    .filter-bar input, .filter-bar select {
      background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1);
      color:var(--text); padding:9px 14px; border-radius:8px; font-size:0.9rem;
      transition:border-color var(--dur), background var(--dur);
      outline:none;
    }
    .filter-bar input:focus, .filter-bar select:focus {
      border-color:var(--accent); background:rgba(0,212,170,0.06);
    }
    .filter-bar input { flex:1; min-width:200px; }
    .filter-bar select option { background:#1A1A2E; }
    .btn {
      padding:9px 18px; border-radius:8px; border:none; cursor:pointer;
      font-size:0.88rem; font-weight:600; display:inline-flex; align-items:center; gap:6px;
      transition:all var(--dur); text-decoration:none;
    }
    .btn-primary { background:linear-gradient(135deg,var(--primary),#8B7CF6); color:#fff; }
    .btn-primary:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(108,99,255,0.4); }
    .btn-accent  { background:linear-gradient(135deg,var(--accent),#00A884); color:#000; font-weight:700; }
    .btn-accent:hover  { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,212,170,0.4); }
    .btn-danger  { background:linear-gradient(135deg,var(--danger),#C0392B); color:#fff; }
    .btn-danger:hover  { transform:translateY(-2px); box-shadow:0 6px 20px rgba(255,71,87,0.35); }
    .btn-ghost   { background:rgba(255,255,255,0.07); color:var(--text); border:1px solid rgba(255,255,255,0.1); }
    .btn-ghost:hover   { background:rgba(255,255,255,0.12); }
    .btn-sm { padding:6px 12px; font-size:0.8rem; }

    /* ─ Table ─ */
    .table-wrap {
      background:var(--card); border-radius:var(--radius); overflow:hidden;
      border:1px solid rgba(255,255,255,0.06); box-shadow:0 4px 30px rgba(0,0,0,0.3);
    }
    .table-head {
      padding:16px 20px; display:flex; align-items:center; justify-content:space-between;
      border-bottom:1px solid rgba(255,255,255,0.06);
    }
    .table-head h2 { font-size:1rem; font-weight:600; }
    table { width:100%; border-collapse:collapse; }
    th { padding:12px 16px; text-align:left; font-size:0.78rem; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted); background:rgba(255,255,255,0.03); border-bottom:1px solid rgba(255,255,255,0.06); white-space:nowrap; }
    td { padding:12px 16px; font-size:0.88rem; border-bottom:1px solid rgba(255,255,255,0.04); vertical-align:middle; }
    tr:hover td { background:rgba(108,99,255,0.04); }
    tr:last-child td { border-bottom:none; }
    .truncate { max-width:160px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:block; }

    /* ─ Status Badge ─ */
    .badge {
      display:inline-block; padding:4px 10px; border-radius:20px; font-size:0.75rem; font-weight:700;
    }
    .badge-pending   { background:rgba(255,165,2,0.18);   color:var(--warning); border:1px solid rgba(255,165,2,0.3); }
    .badge-confirmed { background:rgba(46,213,115,0.18); color:var(--success); border:1px solid rgba(46,213,115,0.3); }
    .badge-progress  { background:rgba(108,99,255,0.18); color:var(--primary); border:1px solid rgba(108,99,255,0.3); }
    .badge-completed { background:rgba(0,212,170,0.18);  color:var(--accent);  border:1px solid rgba(0,212,170,0.3); }
    .badge-canceled  { background:rgba(255,71,87,0.18);  color:var(--danger);  border:1px solid rgba(255,71,87,0.3); }

    /* ─ Actions in table ─ */
    .action-group { display:flex; gap:6px; flex-wrap:nowrap; }

    /* ─ Empty state ─ */
    .empty-state { padding:60px 20px; text-align:center; color:var(--muted); }
    .empty-state .material-icons { font-size:56px; margin-bottom:12px; color:rgba(255,255,255,0.1); }

    /* ─ Modal ─ */
    .modal-overlay {
      display:none; position:fixed; inset:0;
      background:rgba(0,0,0,0.75); z-index:2000;
      justify-content:center; align-items:center;
      backdrop-filter:blur(6px);
    }
    .modal-overlay.show { display:flex; }
    .modal-box {
      background:var(--surface); border-radius:18px; padding:32px;
      width:94%; max-width:600px; max-height:90vh; overflow-y:auto;
      box-shadow:0 20px 60px rgba(0,0,0,0.7);
      border:1px solid rgba(255,255,255,0.08);
      animation:popIn .3s cubic-bezier(.34,1.56,.64,1);
    }
    @keyframes popIn { from{opacity:0;transform:scale(.9)} to{opacity:1;transform:scale(1)} }
    .modal-box h2 { font-size:1.2rem; color:var(--accent); margin-bottom:24px; display:flex; align-items:center; gap:8px; }
    .modal-close { float:right; background:none; border:none; color:var(--muted); font-size:1.5rem; cursor:pointer; transition:color var(--dur); }
    .modal-close:hover { color:var(--danger); }
    .modal-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    .modal-grid.full { grid-template-columns:1fr; }
    .form-group { display:flex; flex-direction:column; gap:6px; }
    .form-group label { font-size:0.8rem; color:var(--muted); text-transform:uppercase; letter-spacing:0.4px; }
    .form-group input, .form-group select, .form-group textarea {
      background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1);
      color:var(--text); padding:10px 14px; border-radius:8px; font-size:0.9rem;
      transition:border-color var(--dur); outline:none; font-family:inherit;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
      border-color:var(--accent); background:rgba(0,212,170,0.05);
    }
    .form-group select option { background:#1A1A2E; }
    .modal-actions { display:flex; gap:12px; margin-top:24px; justify-content:flex-end; flex-wrap:wrap; }

    /* ─ Responsive ─ */
    @media(max-width:900px) {
      .stats-grid { grid-template-columns:1fr 1fr; }
      .modal-grid { grid-template-columns:1fr; }
    }
    @media(max-width:600px) {
      .stats-grid { grid-template-columns:1fr; }
      main { padding:70px 12px 24px; }
      .filter-bar { flex-direction:column; }
      td, th { padding:8px 10px; }
    }
  </style>
</head>
<body>

<!-- ── Header ───────────────────────────────────────────────────────── -->
<header>
  <span class="material-icons menu-icon" id="menuBtn">menu</span>
  <h1><span class="material-icons" style="color:var(--accent);vertical-align:middle;margin-right:8px;font-size:22px;">event</span>Bookings Dashboard</h1>
  <span class="brand">AsanTravels Admin</span>
</header>

<!-- ── Sidebar ──────────────────────────────────────────────────────── -->
<nav id="drawer">
  <a href="admin.php"><span class="material-icons">dashboard</span>Dashboard</a>
  <a href="asn_Gallery.php"><span class="material-icons">photo_library</span>Gallery</a>
  <a href="asn_custom_admin.php"><span class="material-icons">explore</span>Custom Tours</a>
  <a href="asn_Bookings.php" class="active"><span class="material-icons">event</span>Bookings</a>
  <a href="asn_Contact.php"><span class="material-icons">mail</span>Contacts</a>
  <a href="asn_Reviews.php"><span class="material-icons">star</span>Reviews</a>
  <a href="asn_subscribers.php"><span class="material-icons">people</span>Subscribers</a>
  <hr class="nav-divider">
  <form method="post" style="margin:8px 10px;">
    <button name="logout" class="btn btn-ghost" style="width:100%;justify-content:center;">
      <span class="material-icons" style="font-size:18px;">logout</span>Logout
    </button>
  </form>
</nav>

<!-- ── Main Content ──────────────────────────────────────────────────── -->
<main>

  <?php if ($statusMsg): ?>
  <div class="toast <?= $msgType ?>">
    <span class="material-icons" style="font-size:20px;"><?= $msgType === 'success' ? 'check_circle' : 'error' ?></span>
    <?= htmlspecialchars($statusMsg) ?>
  </div>
  <?php endif; ?>

  <!-- Stat Cards -->
  <div class="stats-grid">
    <div class="stat-card blue">
      <div class="stat-label">Total Bookings</div>
      <div class="stat-value"><a href="asn_Bookings.php" style="color:inherit;text-decoration:none;"><?= $total_count ?></a></div>
    </div>
    <div class="stat-card orange">
      <div class="stat-label">Pending</div>
      <div class="stat-value"><?= $pending_count ?></div>
    </div>
    <div class="stat-card green">
      <div class="stat-label">Confirmed</div>
      <div class="stat-value"><?= $confirmed_count ?></div>
    </div>
    <div class="stat-card teal">
      <div class="stat-label">Total Revenue</div>
      <div class="stat-value" style="font-size:1.5rem;">$<?= number_format($total_revenue, 0) ?></div>
    </div>
  </div>

  <!-- Filter Bar -->
  <form method="GET" class="filter-bar" id="filterForm">
    <span class="material-icons" style="color:var(--muted);">search</span>
    <input type="text" name="q" placeholder="Search by name, email or booking ID…" value="<?= htmlspecialchars($search) ?>">
    <select name="status">
      <option value="">All Statuses</option>
      <?php foreach (['Pending','Confirmed','In Progress','Completed','Canceled'] as $s): ?>
        <option value="<?= $s ?>" <?= $filterStatus === $s ? 'selected' : '' ?>><?= $s ?></option>
      <?php endforeach; ?>
    </select>
    <input type="date" name="date" value="<?= htmlspecialchars($filterDate) ?>" title="Filter by start date">
    <button type="submit" class="btn btn-primary">
      <span class="material-icons" style="font-size:18px;">filter_list</span>Filter
    </button>
    <?php if ($search || $filterStatus || $filterDate): ?>
    <a href="asn_Bookings.php" class="btn btn-ghost">
      <span class="material-icons" style="font-size:18px;">clear</span>Clear
    </a>
    <?php endif; ?>
  </form>

  <!-- Bookings Table -->
  <div class="table-wrap">
    <div class="table-head">
      <h2><span class="material-icons" style="color:var(--accent);vertical-align:middle;margin-right:6px;font-size:20px;">table_view</span>
        All Bookings <?php if ($search || $filterStatus || $filterDate): ?><small style="color:var(--muted);font-weight:400;">(filtered: <?= $total_count ?> results)</small><?php endif; ?></h2>
    </div>
    <?php if (empty($bookings)): ?>
      <div class="empty-state">
        <div class="material-icons">event_busy</div>
        <p>No bookings found<?= ($search || $filterStatus || $filterDate) ? ' for the current filter' : '' ?>.</p>
      </div>
    <?php else: ?>
    <div style="overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th>#ID</th>
            <th>Name</th>
            <th>Package</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Pax</th>
            <th>Total</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($bookings as $b): ?>
          <?php
            $status   = $b['status'] ?? 'Pending';
            $badgeClass = match($status) {
              'Confirmed'   => 'badge-confirmed',
              'In Progress' => 'badge-progress',
              'Completed'   => 'badge-completed',
              'Canceled'    => 'badge-canceled',
              default       => 'badge-pending'
            };
            $packageDisplay = $b['package_name'] ?? $b['Package'] ?? 'Custom Tour';
          ?>
          <tr>
            <td><strong style="color:var(--accent);">#<?= str_pad($b['id'], 4, '0', STR_PAD_LEFT) ?></strong></td>
            <td>
              <div style="font-weight:600;"><?= htmlspecialchars($b['name'] ?? '-') ?></div>
              <div style="font-size:0.78rem;color:var(--muted);"><?= htmlspecialchars($b['email'] ?? '') ?></div>
            </td>
            <td><span class="truncate" title="<?= htmlspecialchars($packageDisplay) ?>"><?= htmlspecialchars($packageDisplay) ?></span></td>
            <td><?= !empty($b['start_date']) ? date('d M Y', strtotime($b['start_date'])) : '-' ?></td>
            <td><?= !empty($b['end_date'])   ? date('d M Y', strtotime($b['end_date']))   : '-' ?></td>
            <td>
              <?php $adults = intval($b['num_adults'] ?? 0); $children = intval($b['num_children'] ?? 0); ?>
              <?= $adults ?>A<?= $children ? ' + ' . $children . 'C' : '' ?>
            </td>
            <td><strong style="color:var(--success);">$<?= number_format(floatval($b['total'] ?? 0), 2) ?></strong></td>
            <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span></td>
            <td>
              <div class="action-group">
                <button class="btn btn-ghost btn-sm" onclick='openEditModal(<?= json_encode($b) ?>)' title="Edit booking">
                  <span class="material-icons" style="font-size:16px;">edit</span>
                </button>
                <a href="generate_doc.php?booking_id=<?= $b['id'] ?>" class="btn btn-accent btn-sm" title="Generate Word Document">
                  <span class="material-icons" style="font-size:16px;">description</span>Doc
                </a>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete booking #<?= $b['id'] ?>? This cannot be undone.');">
                  <input type="hidden" name="id" value="<?= $b['id'] ?>">
                  <button type="submit" name="delete" class="btn btn-danger btn-sm" title="Delete">
                    <span class="material-icons" style="font-size:16px;">delete</span>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</main>

<!-- ── Edit Modal ────────────────────────────────────────────────────── -->
<div class="modal-overlay" id="editModal">
  <div class="modal-box">
    <button class="modal-close" onclick="closeModal()">&#10005;</button>
    <h2><span class="material-icons">edit_note</span>Edit Booking</h2>
    <form method="POST" id="editForm">
      <input type="hidden" name="id" id="e_id">
      <div class="modal-grid">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="name" id="e_name" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" id="e_email" required>
        </div>
        <div class="form-group">
          <label>Start Date</label>
          <input type="date" name="start_date" id="e_start">
        </div>
        <div class="form-group">
          <label>End Date</label>
          <input type="date" name="end_date" id="e_end">
        </div>
        <div class="form-group">
          <label>Adults</label>
          <input type="number" name="num_adults" id="e_adults" min="0">
        </div>
        <div class="form-group">
          <label>Children</label>
          <input type="number" name="num_children" id="e_children" min="0">
        </div>
        <div class="form-group">
          <label>Total Passengers</label>
          <input type="number" name="passengers" id="e_pax" min="0">
        </div>
        <div class="form-group">
          <label>Room Option</label>
          <input type="text" name="room_option" id="e_room">
        </div>
        <div class="form-group">
          <label>Total Cost (USD)</label>
          <input type="number" name="total" id="e_total" step="0.01" min="0">
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status" id="e_status">
            <option value="Pending">Pending</option>
            <option value="Confirmed">Confirmed</option>
            <option value="In Progress">In Progress</option>
            <option value="Completed">Completed</option>
            <option value="Canceled">Canceled</option>
          </select>
        </div>
      </div>
      <div class="modal-grid full" style="margin-top:14px;">
        <div class="form-group">
          <label>Special Request</label>
          <textarea name="special_request" id="e_request" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
        <button type="submit" name="update" class="btn btn-primary">
          <span class="material-icons" style="font-size:18px;">save</span>Save Changes
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  // Sidebar
  document.getElementById('menuBtn').addEventListener('click', () => {
    document.getElementById('drawer').classList.toggle('open');
  });

  // Edit Modal
  function openEditModal(b) {
    document.getElementById('e_id').value       = b.id;
    document.getElementById('e_name').value     = b.name        || '';
    document.getElementById('e_email').value    = b.email       || '';
    document.getElementById('e_start').value    = b.start_date  || '';
    document.getElementById('e_end').value      = b.end_date    || '';
    document.getElementById('e_adults').value   = b.num_adults  || 0;
    document.getElementById('e_children').value = b.num_children|| 0;
    document.getElementById('e_pax').value      = b.passengers  || 0;
    document.getElementById('e_room').value     = b.room_option || '';
    document.getElementById('e_total').value    = b.total       || 0;
    document.getElementById('e_request').value  = b.special_request || '';
    const sel = document.getElementById('e_status');
    for (let opt of sel.options) { opt.selected = (opt.value === (b.status || 'Pending')); }
    document.getElementById('editModal').classList.add('show');
  }
  function closeModal() {
    document.getElementById('editModal').classList.remove('show');
  }
  document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
  });

  // Auto-dismiss toast
  const toast = document.querySelector('.toast');
  if (toast) setTimeout(() => { toast.style.opacity='0'; toast.style.transition='opacity 0.5s'; setTimeout(()=>toast.remove(),500); }, 4000);
</script>

<?php $conn->close(); ?>
</body>
</html>
