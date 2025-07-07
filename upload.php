<?php
// PHP-Logik zur Verarbeitung des Formulars
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $upload_verzeichnis = $_SERVER["DOCUMENT_ROOT"] . "/partydates/uploads/flyers/";
    if (!is_dir($upload_verzeichnis)) {
        mkdir($upload_verzeichnis, 0755, true);
    }
    $max_dateigroesse = 5 * 1024 * 1024; // 5 MB
    $erlaubte_dateitypen = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    $eventName = trim($_POST['event-name'] ?? '');
    $eventDate = trim($_POST['event-date'] ?? '');
    $eventOrt = trim($_POST['event-ort'] ?? '');
    $eventEmail = trim($_POST['event-email'] ?? '');
    $eventLink = trim($_POST['event-link'] ?? '');
    
    if (isset($_FILES['flyer_image']) && $_FILES['flyer_image']['error'] == 0) {
        $file = $_FILES['flyer_image'];
        $dateityp = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($dateityp, $erlaubte_dateitypen)) {
            $message = "Fehler: Nur Bilder der Typen " . implode(', ', $erlaubte_dateitypen) . " sind erlaubt.";
        } elseif ($file['size'] > $max_dateigroesse) {
            $message = "Fehler: Die Datei ist zu groß. Maximal 5 MB erlaubt.";
        } else {
            $base_filename = uniqid('flyer_', true);
            $neuer_bild_dateiname = $base_filename . '.' . $dateityp;
            $neuer_text_dateiname = $base_filename . '.txt';
            
            $ziel_pfad_bild = $upload_verzeichnis . $neuer_bild_dateiname;
            $ziel_pfad_text = $upload_verzeichnis . $neuer_text_dateiname;

            if (move_uploaded_file($file['tmp_name'], $ziel_pfad_bild)) {
                $fileContent = "Party Name: " . $eventName . "\n";
                $fileContent .= "Datum: " . $eventDate . "\n";
                $fileContent .= "Location: " . $eventOrt . "\n";
                $fileContent .= "Event Link: " . $eventLink . "\n";
                $fileContent .= "Kontakt-Email: " . $eventEmail . "\n";
                
                file_put_contents($ziel_pfad_text, $fileContent);

                header("Location: upload_success.php");
                exit();
            } else {
                $message = "Fehler: Die Datei konnte nicht auf den Server geladen werden.";
            }
        }
    } else {
        $message = "Fehler: Es wurde keine Datei hochgeladen oder ein Fehler ist aufgetreten.";
    }
}

// --- Seiten-spezifische Variablen für das Template ---
$page_title = "Flyer Upload - Promote dein Event - 4AM TECHNO";
$page_description = "Lade deinen Party-Flyer hoch und promote deine Veranstaltung auf 4AM TECHNO. Schnell, einfach und direkt zur Community.";
$canonical_url = "https://www.4amtechno.com/upload.php";
$active_nav = "partydates"; 

// Header einbinden
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/main-header.php";
?>

<section class="banner-section">
    <img src="/images/4AM-Techno-Banner.webp" alt="4AM Techno Banner" class="full-width-banner">
    <div class="container" style="text-align: center; padding-top: 2rem;">
        <h1 class="section-title">FLYER UPLOAD</h1>
        <h2 style="font-size: 1.5em; text-shadow: 2px 2px 4px #000;">Promote Deine Party</h2>
    </div>
</section>

<section class="content-wrapper" style="padding: 2rem;">
    <div class="container">
        <section class="form-container" style="max-width: 700px; margin: auto; background-color: rgba(26, 26, 26, 0.8); padding: 2rem; border-radius: 15px;">
             <?php if (!empty($message)): ?>
                <div class="error-message" style="color: #ff6b6b; background-color: rgba(255, 107, 107, 0.1); border: 1px solid #ff6b6b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;"><?php echo htmlspecialchars($message); ?></div>
             <?php endif; ?>
             <form id="flyer-upload-form" action="upload.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="event-name">Party Name *</label>
                    <input type="text" id="event-name" name="event-name" required>
                </div>
                <div class="form-group">
                    <label for="event-date">Datum *</label>
                    <input type="date" id="event-date" name="event-date" required>
                </div>
                <div class="form-group">
                    <label for="event-ort">Ort (Club, Stadt) *</label>
                    <input type="text" id="event-ort" name="event-ort" required>
                </div>
                <div class="form-group">
                    <label for="event-email">Deine E-Mail (für Rückfragen) *</label>
                    <input type="email" id="event-email" name="event-email" required>
                </div>
                <div class="form-group">
                    <label for="event-link">Event Link (Social Media, Ticket-Shop)</label>
                    <input type="url" id="event-link" name="event-link" placeholder="https://...">
                </div>
                <div class="form-group">
                    <label for="flyer_image">Flyer-Bild hochladen * (max. 5MB)</label>
                    <input type="file" id="flyer_image" name="flyer_image" required accept="image/png, image/jpeg, image/gif, image/webp">
                </div>
                <button type="submit" class="btn">Upload & Zur Prüfung Senden</button>
            </form>
        </section>
    </div>
</section>

<?php
// Footer einbinden
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/main-footer.php";
?>