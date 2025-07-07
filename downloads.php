<?php
// SEITEN-SPEZIFISCHE VARIABLEN
$page_title = "Kostenlose Techno Samples, Loops & MP3s | 4AM Techno";
$page_description = "Starte deine erste Techno-Produktion! Lade dir kostenlose Samples, Loops, Projekt-Dateien und Tutorials für Anfänger herunter. Alles für deinen ersten Track.";
$active_nav = "downloads";

// HEADER EINBINDEN
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/main-header.php";
?>

<main id="main-content">
    <section class="banner-section">
        <img src="/images/4AM-Techno-Banner.webp" alt="4AM Techno Banner" class="full-width-banner">
        <div class="container" style="text-align: center; padding-top: 2rem;">
            <h1 class="glitch section-title" data-text="DOWNLOADS">DOWNLOADS</h1>
            <h2 style="font-size: 1.5em; text-shadow: 2px 2px 4px #000;">Ressourcen für deine Produktionen</h2>
        </div>
    </section>

    <div class="container">
        <section id="video-section">
            <div class="content-heading-block">
                <h2>Video Erstellung</h2>
                <p>Professionelle Ressourcen für Videoproduktion: LUTs für Color Grading, Motion Graphics, Overlays und Templates für beeindruckende Videos.</p>
            </div>
            <div class="category-grid">
                <div class="category-card"><h3>LUTs (Color Grading)</h3><p>Cinematic Color Grading LUTs für professionelle Videobearbeitung. Von Vintage bis Modern, von Warm bis Cool.</p></div>
                <div class="category-card"><h3>Motion Graphics</h3><p>Animierte Logos, Übergänge und VJ Loops für deine Videos. Ready-to-use Grafiken im 4AM TECHNO Style.</p></div>
                <div class="category-card"><h3>Video Overlays</h3><p>Partikel-Effekte, Light Leaks und Glitch Effects für atmosphärische Videos mit Underground-Feeling.</p></div>
            </div>
            <a href="video-downloads.php" class="download-link-bar">Zu den Video Downloads</a>
        </section>
        
        <section id="music-section" style="margin-top: 5rem;">
             <div class="content-heading-block">
                <h2>Musik Erstellung</h2>
                <p>Hochwertige Samples, Loops und Sounds für Techno und House Produktionen. Plus Tutorials für die beliebtesten DAWs.</p>
            </div>
            <div class="category-grid">
                <div class="category-card"><h3>Samples & Loops</h3><p>Exklusive Techno Drums, Synth Loops, Basslines und FX Sounds für deine Produktionen. Alle Samples sind lizenzfrei und ready-to-use.</p></div>
                <div class="category-card"><h3>DAW Templates</h3><p>Fertige Projekt-Templates für Ableton Live, FL Studio und Logic Pro. Lerne von Profis und starte direkt mit deinen Tracks.</p></div>
                <div class="category-card"><h3>Sound Design Presets</h3><p>Presets für beliebte Synthesizer wie Serum, Sylenth1 und Massive. Erstelle einzigartige Sounds für deine Tracks.</p></div>
            </div>
            <a href="music-downloads.php" class="download-link-bar">Zu den Musik Downloads</a>
        </section>
    </div>
</main>

<?php
// FOOTER EINBINDEN
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/main-footer.php";
?>