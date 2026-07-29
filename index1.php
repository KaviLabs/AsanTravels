<?php
require_once 'security_headers.php';
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ===== Single OOP Database Connection =====
$servername = "sql206.infinityfree.com";
$db_username = "if0_42342516";
$db_password = "cpzbjidK5h1";
$dbname = "if0_42342516_asantravels_og";

// Set connection timeout to 5 seconds to prevent page hanging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);
    $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle Subscribe Form
if (isset($_POST["submit4"])) {
    $email = $conn->real_escape_string($_POST["email"]);
    $query = $conn->query("INSERT INTO subscribe(email) VALUES('$email')");
    if ($query) {
        header('Location: thank_you-s.html');
        exit();
    } else {
        echo ("No record Added: " . $conn->error);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>AsanTravels – Unforgettable Sri Lanka Tours</title>
    <meta name="description" content="Discover Sri Lanka with AsanTravels. Expert local guide, 10+ years experience, 700+ tours. Custom itineraries, beaches, heritage, wildlife & hill country.">
    <meta name="keywords" content="Sri Lanka tours, AsanTravels, Sri Lanka travel, custom Sri Lanka holidays">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700;800&family=Roboto:wght@300;400&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css?v=12" rel="stylesheet">

    <style>
        /* ═══════════════════════════════════════════════════════════
           HOME PAGE — PREMIUM REDESIGN
           Color palette matches Custom Packages page exactly.
           #07090F  dark bg  |  #13357B  primary blue
           #C9A84C  gold     |  #E8C96A  light gold
        ═══════════════════════════════════════════════════════════ */

        *, *::before, *::after { box-sizing: border-box; }
        html { overflow-x: hidden; scroll-behavior: smooth; }
        body { overflow-x: hidden; font-family: 'Roboto', sans-serif; }

        /* ── Scroll Progress Bar ─────────────────────────── */
        #scroll-progress {
            position: fixed; top: 0; left: 0; height: 3px; z-index: 99999;
            background: linear-gradient(90deg, #C9A84C, #E8C96A, #C9A84C);
            width: 0%; transition: width 0.1s linear;
        }

        /* ── Shared Premium Utilities ────────────────────── */
        .section-eyebrow-hp {
            font-size: 0.72rem; font-weight: 700; letter-spacing: 0.22em;
            text-transform: uppercase; color: #C9A84C; margin-bottom: 0.5rem;
            display: block;
        }
        .section-heading-hp {
            font-family: 'Jost', sans-serif;
            font-size: clamp(1.7rem, 3.2vw, 2.6rem);
            font-weight: 800; color: #0d1a3a; line-height: 1.18;
            margin-bottom: 0.8rem;
        }
        .section-heading-hp span { color: #13357B; }
        .section-heading-hp .gold-text {
            background: linear-gradient(90deg, #C9A84C, #E8C96A);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .section-sub-hp {
            font-family: 'Roboto', sans-serif; font-size: 0.96rem;
            color: #6b7280; line-height: 1.75; max-width: 560px; margin: 0 auto;
        }

        /* ── Reveal Animation ────────────────────────────── */
        .reveal {
            opacity: 0; transform: translateY(28px);
            transition: opacity 0.75s ease, transform 0.75s ease;
        }
        .reveal.revealed { opacity: 1; transform: translateY(0); }
        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }

        /* ── Shared Button Styles ────────────────────────── */
        .btn-gold-hp {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 0.75rem 1.8rem;
            font-family: 'Jost', sans-serif; font-size: 0.85rem;
            font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
            color: #07090F; background: linear-gradient(135deg, #C9A84C, #E8C96A);
            border: none; border-radius: 8px; text-decoration: none;
            transition: transform 0.22s ease, box-shadow 0.22s ease;
            box-shadow: 0 0 22px rgba(201,168,76,0.38);
            touch-action: manipulation;
        }
        .btn-gold-hp:hover { transform: translateY(-2px); box-shadow: 0 0 32px rgba(201,168,76,0.6); color: #07090F; }
        .btn-ghost-hp {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 0.75rem 1.8rem;
            font-family: 'Jost', sans-serif; font-size: 0.85rem;
            font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase;
            color: rgba(255,255,255,0.88); background: transparent;
            border: 1px solid rgba(255,255,255,0.3); border-radius: 8px;
            text-decoration: none; transition: all 0.22s ease; backdrop-filter: blur(4px);
            touch-action: manipulation;
        }
        .btn-ghost-hp:hover { border-color: rgba(201,168,76,0.6); color: #E8C96A; }

        /* ════════════════════════════════════════════
           1. HERO — PREMIUM CAROUSEL
        ════════════════════════════════════════════ */
        .hp-hero {
            position: relative; min-height: 100vh;
            display: flex; flex-direction: column; overflow: hidden;
            background: #07090F;
        }
        /* ── Orb ambient decorations ── */
        .hp-hero-orb {
            position: absolute; border-radius: 50%; pointer-events: none; z-index: 1;
            filter: blur(80px); opacity: 0.15;
        }
        .hp-hero-orb-1 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, #C9A84C, transparent 70%);
            top: -120px; right: -100px;
        }
        .hp-hero-orb-2 {
            width: 380px; height: 380px;
            background: radial-gradient(circle, #13357B, transparent 70%);
            bottom: 80px; left: -80px;
        }

        /* ── Carousel wrapper ── */
        .hp-carousel-wrap { position: absolute; inset: 0; z-index: 0; }
        .hp-carousel-wrap .carousel,
        .hp-carousel-wrap .carousel-inner,
        .hp-carousel-wrap .carousel-item { height: 100%; }
        .hp-carousel-wrap .carousel-item img {
            width: 100%; height: 100%; object-fit: cover;
            animation: hpKenBurns 18s ease-in-out infinite alternate;
        }
        @keyframes hpKenBurns {
            from { transform: scale(1.0); }
            to   { transform: scale(1.1); }
        }
        .hp-carousel-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(
                160deg,
                rgba(3,5,12,0.88) 0%,
                rgba(7,9,20,0.75) 50%,
                rgba(3,5,12,0.82) 100%
            );
            z-index: 1;
        }

        /* ── Nav inside hero ── */
        .hp-navbar {
            position: relative; z-index: 10;
            padding: 1rem 2rem;
            background: transparent;
        }
        .hp-navbar .navbar { background: transparent !important; padding: 0; }
        .hp-navbar .navbar-brand { gap: 10px; }
        .hp-navbar .navbar-brand img {
            height: 46px; border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.35); object-fit: cover;
        }
        .hp-navbar .navbar-brand span {
            font-family: 'Jost', sans-serif; font-size: 1.1rem;
            font-weight: 700; color: #fff;
            text-shadow: 0 1px 4px rgba(0,0,0,0.5);
        }

        /* ── Hero content ── */
        .hp-hero-content {
            position: relative; z-index: 2;
            flex: 1; display: flex; align-items: center;
            padding: 2rem 0 6rem;
        }
        .hp-eyebrow {
            display: inline-flex; align-items: center; gap: 7px;
            font-size: 0.7rem; font-weight: 700; letter-spacing: 0.22em;
            text-transform: uppercase; color: #C9A84C;
            border: 1px solid rgba(201,168,76,0.35); border-radius: 30px;
            padding: 5px 14px; margin-bottom: 1.3rem;
            background: rgba(201,168,76,0.08); backdrop-filter: blur(4px);
            animation: fadeUp 0.8s ease forwards;
        }
        .hp-eyebrow .blink {
            width: 6px; height: 6px; border-radius: 50%; background: #C9A84C;
            animation: blink 1.5s ease-in-out infinite;
            box-shadow: 0 0 8px rgba(201,168,76,0.8);
        }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.2} }

        .hp-hero-h1 {
            font-family: 'Jost', sans-serif;
            font-size: clamp(2.4rem, 5.5vw, 4rem);
            font-weight: 800; color: #fff; line-height: 1.1;
            margin-bottom: 1rem;
            animation: fadeUp 0.9s 0.1s ease both;
        }
        .hp-hero-h1 .gold {
            background: linear-gradient(90deg, #C9A84C, #E8C96A, #C9A84C);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text; background-size: 200%;
            animation: shimmer 3s linear infinite;
        }
        @keyframes shimmer { 0%{background-position:0%} 100%{background-position:200%} }

        .hp-hero-sub {
            font-family: 'Roboto', sans-serif; font-size: 1.05rem;
            font-weight: 300; color: rgba(210,205,195,0.82);
            line-height: 1.78; max-width: 590px; margin-bottom: 2rem;
            animation: fadeUp 0.9s 0.2s ease both;
        }
        .hp-hero-btns {
            display: flex; flex-wrap: wrap; gap: 1rem;
            animation: fadeUp 0.9s 0.3s ease both;
        }
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(22px); }
            to   { opacity:1; transform:translateY(0); }
        }

        /* ── Carousel controls ── */
        .hp-carousel-controls {
            position: absolute; bottom: 100px; right: 2rem; z-index: 5;
            display: flex; gap: 0.6rem;
        }
        .hp-carousel-controls button {
            width: 42px; height: 42px; border-radius: 50%;
            background: rgba(201,168,76,0.15);
            border: 1px solid rgba(201,168,76,0.35);
            color: #C9A84C; font-size: 1rem;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.22s ease; cursor: pointer; touch-action: manipulation;
        }
        .hp-carousel-controls button:hover {
            background: rgba(201,168,76,0.3);
            border-color: rgba(201,168,76,0.7);
        }

        /* ── Stats bar ── */
        .hp-stats {
            position: relative; z-index: 3;
            background: rgba(7,9,15,0.6);
            backdrop-filter: blur(20px);
            border-top: 1px solid rgba(201,168,76,0.12);
        }
        .hp-stat-item {
            padding: 1.2rem 0; text-align: center;
            border-right: 1px solid rgba(255,255,255,0.07);
        }
        .hp-stat-item:last-child { border-right: none; }
        .hp-stat-num {
            font-family: 'Jost', sans-serif; font-size: 1.7rem; font-weight: 800;
            background: linear-gradient(135deg, #C9A84C, #E8C96A);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text; line-height: 1;
        }
        .hp-stat-label {
            font-size: 0.66rem; font-weight: 400; letter-spacing: 0.12em;
            text-transform: uppercase; color: rgba(200,195,180,0.58); margin-top: 3px;
        }

        /* ── Carousel slide indicator dots ── */
        .hp-hero .carousel-indicators { bottom: 110px; z-index: 4; }
        .hp-hero .carousel-indicators li,
        .hp-hero .carousel-indicators button {
            width: 8px; height: 8px; border-radius: 50%;
            background: rgba(201,168,76,0.4); border: none;
            transition: all 0.3s ease;
        }
        .hp-hero .carousel-indicators .active,
        .hp-hero .carousel-indicators [aria-current="true"] {
            background: #C9A84C; transform: scale(1.3);
        }

        /* ════════════════════════════════════════════
           2. ABOUT — VIDEO BACKGROUND
        ════════════════════════════════════════════ */
        .hp-about {
            position: relative; overflow: hidden;
            padding: 100px 0; background: #07090F;
        }
        .hp-about-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.95;
            z-index: 0;
        }
        .hp-about-overlay {
            position: absolute; inset: 0; z-index: 1;
            background: rgba(7, 9, 15, 0.45);
        }
        /* Floating orbs */
        .hp-about-orb {
            position: absolute; border-radius: 50%; pointer-events: none;
            filter: blur(70px); z-index: 1;
        }
        .hp-about-orb-1 {
            width: 420px; height: 420px; top: -100px; right: -80px;
            background: radial-gradient(circle, rgba(201,168,76,0.18), transparent 70%);
        }
        .hp-about-orb-2 {
            width: 300px; height: 300px; bottom: -60px; left: -60px;
            background: radial-gradient(circle, rgba(19,53,123,0.25), transparent 70%);
        }

        .hp-about-content { position: relative; z-index: 2; }

        /* Glass card */
        .hp-about-glass {
            background: rgba(7,9,20,0.55);
            backdrop-filter: blur(24px) saturate(160%);
            -webkit-backdrop-filter: blur(24px) saturate(160%);
            border: 1px solid rgba(201,168,76,0.2);
            border-radius: 20px; padding: 2.8rem 2.4rem;
        }

        .hp-about-eyebrow {
            font-size: 0.7rem; font-weight: 700; letter-spacing: 0.22em;
            text-transform: uppercase; color: #C9A84C; margin-bottom: 1rem;
        }
        .hp-about-h2 {
            font-family: 'Jost', sans-serif;
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800; color: #fff; line-height: 1.15;
            margin-bottom: 1.2rem;
        }
        .hp-about-h2 .gold-grad {
            background: linear-gradient(90deg, #C9A84C, #E8C96A);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hp-about-p {
            font-family: 'Roboto', sans-serif; font-size: 0.97rem;
            color: rgba(210,205,195,0.78); line-height: 1.82; margin-bottom: 1.4rem;
        }
        .hp-about-features {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 0.7rem; margin-bottom: 1.8rem;
        }
        .hp-about-feat {
            display: flex; align-items: center; gap: 8px;
            font-family: 'Jost', sans-serif; font-size: 0.82rem; font-weight: 600;
            color: rgba(240,235,220,0.85); letter-spacing: 0.03em;
        }
        .hp-about-feat i { color: #C9A84C; font-size: 0.78rem; }

        /* Stats counters */
        .hp-about-stats {
            display: grid; grid-template-columns: repeat(2, 1fr);
            gap: 1rem; margin-top: 2.5rem;
        }
        .hp-stat-card {
            background: rgba(201,168,76,0.07);
            border: 1px solid rgba(201,168,76,0.18);
            border-radius: 14px; padding: 1.2rem 1rem; text-align: center;
            transition: transform 0.3s ease, border-color 0.3s ease;
        }
        .hp-stat-card:hover {
            transform: translateY(-4px);
            border-color: rgba(201,168,76,0.4);
        }
        .hp-stat-card-num {
            font-family: 'Jost', sans-serif; font-size: 2rem; font-weight: 800;
            background: linear-gradient(135deg, #C9A84C, #E8C96A);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text; line-height: 1;
        }
        .hp-stat-card-label {
            font-size: 0.67rem; font-weight: 500; letter-spacing: 0.12em;
            text-transform: uppercase; color: rgba(200,195,180,0.6); margin-top: 4px;
        }

        /* Floating badge on the right column */
        .hp-about-img-wrap {
            position: relative; border-radius: 20px; overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        .hp-about-img-wrap img {
            width: 100%; height: 100%; object-fit: cover; min-height: 380px;
            border-radius: 20px;
        }
        .hp-about-badge {
            position: absolute; bottom: 24px; left: 24px;
            background: rgba(7,9,20,0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(201,168,76,0.3);
            border-radius: 12px; padding: 0.9rem 1.2rem;
            display: flex; align-items: center; gap: 10px;
        }
        .hp-about-badge i { font-size: 1.6rem; color: #C9A84C; }
        .hp-about-badge-text small {
            font-size: 0.62rem; font-weight: 500; letter-spacing: 0.12em;
            text-transform: uppercase; color: rgba(200,195,180,0.6);
            display: block;
        }
        .hp-about-badge-text strong {
            font-family: 'Jost', sans-serif; font-size: 0.9rem; font-weight: 700;
            color: #F1EBD6; display: block; margin-top: 2px;
        }

        /* ════════════════════════════════════════════
           3. DESTINATIONS
        ════════════════════════════════════════════ */
        .hp-destinations { background: #f8f9fc; padding: 90px 0 80px; }
        .hp-destinations .section-heading-hp { color: #0d1a3a; }

        /* Enhance the existing destination-img cards */
        .destination-img {
            border-radius: 14px !important; overflow: hidden;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            transition: transform 0.35s ease, box-shadow 0.35s ease;
        }
        .destination-img:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 50px rgba(19,53,123,0.15);
        }
        .destination-img img { transition: transform 0.5s ease; }
        .destination-img:hover img { transform: scale(1.06); }

        /* ════════════════════════════════════════════
           4. REVIEWS — GLASS CAROUSEL
        ════════════════════════════════════════════ */
        .hp-reviews {
            background: #07090F; padding: 90px 0;
            position: relative; overflow: hidden;
        }
        .hp-reviews-orb {
            position: absolute; border-radius: 50%; pointer-events: none;
            filter: blur(80px); z-index: 0;
        }
        .hp-reviews-orb-1 {
            width: 400px; height: 400px; top: -80px; left: -100px;
            background: radial-gradient(circle, rgba(19,53,123,0.2), transparent 70%);
        }
        .hp-reviews-orb-2 {
            width: 350px; height: 350px; bottom: -60px; right: -80px;
            background: radial-gradient(circle, rgba(201,168,76,0.12), transparent 70%);
        }
        .hp-reviews-inner { position: relative; z-index: 1; }

        /* Override testimonial item to glassmorphism */
        .testimonial-item {
            background: rgba(10,14,28,0.7) !important;
            backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(201,168,76,0.15) !important;
            border-radius: 18px !important;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4) !important;
            padding: 2rem 1.5rem 1.5rem !important;
            transition: border-color 0.3s ease, transform 0.3s ease !important;
        }
        .testimonial-item:hover {
            border-color: rgba(201,168,76,0.35) !important;
            transform: translateY(-4px) !important;
        }
        .testimonial-comment {
            background: rgba(255,255,255,0.04) !important;
            border: 1px solid rgba(255,255,255,0.07) !important;
            border-radius: 12px !important;
        }
        .testimonial-comment p { color: rgba(220,215,200,0.88) !important; font-style: italic; }
        .testimonial-item h5 { color: #F1EBD6 !important; }
        .testimonial-item .text-muted { color: rgba(200,195,180,0.5) !important; }
        .testimonial-item img {
            border-color: #C9A84C !important;
            box-shadow: 0 0 20px rgba(201,168,76,0.3) !important;
        }
        .testimonial-item .fa-star { color: #C9A84C !important; }

        /* ════════════════════════════════════════════
           5. SUBMIT REVIEW — PREMIUM FORM
        ════════════════════════════════════════════ */
        .hp-submit-section {
            background: #f0f2f8; padding: 80px 0;
        }
        .hp-review-form-wrap {
            max-width: 520px; margin: 0 auto;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(19,53,123,0.1);
            border: 1px solid #eef0f5;
            padding: 2.5rem 2rem;
        }
        .hp-review-form-wrap h2 {
            font-family: 'Jost', sans-serif; font-size: 1.6rem;
            font-weight: 800; color: #0d1a3a; text-align: center; margin-bottom: 1.8rem;
        }
        .hp-form-group { margin-bottom: 1.2rem; }
        .hp-form-group label {
            font-family: 'Jost', sans-serif; font-size: 0.8rem;
            font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase;
            color: #4b5563; display: block; margin-bottom: 6px;
        }
        .hp-form-group input,
        .hp-form-group select,
        .hp-form-group textarea {
            width: 100%; padding: 12px 16px;
            border: 1.5px solid #e5e7eb; border-radius: 10px;
            font-family: 'Roboto', sans-serif; font-size: 0.93rem; color: #1f2937;
            background: #fafafa; transition: border-color 0.2s ease, box-shadow 0.2s ease;
            outline: none;
        }
        .hp-form-group input:focus,
        .hp-form-group select:focus,
        .hp-form-group textarea:focus {
            border-color: #C9A84C;
            box-shadow: 0 0 0 3px rgba(201,168,76,0.15);
            background: #fff;
        }
        .hp-form-group textarea { resize: vertical; min-height: 120px; }
        .hp-form-submit {
            width: 100%; padding: 14px;
            font-family: 'Jost', sans-serif; font-size: 0.9rem; font-weight: 700;
            letter-spacing: 0.08em; text-transform: uppercase;
            color: #07090F;
            background: linear-gradient(135deg, #C9A84C, #E8C96A);
            border: none; border-radius: 10px; cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 0 20px rgba(201,168,76,0.35);
        }
        .hp-form-submit:hover { transform: translateY(-2px); box-shadow: 0 0 30px rgba(201,168,76,0.55); }
        .hp-form-msg-success {
            color: #059669; font-weight: 700; text-align: center;
            margin-top: 14px; font-size: 0.9rem;
        }
        .hp-form-msg-error {
            color: #dc2626; font-weight: 600; text-align: center;
            margin-top: 14px; font-size: 0.88rem;
        }

        /* ════════════════════════════════════════════
           6. SUBSCRIBE — PREMIUM DARK
        ════════════════════════════════════════════ */
        .hp-subscribe {
            background: linear-gradient(135deg, #07090F 0%, #0d1a3a 60%, #07090F 100%);
            padding: 80px 0;
            position: relative; overflow: hidden;
        }
        .hp-subscribe::before {
            content: ''; position: absolute;
            inset: 0;
            background: url('img/subscribe-img.jpg') center/cover no-repeat;
            opacity: 0.06;
        }
        .hp-subscribe-inner { position: relative; z-index: 1; max-width: 600px; margin: 0 auto; text-align: center; }
        .hp-subscribe .section-eyebrow-hp { color: #C9A84C; }
        .hp-subscribe-h2 {
            font-family: 'Jost', sans-serif; font-size: clamp(1.8rem, 3.5vw, 2.6rem);
            font-weight: 800; color: #fff; line-height: 1.2; margin-bottom: 1rem;
        }
        .hp-subscribe-p {
            font-family: 'Roboto', sans-serif; font-size: 0.97rem;
            color: rgba(210,205,195,0.72); line-height: 1.75; margin-bottom: 2.2rem;
        }
        .hp-subscribe-form { display: flex; gap: 0; border-radius: 50px; overflow: hidden;
            box-shadow: 0 0 30px rgba(201,168,76,0.15); max-width: 480px; margin: 0 auto; }
        .hp-subscribe-form input {
            flex: 1; padding: 0.9rem 1.4rem; border: none;
            font-family: 'Roboto', sans-serif; font-size: 0.93rem;
            background: rgba(255,255,255,0.07); color: #fff;
            outline: none; border-right: 1px solid rgba(201,168,76,0.2);
        }
        .hp-subscribe-form input::placeholder { color: rgba(200,195,180,0.5); }
        .hp-subscribe-form button {
            padding: 0.9rem 1.6rem;
            font-family: 'Jost', sans-serif; font-size: 0.82rem;
            font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
            color: #07090F; background: linear-gradient(135deg, #C9A84C, #E8C96A);
            border: none; cursor: pointer; white-space: nowrap;
            transition: opacity 0.2s ease; touch-action: manipulation;
        }
        .hp-subscribe-form button:hover { opacity: 0.88; }

        /* ════════════════════════════════════════════
           7. FOOTER — PREMIUM DARK
        ════════════════════════════════════════════ */
        .hp-footer {
            background: #07090F;
            border-top: 1px solid rgba(201,168,76,0.1);
            padding: 60px 0 0;
        }
        .hp-footer h4 {
            font-family: 'Jost', sans-serif; font-size: 1rem;
            font-weight: 700; color: #F1EBD6;
            letter-spacing: 0.04em; margin-bottom: 1.4rem;
            padding-bottom: 0.7rem;
            border-bottom: 1px solid rgba(201,168,76,0.2);
        }
        .hp-footer p, .hp-footer a {
            font-family: 'Roboto', sans-serif; font-size: 0.88rem;
            color: rgba(200,195,180,0.65); margin-bottom: 0.6rem;
            text-decoration: none; display: block;
            transition: color 0.2s ease;
        }
        .hp-footer a:hover { color: #C9A84C; }
        .hp-footer-brand {
            font-family: 'Jost', sans-serif; font-size: 1.4rem;
            font-weight: 800; color: #F1EBD6; margin-bottom: 0.6rem;
        }
        .hp-footer-brand span { color: #C9A84C; }
        .hp-footer-brand-sub {
            font-size: 0.82rem; color: rgba(200,195,180,0.5);
            margin-bottom: 1.4rem; line-height: 1.5;
        }
        .hp-footer-social a {
            display: inline-flex; align-items: center; justify-content: center;
            width: 36px; height: 36px; border-radius: 50%;
            border: 1px solid rgba(201,168,76,0.3); color: #C9A84C;
            font-size: 0.85rem; margin-right: 0.5rem; margin-bottom: 0;
            transition: all 0.22s ease; text-decoration: none;
        }
        .hp-footer-social a:hover {
            background: rgba(201,168,76,0.15);
            border-color: rgba(201,168,76,0.6);
        }
        .hp-footer-divider {
            margin-top: 50px;
            border-top: 1px solid rgba(255,255,255,0.05);
            padding: 1.2rem 0;
        }
        .hp-footer-copy {
            font-size: 0.78rem; color: rgba(200,195,180,0.35);
            font-family: 'Roboto', sans-serif; text-align: center;
        }

        /* ════════════════════════════════════════════
           RESPONSIVE
        ════════════════════════════════════════════ */
        @media (max-width: 991px) {
            .hp-hero-h1 { font-size: clamp(2rem, 7vw, 2.8rem); }
            .hp-hero-btns { justify-content: center; }
            .hp-hero-content .col-lg-8 { text-align: center; }
            .hp-eyebrow { margin-left: auto; margin-right: auto; }
            .hp-hero-sub { margin-left: auto; margin-right: auto; max-width: 100%; }
            .hp-carousel-controls { right: 1rem; }

            /* Mobile-optimized About Video Section */
            .hp-about {
                padding: 0 0 50px 0;
                display: flex;
                flex-direction: column;
            }
            .hp-about-video {
                position: relative;
                top: 0; left: 0; transform: none;
                width: 100%;
                height: auto;
                aspect-ratio: 16/9;
                object-fit: contain;
                opacity: 1;
                z-index: 1;
                background: #000;
            }
            .hp-about-overlay {
                display: none;
            }
            .hp-about-content {
                margin-top: -30px;
                padding: 0 15px;
                position: relative;
                z-index: 2;
            }
            .hp-about-glass {
                padding: 25px 20px;
                background: rgba(7, 9, 20, 0.95);
            }
        }
        @media (max-width: 767px) {
            .hp-about { padding: 0 0 40px 0; }
            .hp-about-features { grid-template-columns: 1fr; }
            .hp-about-stats { grid-template-columns: 1fr 1fr; }
            .hp-destinations { padding: 60px 0 50px; }
            .hp-reviews { padding: 60px 0; }
            .hp-subscribe { padding: 60px 0; }
            .hp-subscribe-form { flex-direction: column; border-radius: 12px; overflow: visible; gap: 0.7rem; }
            .hp-subscribe-form input { border-radius: 8px; border-right: none; }
            .hp-subscribe-form button { border-radius: 8px; }
            .hp-stat-item { padding: 0.75rem 0; }
            .hp-stat-num { font-size: 1.3rem; }
            .hp-stat-label { font-size: 0.55rem; }
        }
        @media (max-width: 430px) {
            .hp-hero-h1 { font-size: clamp(1.8rem, 8vw, 2.3rem); }
            .hp-hero-btns { flex-direction: column; align-items: center; }
            .btn-gold-hp, .btn-ghost-hp { width: 100%; max-width: 280px; justify-content: center; min-height: 48px; }
        }
        @media (hover: none) and (pointer: coarse) {
            .destination-img:hover { transform: none; box-shadow: 0 8px 30px rgba(0,0,0,0.12); }
            .destination-img:hover img { transform: none; }
            .testimonial-item:hover { transform: none !important; }
            .hp-stat-card:hover { transform: none; }
            .btn-gold-hp:hover { transform: none; }
        }
    </style>
</head>

<body class="index-page">

    <!-- Scroll Progress -->
    <div id="scroll-progress"></div>

    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- ════════════════════════════════════════════
         1. PREMIUM HERO (wraps Navbar + Carousel)
    ════════════════════════════════════════════ -->
    <section class="hp-hero" aria-label="Hero section">
        <!-- Ambient orbs -->
        <div class="hp-hero-orb hp-hero-orb-1" aria-hidden="true"></div>
        <div class="hp-hero-orb hp-hero-orb-2" aria-hidden="true"></div>

        <!-- Background carousel -->
        <div class="hp-carousel-wrap" aria-hidden="true">
            <div id="carouselId" class="carousel slide h-100" data-bs-ride="carousel">
                <div class="carousel-inner h-100" role="listbox">
                    <div class="carousel-item active h-100">
                        <img src="img/carousel-2.jpg" alt="Explore Sri Lanka — unforgettable journeys" loading="eager">
                    </div>
                    <div class="carousel-item h-100">
                        <img src="img/carousel-1.jpg" alt="Find Your Perfect Tour at AsanTravels" loading="lazy">
                    </div>
                    <div class="carousel-item h-100">
                        <img src="img/carousel-3.jpg" alt="Explore, relax, and enjoy every moment" loading="lazy">
                    </div>
                </div>
            </div>
            <div class="hp-carousel-overlay"></div>
        </div>

        <!-- Top bar — social icons (desktop only) -->
        <div class="container-fluid bg-primary px-5 d-none d-lg-block" style="position:relative;z-index:10;background:rgba(19,53,123,0.7)!important;backdrop-filter:blur(12px);">
            <div class="row gx-0">
                <div class="col-lg-8 text-center text-lg-start">
                    <div class="d-inline-flex align-items-center" style="height:40px;">
                        <a class="btn-square btn btn-primary rounded-circle mx-1" href="https://www.facebook.com/share/16T8gBySGv/?mibextid=wwXIfr" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a class="btn-square btn btn-primary rounded-circle mx-1" href="https://www.instagram.com/_asantravels_?igsh=MW1xamdpejN5Zmk5Mw%3D%3D&utm_source=qr" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a class="btn-square btn btn-primary rounded-circle mx-1" href="http://www.tiktok.com/@asantravels" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navbar -->
        <div class="container-fluid position-relative" style="z-index:10;">
            <nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0">
                <a href="index1.php" class="navbar-brand p-0 d-flex align-items-center gap-2">
                    <img src="img/asntravel logo.jpg" alt="AsanTravels logo" style="max-height:46px;border-radius:6px;box-shadow:0 2px 8px rgba(0,0,0,0.4);transition:transform 0.3s;">
                    <span style="font-family:'Jost',sans-serif;font-size:1.1rem;font-weight:700;color:#fff;text-shadow:0 1px 4px rgba(0,0,0,0.5);"><i class="fa fa-map-marker-alt me-1" style="color:#C9A84C;"></i>AsanTravels</span>
                </a>
                <button class="navbar-toggler navbar-toggler-custom" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">
                        <a href="index1.php" class="nav-item nav-link active">Home</a>
                        <a href="about.html" class="nav-item nav-link">About</a>
                        <a href="packages.html" class="nav-item nav-link">Packages</a>
                        <a href="Custom_Packages.html" class="nav-item nav-link">Custom Tours</a>
                        <a href="contact.php" class="nav-item nav-link">Contact</a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Hero Content -->
        <div class="hp-hero-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-xl-7">
                        <div class="hp-eyebrow" aria-label="Category label">
                            <span class="blink" aria-hidden="true"></span>
                            Explore Sri Lanka
                        </div>
                        <h1 class="hp-hero-h1">
                            Unforgettable Journeys,<br>
                            <span class="gold">Beautiful Memories</span>
                        </h1>
                        <p class="hp-hero-sub">
                            Explore the wonders of Sri Lanka with <strong style="color:#E8C96A;">Asanka Rajapakse</strong> — 
                            passionate expert with <strong style="color:#E8C96A;">10+ years</strong> and 
                            <strong style="color:#E8C96A;">700+ tours</strong>. We craft personalised itineraries 
                            that connect you with the island's soul, its people, and its timeless beauty.
                        </p>
                        <div class="hp-hero-btns">
                            <a href="packages.html" class="btn-gold-hp" id="hero-cta-packages">
                                <i class="fas fa-compass"></i> Explore Packages
                            </a>
                            <a href="Custom_Packages.html" class="btn-ghost-hp" id="hero-cta-custom">
                                <i class="fas fa-route"></i> Custom Tour
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carousel prev/next controls -->
        <div class="hp-carousel-controls" aria-label="Carousel navigation">
            <button type="button" data-bs-target="#carouselId" data-bs-slide="prev" aria-label="Previous slide">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button type="button" data-bs-target="#carouselId" data-bs-slide="next" aria-label="Next slide">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <!-- Stats bar -->
        <div class="hp-stats" aria-label="Key statistics">
            <div class="container">
                <div class="row">
                    <div class="col-3 col-md-3 hp-stat-item">
                        <div class="hp-stat-num">10+</div>
                        <div class="hp-stat-label">Years Experience</div>
                    </div>
                    <div class="col-3 col-md-3 hp-stat-item">
                        <div class="hp-stat-num">700+</div>
                        <div class="hp-stat-label">Tours Led</div>
                    </div>
                    <div class="col-3 col-md-3 hp-stat-item">
                        <div class="hp-stat-num">26</div>
                        <div class="hp-stat-label">Destinations</div>
                    </div>
                    <div class="col-3 col-md-3 hp-stat-item">
                        <div class="hp-stat-num">24/7</div>
                        <div class="hp-stat-label">Support</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero End -->

    <!-- ════════════════════════════════════════════
         2. ABOUT — VIDEO BACKGROUND SECTION
    ════════════════════════════════════════════ -->
    <section class="hp-about" id="about" aria-labelledby="about-heading">
        <!-- Video background -->
        <video class="hp-about-video" autoplay muted loop playsinline preload="metadata" aria-hidden="true">
            <source src="img/logo_video.mp4" type="video/mp4">
        </video>
        <div class="hp-about-overlay" aria-hidden="true"></div>
        <div class="hp-about-orb hp-about-orb-1" aria-hidden="true"></div>
        <div class="hp-about-orb hp-about-orb-2" aria-hidden="true"></div>

        <div class="container hp-about-content">
            <div class="row g-5 align-items-center">
                <!-- Text side -->
                <div class="col-lg-6 reveal">
                    <div class="hp-about-glass">
                        <p class="hp-about-eyebrow">About AsanTravels</p>
                        <h2 class="hp-about-h2" id="about-heading">
                            Welcome to<br><span class="gold-grad">AsanTravels</span>
                        </h2>
                        <p class="hp-about-p">
                            At AsanTravels, you're guided by <strong style="color:#E8C96A;">Asanka Rajapakse</strong>, a passionate tour guide with over <strong style="color:#E8C96A;">10 years of experience</strong> and <strong style="color:#E8C96A;">700+ successful tours</strong>. Asanka's deep knowledge and friendly approach make every journey personal and memorable.
                        </p>
                        <p class="hp-about-p">
                            Fluent in English, he ensures you feel comfortable and informed from start to finish. Enjoy flexible tour plans, handpicked hotels, customizable vehicles, and 24/7 service — all tailored to your needs.
                        </p>
                        <div class="hp-about-features">
                            <div class="hp-about-feat"><i class="fas fa-check-circle"></i> Flight Booking on Request</div>
                            <div class="hp-about-feat"><i class="fas fa-check-circle"></i> Handpicked Hotels</div>
                            <div class="hp-about-feat"><i class="fas fa-check-circle"></i> Customizable Vehicles</div>
                            <div class="hp-about-feat"><i class="fas fa-check-circle"></i> 24/7 Support</div>
                            <div class="hp-about-feat"><i class="fas fa-check-circle"></i> English-Speaking Guide</div>
                            <div class="hp-about-feat"><i class="fas fa-check-circle"></i> Flexible Itineraries</div>
                        </div>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="about.html" class="btn-gold-hp"><i class="fas fa-user"></i> Meet Asanka</a>
                            <a href="contact.php" class="btn-ghost-hp"><i class="fas fa-comments"></i> Get in Touch</a>
                        </div>
                    </div>
                </div>

                <!-- Stats side -->
                <div class="col-lg-6">
                    <div class="hp-about-stats reveal reveal-delay-2">
                        <div class="hp-stat-card">
                            <div class="hp-stat-card-num" data-target="10" data-suffix="+">0+</div>
                            <div class="hp-stat-card-label">Years Experience</div>
                        </div>
                        <div class="hp-stat-card">
                            <div class="hp-stat-card-num" data-target="700" data-suffix="+">0+</div>
                            <div class="hp-stat-card-label">Tours Completed</div>
                        </div>
                        <div class="hp-stat-card">
                            <div class="hp-stat-card-num" data-target="26" data-suffix="">0</div>
                            <div class="hp-stat-card-label">Destinations</div>
                        </div>
                        <div class="hp-stat-card">
                            <div class="hp-stat-card-num" data-target="100" data-suffix="%">0%</div>
                            <div class="hp-stat-card-label">Client Satisfaction</div>
                        </div>
                    </div>

                    <!-- Decorative image badge -->
                    <div class="hp-about-img-wrap mt-4 reveal reveal-delay-3">
                        <img src="img/carousel-3.jpg" alt="Sri Lanka scenic view" loading="lazy">
                        <div class="hp-about-badge">
                            <i class="fas fa-award"></i>
                            <div class="hp-about-badge-text">
                                <small>Trusted Since</small>
                                <strong>2014 · Sri Lanka's Expert Guide</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- About End -->

        <!-- Destination Start -->
        <div class="container-fluid destination py-5">
            <div class="container py-5">
                <div class="mx-auto text-center mb-5" style="max-width: 1100px;">
                    <h5 class="section-title px-3">Destination</h5>
                    <h1 class="mb-0">Popular Destination in Sri Lanka</h1>
                </div>
                <div class="tab-class text-center">
                    <ul class="nav nav-pills d-inline-flex justify-content-center mb-5">
                    
                       
                    </ul>
                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane fade show p-0 active">
                            <div class="row g-4">
                                <div class="col-xl-8">
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="destination-img">
                                                <img class="img-fluid rounded w-100" src="img/destination-1.jpg" alt="">
                                                <div class="destination-overlay p-4">
                                                    <a href="Southern_Coast.php" class="btn btn-primary text-white rounded-pill border py-2 px-3">20 Photos</a>
                                                    <h4 class="text-white mb-2 mt-3">Southern Coast</h4>
                                                    <a href="Southern_Coast.php" class="btn-hover text-white">View All Place <i class="fa fa-arrow-right ms-2"></i></a>
                                                </div>
                                                <div class="search-icon">
                                                    <a href="img/destination-1.jpg" data-lightbox="destination-1"><i class="fa fa-plus-square fa-1x btn btn-light btn-lg-square text-primary"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="destination-img">
                                                <img class="img-fluid rounded w-100" src="img/destination-2.jpg" alt="">
                                                <div class="destination-overlay p-4">
                                                    <a href="Eastern_Coast.php" class="btn btn-primary text-white rounded-pill border py-2 px-3">20 Photos</a>
                                                    <h4 class="text-white mb-2 mt-3">Eastern Coast</h4>
                                                    <a href="Eastern_Coast.php" class="btn-hover text-white">View All Place <i class="fa fa-arrow-right ms-2"></i></a>
                                                </div>
                                                <div class="search-icon">
                                                    <a href="img/destination-2.jpg" data-lightbox="destination-2"><i class="fa fa-plus-square fa-1x btn btn-light btn-lg-square text-primary"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="destination-img">
                                                <img class="img-fluid rounded w-100" src="img/destination-7.jpg" alt="">
                                                <div class="destination-overlay p-4">
                                                    <a href="Northern_Coast.php" class="btn btn-primary text-white rounded-pill border py-2 px-3">20 Photos</a>
                                                    <h4 class="text-white mb-2 mt-3">Northern Coast</h4>
                                                    <a href="Northern_Coast.php" class="btn-hover text-white">View All Place <i class="fa fa-arrow-right ms-2"></i></a>
                                                </div>
                                                <div class="search-icon">
                                                    <a href="img/destination-7.jpg" data-lightbox="destination-7"><i class="fa fa-plus-square fa-1x btn btn-light btn-lg-square text-primary"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="destination-img">
                                                <img class="img-fluid rounded w-100 h-50" src="img/destination-8.jpg" alt="">
                                                <div class="destination-overlay p-4">
                                                    <a href="Western_Coast.php" class="btn btn-primary text-white rounded-pill border py-2 px-3">20 Photos</a>
                                                    <h4 class="text-white mb-2 mt-3">Western Coast</h4>
                                                    <a href="Western_Coast.php" class="btn-hover text-white">View All Place <i class="fa fa-arrow-right ms-2"></i></a>
                                                </div>
                                                <div class="search-icon">
                                                    <a href="img/destination-8.jpg" data-lightbox="destination-8"><i class="fa fa-plus-square fa-1x btn btn-light btn-lg-square text-primary"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="destination-img h-100">
                                        <img class="img-fluid rounded w-100 h-100" src="img/destination-9.jpg" style="object-fit: cover; min-height: 300px;" alt="">
                                        <div class="destination-overlay p-4">
                                            <a href="Lanka's_Wild_Kingdom.php" class="btn btn-primary text-white rounded-pill border py-2 px-3">20 Photos</a>
                                            <h4 class="text-white mb-2 mt-3"> Lanka's Wild Kingdom</h4>
                                            <a href="Lanka's_Wild_Kingdom.php" class="btn-hover text-white">View All Place <i class="fa fa-arrow-right ms-2"></i></a>
                                        </div>
                                        <div class="search-icon">
                                            <a href="img/destination-9.jpg" data-lightbox="destination-4"><i class="fa fa-plus-square fa-1x btn btn-light btn-lg-square text-primary"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Destination End -->

    <!-- ════════════════════════════════════════════
         4. REVIEWS — GLASSMORPHISM CAROUSEL
    ════════════════════════════════════════════ -->
    <section class="hp-reviews" id="reviews" aria-labelledby="reviews-heading">
        <div class="hp-reviews-orb hp-reviews-orb-1" aria-hidden="true"></div>
        <div class="hp-reviews-orb hp-reviews-orb-2" aria-hidden="true"></div>

        <div class="container hp-reviews-inner">
            <div class="text-center mb-5 reveal">
                <span class="section-eyebrow-hp">Client Testimonials</span>
                <h2 class="section-heading-hp" id="reviews-heading" style="color:#F1EBD6;">What Our <span style="color:#C9A84C;">Clients Say</span></h2>
                <p class="section-sub-hp" style="color:rgba(200,195,180,0.65);">Real experiences from real travellers who explored Sri Lanka with AsanTravels.</p>
            </div>

            <?php
            // Reuse the single $conn connection from top of file
            $result = $conn->query("SELECT * FROM reviews ORDER BY id DESC");
            ?>

            <div class="testimonial-carousel owl-carousel">
                <?php while ($row = $result->fetch_assoc()) {
                    $imagePath = "uploads/" . $row['filename'];
                    if (!file_exists($imagePath) || empty($row['filename'])) {
                        $imagePath = "img/default-user.png";
                    }
                ?>
                    <div class="testimonial-item text-center rounded shadow-lg pb-4 px-3 bg-white">
                        <div class="testimonial-comment bg-light rounded p-4 mb-4">
                            <p class="text-center mb-0 fst-italic">
                                "<?php echo htmlspecialchars($row['comments']); ?>"
                            </p>
                        </div>
                        <div class="testimonial-img p-1">
                            <img src="<?php echo $imagePath; ?>"
                                 class="img-fluid rounded-circle border border-3 border-primary shadow"
                                 style="width:100px;height:100px;object-fit:cover;"
                                 alt="Client Image" loading="lazy">
                        </div>
                        <div style="margin-top:-30px;">
                            <h5 class="mb-1 text-dark"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <p class="mb-2 text-muted small"><?php echo htmlspecialchars($row['email']); ?></p>
                            <div class="d-flex justify-content-center">
                                <?php for ($i = 0; $i < 5; $i++) { ?>
                                    <i class="fas fa-star text-warning"></i>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
    <!-- Reviews End -->

    <!-- ════════════════════════════════════════════
         5. SUBMIT REVIEW — PREMIUM FORM
    ════════════════════════════════════════════ -->
    <section id="review-section" class="hp-submit-section" aria-labelledby="submit-review-heading">
        <div class="container">
            <div class="text-center mb-5 reveal">
                <span class="section-eyebrow-hp" style="color:#13357B;">Share Your Experience</span>
                <h2 class="section-heading-hp" id="submit-review-heading">Submit Your <span>Review</span></h2>
                <p class="section-sub-hp">Your feedback helps us and future travellers. We'd love to hear about your journey!</p>
            </div>

            <div class="hp-review-form-wrap reveal reveal-delay-1">
                <h2>Leave a Review</h2>

<?php
// Reuse the single $conn connection from top of file
$message = "";

if (isset($_POST['submit'])) {
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $rating = intval($_POST['rating'] ?? 0);
    $comments = $conn->real_escape_string($_POST['comments'] ?? '');
    $filename = NULL;

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = basename($_FILES['file']['name']);
        $fileType = mime_content_type($fileTmpPath);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        if (!in_array($fileType, $allowedTypes)) {
            $message = "Error: Only JPG, PNG, GIF images and PDF files are allowed.";
        } else {
            $newFilename = time() . '_' . $fileName;
            $destPath = $uploadDir . $newFilename;
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $filename = $newFilename;
            } else {
                $message = "Error uploading the file.";
            }
        }
    }

    if (empty($message)) {
        $dupStmt = $conn->prepare("SELECT id FROM reviews WHERE name=? AND email=? AND comments=?");
        $dupStmt->bind_param("sss", $name, $email, $comments);
        $dupStmt->execute();
        $dupStmt->store_result();
        if ($dupStmt->num_rows > 0) {
            $message = "Error: Duplicate review detected. Please do not submit the same review multiple times.";
        } else {
            $stmt = $conn->prepare("INSERT INTO reviews (name, email, rating, comments, filename) VALUES (?, ?, ?, ?, ?)");
            if ($stmt === false) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("ssiss", $name, $email, $rating, $comments, $filename);
            if ($stmt->execute()) {
                $message = "Review submitted successfully!";
            } else {
                $message = "Execute failed: " . $stmt->error;
            }
            $stmt->close();
        }
        $dupStmt->close();
    }
}
?>
                <form method="POST" action="index1.php" enctype="multipart/form-data" novalidate>
                    <div class="hp-form-group">
                        <label for="hp-file">Profile Photo</label>
                        <input type="file" id="hp-file" name="file" required class="hp-form-input">
                    </div>
                    <div class="hp-form-group">
                        <label for="hp-name">Your Name</label>
                        <input type="text" id="hp-name" name="name" placeholder="e.g. John Smith" required>
                    </div>
                    <div class="hp-form-group">
                        <label for="hp-email">Email Address</label>
                        <input type="email" id="hp-email" name="email" placeholder="you@example.com" required>
                    </div>
                    <div class="hp-form-group">
                        <label for="hp-rating">Rating</label>
                        <select id="hp-rating" name="rating" required>
                            <option value="" disabled selected>Select your rating</option>
                            <option value="5">⭐⭐⭐⭐⭐ — Excellent</option>
                            <option value="4">⭐⭐⭐⭐ — Good</option>
                            <option value="3">⭐⭐⭐ — Average</option>
                            <option value="2">⭐⭐ — Poor</option>
                            <option value="1">⭐ — Terrible</option>
                        </select>
                    </div>
                    <div class="hp-form-group">
                        <label for="hp-comments">Your Experience</label>
                        <textarea id="hp-comments" name="comments" placeholder="Tell us about your journey..." required></textarea>
                    </div>
                    <button type="submit" name="submit" class="hp-form-submit">
                        <i class="fas fa-paper-plane" style="margin-right:6px;"></i> Submit Review
                    </button>
                    <?php if ($message): ?>
                        <p class="<?php echo (strpos($message,'Error') === 0) ? 'hp-form-msg-error' : 'hp-form-msg-success'; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </section>
    <!-- Submit Review End -->

    <!-- ════════════════════════════════════════════
         6. SUBSCRIBE — PREMIUM DARK
    ════════════════════════════════════════════ -->
    <section class="hp-subscribe" id="newsletter" aria-labelledby="subscribe-heading">
        <div class="container">
            <div class="hp-subscribe-inner reveal">
                <span class="section-eyebrow-hp">Stay Updated</span>
                <h2 class="hp-subscribe-h2" id="subscribe-heading">Subscribe to Our Newsletter</h2>
                <p class="hp-subscribe-p">Be the first to explore new destinations, exclusive travel deals, and trending tour packages from AsanTravels.</p>
                <form method="POST" class="hp-subscribe-form" aria-label="Newsletter subscription form">
                    <input type="email" name="email" placeholder="Enter your email address" required aria-label="Email address">
                    <button type="submit" name="submit4">Subscribe</button>
                </form>
            </div>
        </div>
    </section>
    <!-- Subscribe End -->

    <!-- ════════════════════════════════════════════
         7. FOOTER — PREMIUM DARK
    ════════════════════════════════════════════ -->
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
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary btn-primary-outline-0 btn-md-square back-to-top" aria-label="Back to top">
        <i class="fa fa-arrow-up"></i>
    </a>

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>
    <!-- Template Javascript -->
    <script src="js/main.js"></script>

    <script>
        /* ── Scroll Progress Bar ──────────────────────── */
        (function () {
            const bar = document.getElementById('scroll-progress');
            if (!bar) return;
            window.addEventListener('scroll', function () {
                const scrollTop = window.scrollY || document.documentElement.scrollTop;
                const docHeight = document.documentElement.scrollHeight - window.innerHeight;
                bar.style.width = (docHeight > 0 ? (scrollTop / docHeight) * 100 : 0) + '%';
            }, { passive: true });
        })();

        /* ── Scroll Reveal (IntersectionObserver) ───────── */
        (function () {
            const els = document.querySelectorAll('.reveal');
            if (!els.length) return;
            const io = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) {
                        e.target.classList.add('revealed');
                        io.unobserve(e.target);
                    }
                });
            }, { threshold: 0.12 });
            els.forEach(function (el) { io.observe(el); });
        })();

        /* ── Animated Counters ─────────────────────────── */
        (function () {
            const counters = document.querySelectorAll('[data-target]');
            if (!counters.length) return;
            let started = false;

            function runCounters() {
                if (started) return;
                started = true;
                counters.forEach(function (el) {
                    const target = parseInt(el.getAttribute('data-target'), 10);
                    const suffix = el.getAttribute('data-suffix') || '';
                    const duration = 1600;
                    const step = duration / target;
                    let current = 0;
                    const timer = setInterval(function () {
                        current += Math.ceil(target / 60);
                        if (current >= target) {
                            current = target;
                            clearInterval(timer);
                        }
                        el.textContent = current + suffix;
                    }, step < 16 ? 16 : step);
                });
            }

            const statsSection = document.querySelector('.hp-about-stats');
            if (!statsSection) return;
            const io = new IntersectionObserver(function (entries) {
                if (entries[0].isIntersecting) {
                    runCounters();
                    io.disconnect();
                }
            }, { threshold: 0.3 });
            io.observe(statsSection);
        })();

        /* ── Carousel auto-advance timing ─────────────── */
        // Already handled by data-bs-ride="carousel" (Bootstrap default 5s)
        // We just ensure it is also data-bs-interval compatible
        (function () {
            const carousel = document.getElementById('carouselId');
            if (carousel && window.bootstrap) {
                new bootstrap.Carousel(carousel, { interval: 5500, wrap: true });
            }
        })();

        /* ── Auto-play background video ─────────────────── */
        (function () {
            const video = document.querySelector('.hp-about-video');
            if (!video) return;
            const playVideo = () => {
                video.play().catch(function () {});
            };
            // Try playing immediately
            playVideo();
            // Also attempt to play if it was blocked initially (on first interaction)
            document.addEventListener('touchstart', playVideo, { once: true });
            document.addEventListener('click', playVideo, { once: true });
            
            // Also use IntersectionObserver as fallback for when it comes into view
            const io = new IntersectionObserver(function (entries) {
                if (entries[0].isIntersecting) {
                    playVideo();
                    io.disconnect();
                }
            }, { threshold: 0.1 });
            io.observe(video);
        })();
    </script>

</body>

<?php $conn->close(); ?>
</html>