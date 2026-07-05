<?php
// ── generate_doc.php ────────────────────────────────────────────────────────
// Admin-only: generates a .docx (Word) file for a booking
// METHOD: Raw XML/ZIP approach — no external library needed  
//         Falls back to styled HTML-as-doc if zip not available

ob_start();
error_reporting(0);
ini_set('display_errors', 0);

session_start();

if (!isset($_SESSION['loggedin'])) {
    ob_end_clean();
    header('Location: asn_admin_loging.php');
    exit;
}

// ── Load PHPWord ──────────────────────────────────────────────────────────
$autoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    ob_end_clean();
    die('PHPWord library not found. Please run: <code>php composer.phar require phpoffice/phpword</code>');
}
require_once $autoload;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\IOFactory;

$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
if (!$booking_id) { ob_end_clean(); die('Invalid booking ID.'); }

$conn = new mysqli("localhost", "root", "", "asantravels_og");
if ($conn->connect_error) { ob_end_clean(); die('DB error: ' . $conn->connect_error); }

$stmt = $conn->prepare("SELECT * FROM booking WHERE id = ?");
$stmt->bind_param('i', $booking_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$booking) { ob_end_clean(); die('Booking #' . $booking_id . ' not found.'); }

$day_plan = json_decode($booking['optional_tours'] ?? '{}', true) ?: [];

// ── Setup colors & styles ─────────────────────────────────────────────────
$brandBlue   = '13357B';
$brandLight  = 'EBF1FF';
$white       = 'FFFFFF';
$darkText    = '1A1A2E';
$green       = '1B8A4E';

// Helper: sanitize text for PHPWord (it fails to escape & and some special chars)
function safe($text) {
    $text = (string)$text;
    // Replace & with 'and' (PHPWord doesn't escape it properly in XML)
    $text = str_replace('&', 'and', $text);
    // Remove any control characters that break XML
    $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $text);
    return $text;
}

$phpWord = new PhpWord();
$phpWord->getSettings()->setUpdateFields(true);
$phpWord->setDefaultFontName('Calibri');
$phpWord->setDefaultFontSize(11);

$phpWord->addTitleStyle(2, ['bold' => true, 'size' => 14, 'color' => $brandBlue, 'name' => 'Calibri'],
    ['spaceAfter' => 120, 'spaceBefore' => 160]);

$sectionStyle = ['marginTop' => 900, 'marginBottom' => 900, 'marginLeft' => 1000, 'marginRight' => 1000];
$section = $phpWord->addSection($sectionStyle);

// ── HEADER ──────────────────────────────────────────────────────────────
$headerTable = $section->addTable(['borderSize' => 0, 'width' => 9000, 'unit' => 'dxa', 'cellMargin' => 200]);
$headerTable->addRow(800);
$headerCell = $headerTable->addCell(9000, ['bgColor' => $brandBlue, 'gridSpan' => 1]);
$headerCell->addText('   AsanTravels - Tour Package Document',
    ['bold' => true, 'color' => $white, 'size' => 18, 'name' => 'Calibri'],
    ['alignment' => Jc::CENTER]);
$headerCell->addText('Sri Lanka Custom Itinerary',
    ['color' => 'CCDDFF', 'size' => 11, 'name' => 'Calibri'],
    ['alignment' => Jc::CENTER]);
$section->addTextBreak(1);

$refTable = $section->addTable(['borderSize' => 6, 'borderColor' => $brandBlue, 'width' => 9000, 'unit' => 'dxa', 'cellMargin' => 150]);
$refTable->addRow();
$refCell = $refTable->addCell(9000, ['bgColor' => $brandLight]);
$refCell->addText('Booking Reference: #' . str_pad($booking_id, 5, '0', STR_PAD_LEFT) . '   |   Generated: ' . date('d M Y, h:i A'),
    ['bold' => true, 'size' => 10, 'color' => $brandBlue],
    ['alignment' => Jc::CENTER]);
$section->addTextBreak(1);

// ── INFO & TRIP OVERVIEW ──────────────────────────────────────────────────
$section->addTitle('  Guest Information', 2);
$custTable = $section->addTable(['borderSize' => 4, 'borderColor' => 'DDDDDD', 'width' => 9000, 'unit' => 'dxa', 'cellMargin' => 120]);
$labelCell = ['bgColor' => $brandLight, 'width' => 2800];
$valueCell = ['width' => 6200];

$infoRows = [
    ['Full Name',      safe($booking['name'] ?? '-')],
    ['Email',          safe($booking['email'] ?? '-')],
    ['Special Request', safe($booking['special_request'] ?: 'None')],
    ['Booking Status', safe($booking['status'] ?? 'Pending')],
];
foreach ($infoRows as $row) {
    $custTable->addRow();
    $custTable->addCell(2800, $labelCell)->addText($row[0], ['bold' => true, 'color' => $darkText]);
    $custTable->addCell(6200, $valueCell)->addText($row[1], ['color' => $darkText]);
}
$section->addTextBreak(1);

$section->addTitle('  Trip Overview', 2);
$tripTable = $section->addTable(['borderSize' => 4, 'borderColor' => 'DDDDDD', 'width' => 9000, 'unit' => 'dxa', 'cellMargin' => 120]);
$numAdults   = intval($booking['num_adults'] ?? $booking['passengers'] ?? 1);
$numChildren = intval($booking['num_children'] ?? 0);

$tripRows = [
    ['Package Name',   safe($booking['package_name'] ?? $booking['Package'] ?? 'Custom Tour')],
    ['Start Date',     !empty($booking['start_date']) ? date('d M Y', strtotime($booking['start_date'])) : '-'],
    ['End Date',       !empty($booking['end_date'])   ? date('d M Y', strtotime($booking['end_date']))   : '-'],
    ['Adults',         $numAdults . ' Adult(s)'],
    ['Children',       $numChildren . ' Child(ren)'],
    ['Room Option',    safe($booking['room_option'] ?? 'TBD')],
];
foreach ($tripRows as $row) {
    $tripTable->addRow();
    $tripTable->addCell(2800, $labelCell)->addText($row[0], ['bold' => true, 'color' => $darkText]);
    $tripTable->addCell(6200, $valueCell)->addText($row[1], ['color' => $darkText]);
}
$section->addTextBreak(1);

// ── ITINERARY ─────────────────────────────────────────────────────────────
$section->addTitle('  Day-by-Day Itinerary', 2);
if (!empty($day_plan)) {
    foreach ($day_plan as $dayNum => $plan) {
        $location = $plan['location'] ?? '-';
        $activities = $plan['activities'] ?? [];

        $dayTable = $section->addTable(['borderSize' => 6, 'borderColor' => $brandBlue, 'width' => 9000, 'unit' => 'dxa', 'cellMargin' => 120]);
        $dayTable->addRow(400);
        $dayTable->addCell(9000, ['bgColor' => $brandBlue])->addText('Day ' . $dayNum . '  -  ' . safe($location), ['bold' => true, 'color' => $white, 'size' => 12]);

        if (!empty($activities)) {
            $actTable = $section->addTable(['borderSize' => 4, 'borderColor' => 'CCCCCC', 'width' => 9000, 'unit' => 'dxa', 'cellMargin' => 100]);
            $actTable->addRow(300);
            $actTable->addCell(3500, ['bgColor' => $brandLight])->addText('Activity', ['bold' => true, 'size' => 10, 'color' => $brandBlue]);
            $actTable->addCell(1500, ['bgColor' => $brandLight])->addText('Adult Price', ['bold' => true, 'size' => 10, 'color' => $brandBlue]);
            $actTable->addCell(1500, ['bgColor' => $brandLight])->addText('Child Price', ['bold' => true, 'size' => 10, 'color' => $brandBlue]);
            $actTable->addCell(1200, ['bgColor' => $brandLight])->addText('Pax', ['bold' => true, 'size' => 10, 'color' => $brandBlue]);
            $actTable->addCell(1300, ['bgColor' => $brandLight])->addText('Subtotal', ['bold' => true, 'size' => 10, 'color' => $brandBlue]);

            $daySubtotal = 0;
            foreach ($activities as $act) {
                $subtotal = (floatval($act['adult'] ?? 0) * $numAdults) + (floatval($act['child'] ?? 0) * $numChildren);
                $daySubtotal += $subtotal;

                $actTable->addRow(250);
                $actTable->addCell(3500)->addText(safe($act['name'] ?? 'Activity'), ['size' => 10]);
                $actTable->addCell(1500)->addText('$' . number_format(floatval($act['adult'] ?? 0), 2), ['size' => 10]);
                $actTable->addCell(1500)->addText('$' . number_format(floatval($act['child'] ?? 0), 2), ['size' => 10]);
                $actTable->addCell(1200)->addText($numAdults . 'A / ' . $numChildren . 'C', ['size' => 10]);
                $actTable->addCell(1300)->addText('$' . number_format($subtotal, 2), ['bold' => true, 'size' => 10, 'color' => $green]);
            }
            $actTable->addRow(300);
            $actTable->addCell(7700, ['bgColor' => 'F0F4FF', 'gridSpan' => 4])->addText('Day ' . $dayNum . ' Total', ['bold' => true, 'size' => 10, 'color' => $brandBlue], ['alignment' => Jc::RIGHT]);
            $actTable->addCell(1300, ['bgColor' => 'F0F4FF'])->addText('$' . number_format($daySubtotal, 2), ['bold' => true, 'size' => 10, 'color' => $brandBlue]);
        } else {
            $noActTable = $section->addTable(['width' => 9000, 'unit' => 'dxa', 'cellMargin' => 100]);
            $noActTable->addRow();
            $noActTable->addCell(9000)->addText('No activities selected for this day.', ['italics' => true, 'color' => '999999', 'size' => 10]);
        }
        $section->addTextBreak(1);
    }
} else {
    $section->addText('No itinerary data available.', ['italics' => true, 'color' => '999999']);
    $section->addTextBreak(1);
}

// ── COST SUMMARY ──────────────────────────────────────────────────────────
$section->addTitle('  Cost Summary', 2);
$costTable = $section->addTable(['borderSize' => 6, 'borderColor' => $brandBlue, 'width' => 9000, 'unit' => 'dxa', 'cellMargin' => 140]);
$total      = floatval($booking['total'] ?? 0);
$payArrive  = floatval($booking['pay_on_arrival'] ?? $total / 2);
$remaining  = $total - $payArrive;

$costRows = [
    ['Activities and Transport Cost', '$' . number_format(floatval($booking['base_price'] ?? $total), 2)],
    ['Extras',                      '$' . number_format(floatval($booking['extras'] ?? 0), 2)],
    ['Total Cost',                  '$' . number_format($total, 2)],
    ['50% Deposit (Pay on Arrival)','$' . number_format($payArrive, 2)],
    ['Remaining Balance',           '$' . number_format($remaining, 2)],
];
foreach ($costRows as $i => $row) {
    $isTotalRow = ($row[0] === 'Total Cost');
    $bg = $isTotalRow ? $brandBlue : ($i % 2 === 0 ? $white : $brandLight);
    $textColor = $isTotalRow ? $white : $darkText;
    $costTable->addRow($isTotalRow ? 450 : 300);
    $costTable->addCell(6500, ['bgColor' => $bg])->addText($row[0], ['bold' => $isTotalRow, 'size' => 11, 'color' => $textColor]);
    $costTable->addCell(2500, ['bgColor' => $bg])->addText($row[1], ['bold' => true, 'size' => 11, 'color' => $isTotalRow ? $white : $green], ['alignment' => Jc::RIGHT]);
}
$section->addTextBreak(2);

// ── FOOTER ───────────────────────────────────────────────────────────────
$footerTable = $section->addTable(['borderSize' => 0, 'width' => 9000, 'unit' => 'dxa', 'cellMargin' => 100]);
$footerTable->addRow(600);
$footerCell = $footerTable->addCell(9000, ['bgColor' => '1A1A2E']);
$footerCell->addText('AsanTravels  |  +94 76 208 7708  |  asantravels@gmail.com  |  Negombo, Sri Lanka', ['size' => 9, 'color' => 'AAAACC'], ['alignment' => Jc::CENTER]);

// ── SAVE TO DISK & SERVE AS BINARY DOWNLOAD ──────────────────────────────
$bookingRef = str_pad($booking_id, 5, '0', STR_PAD_LEFT);
$safeName   = preg_replace('/[^a-zA-Z0-9]/', '_', $booking['name'] ?? 'Guest');
$filename   = "AsanTravels_Booking_{$bookingRef}_{$safeName}.docx";

// Create temp directory
$publicTemp = __DIR__ . '/temp_docs';
if (!file_exists($publicTemp)) {
    mkdir($publicTemp, 0777, true);
}

// Cleanup old files (older than 1 hour)
foreach (glob($publicTemp . "/*.docx") as $oldFile) {
    if (time() - filemtime($oldFile) > 3600) {
        @unlink($oldFile);
    }
}

$destPath = $publicTemp . '/' . $filename;

try {
    $writer = IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save($destPath);
} catch (Throwable $e) {
    while (ob_get_level()) ob_end_clean();
    die("Error generating document: " . $e->getMessage());
}

if (!file_exists($destPath) || filesize($destPath) < 100) {
    while (ob_get_level()) ob_end_clean();
    die("Error: Document generation failed - file is empty or missing.");
}

$fileSize = filesize($destPath);

// Kill ALL output buffers - this is critical
while (ob_get_level()) {
    ob_end_clean();
}

// Disable any Apache compression that could corrupt binary
if (function_exists('apache_setenv')) {
    apache_setenv('no-gzip', '1');
}
@ini_set('zlib.output_compression', 'Off');

// Send headers for binary file download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . $fileSize);
header('Accept-Ranges: bytes');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Read file in binary mode and output
$fp = fopen($destPath, 'rb');
fpassthru($fp);
fclose($fp);
exit;

