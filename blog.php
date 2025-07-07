<?php
// Verbesserte blog.php - Blog/News Landing Page und API

// #################################################################
// ## TEIL 1: PHP-LOGIK & API
// #################################################################

// Blog-Artikel aus /blog/ Verzeichnis
    $blogArticles = [
        [
            'id' => 1,
            'title' => 'Entdecke die Minimal Techno Elite',
            'excerpt' => 'Tauche ein in die Welt der Minimal Techno Elite und entdecke die Künstler, die diese faszinierende Musikrichtung prägen.',
            'category' => 'Musik',
            'date' => '2025-01-19',
            'image' => 'images/news/minimal-techno-elite.webp',
            'slug' => 'entdecke-die-minimal-techno-elite',
            'url' => '/blog/entdecke-die-minimal-techno-elite.php'
        ],
        [
            'id' => 2,
            'title' => 'Decade Underground Bad Frankenhausen',
            'excerpt' => 'Ein Blick auf die Underground-Szene in Bad Frankenhausen und die Events, die diese Stadt zu einem Techno-Hotspot machen.',
            'category' => 'Events',
            'date' => '2024-12-22',
            'image' => 'images/news/decade-underground.webp',
            'slug' => 'decade-underground-bad-frankenhausen',
            'url' => '/blog/decade-underground-bad-frankenhausen.php'
        ],
        [
            'id' => 3,
            'title' => 'Boris Brejcha - High Tech Minimal Meister',
            'excerpt' => 'Portrait des deutschen Produzenten Boris Brejcha und seinem einzigartigen High Tech Minimal Sound.',
            'category' => 'Artist Portrait',
            'date' => '2025-01-09',
            'image' => 'images/news/boris-brejcha.webp',
            'slug' => 'boris-brejcha-high-tech-minimal-meister',
            'url' => '/blog/boris-brejcha-high-tech-minimal-meister.php'
        ],
        [
            'id' => 4,
            'title' => 'Lilly Palmer - Die Faszination einer Techno-Ikone',
            'excerpt' => 'Erfahre mehr über Lilly Palmer und ihren Weg zur internationalen Techno-Ikone.',
            'category' => 'Artist Portrait',
            'date' => '2024-12-28',
            'image' => 'images/news/lilly-palmer.webp',
            'slug' => 'lilly-palmer-die-faszination-einer',
            'url' => '/blog/lilly-palmer-aufstieg-techno-ikone.php'
        ],
        [
            'id' => 5,
            'title' => 'Sara Landry - Hard Techno Portrait 2025',
            'excerpt' => 'Ein aktuelles Portrait der Hard Techno Künstlerin Sara Landry und ihrer neuesten Projekte.',
            'category' => 'Artist Portrait',
            'date' => '2024-10-11',
            'image' => 'images/news/sara-landry.webp',
            'slug' => 'sara-landry-hard-techno-portrait-2025',
            'url' => '/blog/sara-landry-hard-techno-ikone.php'
        ],
        [
            'id' => 6,
            'title' => 'Marco Carola - Der König des Minimal Techno',
            'excerpt' => 'Portrait des italienischen Techno-Veteranen Marco Carola und seinem Einfluss auf die Minimal Techno Szene.',
            'category' => 'Artist Portrait',
            'date' => '2024-10-08',
            'image' => 'images/news/marco-carola.webp',
            'slug' => 'marco-carola-der-konig-des-minimal',
            'url' => '/blog/marco-carola-der-konig-des-minimal.php'
        ],    
        [    'id' => 7,
            'title' => 'Tech Techn Techno volle pulle oder?',
            'excerpt' => 'Ein kleiner Streifzug für alle, die neugierig sind und vielleicht ihre ersten Schritte in die faszinierende Welt des Techno wagen wollen.',
            'category' => 'Techno-Kultur',
            'date' => '2024-12-03',
            'image' => 'images/news/techno-turntable-headphones.webp',
            'slug' => 'willkommen-in-der-pulsierenden-welt-des-techno',
            'url' => '/blog/willkommen-in-der-pulsierenden-welt-des-techno.php'
        ],
        [
            'id' => 8,
            'title' => 'Minimal Techno in Japan',
            'excerpt' => 'Japan beherbergt eine einzigartige und hochentwickelte Minimal Techno Szene, die Liebhaber elektronischer Musik aus aller Welt anzieht.',
            'category' => 'Techno-Kultur',
            'date' => '2024-12-03',
            'image' => 'images/news/tokyo-techno-club.webp',
            'slug' => 'minimal-techno-japan-szene',
            'url' => '/blog/minimal-techno-japan-szene.php'
        ],
        [
            'id' => 9,
            'title' => 'Minimal Techno in Berlin: Clubs & Sound',
            'excerpt' => 'Ein Guide zu den legendären Clubs, dem typischen Sound und der einzigartigen Kultur, die Berlin zum globalen Epizentrum für Minimal Techno machen.',
            'category' => 'Clubs',
            'date' => '2024-12-03',
            'image' => 'images/news/berlin-techno-club.webp',
            'slug' => 'minimal-techno-berlin-clubs-sound',
            'url' => '/blog/minimal-techno-berlin-clubs-sound.php'
        ],
        [
            'id' => 10,
            'title' => 'Djane Techno-Szene und die Techno - Frauen',
            'excerpt' => 'Ein Blick auf die wichtige Rolle, die Frauen von Anfang an als DJs, Produzentinnen und Labelbetreiberinnen in der Techno-Szene gespielt haben und heute spielen.',
            'category' => 'Techno-Kultur',
            'date' => '2024-12-03',
            'image' => 'images/news/djane-turntables.webp',
            'slug' => 'frauen-in-der-techno-szene-pionierinnen',
            'url' => '/blog/frauen-in-der-techno-szene-pionierinnen.php'
        ],
        [
            'id' => 11,
            'title' => 'Clara Cuvé: Von den Klaviertasten zu den Turntables',
            'excerpt' => 'Die aus München stammende DJ hat einen bemerkenswerten Weg hinter sich – von der klassischen Klavierausbildung bis zu den Plattentellern der wichtigsten Clubs.',
            'category' => 'Artist Portrait',
            'date' => '2024-12-03',
            'image' => 'images/news/clara-cuve-dj.webp',
            'slug' => 'clara-cuve-klavier-techno-turntables',
            'url' => '/blog/clara-cuve-klavier-techno-turntables.php'
        ],
        [
            'id' => 12,
            'title' => 'Charlotte de Witte & Amelie Lens',
            'excerpt' => 'Zwei Namen, die sinnbildlich für den Wandel und die neue Generation weiblicher Power an den Decks stehen: Ein Blick auf die beiden belgischen Techno-Titaninnen.',
            'category' => 'Artist Portrait',
            'date' => '2024-12-03',
            'image' => 'images/news/charlotte-de-witte.webp',
            'slug' => 'charlotte-de-witte-und-amelie-lens',
            'url' => '/blog/charlotte-de-witte-und-amelie-lens.php'
		],
        [
			'id' => 13,
			'title' => 'Seega Summer Beats: Elektronische Beats im Herzen des Kyffhäuserlandes',
			'excerpt' => 'Die Seega Summer Beats, ein einzigartiges elektronisches Musikevent im Kyffhäuserland, was Tradition und moderne Beats vereint.',
			'category' => 'Events',
			'date' => '2025-06-23',
			'image' => 'images/blog/seega-summer-beats-kyffhaeuserland.webp',
			'url' => '/blog/seega-summer-beats-kyffhaeuserland.php'
        ],
        [
            'id' => 14,
            'title' => 'Bad Frankenhausen tanzt! – Dein Sommer Open Air 2025 Highlight in Thüringen"',
            'excerpt' => 'Entdecke das einzigartige Electronic Music Festival Bad Frankenhausen tanzt! am 27. Juni 2025 - mit Westbam, Tief & Ton und Justin Prince im historischen Ambiente des Schlossplatzes.',
            'category' => 'Events',
            'date' => '2025-06-25',
            'image' => '/images/blog/bad-frankenhausen-tanzt.webp',
            'slug' => 'Bad-f-Tanzt-open-air-juni-2025',
            'url' => '/blog/bad-frankenhausen-tanzt-sommer-open-air-2025'
		]
         
    ];
// Nach Datum sortieren (neueste zuerst)
usort($blogArticles, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

// API-Modus: JSON-Daten zur ckgeben, wenn 'limit' oder 'category' als Parameter gesetzt ist
if (isset($_GET['limit']) || isset($_GET['category'])) {
    header('Content-Type: application/json');

    $filteredArticles = $blogArticles;

    // Nach Kategorie filtern, falls angefragt
    if (isset($_GET['category']) && $_GET['category'] !== 'all') {
        $filteredArticles = array_filter($blogArticles, function($article) {
            return strtolower(str_replace(' ', '-', $article['category'])) === $_GET['category'];
        });
    }
    
    // Nach Limit filtern, falls angefragt
    if (isset($_GET['limit'])) {
        $limit = intval($_GET['limit']);
        $filteredArticles = array_slice($filteredArticles, 0, $limit);
    }
    
    echo json_encode(array_values($filteredArticles));
    exit; // Wichtig: Skript hier beenden, wenn es eine API-Anfrage war
}


// #################################################################
// ## TEIL 2: HTML-SEITE AUFBAUEN (Landing Page Modus)
// #################################################################

// Erst JETZT, nachdem die API-Logik durch ist, definieren wir die Seitendaten und binden den Header ein.
$page_title = "Blog & News - 4AM TECHNO";
$page_description = "Aktuelle News, Artikel und Einblicke in die Welt von 4AM TECHNO und der elektronischen Musikszene.";
$active_nav = "blog";

include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/main-header.php";
?>
<h1>Unser Techno Blog: News & Artikel</h1>
<section class="banner-section">
    <img src="/images/4AM-Techno-Banner.webp" alt="4AM Techno Banner" class="full-width-banner">
    <div class="container" style="text-align: center; padding-top: 2rem;">
        <p style="font-size: 1.5em; text-shadow: 2px 2px 4px #000;">Updates aus dem World Wide Web und der Underground Techno Szene</p>
        <div style="max-width: 800px; margin: 2rem auto; text-align: left; background: rgba(26, 26, 26, 0.8); padding: 2rem; border-radius: 15px;">
            <p>Willkommen im 4AM TECHNO Blog - deiner Quelle für die neuesten Nachrichten, Artikel und Einblicke in die Welt der elektronischen Musik. Hier findest du exklusive Artist Portraits, Event-Berichte, Techno-Kultur-Analysen und vieles mehr.</p>
            <p>Unsere Redaktion berichtet über die wichtigsten Entwicklungen in der Underground-Szene, stellt aufstrebende Künstler vor und gibt dir Insider-Tipps für die besten Events in Deutschland und Europa. Von Minimal Techno bis Hard Techno - wir decken das gesamte Spektrum der elektronischen Musik ab.</p>
            <p>Bleib auf dem Laufenden über die neuesten Releases, Club-Openings, Festival-Ankündigungen und Techno-Trends. Unser Blog ist deine tägliche Dosis Underground-Kultur.</p>
        </div>
    </div>
</section>

<section class="news-filters">
    <div class="container">
        <div class="filter-buttons">
            <button class="filter-btn active" data-category="all">Alle Artikel</button>
            <button class="filter-btn" data-category="artist-portrait">Artist Portraits</button>
            <button class="filter-btn" data-category="events">Events</button>
            <button class="filter-btn" data-category="techno-kultur">Techno-Kultur</button>
            <button class="filter-btn" data-category="musik">Musik</button>
            <button class="filter-btn" data-category="clubs">Clubs</button>
        </div>
    </div>
</section>

<section class="news-content">
    <div class="container">
        <div class="news-grid-compact">
            </div>
        <div class="load-more-section">
            <button class="load-more-btn" id="loadMoreBtn" style="display: none;">
                <i class="fas fa-plus"></i>
                Weitere Artikel laden
            </button>
        </div>
    </div>
</section>

<?php
// Und zum Schluss den Footer einbinden
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/main-footer.php";
?>