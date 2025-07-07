<?php
// SEITEN-SPEZIFISCHE VARIABLEN
$page_title = "4AM TECHNO - Apps & Tools für DJs und Produzenten";
$page_description = "Nützliche Web-Apps für deine Musik: Finde ähnliche Songs mit der Deezer API, ermittle das Tempo mit dem BPM Tapper und entdecke weitere Tools.";
$canonical_url = "https://www.4amtechno.com/apps.php";
$active_nav = "apps";

// HEADER EINBINDEN
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/main-header.php";
?>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<style>
    /* Spezifische Styles für die Apps-Seite aus deiner Vorlage */
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
        --color-success: #4CAF50;
        --color-warning: #FF9800;
        --color-error: #F44336;
        --color-info: #2196F3;
        
        /* Typography */
        --font-primary: 'Montserrat', sans-serif;
        --font-accent: 'Orbitron', sans-serif;
        
        /* Spacing */
        --spacing-xs: 0.25rem;
        --spacing-sm: 0.5rem;
        --spacing-md: 1rem;
        --spacing-lg: 1.5rem;
        --spacing-xl: 2rem;
        --spacing-2xl: 3rem;
        --spacing-3xl: 4rem;
        --spacing-4xl: 6rem;
        
        /* Transitions */
        --transition-fast: 0.15s ease;
        --transition-normal: 0.3s ease;
        --transition-slow: 0.5s ease;
        
        /* Shadows */
        --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
        --shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.15);
        --shadow-neon: 0 0 20px var(--color-primary-accent);
        
        /* Border Radius */
        --radius-sm: 0.25rem;
        --radius-md: 0.5rem;
        --radius-lg: 1rem;
        --radius-xl: 1.5rem;
        --radius-full: 9999px;
    }

    body {
        font-family: var(--font-primary);
        background-color: var(--color-bg-dark);
        color: var(--color-text-primary);
        line-height: 1.6;
        overflow-x: hidden;
        background-image: radial-gradient(circle at 20% 50%, rgba(192, 84, 39, 0.1) 0%, transparent 50%),
                          radial-gradient(circle at 80% 20%, rgba(192, 192, 192, 0.05) 0%, transparent 50%),
                          radial-gradient(circle at 40% 80%, rgba(192, 84, 39, 0.05) 0%, transparent 50%);
    }

    .floating-particles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 0;
        overflow: hidden;
    }

    .particle {
        position: absolute;
        background: var(--color-primary-accent);
        border-radius: 50%;
        opacity: 0.1;
        animation: float 6s ease-in-out infinite;
    }

    .particle:nth-child(1) { width: 2px; height: 2px; top: 20%; left: 10%; animation-delay: -0.5s; }
    .particle:nth-child(2) { width: 3px; height: 3px; top: 60%; left: 20%; animation-delay: -1s; }
    .particle:nth-child(3) { width: 1px; height: 1px; top: 40%; left: 70%; animation-delay: -1.5s; }
    .particle:nth-child(4) { width: 2px; height: 2px; top: 80%; left: 80%; animation-delay: -2s; }
    .particle:nth-child(5) { width: 3px; height: 3px; top: 30%; left: 50%; animation-delay: -2.5s; }
    .particle:nth-child(6) { width: 1px; height: 1px; top: 70%; left: 30%; animation-delay: -3s; }

    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0.1; }
        50% { transform: translateY(-20px) rotate(180deg); opacity: 0.3; }
    }

    .hero-section {
        position: relative;
        padding: var(--spacing-4xl) 0 var(--spacing-2xl);
        text-align: center;
        z-index: 1;
    }

    .hero-title {
        font-family: var(--font-accent);
        font-size: clamp(2.5rem, 6vw, 4rem);
        font-weight: 900;
        color: var(--color-text-headings);
        margin-bottom: var(--spacing-lg);
        text-transform: uppercase;
        letter-spacing: 3px;
        background: var(--gradient-accent);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 0 30px rgba(192, 192, 192, 0.3);
    }

    .hero-subtitle {
        font-size: clamp(1.2rem, 3vw, 1.5rem);
        color: var(--color-text-primary);
        margin-bottom: var(--spacing-xl);
        font-weight: 400;
    }

    .button-3d {
        position: relative;
        padding: 0;
        width: 200px;
        height: 200px;
        border: 4px solid #888888;
        outline: none;
        background-color: var(--color-bg-dark);
        border-radius: 40px;
        box-shadow: -6px -20px 35px rgba(255, 255, 255, 0.1), 
                    -6px -10px 15px rgba(255, 255, 255, 0.05), 
                    -20px 0px 30px rgba(255, 255, 255, 0.03), 
                    6px 20px 25px rgba(0, 0, 0, 0.4);
        transition: 0.13s ease-in-out;
        cursor: pointer;
        margin: 0 auto var(--spacing-lg);
    }

    .button-3d:hover {
        transform: translateY(-3px);
        box-shadow: -8px -25px 40px rgba(255, 255, 255, 0.15), 
                    -8px -15px 20px rgba(255, 255, 255, 0.08), 
                    -25px 0px 35px rgba(255, 255, 255, 0.05), 
                    8px 25px 30px rgba(0, 0, 0, 0.5);
    }

    .button-3d:active {
        box-shadow: none;
        transform: translateY(0);
    }

    .button-3d:active .button__content {
        box-shadow: none;
    }

    .button-3d:active .button__text,
    .button-3d:active .button__icon {
        transform: translate3d(0px, 0px, 0px);
    }

    .button__content {
        position: relative;
        display: grid;
        padding: 20px;
        width: 100%;
        height: 100%;
        grid-template-columns: 1fr 1fr 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        box-shadow: inset 0px -8px 0px rgba(221, 221, 221, 0.1), 
                    0px -8px 0px var(--color-bg-dark);
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
        width: 32px;
        height: 32px;
        font-size: 24px;
        color: var(--color-primary-accent);
        display: flex;
        align-items: center;
        justify-content: center;
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
        -moz-background-clip: text;
        background-clip: text;
        transition: 0.13s ease-in-out;
        font-family: var(--font-accent);
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .apps-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
        gap: var(--spacing-2xl);
        padding: var(--spacing-2xl) 0;
        position: relative;
        z-index: 1;
    }

    .app-card {
        background: rgba(40, 40, 40, 0.9);
        backdrop-filter: blur(10px);
        border-radius: var(--radius-xl);
        padding: var(--spacing-2xl);
        border: 1px solid rgba(192, 192, 192, 0.1);
        transition: all var(--transition-normal);
        position: relative;
        overflow: hidden;
    }

    .app-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: var(--gradient-accent);
        opacity: 0;
        transition: opacity var(--transition-normal);
    }

    .app-card:hover {
        transform: translateY(-8px);
        border-color: rgba(192, 192, 192, 0.3);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .app-card:hover::before {
        opacity: 1;
    }

    .app-card h3 {
        font-family: var(--font-accent);
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--color-text-headings);
        margin-bottom: var(--spacing-md);
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .app-card p {
        color: var(--color-text-primary);
        text-align: center;
        margin-bottom: var(--spacing-xl);
        line-height: 1.6;
    }

    .status-badge {
        position: absolute;
        top: var(--spacing-lg);
        right: var(--spacing-lg);
        padding: var(--spacing-xs) var(--spacing-md);
        border-radius: var(--radius-full);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        z-index: 2;
    }

    .badge-active {
        background: var(--color-success);
        color: white;
        box-shadow: 0 0 15px rgba(76, 175, 80, 0.3);
    }

    .badge-beta {
        background: var(--color-warning);
        color: white;
        box-shadow: 0 0 15px rgba(255, 152, 0, 0.3);
    }

    .badge-coming-soon {
        background: var(--color-info);
        color: white;
        box-shadow: 0 0 15px rgba(33, 150, 243, 0.3);
    }

    .form-group {
        margin-bottom: var(--spacing-lg);
        text-align: left;
    }

    .form-group label {
        display: block;
        margin-bottom: var(--spacing-sm);
        color: var(--color-text-headings);
        font-weight: 600;
        font-size: 0.9rem;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: var(--spacing-md);
        background: rgba(18, 18, 18, 0.8);
        border: 2px solid rgba(192, 192, 192, 0.2);
        border-radius: var(--radius-md);
        color: var(--color-text-primary);
        font-family: var(--font-primary);
        transition: all var(--transition-normal);
        font-size: 0.9rem;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--color-primary-accent);
        box-shadow: 0 0 15px rgba(192, 192, 192, 0.2);
    }

    .btn-primary {
        background: var(--gradient-accent);
        color: var(--color-bg-dark);
        border: none;
        padding: var(--spacing-md) var(--spacing-xl);
        border-radius: var(--radius-full);
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        transition: all var(--transition-normal);
        display: inline-flex;
        align-items: center;
        gap: var(--spacing-sm);
        width: 100%;
        justify-content: center;
    }

    .btn-primary:hover {
        background: var(--color-hover-accent);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(192, 192, 192, 0.3);
    }

    .btn-primary:disabled {
        background: rgba(192, 192, 192, 0.3);
        cursor: not-allowed;
        transform: none;
    }

    .btn-secondary {
        background: rgba(192, 84, 39, 0.8);
        color: white;
        border: none;
        padding: var(--spacing-sm) var(--spacing-lg);
        border-radius: var(--radius-full);
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all var(--transition-normal);
        display: inline-flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .btn-secondary:hover {
        background: rgba(192, 84, 39, 1);
        transform: translateY(-2px);
    }

    .hidden {
        display: none !important;
    }

    /* BPM Tapper Styles */
    .bpm-display {
        font-family: var(--font-accent);
        font-size: 4rem;
        font-weight: 900;
        color: var(--color-text-headings);
        text-align: center;
        margin: var(--spacing-xl) 0;
        text-shadow: 0 0 20px rgba(192, 192, 192, 0.3);
    }

    .tap-button {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: var(--gradient-accent);
        border: none;
        color: var(--color-bg-dark);
        font-family: var(--font-accent);
        font-size: 1.5rem;
        font-weight: 700;
        cursor: pointer;
        transition: all var(--transition-normal);
        margin: 0 auto;
        display: block;
        box-shadow: 0 10px 30px rgba(192, 192, 192, 0.3);
    }

    .tap-button:hover {
        transform: scale(1.05);
        box-shadow: 0 15px 40px rgba(192, 192, 192, 0.4);
    }

    .tap-button:active {
        transform: scale(0.95);
    }

    .tap-info {
        text-align: center;
        margin-top: var(--spacing-lg);
        color: var(--color-text-primary);
    }

    .reset-button {
        background: rgba(244, 67, 54, 0.8);
        color: white;
        border: none;
        padding: var(--spacing-sm) var(--spacing-lg);
        border-radius: var(--radius-full);
        font-weight: 600;
        cursor: pointer;
        transition: all var(--transition-normal);
        margin: var(--spacing-lg) auto 0;
        display: block;
    }

    .reset-button:hover {
        background: rgba(244, 67, 54, 1);
        transform: translateY(-2px);
    }
</style>

<div class="floating-particles">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
</div>

<section class="hero-section">
    <div class="container mx-auto px-4">
        <h1 class="hero-title">Music Tools</h1>
        <p class="hero-subtitle">Professionelle Tools für DJs und Produzenten</p>
        
        <div class="apps-grid">
            <!-- Techno Finder App -->
            <div class="app-card">
                <div class="status-badge badge-active">Aktiv</div>
                <h3>🎵 Techno Finder</h3>
                <p>Entdecke ähnliche Songs, Playlists und Top Charts mit der Deezer API. Finde neue Tracks für deine Sets und erweitere deine Musiksammlung.</p>
                <button class="button-3d" onclick="toggleApp('techno-finder')" aria-label="Techno Finder starten" role="button">
                    <div class="button__content">
                        <div class="button__icon">
                            <i class="fas fa-music"></i>
                        </div>
                        <p class="button__text">Starten</p>
                    </div>
                </button>
            </div>

            <!-- BPM Tapper App -->
            <div class="app-card">
                <div class="status-badge badge-active">Aktiv</div>
                <h3>🥁 BPM Tapper</h3>
                <p>Ermittle das Tempo deiner Tracks durch einfaches Tippen. Perfekt für DJs zum Beatmatching und für Produzenten zur Tempo-Analyse.</p>
                <button class="button-3d" onclick="toggleApp('bpm-tapper')" aria-label="BPM Tapper starten" role="button">
                    <div class="button__content">
                        <div class="button__icon">
                            <i class="fas fa-drum"></i>
                        </div>
                        <p class="button__text">Starten</p>
                    </div>
                </button>
            </div>

            <!-- Genre Analyzer App (Coming Soon) -->
            <div class="app-card">
                <div class="status-badge badge-coming-soon">Bald</div>
                <h3>🎛️ Genre Analyzer</h3>
                <p>Analysiere automatisch das Genre deiner Tracks mit KI-Technologie. Organisiere deine Musikbibliothek effizienter.</p>
                <button class="button-3d" onclick="toggleApp('genre-analyzer')" aria-label="Genre Analyzer bald verfügbar" role="button">
                    <div class="button__content">
                        <div class="button__icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <p class="button__text">Bald</p>
                    </div>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Techno Finder App Content -->
<div id="techno-finder" class="app-content hidden">
    <div class="container mx-auto px-4">
        <div class="app-card">
            <h3>🎵 Similar Song Finder</h3>
            <div class="form-group">
                <label for="song-input">Song oder Künstler suchen:</label>
                <input type="text" id="song-input" placeholder="z.B. 'Daft Punk' oder 'Techno Track'">
            </div>
            <button class="btn-primary" onclick="searchSimilarSongs()">
                <i class="fas fa-search"></i>
                Ähnliche Songs finden
            </button>
            
            <div id="song-loading" class="techno-loading hidden">
                <div class="techno-spinner"></div>
                <span>Suche nach ähnlichen Songs...</span>
            </div>
            
            <div id="song-error" class="techno-error hidden"></div>
            
            <div id="song-results" class="techno-results hidden">
                <h4 class="techno-section-title">Ähnliche Songs gefunden:</h4>
                <div id="song-results-grid" class="techno-results-grid"></div>
            </div>
        </div>

        <div class="app-card">
            <h3>🎧 Playlist Finder</h3>
            <div class="form-group">
                <label for="playlist-input">Playlist-Name eingeben:</label>
                <input type="text" id="playlist-input" placeholder="z.B. 'Techno Mix' oder 'Deep House'">
            </div>
            <button class="btn-primary" onclick="searchPlaylists()">
                <i class="fas fa-list"></i>
                Playlists finden
            </button>
            
            <div id="playlist-loading" class="techno-loading hidden">
                <div class="techno-spinner"></div>
                <span>Suche nach Playlists...</span>
            </div>
            
            <div id="playlist-error" class="techno-error hidden"></div>
            
            <div id="playlist-results" class="techno-results hidden">
                <h4 class="techno-section-title">Playlists gefunden:</h4>
                <div id="playlist-results-grid" class="techno-results-grid"></div>
            </div>
        </div>

        <div class="app-card">
            <h3>🏆 Top Hits nach Genre</h3>
            <div class="form-group">
                <label for="genre-select">Genre auswählen:</label>
                <select id="genre-select">
                    <option value="">Genre auswählen...</option>
                </select>
            </div>
            <button class="btn-primary" onclick="loadTopHits()">
                <i class="fas fa-chart-line"></i>
                Charts laden
            </button>
            
            <div id="genre-loading" class="techno-loading hidden">
                <div class="techno-spinner"></div>
                <span>Lade Top Hits...</span>
            </div>
            
            <div id="genre-error" class="techno-error hidden"></div>
            
            <div id="genre-results" class="techno-results hidden">
                <h4 class="techno-section-title">Top Hits:</h4>
                <div id="genre-results-grid" class="techno-chart-grid"></div>
            </div>
        </div>
    </div>
</div>

<!-- BPM Tapper App Content -->
<div id="bpm-tapper" class="app-content hidden">
    <div class="container mx-auto px-4">
        <div class="app-card">
            <h3>🥁 BPM Tapper</h3>
            <div class="bpm-display" id="bpm-display">---</div>
            <button class="tap-button" onclick="tap()">TAP</button>
            <div class="tap-info">
                <p>Taps: <span id="tap-count">0</span></p>
                <p>Tippe im Rhythmus der Musik oder drücke die Leertaste</p>
            </div>
            <button class="reset-button" onclick="resetTaps()">Reset</button>
        </div>
    </div>
</div>

<!-- Genre Analyzer App Content -->
<div id="genre-analyzer" class="app-content hidden">
    <div class="container mx-auto px-4">
        <div class="app-card">
            <h3>🎛️ Genre Analyzer</h3>
            <p style="text-align: center; color: var(--color-info); font-size: 1.2rem; margin: var(--spacing-xl) 0;">
                Diese Funktion ist noch in der Entwicklung und wird bald verfügbar sein.
            </p>
            <div id="genre-results"></div>
        </div>
    </div>
</div>

<!-- Audio Player -->
<div id="techno-audio-player" class="techno-audio-player hidden">
    <div class="techno-player-info" id="techno-player-info"></div>
    <div class="techno-player-controls">
        <audio id="techno-audio-element" controls></audio>
        <button onclick="closeTechnoPlayer()" class="techno-close-player">✕</button>
    </div>
</div>

<script>
// NEUE PROXY-URL
const DEEZER_API_BASE = 'https://proxy.4amtechno.com/api';

let activeApp = null;

function toggleApp(appName) {
    const allApps = document.querySelectorAll('.app-content');
    const targetApp = document.getElementById(appName);
    
    // Schließe alle anderen Apps
    allApps.forEach(app => {
        if (app.id !== appName) {
            app.classList.add('hidden');
        }
    });
    
    // Toggle der gewählten App
    if (targetApp.classList.contains('hidden')) {
        targetApp.classList.remove('hidden');
        activeApp = appName;
        
        // Spezielle Initialisierung für Techno Finder
        if (appName === 'techno-finder') {
            initializeTechnoFinder();
        }
        
        // Beim Öffnen der BPM-App auch die Tastatureingabe aktivieren
        if (appName === 'bpm-tapper') {
            document.addEventListener('keyup', handleKeyPress);
        } else {
            document.removeEventListener('keyup', handleKeyPress);
        }
    } else {
        targetApp.classList.add('hidden');
        activeApp = null;
        document.removeEventListener('keyup', handleKeyPress);
    }
}

// --- TECHNO FINDER FUNKTIONEN ---

// Initialisierung nur einmal durchführen
let technoFinderInitialized = false;

function initializeTechnoFinder() {
    if (technoFinderInitialized) return;
    technoFinderInitialized = true;

    loadGenres();
    
    // Enter-Taste Support für Eingabefelder
    document.getElementById('song-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchSimilarSongs();
        }
    });
    
    document.getElementById('playlist-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchPlaylists();
        }
    });
}

function loadGenres() {
    fetch(`${DEEZER_API_BASE}/genre`)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('genre-select');
            select.innerHTML = '<option value="">Genre auswählen...</option>';
            
            if (data.data) {
                data.data.forEach(genre => {
                    const option = document.createElement('option');
                    option.value = genre.id;
                    option.textContent = genre.name;
                    select.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading genres:', error);
        });
}

function searchSimilarSongs() {
    const query = document.getElementById('song-input').value.trim();
    if (!query) {
        showTechnoError('song-error', 'Bitte gib einen Suchbegriff ein.');
        return;
    }
    
    showTechnoLoading('song-loading');
    hideTechnoError('song-error');
    hideTechnoResults('song-results');
    
    fetch(`${DEEZER_API_BASE}/search?q=${encodeURIComponent(query)}&limit=50`)
        .then(response => response.json())
        .then(data => {
            hideTechnoLoading('song-loading');
            
            if (data.data && data.data.length > 0) {
                displaySongResults(data.data);
                showTechnoResults('song-results');
            } else {
                showTechnoError('song-error', 'Keine ähnlichen Songs gefunden. Versuche es mit einem anderen Suchbegriff.');
            }
        })
        .catch(error => {
            hideTechnoLoading('song-loading');
            showTechnoError('song-error', 'Fehler beim Laden der Songs. Bitte versuche es später erneut.');
            console.error('Error:', error);
        });
}

function searchPlaylists() {
    const query = document.getElementById('playlist-input').value.trim();
    if (!query) {
        showTechnoError('playlist-error', 'Bitte gib einen Playlist-Namen ein.');
        return;
    }
    
    showTechnoLoading('playlist-loading');
    hideTechnoError('playlist-error');
    hideTechnoResults('playlist-results');
    
    fetch(`${DEEZER_API_BASE}/search/playlist?q=${encodeURIComponent(query)}&limit=25`)
        .then(response => response.json())
        .then(data => {
            hideTechnoLoading('playlist-loading');
            
            if (data.data && data.data.length > 0) {
                displayPlaylistResults(data.data);
                showTechnoResults('playlist-results');
            } else {
                showTechnoError('playlist-error', 'Keine Playlists gefunden. Versuche es mit einem anderen Suchbegriff.');
            }
        })
        .catch(error => {
            hideTechnoLoading('playlist-loading');
            showTechnoError('playlist-error', 'Fehler beim Laden der Playlists. Bitte versuche es später erneut.');
            console.error('Error:', error);
        });
}

function loadTopHits() {
    const genreId = document.getElementById('genre-select').value;
    if (!genreId) {
        showTechnoError('genre-error', 'Bitte wähle ein Genre aus.');
        return;
    }
    
    showTechnoLoading('genre-loading');
    hideTechnoError('genre-error');
    hideTechnoResults('genre-results');
    
    fetch(`${DEEZER_API_BASE}/chart/${genreId}/tracks?limit=25`)
        .then(response => response.json())
        .then(data => {
            hideTechnoLoading('genre-loading');
            
            if (data.data && data.data.length > 0) {
                displayChartResults(data.data);
                showTechnoResults('genre-results');
            } else {
                showTechnoError('genre-error', 'Keine Charts für dieses Genre gefunden.');
            }
        })
        .catch(error => {
            hideTechnoLoading('genre-loading');
            showTechnoError('genre-error', 'Fehler beim Laden der Charts. Bitte versuche es später erneut.');
            console.error('Error:', error);
        });
}

function displaySongResults(songs) {
    const grid = document.getElementById('song-results-grid');
    grid.innerHTML = '';
    
    songs.forEach(song => {
        const card = document.createElement('div');
        card.className = 'techno-result-card';
        
        const imageUrl = song.album?.cover_medium || song.artist?.picture_medium || '/images/placeholder-album.jpg';
        const duration = formatDuration(song.duration);
        
        card.innerHTML = `
            <img src="${imageUrl}" alt="${song.title}" class="techno-result-image" loading="lazy">
            <div class="techno-result-info">
                <div class="techno-result-title">${song.title}</div>
                <div class="techno-result-artist">von ${song.artist?.name || 'Unbekannter Künstler'}</div>
                <div class="techno-result-album">Album: ${song.album?.title || 'Unbekannt'}</div>
                <div class="techno-result-duration">Dauer: ${duration}</div>
                ${song.preview ? `<button class="btn-secondary" onclick="playTechnoPreview('${song.preview}', '${song.title}', '${song.artist?.name || 'Unbekannt'}')">30s Preview</button>` : '<span style="color: #666;">Kein Preview verfügbar</span>'}
            </div>
        `;
        
        grid.appendChild(card);
    });
}

function displayPlaylistResults(playlists) {
    const grid = document.getElementById('playlist-results-grid');
    grid.innerHTML = '';
    
    playlists.forEach(playlist => {
        const card = document.createElement('div');
        card.className = 'techno-playlist-card';
        
        const imageUrl = playlist.picture_medium || '/images/placeholder-playlist.jpg';
        
        card.innerHTML = `
            <img src="${imageUrl}" alt="${playlist.title}" class="techno-result-image" loading="lazy">
            <div class="techno-result-info">
                <div class="techno-result-title">${playlist.title}</div>
                <div class="techno-playlist-creator">von ${playlist.user?.name || 'Unbekannt'}</div>
                <div class="techno-playlist-tracks">${playlist.nb_tracks || 0} Tracks</div>
                <div class="techno-playlist-fans">${playlist.fans || 0} Fans</div>
                <a href="${playlist.link}" target="_blank" class="techno-playlist-link">Auf Deezer öffnen</a>
            </div>
        `;
        
        grid.appendChild(card);
    });
}

function displayChartResults(tracks) {
    const grid = document.getElementById('genre-results-grid');
    grid.innerHTML = '';
    
    tracks.forEach((track, index) => {
        const card = document.createElement('div');
        card.className = 'techno-chart-item';
        
        const rank = index + 1;
        const imageUrl = track.album?.cover_small || track.artist?.picture_small || '/images/placeholder-album.jpg';
        const duration = formatDuration(track.duration);
        
        let rankClass = '';
        if (rank <= 3) rankClass = 'top-3';
        else if (rank <= 10) rankClass = 'top-10';
        
        card.innerHTML = `
            <div class="techno-chart-rank ${rankClass}">${rank}</div>
            <img src="${imageUrl}" alt="${track.title}" class="techno-chart-image" loading="lazy">
            <div class="techno-chart-info">
                <div class="techno-chart-title">${track.title}</div>
                <div class="techno-chart-artist">${track.artist?.name || 'Unbekannter Künstler'}</div>
                <div class="techno-chart-album">${track.album?.title || 'Unbekannt'} • ${duration}</div>
            </div>
            <div class="techno-chart-actions">
                ${track.preview ? `<button class="btn-secondary" onclick="playTechnoPreview('${track.preview}', '${track.title}', '${track.artist?.name || 'Unbekannt'}')">30s Preview</button>` : '<span style="color: #666; font-size: 0.8rem;">Kein Preview</span>'}
            </div>
        `;
        
        grid.appendChild(card);
    });
}

function playTechnoPreview(previewUrl, title, artist) {
    const player = document.getElementById('techno-audio-player');
    const audio = document.getElementById('techno-audio-element');
    const info = document.getElementById('techno-player-info');
    
    audio.src = previewUrl;
    info.textContent = `${title} - ${artist}`;
    player.classList.remove('hidden');
    
    audio.play().catch(error => {
        console.error('Error playing audio:', error);
        showTechnoError('song-error', 'Fehler beim Abspielen des Previews.');
    });
}

function closeTechnoPlayer() {
    const player = document.getElementById('techno-audio-player');
    const audio = document.getElementById('techno-audio-element');
    
    audio.pause();
    audio.src = '';
    player.classList.add('hidden');
}

function formatDuration(seconds) {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
}

function showTechnoLoading(elementId) {
    document.getElementById(elementId).classList.remove('hidden');
}

function hideTechnoLoading(elementId) {
    document.getElementById(elementId).classList.add('hidden');
}

function showTechnoError(elementId, message) {
    const element = document.getElementById(elementId);
    element.textContent = message;
    element.classList.remove('hidden');
}

function hideTechnoError(elementId) {
    document.getElementById(elementId).classList.add('hidden');
}

function showTechnoResults(elementId) {
    document.getElementById(elementId).classList.remove('hidden');
}

function hideTechnoResults(elementId) {
    document.getElementById(elementId).classList.add('hidden');
}

// --- BESTEHENDE BPM TAPPER FUNKTIONEN ---
let taps = [];
const tapTimeout = 2000; // Reset nach 2 Sekunden ohne Tap

function tap() {
    const now = Date.now();
    // Wenn der letzte Tap zu lange her ist, fange von vorne an
    if (taps.length > 0 && (now - taps[taps.length - 1]) > tapTimeout) {
        taps = [];
    }
    
    taps.push(now);
    document.getElementById('tap-count').textContent = taps.length;

    if (taps.length > 1) {
        const intervals = [];
        for (let i = 1; i < taps.length; i++) {
            intervals.push(taps[i] - taps[i-1]);
        }
        const averageInterval = intervals.reduce((a, b) => a + b, 0) / intervals.length;
        const bpm = (60000 / averageInterval).toFixed(1);
        document.getElementById('bpm-display').textContent = bpm;
    }
}

function resetTaps() {
    taps = [];
    document.getElementById('bpm-display').textContent = '---';
    document.getElementById('tap-count').textContent = '0';
}

// Leertaste für den BPM Tapper nutzen
function handleKeyPress(event) {
    if (event.code === 'Space' && activeApp === 'bpm-tapper') {
        event.preventDefault(); // Verhindert Scrollen der Seite
        tap();
    }
}

// --- GENRE ANALYZER (Platzhalter) ---
function analyzeGenre() {
    const resultsContainer = document.getElementById('genre-results');
    resultsContainer.innerHTML = 'Diese Funktion ist noch in der Entwicklung.';
    // Hier kommt später die Logik für den Genre Analyzer hin
}

</script>

<?php
// FOOTER EINBINDEN
include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/main-footer.php";
?>

