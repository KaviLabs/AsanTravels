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

$returnUrl = 'packages.html';
$returnUrlRaw = trim($_GET['return_url'] ?? '');
if ($returnUrlRaw !== '') {
    $decodedReturn = rawurldecode($returnUrlRaw);
    $parsed = parse_url($decodedReturn);
    if (!isset($parsed['scheme']) && !isset($parsed['host']) && !preg_match('#^[\\/]{2}#', $decodedReturn)) {
        $returnUrl = $decodedReturn;
    }
}

// Read optional base_price param passed by each booking page (e.g. base_price=2000)
$basePriceParam = filter_var($_GET['base_price'] ?? 0, FILTER_VALIDATE_FLOAT);
$basePriceParam = ($basePriceParam !== false && $basePriceParam > 0) ? $basePriceParam : 0;

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
                    <?php if ($basePriceParam > 0): ?>
                        <p>$<?= number_format($basePriceParam, 2) ?> (2 persons)</p>
                    <?php else: ?>
                        <p>Varies by package</p>
                    <?php endif; ?>
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
                    <p id="package-total"><?php echo $basePriceParam > 0 ? '$' . number_format($basePriceParam, 2) : 'Varies'; ?></p>
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
            <button class="btn-primary" id="add-and-return">Add Activities &amp; Return to Package</button>
        </div>

            <!-- Premium Footer Start -->
    <footer class="site-footer" role="contentinfo">
        <div class="container">
            <div class="row g-5 pb-5">
                <div class="col-md-6 col-lg-4">
                    <span class="site-footer-brand">Asan<span>Travels</span></span>
                    <span class="site-footer-brand-sub">Your trusted partner for authentic, personalised Sri Lanka experiences. Expert local guide with 10+ years of unforgettable tours.</span>
                    <div class="site-footer-social">
                        <a href="https://www.facebook.com/share/16T8gBySGv/?mibextid=wwXIfr" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/_asantravels_?igsh=MW1xamdpejN5Zmk5Mw%3D%3D&amp;utm_source=qr" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="http://www.tiktok.com/@asantravels" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <h4>Get In Touch</h4>
                    <p><i class="fas fa-map-marker-alt me-2" style="color:#C9A84C;"></i>Negombo, Sri Lanka</p>
                    <p><i class="fas fa-envelope me-2" style="color:#C9A84C;"></i>asantravels@gmail.com</p>
                    <p><i class="fas fa-phone me-2" style="color:#C9A84C;"></i>+94 76 208 7707</p>
                    <p><i class="fab fa-whatsapp me-2" style="color:#C9A84C;"></i>+94 77 337 8244</p>
                </div>
                <div class="col-md-6 col-lg-4">
                    <h4>Quick Links</h4>
                    <a href="about.html"><i class="fas fa-angle-right me-2"></i>About Us</a>
                    <a href="packages.html"><i class="fas fa-angle-right me-2"></i>Tour Packages</a>
                    <a href="Custom_Packages.html"><i class="fas fa-angle-right me-2"></i>Custom Tours</a>
                    <a href="contact.php"><i class="fas fa-angle-right me-2"></i>Contact</a>
                </div>
            </div>
            <div class="site-footer-divider">
                <p class="site-footer-copy">&copy; <?php echo date('Y'); ?> AsanTravels. All Rights Reserved. &nbsp;|&nbsp; Designed by <a href="#" style="color:rgba(201,168,76,0.6);text-decoration:none;">Kavinu Rajapakse</a></p>
            </div>
        </div>
    </footer>
    <!-- Premium Footer End -->

    </div>

    <script>
        // Base price passed from the booking page via URL param (for display only)
        const basePrice = <?= json_encode((float)$basePriceParam) ?>;
        const packageTotalEl = document.getElementById('package-total');
        const activitiesTotalEl = document.getElementById('activities-total');
        const selectedCountEl = document.getElementById('selected-count');
        const selectedListEl = document.getElementById('selected-list');
        const addButton = document.getElementById('add-and-return');
        const checkboxes = Array.from(document.querySelectorAll('.activity-checkbox'));
        let addedActivities = [];

        function getActivityFromCheckbox(cb) {
            const card = cb.closest('.activity-card');
            return {
                id: cb.value,
                title: card.querySelector('.title').textContent.trim(),
                price: parseFloat(cb.dataset.price) || 0,
            };
        }

        function getDraftActivities() {
            const addedIds = new Set(addedActivities.map(item => item.id));
            return checkboxes.reduce((draft, cb) => {
                if (cb.checked) {
                    const activity = getActivityFromCheckbox(cb);
                    if (!addedIds.has(activity.id)) {
                        draft.push(activity);
                    }
                }
                return draft;
            }, []);
        }

        function updateSummary() {
            const total = addedActivities.reduce((sum, activity) => sum + activity.price, 0);
            selectedCountEl.textContent = addedActivities.length;
            activitiesTotalEl.textContent = '$' + total.toFixed(2);
            packageTotalEl.textContent = '$' + (basePrice + total).toFixed(2);
            const payOnArrivalEl = document.getElementById('pay-on-arrival');
            if (payOnArrivalEl) {
                const rate = parseFloat(payOnArrivalEl.dataset.rate || '0');
                if (!Number.isNaN(rate)) {
                    payOnArrivalEl.textContent = '$' + ((basePrice + total) * rate).toFixed(2);
                }
            }
        }

        function updateSelectedList() {
            if (addedActivities.length === 0) {
                selectedListEl.textContent = 'None selected';
                return;
            }
            selectedListEl.innerHTML = addedActivities.map(activity => `
                <div style="margin-bottom: 8px; display:flex; justify-content:space-between; align-items:center; gap: 12px;">
                    <span>• ${activity.title} (+$${activity.price.toFixed(2)} pp)</span>
                    <button type="button" class="remove-activity" data-id="${activity.id}" style="background:none;border:none;color:#3282b8;cursor:pointer;font-size:0.95rem;padding:0;">Remove</button>
                </div>
            `).join('');
            selectedListEl.querySelectorAll('.remove-activity').forEach(btn => {
                btn.addEventListener('click', () => removeAddedActivity(btn.dataset.id));
            });
        }

        function saveAddedActivities() {
            localStorage.setItem('added_custom_activities', JSON.stringify(addedActivities.map(activity => activity.id)));
        }

        function restoreAddedActivities() {
            try {
                const savedIds = JSON.parse(localStorage.getItem('added_custom_activities') || '[]');
                if (!Array.isArray(savedIds)) {
                    return;
                }
                const idSet = new Set(savedIds.map(id => String(id)));
                addedActivities = checkboxes.reduce((result, cb) => {
                    if (idSet.has(cb.value)) {
                        result.push(getActivityFromCheckbox(cb));
                    }
                    return result;
                }, []);
            } catch (err) {
                console.warn('Unable to restore added activities:', err);
            }
        }

        function restoreLegacySelection() {
            try {
                const legacyIds = JSON.parse(localStorage.getItem('selected_custom_activities') || '[]');
                if (!Array.isArray(legacyIds)) {
                    return;
                }
                const idSet = new Set(legacyIds.map(id => String(id)));
                const legacyActivities = checkboxes.reduce((result, cb) => {
                    if (idSet.has(cb.value)) {
                        result.push(getActivityFromCheckbox(cb));
                    }
                    return result;
                }, []);
                if (legacyActivities.length > 0 && addedActivities.length === 0) {
                    addedActivities = legacyActivities;
                    saveAddedActivities();
                }
            } catch (err) {
                console.warn('Unable to restore legacy selected activities:', err);
            }
        }

        function addSelectedActivities() {
            const draftActivities = getDraftActivities();
            if (draftActivities.length === 0) {
                return;
            }
            const addedIds = new Set(addedActivities.map(activity => activity.id));
            draftActivities.forEach(activity => {
                if (!addedIds.has(activity.id)) {
                    addedActivities.push(activity);
                }
            });
            checkboxes.forEach(cb => {
                if (draftActivities.some(activity => activity.id === cb.value)) {
                    cb.checked = false;
                }
            });
            saveAddedActivities();
            updateSummary();
            updateSelectedList();
            updateAddButtonState();
        }

        function removeAddedActivity(activityId) {
            addedActivities = addedActivities.filter(activity => activity.id !== activityId);
            saveAddedActivities();
            updateSummary();
            updateSelectedList();
            updateAddButtonState();
        }

        function updateAddButtonState() {
            addButton.disabled = getDraftActivities().length === 0;
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updateAddButtonState));
        addButton.addEventListener('click', () => {
            // Add any checked (but not yet added) activities
            const draftActivities = getDraftActivities();
            if (draftActivities.length > 0) {
                const addedIds = new Set(addedActivities.map(a => a.id));
                draftActivities.forEach(activity => {
                    if (!addedIds.has(activity.id)) {
                        addedActivities.push(activity);
                    }
                });
                saveAddedActivities();
            }
            // Redirect back to the booking/package page
            window.location.href = <?= json_encode($returnUrl) ?>;
        });

        restoreAddedActivities();
        restoreLegacySelection();
        updateSummary();
        updateSelectedList();
        updateAddButtonState();
    </script>
</body>
</html>
