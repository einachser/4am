<?php
// =================================================================
// TEIL 1: SEITENVARIABLEN & LOGIK
// =================================================================

// SEITEN-SPEZIFISCHE VARIABLEN
$page_title = "Party Dates & Events - 4AM TECHNO";
$page_description = "Finde die nächsten Underground Techno Events und Partys. Lade deinen eigenen Flyer hoch und promote deine Veranstaltung.";
$canonical_url = "https://www.4amtechno.com/partydates.php";
$active_nav = "partydates";

// DAS "GEHIRN" DER SEITE
$approvedDir = 'partydates/images/flyers/';
$flyers = [];
if (file_exists($approvedDir) && is_dir($approvedDir)) {
    $imageFiles = glob($approvedDir . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
    foreach ($imageFiles as $imageFile) {
        $infoFilePath = $approvedDir . pathinfo($imageFile, PATHINFO_FILENAME) . '.txt';
        $flyerData = ['image' => '/' . $imageFile, 'date' => 'N/A', 'name' => 'N/A', 'location' => 'N/A', 'link' => ''];
        if (file_exists($infoFilePath)) {
            $infoContent = file_get_contents($infoFilePath);
            preg_match('/Party Name: (.+)/', $infoContent, $nameMatches);
            preg_match('/Datum: (.+)/', $infoContent, $dateMatches);
            preg_match('/Location: (.+)/', $infoContent, $locationMatches);
            preg_match('/Event Link: (.*)/', $infoContent, $linkMatches);
            $flyerData['name'] = trim($nameMatches[1] ?? 'Event');
            $flyerData['date'] = trim($dateMatches[1] ?? 'N/A');
            $flyerData['location'] = trim($locationMatches[1] ?? 'N/A');
            $flyerData['link'] = trim($linkMatches[1] ?? '');
        }
        $flyers[] = $flyerData;
    }
    
    usort($flyers, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
}

// API-TEIL FÜR DIE STARTSEITE
if (isset($_GET['limit'])) {
    $future_events = array_filter($flyers, function($flyer) {
        return strtotime($flyer['date']) >= strtotime('today');
    });
    usort($future_events, function($a, $b) {
        return strtotime($a['date']) - strtotime($b['date']);
    });
    $limit = intval($_GET['limit']);
    $upcoming_events = array_slice($future_events, 0, $limit);
    header('Content-Type: application/json');
    echo json_encode($upcoming_events);
    exit;
}

// =================================================================
// TEIL 2: SEITENAUFBAU (HEADER)
// =================================================================
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/main-header.php";
?>

<section class="banner-section">
    <img src="/images/4AM-Techno-Banner.webp" alt="4AM Techno Banner" class="full-width-banner">
    <div class="container" style="text-align: center; padding-top: 2rem;">
        <h1 class="section-title">PARTY DATES</h1>
        <h2 class="glitch" data-text="Der Kalender für Underground Techno">Der Kalender für Underground Techno</h2>
        <div style="max-width: 800px; margin: 2rem auto; text-align: left; background: rgba(26, 26, 26, 0.8); padding: 2rem; border-radius: 15px;">
            <p>Entdecke die heißesten Underground Techno Events in Deutschland und Europa. Unser Party-Kalender bietet dir eine kuratierte Auswahl der besten Techno-Veranstaltungen, von intimen Club-Nächten bis hin zu großen Festival-Events.</p>
            <p>Ob Minimal Techno in Berlin, Hard Techno in Frankfurt oder House Music in Hamburg - hier findest du alle wichtigen Termine der elektronischen Musikszene. Wir listen sowohl etablierte Events als auch Geheimtipps für echte Underground-Liebhaber.</p>
            <p>Du organisierst selbst ein Event? Nutze unseren kostenlosen Flyer-Upload-Service und erreiche die 4AM TECHNO Community. Gemeinsam machen wir die Nacht zum Tag!</p>
        </div>
    </div>
</section>

<main class="container" id="main-content" style="padding-top: 2rem;">

    <section class="flyer-gallery">
        <?php if (empty($flyers)): ?>
            <div style="background: rgba(26, 26, 26, 0.8); border: 1px solid rgba(204, 204, 204, 0.2); backdrop-filter: blur(10px); border-radius: 15px; padding: 2rem; text-align:center; grid-column: 1 / -1;">
                <h3>Keine Events gefunden</h3>
                <p>Aktuell sind keine Events vorhanden. Schau bald wieder vorbei!</p>
            </div>
        <?php else: ?>
            <?php foreach ($flyers as $flyer): ?>
                <div class="flyer-card">
                    <img src="<?php echo htmlspecialchars($flyer['image']); ?>" alt="Flyer für <?php echo htmlspecialchars($flyer['name']); ?>" loading="lazy">
                    <div class="flyer-info">
                        <div class="flyer-info-content">
                            <h3><?php echo htmlspecialchars($flyer['name']); ?></h3>
                            <p><strong><i class="fas fa-calendar-alt" style="margin-right: 8px;"></i>Datum:</strong> <?php echo htmlspecialchars(date("d.m.Y", strtotime($flyer['date']))); ?></p>
                            <p><strong><i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i>Ort:</strong> <?php echo htmlspecialchars($flyer['location']); ?></p>
                        </div>
                        <?php if (!empty($flyer['link'])): ?>
                            <a href="<?php echo htmlspecialchars($flyer['link']); ?>" target="_blank" rel="nofollow ugc" class="btn">Zum Event <i class="fas fa-arrow-right"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <section class="upload-section">
        <h2 class="glitch" data-text="Promote dein Party">Promote deine Party</h2>
        <p style="margin-bottom: 1.5rem; max-width: 500px; margin-left: auto; margin-right: auto;">Du willst deine eigene Party promoten? Lade hier deinen Flyer hoch und erreiche die Community.</p>
        <a href="/upload.php" class="btn">Flyer jetzt hochladen <i class="fas fa-upload"></i></a>
    </section>
</main>

<?php
// =================================================================
// TEIL 4: SEITENABSCHLUSS (FOOTER)
// =================================================================
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/main-footer.php";
?>