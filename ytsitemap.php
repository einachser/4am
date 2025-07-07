<<?php
/**
 * ytsitemap.php
 * Logik muss VOR dem Header-Include passieren.
 */

// 1. Konfiguration und API-Klasse laden
require_once $_SERVER["DOCUMENT_ROOT"] . "/config/youtube-config.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/youtube-api.php";

// 2. API initialisieren und ALLE Videos aus dem Cache oder von der API holen
$youtube = new YouTubeAPI(YOUTUBE_API_KEY, YOUTUBE_CHANNEL_ID);

$masterCacheKey = "all_youtube_videos";
$allVideos = YouTubeCache::get($masterCacheKey);
if ($allVideos === null) {
    $allVideos = $youtube->getChannelVideos(YOUTUBE_MAX_VIDEOS);
    if ($allVideos) {
        YouTubeCache::set($masterCacheKey, $allVideos);
    } else {
        $allVideos = [];
    }
}

// 3. Wichtige Variablen für die Seite und den Header berechnen
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$videosPerPage = YOUTUBE_VIDEOS_PER_PAGE;
$totalVideos = count($allVideos);
$totalPages = ceil($totalVideos / $videosPerPage);

// 4. Seitenvariablen für den Header definieren
$page_suffix = ($page > 1) ? " | Seite {$page}" : "";
$page_title = "YouTube Video Sitemap{$page_suffix} - 4AM Techno";
$page_description = "Entdecke alle Videos vom 4AM Techno YouTube-Kanal. Seite {$page} von {$totalPages}.";
$page_keywords = "YouTube Videos, 4AM Techno, E1nachser, Minimal Techno, House Music, DJ Sets";
$active_nav = "videos";
$og_image = "https://4amtechno.com/images/og-image-youtube.jpg";

// Canonical URL für die aktuelle Seite festlegen
$canonical_url = 'https://www.4amtechno.com/ytsitemap.php';
if ($page > 1) {
    $canonical_url .= '?page=' . $page;
}

// Prev/Next Links für den Header definieren (wichtig für SEO bei Paginierung)
$prev_url = null;
$next_url = null;
if ($page > 1) {
    $prev_url = 'https://www.4amtechno.com/ytsitemap.php?page=' . ($page - 1);
}
if ($page < $totalPages) {
    $next_url = 'https://www.4amtechno.com/ytsitemap.php?page=' . ($page + 1);
}

// 5. JETZT ERST den Header einbinden, der all diese Variablen verwendet
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/main-header.php";


// 6. Die Videos für die aktuelle Seite aus der Gesamtliste "herausschneiden"
$offset = ($page - 1) * $videosPerPage;
$videos = array_slice($allVideos, $offset, $videosPerPage);

// 7. Kanal-Infos für die Anzeige holen (kann nach dem Header passieren)
$channelInfo = YouTubeCache::get("channel_info");
if ($channelInfo === null) {
    $channelInfo = $youtube->getChannelInfo();
    if ($channelInfo) {
        YouTubeCache::set("channel_info", $channelInfo);
    }
}
?>
<main id="main-content" role="main">
    <!-- Hero Section -->
    <section class="hero-enhanced youtube-hero" aria-label="YouTube Video Sitemap">
        <div class="hero-overlay">
            <div class="hero-content">
                <h1 class="hero-title">YouTube Video Sitemap <span class="page-number">- Seite <?php echo $page; ?></span></h1>
                <h2 class="hero-subtitle">Alle Videos von 4AM Techno</h2>
                <p class="hero-description">
                    Entdecke alle <?php echo number_format($totalVideos); ?> Videos aus unserem YouTube-Kanal. 
                    Von Underground Techno bis Minimal House - hier findest du alles.
                </p>
                
                <?php if ($channelInfo): ?>
                <div class="channel-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo YouTubeAPI::formatNumber($channelInfo['subscriberCount']); ?></span>
                        <span class="stat-label">Abonnenten</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo YouTubeAPI::formatNumber($channelInfo['videoCount']); ?></span>
                        <span class="stat-label">Videos</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo YouTubeAPI::formatNumber($channelInfo['viewCount']); ?></span>
                        <span class="stat-label">Aufrufe</span>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="hero-cta">
                    <a href="https://www.youtube.com/@4AMTechno" target="_blank" rel="noopener noreferrer" class="btn-primary">
                        <i class="fab fa-youtube" aria-hidden="true"></i>
                        YouTube Kanal besuchen
                    </a>
                    <a href="#video-grid" class="btn-secondary">
                        <i class="fas fa-video" aria-hidden="true"></i>
                        Videos durchsuchen
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Grid Section -->
    <section id="video-grid" class="videos-section" aria-label="Video-Übersicht">
        <div class="container">
            <header class="section-header">
                <h2 class="section-title">Alle Videos</h2>
                <p class="section-subtitle">
                    Seite <?php echo $page; ?> von <?php echo $totalPages; ?> 
                    (<?php echo number_format($totalVideos); ?> Videos insgesamt)
                </p>
            </header>

            <?php if (!empty($videos)): ?>
            <div class="video-grid-container">
                <?php foreach ($videos as $video): ?>
                <article class="video-card" itemscope itemtype="https://schema.org/VideoObject">
                    <div class="video-thumbnail">
                        <a href="<?php echo htmlspecialchars($video['url']); ?>" target="_blank" rel="noopener noreferrer">
                            <img src="<?php echo htmlspecialchars($video['thumbnail']); ?>" 
                                 alt="<?php echo htmlspecialchars($video['title']); ?>"
                                 loading="lazy"
                                 itemprop="thumbnailUrl">
                            <div class="video-overlay">
                                <i class="fas fa-play" aria-hidden="true"></i>
                            </div>
                        </a>
                    </div>
                    
                    <div class="video-info">
                        <h3 class="video-title" itemprop="name">
                            <a href="<?php echo htmlspecialchars($video['url']); ?>" target="_blank" rel="noopener noreferrer">
                                <?php echo htmlspecialchars($video['title']); ?>
                            </a>
                        </h3>
                        
                        <p class="video-description" itemprop="description">
                            <?php 
                            $description = $video['description'];
                            echo htmlspecialchars(strlen($description) > 150 ? substr($description, 0, 150) . '...' : $description); 
                            ?>
                        </p>
                        
                        <div class="video-meta">
                            <time class="video-date" datetime="<?php echo $video['publishedAt']; ?>" itemprop="uploadDate">
                                <?php echo date('d.m.Y', strtotime($video['publishedAt'])); ?>
                            </time>
                            <a href="<?php echo htmlspecialchars($video['url']); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="video-link"
                               itemprop="url">
                                <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                                Auf YouTube ansehen
                            </a>
                        </div>
                    </div>
                    
                    <!-- Schema.org Markup -->
                    <meta itemprop="embedUrl" content="https://www.youtube.com/embed/<?php echo htmlspecialchars($video['id']); ?>">
                    <span itemprop="author" itemscope itemtype="https://schema.org/Person" style="display: none;">
                        <span itemprop="name">E1nachser</span>
                    </span>
                </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav class="pagination-nav" aria-label="Video-Seiten Navigation">
                <ul class="pagination">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a href="?page=<?php echo $page - 1; ?>" class="page-link" aria-label="Vorherige Seite">
                            <i class="fas fa-chevron-left" aria-hidden="true"></i>
                            Zurück
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php
                    // Zeige max. 5 Seitenzahlen
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    
                    if ($startPage > 1): ?>
                    <li class="page-item">
                        <a href="?page=1" class="page-link">1</a>
                    </li>
                    <?php if ($startPage > 2): ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a href="?page=<?php echo $i; ?>" class="page-link" <?php echo $i == $page ? 'aria-current="page"' : ''; ?>>
                            <?php echo $i; ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a href="?page=<?php echo $totalPages; ?>" class="page-link"><?php echo $totalPages; ?></a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a href="?page=<?php echo $page + 1; ?>" class="page-link" aria-label="Nächste Seite">
                            Weiter
                            <i class="fas fa-chevron-right" aria-hidden="true"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>

            <?php else: ?>
            <div class="no-videos">
                <div class="no-videos-content">
                    <i class="fas fa-video-slash" aria-hidden="true"></i>
                    <h3>Keine Videos gefunden</h3>
                    <p>Momentan können keine Videos geladen werden. Bitte versuche es später erneut.</p>
                    <a href="https://www.youtube.com/@4AMTechno" target="_blank" rel="noopener noreferrer" class="btn-outline">
                        Direkt zu YouTube
                        <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section" aria-label="Weitere Aktionen">
        <div class="container">
            <div class="cta-content">
                <h2>Verpasse keine neuen Videos!</h2>
                <p>Abonniere unseren YouTube-Kanal und aktiviere die Glocke für Benachrichtigungen.</p>
                <div class="cta-buttons">
                    <a href="https://www.youtube.com/@4AMTechno?sub_confirmation=1" target="_blank" rel="noopener noreferrer" class="btn-primary">
                        <i class="fab fa-youtube" aria-hidden="true"></i>
                        Jetzt abonnieren
                    </a>
                    <a href="/index.php#videos" class="btn-secondary">
                        <i class="fas fa-home" aria-hidden="true"></i>
                        Zurück zur Startseite
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
/* YouTube Sitemap spezifische Styles */
.youtube-hero {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d1810 50%, #1a1a1a 100%);
    min-height: 60vh;
}

.channel-stats {
    display: flex;
    gap: 2rem;
    margin: 2rem 0;
    justify-content: center;
    flex-wrap: wrap;
}

.channel-stats .stat-item {
    text-align: center;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    backdrop-filter: blur(10px);
    min-width: 120px;
}

.channel-stats .stat-number {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    color: #C05427;
    font-family: 'Orbitron', monospace;
}

.channel-stats .stat-label {
    display: block;
    font-size: 0.9rem;
    color: #ccc;
    margin-top: 0.5rem;
}

.video-grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
    margin: 3rem 0;
}

.video-card {
    background: #1a1a1a;
    border-radius: 15px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid rgba(192, 84, 39, 0.2);
}

.video-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(192, 84, 39, 0.3);
}

.video-thumbnail {
    position: relative;
    aspect-ratio: 16/9;
    overflow: hidden;
}

.video-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.video-card:hover .video-thumbnail img {
    transform: scale(1.05);
}

.video-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(192, 84, 39, 0.9);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.video-card:hover .video-overlay {
    opacity: 1;
}

.video-overlay i {
    color: white;
    font-size: 1.5rem;
    margin-left: 3px;
}

.video-info {
    padding: 1.5rem;
}

.video-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.8rem;
    line-height: 1.4;
}

.video-title a {
    color: #fff;
    text-decoration: none;
    transition: color 0.3s ease;
}

.video-title a:hover {
    color: #C05427;
}

.video-description {
    color: #ccc;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.video-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.85rem;
    color: #999;
}

.video-date {
    color: #C05427;
    font-weight: 500;
}

.video-link {
    color: #999;
    text-decoration: none;
    transition: color 0.3s ease;
}

.video-link:hover {
    color: #C05427;
}

.pagination-nav {
    margin: 3rem 0;
    display: flex;
    justify-content: center;
}

.pagination {
    display: flex;
    list-style: none;
    gap: 0.5rem;
    margin: 0;
    padding: 0;
}

.page-item {
    margin: 0;
}

.page-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: #1a1a1a;
    color: #ccc;
    text-decoration: none;
    border-radius: 8px;
    border: 1px solid rgba(192, 84, 39, 0.2);
    transition: all 0.3s ease;
}

.page-link:hover {
    background: #C05427;
    color: white;
    border-color: #C05427;
}

.page-item.active .page-link {
    background: #C05427;
    color: white;
    border-color: #C05427;
}

.page-item.disabled .page-link {
    opacity: 0.5;
    cursor: not-allowed;
}

.hero-title .page-number {
    font-size: 1.5rem;
    opacity: 0.7;
}

.no-videos {
    text-align: center;
    padding: 4rem 2rem;
}

.no-videos-content i {
    font-size: 4rem;
    color: #C05427;
    margin-bottom: 1rem;
}

.no-videos-content h3 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: #fff;
}

.no-videos-content p {
    color: #ccc;
    margin-bottom: 2rem;
}

.cta-section {
    background: linear-gradient(135deg, #2d1810 0%, #1a1a1a 100%);
    padding: 4rem 0;
    text-align: center;
}

.cta-content h2 {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: #fff;
}

.cta-content p {
    color: #ccc;
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

/* Responsive Design */
@media (max-width: 768px) {
    .video-grid-container {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .channel-stats {
        gap: 1rem;
    }
    
    .channel-stats .stat-item {
        min-width: 100px;
        padding: 0.8rem;
    }
    
    .channel-stats .stat-number {
        font-size: 1.5rem;
    }
    
    .pagination {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .cta-buttons .btn-primary,
    .cta-buttons .btn-secondary {
        width: 100%;
        max-width: 300px;
    }
}
</style>

<?php include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/main-footer.php"; ?>

