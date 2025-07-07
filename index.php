<?php
// Standard-Werte setzen falls nicht definiert
$page_title = "4AM Techno - E1nachser | Minimal Techno & House Music";
$page_description = "Minimal & House! Bei 4AM Techno findest du DJ-Sets, Musik-Videos und kostenlose Tools wie Samples, LUTs & Tutorials. Dein Weg zum maximalen Techno-Feeling!";
$page_keywords = "Techno Producer, Underground Techno, 4AM Techno, E1nachser, Minimal Techno, House Music, Electronic Music, Remix, Party, Musik, DJ, News, Nightlife, 4AM";
$canonical_url = "https://4amtechno.com" . $_SERVER["REQUEST_URI"];
$og_image = "https://4amtechno.com/images/og-image.jpg";
$active_nav = "home";

include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/main-header.php";
?>

    <!-- Main Content -->
    <main id="main-content" role="main">
        
        <!-- Hero Section -->
        <section id="home" class="hero-enhanced" aria-label="Hauptbereich">

            
            <div class="hero-overlay">
                <div class="hero-content">
                    <h1 class="hero-title">4AM TECHNO</h1>
                    <h2 class="hero-subtitle">Underground Beats für die Nacht</h2>
                    <p class="hero-description">Dunkle Techno-Sounds aus Deutschland - Produziert für die Stunden zwischen Mitternacht und Morgengrauen</p>
                    
                    <div class="hero-cta">
                        <a href="#videos" class="btn-primary" aria-label="Zu den neuesten Videos">
                            <i class="fas fa-video" aria-hidden="true"></i>
                            Neueste Videos
                        </a>
                        <a href="partydates.php" class="btn-secondary" aria-label="Zu den kommenden Party Dates">
                            <i class="fas fa-calendar" aria-hidden="true"></i>
                            Party Dates
                        </a>
                    </div>
                    
                    <div class="social-links-hero">
                        <a href="https://open.spotify.com/intl-de/artist/0Yta8Xgakuh7wRRA2tG2r8" target="_blank" rel="noopener noreferrer" aria-label="Spotify Profil">
                            <i class="fab fa-spotify" aria-hidden="true"></i>
                        </a>
                        <a href="https://www.instagram.com/E1nachser" target="_blank" rel="noopener noreferrer" aria-label="Instagram Profil">
                            <i class="fab fa-instagram" aria-hidden="true"></i>
                        </a>
                        <a href="https://www.youtube.com/@4AMTechno" target="_blank" rel="noopener noreferrer" aria-label="YouTube Kanal">
                            <i class="fab fa-youtube" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="scroll-indicator" aria-hidden="true">
                <div class="scroll-arrow"></div>
                <span>Scroll</span>
            </div>
        </section>

        <!-- Latest Videos Section -->
        <section id="videos" class="music-section" aria-label="Neueste Videos">
            <div class="container">
                <header class="section-header">
                    <h2 class="section-title">Neueste Videos</h2>
                    <p class="section-subtitle">Die neuesten Videos von meinem YouTube-Kanal</p>
                </header>
                
                <div class="video-grid" role="list">
                    <!-- Dynamisch geladene YouTube-Videos hier -->
                </div>
                
                <div class="section-cta">
                    <a href="https://www.youtube.com/@4AMTechno" target="_blank" rel="noopener noreferrer" class="btn-outline" aria-label="Alle Videos auf YouTube ansehen">
                        Alle Videos auf YouTube ansehen
                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="about-section" aria-label="Über den Künstler">
            <div class="container">
                <div class="about-grid">
                    <div class="about-content animate-on-scroll">
                        <header class="section-header">
                            <h2 class="section-title">About E1nachser</h2>
                            <p class="section-subtitle">Der Produzent hinter 4AM Techno</p>
                        </header>
                        
                            <div class="about-text">
                                <p>Hinter 4AM TECHNO steckt meine Vision! Ich bin E1nachser, dein Content Creator und Spacial Agent für alle fälle. 4AM TECHNO ist dein Place-to-be für krassen Underground House & Minimal Sound!</p>

                                <p>4AM TECHNO | Herzstück der Kyffhäuser Region! Erlebe die einzigartige Atmosphäre von Bad Frankenhausen und lass dich von unseren Beats mitreißen. House, Techno und mehr – direkt aus dem Herzen Thüringens.</p>

                                <p>4AM TECHNO | Bad Frankenhausen | Dein Sound für unvergessliche Nächte! Von energiegeladenen Playlists bis hin zu atemberaubenden Music Videos – hier findest du alles, was dein Herz begehrt.</p>

                                <p>Als Underground Techno Producer aus Deutschland konzentriere ich mich auf Peak Time Techno und House Music. Meine Musik entsteht in den frühen Morgenstunden zwischen 4 und 6 Uhr - daher der Name 4AM TECHNO. Diese Zeit inspiriert mich zu den dunkelsten und intensivsten Sounds.</p>

                                <p>Die elektronische Musikszene in Deutschland hat eine reiche Geschichte, und ich bin stolz darauf, Teil dieser Tradition zu sein. Von Berlin bis Bad Frankenhausen - überall pulsiert der Underground-Beat, der Menschen zusammenbringt und unvergessliche Nächte schafft.</p>

                                <p>Auf dieser Plattform findest du nicht nur meine neuesten Tracks und DJ-Sets, sondern auch kostenlose Samples, LUTs für Videobearbeitung, Motion Graphics und vieles mehr. Alles was du brauchst, um deine eigenen Projekte auf das nächste Level zu bringen.</p>

                                <p>4AM TECHNO lässt die Vergangenheit wieder aufleben... <a href="about.php" class="news-link" style="margin-left: 5px;">weiterlesen</a></p>

                              <div class="stats-grid">
                                  <div class="stat-item">
                                      <span class="stat-number" data-target="2500">0</span>
                                      <span class="stat-label">Play time (Std.)</span>
                                  </div>
                                  <div class="stat-item">
                                      <span class="stat-number" data-target="23">0</span>
                                      <span class="stat-label">Music Kiste</span>
                                  </div>
                                  <div class="stat-item">
                                      <span class="stat-number" data-target="69">0</span>
                                      <span class="stat-label">Playlist</span>
                                  </div>
                              </div>
                        </div>
                    </div>
                    
                    <div class="about-visual animate-on-scroll">
                        <div class="youtube-live-stream">
                            <iframe src="https://www.youtube.com/embed/pMsdZqCKltw?si=PcF1nocqvoqW5KXP" frameborder="0" allow="encrypted-media" allowfullscreen loading="lazy"></iframe>
                            <div class="stream-overlay">
                                <h3>4AM Techno Live Stream</h3>
                                <p>24/7 Underground Beats</p>
                                <button class="stream-play-btn" onclick="this.parentElement.previousElementSibling.src += '&autoplay=1'; this.style.display='none';">
                                    <i class="fas fa-play"></i> Stream starten
                                </button>
                            </div>
                        </div>
                        
                        <div class="equipment-showcase">
                            <h3>Studio Setup</h3>
                            <ul class="equipment-list">
                                <li><i class="fas fa-circle" aria-hidden="true"></i> Ableton Live 12</li>
                                <li><i class="fas fa-circle" aria-hidden="true"></i> Roland TR-909</li>
                                <li><i class="fas fa-circle" aria-hidden="true"></i> Moog Subsequent 37</li>
                                <li><i class="fas fa-circle" aria-hidden="true"></i> Native Instruments Maschine</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Party Dates Section -->
        <section id="party-dates" class="events-section" aria-label="Kommende Party Dates">
            <div class="container">
                <header class="section-header">
                    <h2 class="section-title">Party Dates</h2>
                    <p class="section-subtitle">Die nächsten Termine</p>
                </header>
                
                <div class="events-grid">
                    <!-- Dynamisch geladene Party Dates hier -->
                </div>
                
                <div class="section-cta">
                    <a href="partydates.php" class="btn-outline" aria-label="Alle Party Dates ansehen">
                        Alle Party Dates ansehen
                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- News Section -->
        <section id="news" class="news-section" aria-label="Neuigkeiten">
            <div class="container">
                <header class="section-header">
                    <h2 class="section-title">Latest News</h2>
                    <p class="section-subtitle">Updates aus dem Studio und der Szene</p>
                </header>
                
                <div class="news-grid">
                    <!-- Dynamisch geladene News-Beiträge hier -->
                </div>
                
                <div class="section-cta">
                    <a href="blog" class="btn-outline" aria-label="Alle News ansehen">
                        Alle News ansehen
                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="contact-section" aria-label="Kontakt">
            <div class="container">
                <header class="section-header">
                    <h2 class="section-title">Get in Touch</h2>
                    <p class="section-subtitle">Booking, Kollaborationen und Business-Anfragen</p>
                </header>
                
                <div class="contact-grid">
                    <div class="contact-info animate-on-scroll">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope" aria-hidden="true"></i>
                            </div>
                            <div class="contact-details">
                                <h3>Booking</h3>
                                <p>info@4amtechno.com</p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-music" aria-hidden="true"></i>
                            </div>
                            <div class="contact-details">
                                <h3>Label Anfragen</h3>
                                <p>info@4amtechno.com</p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-handshake" aria-hidden="true"></i>
                            </div>
                            <div class="contact-details">
                                <h3>Kollaborationen</h3>
                                <p>info@4amtechno.com</p>
                            </div>
                        </div>
                    </div>
                    
                    <form class="contact-form animate-on-scroll" action="contact.php" method="POST" aria-label="Kontaktformular">
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" id="name" name="name" required aria-required="true">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">E-Mail *</label>
                            <input type="email" id="email" name="email" required aria-required="true">
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Betreff</label>
                            <select id="subject" name="subject">
                                <option value="">Bitte wählen...</option>
                                <option value="booking">Booking Anfrage</option>
                                <option value="collaboration">Kollaboration</option>
                                <option value="label">Label Anfrage</option>
                                <option value="other">Sonstiges</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Nachricht *</label>
                            <textarea id="message" name="message" rows="5" required aria-required="true"></textarea>
                        </div>
                        
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane" aria-hidden="true"></i>
                            Nachricht senden
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </main>

<?php include_once $_SERVER["DOCUMENT_ROOT"] . "/includes/main-footer.php"; ?>

