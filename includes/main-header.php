<?php
/**
 * Haupt-Header-Template für 4AM Techno Website
 */

// Standard-Werte setzen falls nicht definiert
if (!isset($page_title)) $page_title = "4AM Techno - E1nachser | Minimal Techno & House Music";
if (!isset($page_description)) $page_description = "Minimal & House! Bei 4AM Techno findest du DJ-Sets, Musik-Videos und kostenlose Tools wie Samples, LUTs & Tutorials. Dein Weg zum maximalen Techno-Feeling!";
if (!isset($page_keywords)) $page_keywords = "Techno Producer, Underground Techno, 4AM Techno, E1nachser, Minimal Techno, House Music, Electronic Music, Remix, Party, Musik, DJ, News, Nightlife, 4AM";
if (!isset($og_image)) $og_image = "https://4amtechno.com/images/og-image.jpg";
if (!isset($active_nav)) $active_nav = "";
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($page_keywords); ?>">
    <meta name="author" content="E1nachser">
    
    <?php
    // ===== DYNAMISCHER CANONICAL TAG =====
    
    // 1. Die "Landkarte" aller deiner Seiten
    // Definiert die sauberen URLs für jede Seite
    $urlMap = [
        'index.php'                 => '/',
        'about.php'                 => '/about',
        'partydates.php'            => '/partydates',
        'blog.php'                  => '/blog',
        'downloads.php'             => '/downloads',
        'music-downloads.php'       => '/music-downloads',
        'video-downloads.php'       => '/video-downloads',
        'apps.php'                  => '/apps',
        'music.php'                 => '/music',
        'upload.php'                => '/upload',
        'legal.php'                 => '/impressum', // oder /legal, wie du es bevorzugst
        'datenschutz.php'           => '/datenschutz',
        'agb.php'                   => '/agb'
    ];
    
    // 2. Die aktuelle Datei ermitteln
    $currentPage = basename($_SERVER['SCRIPT_NAME']);
    
    // 3. Prüfen, ob die aktuelle Seite in unserer Landkarte existiert
    if (array_key_exists($currentPage, $urlMap)) {
        // 4. Die saubere, vollständige Canonical-URL erstellen
        $canonicalSlug = $urlMap[$currentPage];
        $canonical_url = 'https://www.4amtechno.com' . $canonicalSlug;
    
        // 5. Den Canonical-Tag ausgeben
        echo '<link rel="canonical" href="' . htmlspecialchars($canonical_url) . '">';
    }
    ?>
    
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($og_image); ?>">
    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($og_image); ?>">
    
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Orbitron:wght@400;500;700;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <meta name="theme-color" content="#C05427">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/manifest.json">	
    
    <link rel="stylesheet" href="/css/style-enhanced.css?v=2">

</head>
<body>
    <a href="#main-content" class="skip-link">Zum Hauptinhalt springen</a>
    
    <header class="header-enhanced" role="banner">
        <nav class="nav-container" aria-label="Hauptnavigation">
            <div class="logo-enhanced">
                <a href="/index.php"><img src="/images/4am-logo.webp" alt="4AM Techno Logo" width="50" height="50"></a>
                <a href="/index.php"><span class="logo-text">4AM TECHNO</span></a>
            </div>
            
            <ul class="main-nav-enhanced" role="menubar">
                <li role="none"><a href="/index.php" role="menuitem" class="<?php echo ($active_nav == 'home') ? 'active' : ''; ?>">Home</a></li>
                <li role="none"><a href="/index.php#videos" role="menuitem">Videos</a></li>
                <li role="none"><a href="/about.php" role="menuitem" class="<?php echo ($active_nav == 'about') ? 'active' : ''; ?>">About</a></li>
                <li role="none"><a href="/partydates.php" role="menuitem" class="<?php echo ($active_nav == 'partydates') ? 'active' : ''; ?>">Party Dates</a></li>
                <li role="none"><a href="/blog.php" role="menuitem" class="<?php echo ($active_nav == 'blog') ? 'active' : ''; ?>">Blog</a></li>
                <li role="none"><a href="/downloads.php" role="menuitem" class="<?php echo ($active_nav == 'downloads') ? 'active' : ''; ?>">Downloads</a></li>
                <li role="none"><a href="/apps.php" role="menuitem" class="<?php echo ($active_nav == 'apps') ? 'active' : ''; ?>">Apps</a></li>
              	<li role="none"><a href="/music.php" role="menuitem" class="<?php echo ($active_nav == 'music') ? 'active' : ''; ?>">Music</a></li>
                <li role="none"><a href="/index.php#contact" role="menuitem">Kontakt</a></li>
            </ul>
            
            <button class="hamburger-enhanced" aria-label="Menü öffnen" aria-expanded="false">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>
        </nav>
    </header>

    <main id="main-content" role="main"></main>

<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Musician",
        "name": "4AM Techno",
        "alternateName": "E1nachser",
        "genre": "Techno, Minimal Techno",
        "url": "https://4amtechno.com",
        "description": "Underground Techno Producer aus Deutschland mit Fokus auf Peak Time Techno & House Music. Hier findest du DJ-Sets, Musik-Videos und kostenlose Tools wie Samples, LUTs & Tutorials.",
        "sameAs": [
            "https://open.spotify.com/artist/0Yta8Xgakuh7wRRA2tG2r8",
            "https://music.apple.com/fi/artist/4am-techno/1786174902",
            "https://music.youtube.com/channel/UC-SeFA2Sm2NheLBReFjz8gQ?si=ORSD_b05WoDOTyIR",
            "https://music.amazon.com/artists/B0DQVQ8KTG/4am-techno",
            "https://tidal.com/browse/artist/52662700",
            "https://soundcloud.com/4amtechno",
            "https://einachser.bandcamp.com/",
            "https://www.youtube.com/channel/UCSIQsJz88OfyACfTr3hZ7vA",
            "https://www.youtube.com/channel/UCak3dMt91jS5iQ29_0T4oNA",
            "https://www.instagram.com/e1nachser/",
            "https://www.tiktok.com/@4amtechno",
            "https://www.pinterest.de/4amtechno/",
            "https://www.capcut.com/profile/XkqoX3OUCCF3Isxomdt4wSHE6qlT6Rg_vXetX7Eabjk"
        ],
        "member": {
            "@type": "Person",
            "name": "E1nachser"
        }
    }
    </script>




<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "VideoObject",
  "name": "4AM TECHNO RADIO | 24/7 Livestream: Minimal Techno, Deep House & Dark Techno",
  "description": "Welcome to 4AM TECHNO! Your refuge for the crispest Minimal Techno and House vibes. Here, you'll fin...",
  "uploadDate": "2025-06-24",
  "thumbnailUrl": "https://4amtechno.com/images/4am-techno-radio-livestream.webp",
  "embedUrl": "https://www.youtube.com/embed/YOUR_VIDEO_ID_HERE",
  "publisher": {
    "@type": "Organization",
    "name": "4AM Techno",
    "url": "https://4amtechno.com"
  }
}
</script>




<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "VideoObject",
  "name": "4AM TECHNO | Running Up that Hill",
  "description": "E1nachser Kate Bush - Running Up that Hill (J2D2 synth wave Remix) SpaceARTronaut Sound on for...",
  "uploadDate": "2025-06-04",
  "thumbnailUrl": "https://4amtechno.com/images/4am-techno-running-up-that-hill.webp",
  "embedUrl": "https://www.youtube.com/embed/YOUR_VIDEO_ID_HERE",
  "publisher": {
    "@type": "Organization",
    "name": "4AM Techno",
    "url": "https://4amtechno.com"
  }
}
</script>




<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": "Entdecke die Minimal Techno Elite",
  "description": "Tauche ein in die Welt der Minimal Techno Elite und entdecke die Künstler, die diese faszinierende Musikrichtung prägen.",
  "image": "https://4amtechno.com/images/news/minimal-techno-elite.webp",
  "datePublished": "2025-01-19",
  "author": {
    "@type": "Person",
    "name": "E1nachser"
  },
  "publisher": {
    "@type": "Organization",
    "name": "4AM Techno",
    "url": "https://4amtechno.com"
  },
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "https://4amtechno.com/blog/entdecke-die-minimal-techno-elite.php"
  }
}
</script>




<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": "Decade Underground Bad Frankenhausen",
  "description": "Ein Blick auf die Underground-Szene in Bad Frankenhausen und die Events, die diese Stadt zu einem Techno-Hotspot machen.",
  "image": "https://4amtechno.com/images/news/decade-underground.webp",
  "datePublished": "2024-12-22",
  "author": {
    "@type": "Person",
    "name": "E1nachser"
  },
  "publisher": {
    "@type": "Organization",
    "name": "4AM Techno",
    "url": "https://4amtechno.com"
  },
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "https://4amtechno.com/blog/decade-underground-bad-frankenhausen.php"
  }
}
</script>




<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": "Boris Brejcha - High Tech Minimal Meister",
  "description": "Portrait des deutschen Produzenten Boris Brejcha und seinem einzigartigen High Tech Minimal Sound.",
  "image": "https://4amtechno.com/images/news/boris-brejcha.webp",
  "datePublished": "2025-01-09",
  "author": {
    "@type": "Person",
    "name": "E1nachser"
  },
  "publisher": {
    "@type": "Organization",
    "name": "4AM Techno",
    "url": "https://4amtechno.com"
  },
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "https://4amtechno.com/blog/boris-brejcha-high-tech-minimal-meister.php"
  }
}
</script>




<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Event",
  "name": "Bad Frankenhausen tanzt! – Dein Sommer Open Air 2025 Highlight in Thüringen",
  "startDate": "2025-06-27T18:00:00+02:00",
  "endDate": "2025-06-27T23:59:59+02:00",
  "eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",
  "eventStatus": "https://schema.org/EventScheduled",
  "location": {
    "@type": "Place",
    "name": "Schlossplatz",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "Schlossplatz",
      "addressLocality": "Bad Frankenhausen",
      "postalCode": "06567",
      "addressRegion": "Thüringen",
      "addressCountry": "DE"
    }
  },
  "image": [
    "https://4amtechno.com/images/blog/bad-frankenhausen-tanzt.webp"
  ],
  "description": "Entdecke das einzigartige Electronic Music Festival Bad Frankenhausen tanzt! am 27. Juni 2025 - mit Westbam, Tief & Ton und Justin Prince im historischen Ambiente des Schlossplatzes.",
  "offers": {
    "@type": "Offer",
    "url": "https://4amtechno.com/partydates.php",
    "price": "0",
    "priceCurrency": "EUR",
    "availability": "https://schema.org/InStock",
    "validFrom": "2025-01-01T00:00:00+01:00"
  },
  "performer": {
    "@type": "MusicGroup",
    "name": "Westbam, Tief & Ton, Justin Prince"
  },
  "organizer": {
    "@type": "Organization",
    "name": "4AM Techno",
    "url": "https://4amtechno.com"
  }
}
</script>




<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Event",
  "name": "Seega Summer Beats: Elektronische Beats im Herzen des Kyffhäuserlandes",
  "startDate": "2025-07-04T18:00:00+02:00",
  "endDate": "2025-07-04T23:59:59+02:00",
  "eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",
  "eventStatus": "https://schema.org/EventScheduled",
  "location": {
    "@type": "Place",
    "name": "Kyffhäuserland / OT Seega Göllinger Straße",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "Göllinger Straße",
      "addressLocality": "Seega",
      "postalCode": "99707",
      "addressRegion": "Thüringen",
      "addressCountry": "DE"
    }
  },
  "image": [
    "https://4amtechno.com/images/blog/seega-summer-beats-kyffhaeuserland.webp"
  ],
  "description": "Die Seega Summer Beats, ein einzigartiges elektronisches Musikevent im Kyffhäuserland, was Tradition und moderne Beats vereint.",
  "offers": {
    "@type": "Offer",
    "url": "https://4amtechno.com/partydates.php",
    "price": "0",
    "priceCurrency": "EUR",
    "availability": "https://schema.org/InStock",
    "validFrom": "2025-01-01T00:00:00+01:00"
  },
  "organizer": {
    "@type": "Organization",
    "name": "4AM Techno",
    "url": "https://4amtechno.com"
  }
}
</script>




<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "Cinematic Color Grading LUTs",
  "description": "Cinematic Color Grading LUTs für professionelle Videobearbeitung. Von Vintage bis Modern, von Warm bis Cool.",
  "image": "https://4amtechno.com/images/downloads/luts.webp",
  "offers": {
    "@type": "Offer",
    "url": "https://4amtechno.com/downloads.php",
    "price": "0",
    "priceCurrency": "EUR",
    "availability": "https://schema.org/InStock",
    "category": "DigitalProduct",
    "priceValidUntil": "2026-12-31"
  }
}
</script>




<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "Motion Graphics",
  "description": "Animierte Logos, Übergänge und VJ Loops für deine Videos. Ready-to-use Grafiken im 4AM TECHNO Style.",
  "image": "https://4amtechno.com/images/downloads/motion-graphics.webp",
  "offers": {
    "@type": "Offer",
    "url": "https://4amtechno.com/downloads.php",
    "price": "0",
    "priceCurrency": "EUR",
    "availability": "https://schema.org/InStock",
    "category": "DigitalProduct",
    "priceValidUntil": "2026-12-31"
  }
}
</script>




<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "Video Overlays",
  "description": "Partikel-Effekte, Light Leaks und Glitch Effects für atmosphärische Videos mit Underground-Feeling.",
  "image": "https://4amtechno.com/images/downloads/video-overlays.webp",
  "offers": {
    "@type": "Offer",
    "url": "https://4amtechno.com/downloads.php",
    "price": "0",
    "priceCurrency": "EUR",
    "availability": "https://schema.org/InStock",
    "category": "DigitalProduct",
    "priceValidUntil": "2026-12-31"
  }
}
</script>




<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "Techno Samples",
  "description": "Exklusive Techno Drums, Synth Loops, Basslines und FX Sounds für deine Produktionen. Alle Samples sind lizenzfrei und ready-to-use.",
  "image": "https://4amtechno.com/images/downloads/techno-samples.webp",
  "offers": {
    "@type": "Offer",
    "url": "https://4amtechno.com/downloads.php",
    "price": "0",
    "priceCurrency": "EUR",
    "availability": "https://schema.org/InStock",
    "category": "DigitalProduct",
    "priceValidUntil": "2026-12-31"
  }
}
</script>




<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "DAW Templates",
  "description": "Fertige Projekt-Templates für Ableton Live, FL Studio und Logic Pro. Lerne von Profis und starte direkt mit deinen Tracks.",
  "image": "https://4amtechno.com/images/downloads/daw-templates.webp",
  "offers": {
    "@type": "Offer",
    "url": "https://4amtechno.com/downloads.php",
    "price": "0",
    "priceCurrency": "EUR",
    "availability": "https://schema.org/InStock",
    "category": "DigitalProduct",
    "priceValidUntil": "2026-12-31"
  }
}
</script>




<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "Synth Presets",
  "description": "Presets für beliebte Synthesizer wie Serum, Sylenth1 und Massive. Erstelle einzigartige Sounds für deine Tracks.",
  "image": "https://4amtechno.com/images/downloads/synth-presets.webp",
  "offers": {
    "@type": "Offer",
    "url": "https://4amtechno.com/downloads.php",
    "price": "0",
    "priceCurrency": "EUR",
    "availability": "https://schema.org/InStock",
    "category": "DigitalProduct",
    "priceValidUntil": "2026-12-31"
  }
}
</script>