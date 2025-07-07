<?php
// --- Seiten-spezifische Variablen für das Template ---
$page_title = "Upload Erfolgreich - 4AM TECHNO";
$page_description = "Dein Event-Flyer wurde erfolgreich hochgeladen.";
$canonical_url = "https://www.4amtechno.com/upload_success.php";
$active_nav = "partydates"; 

// Header einbinden
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/main-header.php";
?>

<section class="success-page-content" style="padding: 4rem 2rem; text-align: center;">
    <div class="container">
        <div class="success-box" style="max-width: 700px; margin: auto;">
            <div class="icon" style="font-size: 4rem; color: var(--color-primary-accent); margin-bottom: 1rem;"><i class="fas fa-check-circle"></i></div>
            <h1>Vielen Dank!</h1>
            <p>Dein Flyer wurde erfolgreich hochgeladen und wird nach einer kurzen Überprüfung veröffentlicht.</p>
            <a href="partydates.php" class="btn" style="display: inline-block; max-width: 300px; margin-top: 2rem;">Zurück zu den Party Dates</a>
        </div>
    </div>
</section>

<?php
// Footer einbinden
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/main-footer.php";
?>