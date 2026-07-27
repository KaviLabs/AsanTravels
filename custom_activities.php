<?php
// Disable error display in production
error_reporting(0);
ini_set('display_errors', 0);

$con = mysqli_connect("sql206.infinityfree.com", "if0_42342516", "cpzbjidK5h1", "if0_42342516_asantravels_og");
if (!$con) {
    die("Couldn't connect to server: " . mysqli_connect_error());
}
mysqli_set_charset($con, 'utf8mb4');

$activitiesByCategory = [];
$allowedLocations = [];
$locationsQuery = trim($_GET['locations'] ?? '');
if ($locationsQuery !== '') {
    $allowedLocations = array_unique(array_filter(array_map('trim', explode(',', $locationsQuery))));
}

if (!empty($allowedLocations)) {
    $allowedLocations = array_values(array_unique(array_filter(array_map('trim', $allowedLocations))));
    $escapedLocations = array_map(function ($loc) use ($con) {
        return "'" . mysqli_real_escape_string($con, mb_strtolower($loc)) . "'";
    }, $allowedLocations);
    $inClause = implode(',', $escapedLocations);
    $activityResults = mysqli_query($con, "SELECT id, activity, category, location, description, foreign_adult_usd FROM custom_tours WHERE LOWER(TRIM(location)) IN ($inClause) ORDER BY category, activity");
} else {
    $activityResults = false;
}

if ($activityResults) {
    while ($row = mysqli_fetch_assoc($activityResults)) {
        $category = trim($row['category']) ?: 'Other';
        if (!isset($activitiesByCategory[$category])) {
            $activitiesByCategory[$category] = [];
        }
        $activitiesByCategory[$category][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Customize Your Activities • AsanTravels</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; margin:0; padding:0; background: #f4f9ff; color:#12233f; }
        .container { max-width: 1040px; margin: 0 auto; padding: 24px; }
        .hero { background: linear-gradient(135deg, #0b3c5d, #3282b8); color:#fff; border-radius: 20px; padding: 32px; margin-bottom: 24px; box-shadow: 0 14px 40px rgba(18,35,63,.12); }
        .hero h1 { margin-bottom: 12px; font-size: 2.6rem; }
        .hero p { font-size: 1.05rem; line-height:1.7; max-width: 860px; }
        .section { background: #fff; border-radius: 20px; padding: 24px; margin-bottom: 22px; box-shadow: 0 12px 28px rgba(18,35,63,.06); }
        .section h2 { font-size: 1.45rem; margin-bottom: 18px; }
        .category-heading { font-size: 1.2rem; margin: 28px 0 16px; color: #0b3c5d; }
        .activity-card { border: 1px solid #e2e8f0; border-radius: 16px; padding: 18px; margin-bottom: 14px; display: grid; grid-template-columns: auto 1fr; gap: 18px; align-items: center; }
        .activity-card label { width: 100%; cursor: pointer; display: grid; grid-template-columns: auto 1fr; gap: 16px; align-items: center; }
        .activity-card .details { display: grid; gap: 5px; }
        .activity-card .details span { display:block; }
        .activity-card .title { font-weight: 600; color: #0b3c5d; }
        .activity-card .subtitle { color: #475569; font-size: .96rem; }
        .activity-card .price { font-weight: 700; color: #0b3c5d; }
        .activity-card input[type="checkbox"] { width: 20px; height: 20px; accent-color: #0b3c5d; }
        .summary { display: flex; flex-wrap: wrap; gap: 16px; justify-content: space-between; align-items: flex-end; }
        .summary .summary-box { background: #f8fbff; border: 1px solid #cfe3f2; border-radius: 16px; padding: 20px; flex: 1 1 220px; }
        .summary .summary-box h3 { margin-bottom: 10px; font-size: 1rem; color: #0b3c5d; }
        .summary .summary-box p { margin: 0; font-size: 1.35rem; font-weight: 700; }
        .note { background: rgba(50,130,184,.08); border-left: 4px solid #3282b8; padding: 16px 18px; border-radius: 12px; margin-bottom: 20px; }
        .note a { color: #0b3c5d; text-decoration: underline; }
        .btn-primary { background: #0b3c5d; color:#fff; border:none; padding: 12px 26px; border-radius: 10px; font-size: 1rem; transition: transform .2s ease; }
        .btn-primary:hover { transform: translateY(-1px); }
        .footer { padding: 26px 0; color:#475569; font-size:.95rem; text-align:center; }
        @media (max-width: 768px) { .activity-card, .summary { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero">
            <h1>Customize Your Activities</h1>
            <p>Select extra experiences from our live activity catalogue and see the total package update automatically. Your chosen activities stay visible before booking, and prices always reflect the latest admin-managed data.</p>
        </div>

        <div class="note">
            <strong>Note:</strong> Changes made in the Admin Panel to activities and prices are reflected here immediately.
            <?php if (!empty($allowedLocations)): ?>
                <br>Only activities for: <strong><?= htmlspecialchars(implode(', ', $allowedLocations)) ?></strong> are shown.
            <?php endif; ?>
        </div>

        <div class="section">
            <div class="summary">
                <div class="summary-box">
                    <h3>Base Package Price</h3>
                    <p>$750.00 pp</p>
                </div>
                <div class="summary-box">
                    <h3>Selected Activities</h3>
                    <p id="selected-count">0</p>
                </div>
                <div class="summary-box">
                    <h3>Activities Total</h3>
                    <p id="activities-total">$0.00</p>
                </div>
                <div class="summary-box">
                    <h3>Package Total</h3>
                    <p id="package-total">$750.00</p>
                </div>
            </div>
            <div class="section">
                <h2>Selected Activities</h2>
                <div id="selected-list" style="min-height: 36px; color: #0b3c5d; font-weight: 500;">None selected</div>
            </div>
        </div>

        <?php if (!empty($activitiesByCategory)): ?>
            <?php foreach ($activitiesByCategory as $category => $activities): ?>
                <div class="section">
                    <h2 class="category-heading"><?= htmlspecialchars($category) ?></h2>
                    <?php foreach ($activities as $activity): ?>
                        <div class="activity-card">
                            <label>
                                <input type="checkbox" class="activity-checkbox" data-price="<?= number_format($activity['foreign_adult_usd'], 2, '.', '') ?>" value="<?= intval($activity['id']) ?>">
                                <div class="details">
                                    <span class="title"><?= htmlspecialchars($activity['activity']) ?></span>
                                    <span class="subtitle"><?= htmlspecialchars($activity['location'] ?: 'Location not specified') ?></span>
                                    <span class="subtitle"><?= htmlspecialchars($activity['description'] ?: 'No description available.') ?></span>
                                </div>
                            </label>
                            <div class="price">+$<?= number_format($activity['foreign_adult_usd'], 2) ?> pp</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="section">
                <p>No activities are available right now. Please check back later or contact us for assistance.</p>
            </div>
        <?php endif; ?>

        <div class="section">
            <button class="btn-primary" id="view-package">View Package Details</button>
        </div>

        <div class="footer">
            Need help? <a href="contact.php">Contact us</a> and we can build a custom itinerary with you.
        </div>
    </div>

    <script>
        const basePrice = 750.00;
        const packageTotalEl = document.getElementById('package-total');
        const activitiesTotalEl = document.getElementById('activities-total');
        const selectedCountEl = document.getElementById('selected-count');
        const checkboxes = document.querySelectorAll('.activity-checkbox');

        function updateSummary() {
            let selectedCount = 0;
            let total = 0;
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    selectedCount += 1;
                    total += parseFloat(cb.dataset.price) || 0;
                }
            });
            selectedCountEl.textContent = selectedCount;
            activitiesTotalEl.textContent = '$' + total.toFixed(2);
            packageTotalEl.textContent = '$' + (basePrice + total).toFixed(2);
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updateSummary));
        updateSummary();

        function getSelectedActivities() {
            const selected = [];
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    selected.push({
                        id: cb.value,
                        title: cb.closest('.activity-card').querySelector('.title').textContent.trim(),
                        price: parseFloat(cb.dataset.price) || 0,
                    });
                }
            });
            return selected;
        }

        function updateSelectedList() {
            const selected = getSelectedActivities();
            if (selected.length === 0) {
                document.getElementById('selected-list').textContent = 'None selected';
                return;
            }
            const listHtml = selected.map(item => `<div style="margin-bottom: 8px;">• ${item.title} (+$${item.price.toFixed(2)} pp)</div>`).join('');
            document.getElementById('selected-list').innerHTML = listHtml;
        }

        function saveSelectedActivities() {
            const selected = getSelectedActivities().map(item => item.id);
            localStorage.setItem('selected_custom_activities', JSON.stringify(selected));
        }

        function restoreSelectedActivities() {
            try {
                const saved = JSON.parse(localStorage.getItem('selected_custom_activities') || '[]');
                if (!Array.isArray(saved)) return;
                checkboxes.forEach(cb => {
                    cb.checked = saved.includes(cb.value);
                });
            } catch (err) {
                console.warn('Unable to restore selected activities:', err);
            }
        }

        checkboxes.forEach(cb => cb.addEventListener('change', function() {
            updateSummary();
            updateSelectedList();
            saveSelectedActivities();
        }));
        restoreSelectedActivities();
        updateSummary();
        updateSelectedList();

        document.getElementById('view-package').addEventListener('click', function () {
            window.location.href = 'Book the Cultural Gems & Wildlife Wonders.php';
        });
    </script>
</body>
</html>
