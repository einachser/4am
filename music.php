<?php
// Seiten-spezifische Variablen
$page_title = "4AM TECHNO | Musik Playlists und Musik Sammlung";
$page_description = "Höre die neuesten DJ-Sets, entdecke neue Tracks in meinen kuratierten Playlists und durchstöbere die komplette Musiksammlung von 4AM TECHNO.";
$canonical_url = "https://www.4amtechno.com/music.php";
$active_nav = "music";

// Lade YouTube API und Konfiguration
require_once $_SERVER["DOCUMENT_ROOT"] . "/config/youtube-config.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/includes/youtube-api.php";

// Header einbinden
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/main-header.php";

// YouTube API initialisieren und Playlists abrufen (mit Caching)
$youtube = new YouTubeAPI(YOUTUBE_API_KEY, YOUTUBE_CHANNEL_ID);

$playlistsCacheKey = 'all_channel_playlists';
$playlists = YouTubeCache::get($playlistsCacheKey);

if ($playlists === null) {
    $playlists = $youtube->getChannelPlaylists();
    if($playlists) {
        YouTubeCache::set($playlistsCacheKey, $playlists);
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;900&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    
<style>
    /* 4AM TECHNO Enhanced Website Styles */
    :root {
        /* Primary Colors */
        --color-bg-dark: #121212;
        --color-primary-accent: #C0C0C0;
        --color-silver: #C0C0C0;
        --color-section-bg: #282828;
        --color-text-primary: #B3B3B3;
        --color-text-headings: #FFFFFF;
        --color-hover-accent: #FFFFFF;
        --color-highlight-accent: #C05427;
        
        /* Extended Colors */
        --gradient-primary: linear-gradient(135deg, #121212 0%, #282828 100%);
        --gradient-accent: linear-gradient(135deg, #C0C0C0 0%, #FFFFFF 100%);
        --gradient-neon: linear-gradient(135deg, #C05427 0%, #FF6B47 100%);
        --color-overlay-light: rgba(18, 18, 18, 0.8);
        --color-accent-transparent: rgba(192, 192, 192, 0.1);
        
        /* Typography */
        --font-primary: 'Montserrat', sans-serif;
        --font-accent: 'Orbitron', sans-serif;
        
        /* Spacing */
        --spacing-md: 1rem;
        --spacing-lg: 1.5rem;
        --spacing-xl: 2rem;
        --spacing-2xl: 3rem;
        --spacing-3xl: 4rem;

        /* Transitions */
        --transition-normal: 0.3s ease;
        
        /* Border Radius */
        --radius-lg: 1rem;
        --radius-xl: 1.5rem;
    }

    body {
        background-color: var(--color-bg-dark);
        color: var(--color-text-primary);
        background-image: radial-gradient(circle at 20% 50%, rgba(192, 84, 39, 0.1) 0%, transparent 50%),
                          radial-gradient(circle at 80% 20%, rgba(192, 192, 192, 0.05) 0%, transparent 50%),
                          radial-gradient(circle at 40% 80%, rgba(192, 84, 39, 0.05) 0%, transparent 50%);
    }

    .section-title {
        font-family: var(--font-accent);
        font-size: clamp(2rem, 5vw, 3rem);
        font-weight: 900;
        color: var(--color-text-headings);
        text-align: center;
        margin-bottom: var(--spacing-xl);
        text-transform: uppercase;
        letter-spacing: 3px;
        background: var(--gradient-accent);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 0 30px rgba(192, 192, 192, 0.3);
    }

    /* 3D Button Styles */
    .button-3d {
        position: relative;
        padding: 0;
        width: 200px;
        height: 200px;
        border: 4px solid #888888;
        outline: none;
        background-color: var(--color-bg-dark);
        border-radius: 40px;
        box-shadow: -6px -20px 35px rgba(255, 255, 255, 0.1), -6px -10px 15px rgba(255, 255, 255, 0.05), -20px 0px 30px rgba(255, 255, 255, 0.03), 6px 20px 25px rgba(0, 0, 0, 0.4);
        transition: 0.13s ease-in-out;
        cursor: pointer;
        margin: 0 auto var(--spacing-lg);
        text-decoration: none;
        display: block;
    }
    .button-3d:hover {
        transform: translateY(-3px);
        box-shadow: -8px -25px 40px rgba(255, 255, 255, 0.15), -8px -15px 20px rgba(255, 255, 255, 0.08), -25px 0px 35px rgba(255, 255, 255, 0.05), 8px 25px 30px rgba(0, 0, 0, 0.5);
    }
    .button__content {
        position: relative;
        display: grid;
        padding: 20px;
        width: 100%;
        height: 100%;
        grid-template-columns: 1fr 1fr 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        box-shadow: inset 0px -8px 0px rgba(221, 221, 221, 0.1), 0px -8px 0px var(--color-bg-dark);
        border-radius: 40px;
        transition: 0.13s ease-in-out;
        z-index: 1;
    }
    .button__icon {
        position: relative;
        display: flex;
        transform: translate3d(0px, -4px, 0px);
        grid-column: 4;
        align-self: start;
        justify-self: end;
        width: 32px;
        height: 32px;
        transition: 0.13s ease-in-out;
    }
    .button__icon i {
        font-size: 24px;
        color: var(--color-primary-accent);
    }
    .button__text {
        position: relative;
        transform: translate3d(0px, -4px, 0px);
        margin: 0;
        align-self: end;
        grid-column: 1/5;
        grid-row: 2;
        text-align: center;
        font-size: 28px;
        font-weight: 700;
        background: var(--gradient-accent);
        color: transparent;
        text-shadow: 2px 2px 3px rgba(255, 255, 255, 0.2);
        -webkit-background-clip: text;
        background-clip: text;
        transition: 0.13s ease-in-out;
        font-family: var(--font-accent);
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    .music-collection-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--spacing-lg);
        justify-items: center;
        margin-bottom: var(--spacing-3xl);
    }
    .content-section {
        background: rgba(20, 20, 20, 0.4);
        backdrop-filter: blur(10px);
        border-radius: var(--radius-xl);
        padding: var(--spacing-xl);
        margin-bottom: var(--spacing-2xl);
        border: 1px solid rgba(192, 192, 192, 0.1);
        position: relative;
        overflow: hidden;
    }
    .content-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: var(--gradient-accent);
        opacity: 0.5;
    }

    /* Floating Particles */
    .floating-particles {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: -1;
    }
    .particle {
        position: absolute; background: var(--color-primary-accent); border-radius: 50%; opacity: 0.1; animation: float 15s ease-in-out infinite;
    }
    .particle:nth-child(1) { width: 2px; height: 2px; top: 20%; left: 10%; animation-delay: -0.5s; }
    .particle:nth-child(2) { width: 3px; height: 3px; top: 60%; left: 20%; animation-delay: -3s; }
    .particle:nth-child(3) { width: 1px; height: 1px; top: 40%; left: 70%; animation-delay: -5.5s; }
    .particle:nth-child(4) { width: 2px; height: 2px; top: 80%; left: 80%; animation-delay: -7s; }
    .particle:nth-child(5) { width: 3px; height: 3px; top: 30%; left: 50%; animation-delay: -9.5s; }
    .particle:nth-child(6) { width: 1px; height: 1px; top: 70%; left: 30%; animation-delay: -12s; }

    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0.1; }
        50% { transform: translateY(-25px) rotate(180deg); opacity: 0.2; }
    }

    /* DYNAMISCHE PLAYLIST-KARTE */
    .dynamic-playlist-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: var(--spacing-lg);
        margin-top: var(--spacing-xl);
    }
    .dynamic-playlist-card {
        position: relative;
        border-radius: var(--radius-lg);
        overflow: hidden;
        border: 1px solid rgba(192, 192, 192, 0.1);
        transition: all var(--transition-normal);
        text-decoration: none;
    }
    .dynamic-playlist-card:hover {
        transform: translateY(-5px);
        border-color: rgba(192, 192, 192, 0.3);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
    }
    .playlist-thumbnail img {
        width: 100%; height: auto; aspect-ratio: 16/9; object-fit: cover; transition: transform 0.3s ease;
    }
    .dynamic-playlist-card:hover .playlist-thumbnail img {
        transform: scale(1.05);
    }
    .playlist-info {
        position: absolute; bottom: 0; left: 0; right: 0; padding: 1.5rem 1rem;
        background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0) 100%);
    }
    .playlist-title {
        color: white; font-weight: 700; font-size: 1.2rem; margin-bottom: 0.25rem; text-shadow: 1px 1px 3px black;
        font-family: var(--font-accent);
    }
    .playlist-meta {
        color: #ccc; font-size: 0.9rem;
    }
</style>

<div class="floating-particles">
    <?php for ($i = 0; $i < 6; $i++): ?><div class="particle"></div><?php endfor; ?>
</div>

<section class="banner-section" style="margin-top: 3rem;">
    <div class="container">
        <div style="text-align: center;">
            <h1 class="section-title">MUSIK</h1>
            <h2 style="font-size: 1.5em; text-shadow: 2px 2px 4px #000; color: var(--color-text-primary);">DJ Sets, Playlists & die komplette Sammlung</h2>
        </div>
    </div>
</section>

<main class="container" id="main-content">
    
    <section class="content-section">
        <h2 class="section-title" style="margin-bottom: 2rem;">Komplette Sammlung durchsuchen</h2>
        <div class="music-collection-grid">
            <?php
            $music_links = [
                'A - C' => 'MyMusic_A-C.txt', 'D - F' => 'MyMusic_D-F.txt',
                'G - I' => 'MyMusic_G-I.txt', 'J - L' => 'MyMusic_J-L.txt',
                'M - O' => 'MyMusic_M-O.txt', 'P - R' => 'MyMusic_P-R.txt',
                'S - V' => 'MyMusic_S-V.txt', 'W - Z' => 'MyMusic_W-Z.txt'
            ];
            foreach ($music_links as $text => $url):
            ?>
                <a href="<?php echo $url; ?>" class="button-3d" target="_blank">
                    <div class="button__content">
                        <div class="button__icon">
                            <i class="fas fa-compact-disc"></i>
                        </div>
                        <p class="button__text"><?php echo $text; ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="content-section">
        <h2 class="section-title">Auf Spotify hören</h2>
        <div class="spotify-player">
            <iframe style="border-radius:12px" src="https://open.spotify.com/embed/playlist/4wRWsi7wAqXZTji4Rwiqs7?utm_source=generator" width="100%" height="352" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
        </div>
    </section>

    <section class="content-section">
        <h2 class="section-title">YouTube Playlists</h2>
        <?php if (!empty($playlists)): ?>
            <div class="dynamic-playlist-grid">
                <?php foreach ($playlists as $playlist): ?>
                    <a href="<?php echo htmlspecialchars($playlist['url']); ?>" target="_blank" rel="noopener noreferrer" class="dynamic-playlist-card">
                        <div class="playlist-thumbnail">
                            <img src="<?php echo htmlspecialchars($playlist['thumbnail']); ?>" alt="<?php echo htmlspecialchars($playlist['title']); ?>" loading="lazy">
                        </div>
                        <div class="playlist-info">
                            <h3 class="playlist-title"><?php echo htmlspecialchars($playlist['title']); ?></h3>
                            <p class="playlist-meta"><?php echo $playlist['videoCount']; ?> Videos</p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center;">Momentan konnten keine Playlists geladen werden. Bitte versuche es später erneut.</p>
        <?php endif; ?>
    </section>
</main>

<script>
    // Parallax effect for particles on scroll
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const particles = document.querySelectorAll('.particle');
        particles.forEach((particle, index) => {
            // Different speeds for a more dynamic effect
            const speed = (index % 3 + 1) * 0.15;
            const y = scrolled * speed;
            const x = Math.sin(scrolled * 0.001 * (index % 4 + 1)) * 20; // Add some horizontal movement
            particle.style.transform = `translate3d(${x}px, ${y}px, 0)`;
        });
    });
</script>

<?php include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/main-footer.php"; ?>