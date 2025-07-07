/**
 * 4AM TECHNO Enhanced Website JavaScript
 * Author: R.I (based on E1nachser design)
 * Date: Juni 2025
 * Version: 2.0 - SEO Optimized & Enhanced
 */

// ===== UTILITY FUNCTIONS =====
const utils = {
    // Debounce function for performance optimization
    debounce: (func, wait) => {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Throttle function for scroll events
    throttle: (func, limit) => {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        }
    },

    // Check if element is in viewport
    isInViewport: (element) => {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    },

    // Smooth scroll to element
    smoothScrollTo: (element) => {
        const headerHeight = document.querySelector(".header-enhanced").offsetHeight;
        const elementPosition = element.offsetTop - headerHeight;
        
        window.scrollTo({
            top: elementPosition,
            behavior: "smooth"
        });
    }
};

// ===== LOADING SCREEN =====
class LoadingScreen {
    constructor() {
        this.loadingScreen = document.getElementById("loadingScreen");
        this.init();
    }

    init() {
        // Simulate loading time
        setTimeout(() => {
            this.hide();
        }, 2000);

        // Hide loading screen when page is fully loaded
        window.addEventListener("load", () => {
            setTimeout(() => {
                this.hide();
            }, 500);
        });
    }

    hide() {
        if (this.loadingScreen) {
            this.loadingScreen.classList.add("hidden");
            setTimeout(() => {
                this.loadingScreen.style.display = "none";
            }, 500);
        }
    }
}

// ===== NAVIGATION =====
class Navigation {
    constructor() {
        this.header = document.querySelector(".header-enhanced");
        this.hamburger = document.querySelector(".hamburger-enhanced");
        this.nav = document.querySelector(".main-nav-enhanced");
        this.navLinks = document.querySelectorAll(".main-nav-enhanced a");
        this.lastScrollY = window.scrollY;
        
        this.init();
    }

    init() {
        this.setupHamburgerMenu();
        this.setupSmoothScrolling();
        this.setupScrollEffects();
        this.setupActiveNavigation();
    }

    setupHamburgerMenu() {
        if (this.hamburger && this.nav) {
            this.hamburger.addEventListener("click", () => {
                this.toggleMobileMenu();
            });

            // Close menu when clicking on nav links
            this.navLinks.forEach(link => {
                link.addEventListener("click", () => {
                    this.closeMobileMenu();
                });
            });

            // Close menu when clicking outside
            document.addEventListener("click", (e) => {
                if (!this.header.contains(e.target)) {
                    this.closeMobileMenu();
                }
            });
        }
    }

    toggleMobileMenu() {
        const isExpanded = this.hamburger.getAttribute("aria-expanded") === "true";
        
        this.hamburger.setAttribute("aria-expanded", !isExpanded);
        this.hamburger.classList.toggle("active");
        this.nav.classList.toggle("active");
        
        // Prevent body scroll when menu is open
        document.body.style.overflow = isExpanded ? "auto" : "hidden";
    }

    closeMobileMenu() {
        this.hamburger.setAttribute("aria-expanded", "false");
        this.hamburger.classList.remove("active");
        this.nav.classList.remove("active");
        document.body.style.overflow = "auto";
    }

    setupSmoothScrolling() {
        this.navLinks.forEach(link => {
            link.addEventListener("click", (e) => {
                const href = link.getAttribute("href");
                
                if (href.startsWith("#")) {
                    e.preventDefault();
                    const targetId = href.substring(1);
                    const targetElement = document.getElementById(targetId);
                    
                    if (targetElement) {
                        utils.smoothScrollTo(targetElement);
                    }
                }
            });
        });
    }

    setupScrollEffects() {
        const handleScroll = utils.throttle(() => {
            const currentScrollY = window.scrollY;
            
            // Header background opacity based on scroll
            if (currentScrollY > 100) {
                this.header.style.background = "rgba(18, 18, 18, 0.95)";
            } else {
                this.header.style.background = "rgba(18, 18, 18, 0.8)";
            }
            
            this.lastScrollY = currentScrollY;
        }, 10);

        window.addEventListener("scroll", handleScroll);
    }

    setupActiveNavigation() {
        const sections = document.querySelectorAll("section[id]");
        
        const handleScroll = utils.throttle(() => {
            const scrollPosition = window.scrollY + 200;
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                const sectionId = section.getAttribute("id");
                
                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                    // Remove active class from all nav links
                    this.navLinks.forEach(link => {
                        link.removeAttribute("aria-current");
                    });
                    
                    // Add active class to current section nav link
                    const activeLink = document.querySelector(`a[href="#${sectionId}"]`);
                    if (activeLink) {
                        activeLink.setAttribute("aria-current", "page");
                    }
                }
            });
        }, 100);

        window.addEventListener("scroll", handleScroll);
    }
}

// ===== 3D CANVAS EFFECT (Three.js Integration) =====
// ===== SCROLL ANIMATIONS =====
class ScrollAnimations {
    constructor() {
        this.animatedElements = document.querySelectorAll(".animate-on-scroll");
        this.init();
    }

    init() {
        this.setupIntersectionObserver();
    }

    setupIntersectionObserver() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: "0px 0px -50px 0px"
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("animate-in");
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        this.animatedElements.forEach(element => {
            observer.observe(element);
        });
    }
}

// ===== SCROLL TO TOP =====
class ScrollToTop {
    constructor() {
        this.button = document.getElementById("scrollToTop");
        this.init();
    }

    init() {
        if (this.button) {
            this.setupScrollVisibility();
            this.setupClickHandler();
        }
    }

    setupScrollVisibility() {
        const handleScroll = utils.throttle(() => {
            if (window.scrollY > 300) {
                this.button.classList.add("visible");
            } else {
                this.button.classList.remove("visible");
            }
        }, 100);

        window.addEventListener("scroll", handleScroll);
    }

    setupClickHandler() {
        this.button.addEventListener("click", (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });
    }
}

// ===== STATISTICS COUNTER =====
class StatisticsCounter {
    constructor() {
        this.statNumbers = document.querySelectorAll(".stat-number");
        this.init();
    }

    init() {
        this.setupCounterAnimation();
    }

    setupCounterAnimation() {
        const observerOptions = {
            threshold: 0.5,
            rootMargin: "0px"
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        this.statNumbers.forEach(element => {
            observer.observe(element);
        });
    }

    animateCounter(element) {
        const target = parseInt(element.getAttribute("data-target"));
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps
        let current = 0;

        const updateCounter = () => {
            current += increment;
            if (current < target) {
                element.textContent = Math.floor(current).toLocaleString();
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target.toLocaleString();
            }
        };

        updateCounter();
    }
}

// ===== VIDEO PLAYER =====
class VideoPlayer {
    constructor() {
        this.playButtons = document.querySelectorAll(".play-btn, .play-button-large, .video-play-btn");
        this.likeButtons = document.querySelectorAll(".btn-like");
        this.init();
    }

    init() {
        this.setupPlayButtons();
        this.setupLikeButtons();
    }

    setupPlayButtons() {
        this.playButtons.forEach(button => {
            button.addEventListener("click", (e) => {
                e.preventDefault();
                const videoId = button.getAttribute('data-video-id');
                if (videoId) {
                    this.openYouTubeVideo(videoId);
                }
            });
        });
    }

    openYouTubeVideo(videoId) {
        const youtubeUrl = `https://www.youtube.com/watch?v={videoId}`;
        window.open(youtubeUrl, '_blank', 'noopener,noreferrer');
    }

    setupLikeButtons() {
        this.likeButtons.forEach(button => {
            button.addEventListener("click", (e) => {
                e.preventDefault();
                this.toggleLike(button);
            });
        });
    }

    toggleLike(button) {
        const icon = button.querySelector("i");
        const isLiked = icon.classList.contains("fas");
        
        if (isLiked) {
            icon.classList.remove("fas", "fa-heart");
            icon.classList.add("far", "fa-heart");
            button.style.borderColor = "var(--color-text-primary)";
            button.style.color = "var(--color-text-primary)";
        } else {
            icon.classList.remove("far", "fa-heart");
            icon.classList.add("fas", "fa-heart");
            button.style.borderColor = "var(--color-error)";
            button.style.color = "var(--color-error)";
        }
        
        // Add animation
        button.style.transform = "scale(1.1)";
        setTimeout(() => {
            button.style.transform = "";
        }, 200);
    }
}

// ===== CONTACT FORM =====
class ContactForm {
    constructor() {
        this.form = document.querySelector(".contact-form");
        this.init();
    }

    init() {
        if (this.form) {
            this.setupFormValidation();
            this.setupFormSubmission();
        }
    }

    setupFormValidation() {
        const inputs = this.form.querySelectorAll("input, textarea, select");
        
        inputs.forEach(input => {
            input.addEventListener("blur", () => {
                this.validateField(input);
            });
            
            input.addEventListener("input", () => {
                this.clearFieldError(input);
            });
        });
    }

    validateField(field) {
        const value = field.value.trim();
        const isRequired = field.hasAttribute("required");
        
        if (isRequired && !value) {
            this.showFieldError(field, "Dieses Feld ist erforderlich");
            return false;
        }
        
        if (field.type === "email" && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                this.showFieldError(field, "Bitte geben Sie eine gültige E-Mail-Adresse ein");
                return false;
            }
        }
        
        this.clearFieldError(field);
        return true;
    }

    showFieldError(field, message) {
        this.clearFieldError(field);
        
        field.style.borderColor = "var(--color-error)";
        
        const errorElement = document.createElement("div");
        errorElement.className = "field-error";
        errorElement.textContent = message;
        errorElement.style.color = "var(--color-error)";
        errorElement.style.fontSize = "var(--font-size-sm)";
        errorElement.style.marginTop = "var(--spacing-xs)";
        
        field.parentNode.appendChild(errorElement);
    }

    clearFieldError(field) {
        field.style.borderColor = "";
        const errorElement = field.parentNode.querySelector(".field-error");
        if (errorElement) {
            errorElement.remove();
        }
    }

    setupFormSubmission() {
        this.form.addEventListener("submit", (e) => {
            e.preventDefault();
            
            const inputs = this.form.querySelectorAll("input, textarea, select");
            let isValid = true;
            
            inputs.forEach(input => {
                if (!this.validateField(input)) {
                    isValid = false;
                }
            });
            
            if (isValid) {
                this.submitForm();
            }
        });
    }

    async submitForm() {
        const submitButton = this.form.querySelector(".btn-submit");
        const originalText = submitButton.innerHTML;
        
        // Show loading state
        submitButton.innerHTML = "<i class=\"fas fa-spinner fa-spin\"></i> Wird gesendet...";
        submitButton.disabled = true;
        
        try {
            // Submit to contact.php with info@4amtechno.com
            const formData = new FormData(this.form);
            formData.append('to_email', 'info@4amtechno.com');
            
            const response = await fetch('contact.php', {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                this.showSuccessMessage();
                this.form.reset();
            } else {
                throw new Error('Server error');
            }
            
        } catch (error) {
            this.showErrorMessage();
        } finally {
            // Reset button
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }
    }

    showSuccessMessage() {
        const message = document.createElement("div");
        message.className = "form-message success";
        message.innerHTML = "<i class=\"fas fa-check-circle\"></i> Nachricht erfolgreich gesendet!";
        message.style.cssText = `
            background: var(--color-success);
            color: white;
            padding: var(--spacing-md);
            border-radius: var(--radius-md);
            margin-top: var(--spacing-lg);
            text-align: center;
            opacity: 0;
            animation: fadeIn 0.5s forwards;
        `;
        this.form.parentNode.insertBefore(message, this.form.nextSibling);
        setTimeout(() => message.remove(), 5000);
    }

    showErrorMessage() {
        const message = document.createElement("div");
        message.className = "form-message error";
        message.innerHTML = "<i class=\"fas fa-exclamation-circle\"></i> Fehler beim Senden der Nachricht. Bitte versuchen Sie es später erneut.";
        message.style.cssText = `
            background: var(--color-error);
            color: white;
            padding: var(--spacing-md);
            border-radius: var(--radius-md);
            margin-top: var(--spacing-lg);
            text-align: center;
            opacity: 0;
            animation: fadeIn 0.5s forwards;
        `;
        this.form.parentNode.insertBefore(message, this.form.nextSibling);
        setTimeout(() => message.remove(), 5000);
    }
}

// ===== DYNAMIC CONTENT LOADER =====
class DynamicContentLoader {
    constructor() {
        this.youtubeApiKey = 'AIzaSyADqbmAnH1bqjSpWQ80DTCyoIAXoceokrE'; // User needs to replace this with real API key
        this.youtubeChannelId = 'UCSIQsJz88OfyACfTr3hZ7vA'; // 4AM Techno Channel ID
        this.init();
    }

    init() {
        this.loadYouTubeVideos();
        this.loadPartyDates();
        this.loadNews();
    }

    // ===== YOUTUBE VIDEOS =====
    async loadYouTubeVideos() {
        const videoGrid = document.querySelector('.video-grid');
        if (!videoGrid) return;

        try {
            const videos = await this.fetchYouTubeVideos();
            this.renderVideos(videos, videoGrid);
        } catch (error) {
            console.error('Error loading YouTube videos:', error);
            this.renderVideoPlaceholders(videoGrid);
        }
    }

async fetchYouTubeVideos() {
        try {
            // Ruft jetzt sicher deine eigene PHP-Datei auf
            const response = await fetch('youtube-proxy.php');
            
            if (!response.ok) {
                // Gibt einen Fehler aus, wenn die PHP-Datei ein Problem meldet
                throw new Error('Proxy-Anfrage fehlgeschlagen. Status: ' + response.status);
            }
            
            const data = await response.json();
            
            // Prüft auf Fehler, die von der YouTube API kommen (über dein PHP Skript)
            if (data.error) {
                throw new Error('YouTube API Fehler: ' + data.error.message);
            }

            return data.items;

        } catch (error) {
            console.error('Fehler beim Abrufen der YouTube-Videos über den Proxy:', error);
            // Hier kann deine Fallback-Lösung (Demo-Daten) bleiben, falls alles schiefgeht
            console.log('Using demo data for YouTube videos');
            return [
                { id: { videoId: 'dQw4w9WgXcQ' }, snippet: { title: 'Fallback Video 1', description: 'Beschreibung...', publishedAt: '2025-06-10T00:00:00Z', thumbnails: { medium: { url: 'images/track1.webp' } } } },
                { id: { videoId: 'dQw4w9WgXcQ' }, snippet: { title: 'Fallback Video 2', description: 'Beschreibung...', publishedAt: '2025-06-08T00:00:00Z', thumbnails: { medium: { url: 'images/track2.webp' } } } },
                { id: { videoId: 'dQw4w9WgXcQ' }, snippet: { title: 'Fallback Video 3', description: 'Beschreibung...', publishedAt: '2025-06-05T00:00:00Z', thumbnails: { medium: { url: 'images/track3.webp' } } } }
            ];
        }
    }
    renderVideos(videos, container) {
        container.innerHTML = '';
        
        videos.forEach(video => {
            const videoCard = this.createVideoCard(video);
            container.appendChild(videoCard);
        });
    }

    createVideoCard(video) {
        const card = document.createElement('article');
        card.className = 'video-card animate-on-scroll';
        card.setAttribute('role', 'listitem');

        const publishDate = new Date(video.snippet.publishedAt).toLocaleDateString('de-DE');

        card.innerHTML = `
            <div class="video-thumbnail">
                <img src="${video.snippet.thumbnails.medium.url}" alt="${video.snippet.title} Thumbnail" loading="lazy">
                <div class="video-play-overlay">
                    <button class="video-play-btn" aria-label="${video.snippet.title} abspielen" data-video-id="${video.id.videoId}">
                        <i class="fas fa-play" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
            <div class="video-info">
                <h3>${video.snippet.title}</h3>
                <p class="video-meta">${publishDate} • YouTube</p>
                <p class="video-description">${video.snippet.description.substring(0, 100)}...</p>
            </div>
        `;

        return card;
    }

    renderVideoPlaceholders(container) {
        container.innerHTML = `
            <div class="video-placeholder">
                <p>Videos werden geladen...</p>
                <p><small>Für die vollständige Funktionalität wird ein YouTube API-Schlüssel benötigt.</small></p>
            </div>
        `;
    }

    // ===== PARTY DATES =====
    async loadPartyDates() {
        const eventsGrid = document.querySelector('.events-grid');
        if (!eventsGrid) {
            console.log('Events grid not found');
            return;
        }

        try {
            const events = await this.fetchPartyDates();
            console.log('Loaded party dates:', events);
            this.renderEvents(events, eventsGrid);
        } catch (error) {
            console.error('Error loading party dates:', error);
            this.renderEventPlaceholders(eventsGrid);
        }
    }

    async fetchPartyDates() {
        try {
            const response = await fetch('partydates.php?limit=9');
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const data = await response.json();
            console.log('Party dates response:', data);
            return data;
        } catch (error) {
            console.log('Using demo data for party dates');
            // Demo data for now
            return [
                {
                    name: 'Berghain Nacht',
                    location: 'Berlin, Deutschland',
                    date: '2025-07-15',
                    link: '#',
                    image: 'images/party-placeholder.webp'
                },
                {
                    name: 'Underground Festival',
                    location: 'Hamburg, Deutschland',
                    date: '2025-07-22',
                    link: '#',
                    image: 'images/party-placeholder.webp'
                },
                {
                    name: 'Techno Warehouse',
                    location: 'München, Deutschland',
                    date: '2025-08-05',
                    link: '#',
                    image: 'images/party-placeholder.webp'
                }
            ];
        }
    }

    renderEvents(events, container) {
        container.innerHTML = '';
        
        events.forEach(event => {
            const eventCard = this.createEventCard(event);
            container.appendChild(eventCard);
        });
    }

    createEventCard(event) {
        const card = document.createElement('article');
        card.className = 'event-card animate-on-scroll';

        const eventDate = new Date(event.date);
        const day = eventDate.getDate();
        const month = eventDate.toLocaleDateString('de-DE', { month: 'short' }).toUpperCase();

        card.innerHTML = `
            <div class="event-date">
                <span class="day">${day}</span>
                <span class="month">${month}</span>
            </div>
            <div class="event-info">
                <h3>${event.name}</h3>
                <div class="event-location">
                    <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                    <span>${event.location}</span>
                </div>
                <div class="event-time">
                    <i class="fas fa-clock" aria-hidden="true"></i>
                    <span>${event.date}</span>
                </div>
                ${event.link ? `<a href="${event.link}" class="btn-tickets" aria-label="Tickets für ${event.name} kaufen">
                    Tickets
                </a>` : ''}
            </div>
        `;

        return card;
    }

    renderEventPlaceholders(container) {
        container.innerHTML = `
            <div class="event-placeholder">
                <p>Party Dates werden geladen...</p>
                <p><small>Verbindung zu partydates.php wird hergestellt...</small></p>
            </div>
        `;
    }

    // ===== NEWS =====
    async loadNews() {
        const newsGrid = document.querySelector('.news-grid');
        if (!newsGrid) {
            console.log('News grid not found');
            return;
        }

        try {
            const news = await this.fetchNews();
            console.log('Loaded news:', news);
            this.renderNews(news, newsGrid);
        } catch (error) {
            console.error('Error loading news:', error);
            this.renderNewsPlaceholders(newsGrid);
        }
    }

    async fetchNews() {
        try {
            const response = await fetch('blog.php?limit=3');
            if (!response.ok) {
                throw new Error('Failed to fetch news');
            }
            const data = await response.json();
            console.log('News response:', data);
            return data;
        } catch (error) {
            console.log('Using demo data for news');
            // Demo data for now
            return [
                {
                    id: 1,
                    title: 'Neues Album in Arbeit',
                    excerpt: 'Die Arbeiten am neuen Album "Midnight Sessions" sind in vollem Gange. Erwartet dunkle Atmosphären und treibende Beats...',
                    category: 'Studio Update',
                    date: '2025-06-10',
                    image: 'images/track1.webp',
                    slug: 'neues-album-in-arbeit'
                },
                {
                    id: 2,
                    title: 'Kollaboration mit Underground-Label',
                    excerpt: 'Freue mich, eine neue Partnerschaft mit einem führenden Underground-Techno-Label bekannt zu geben...',
                    category: 'Kollaboration',
                    date: '2025-06-05',
                    image: 'images/track2.webp',
                    slug: 'kollaboration-underground-label'
                },
                {
                    id: 3,
                    title: 'Deutschland-Tour angekündigt',
                    excerpt: 'Die "Dark Frequencies" Tour führt durch die wichtigsten Techno-Clubs Deutschlands. Tickets ab sofort verfügbar...',
                    category: 'Tour',
                    date: '2025-06-01',
                    image: 'images/track3.webp',
                    slug: 'deutschland-tour-angekundigt'
                }
            ];
        }
    }

    renderNews(news, container) {
        container.innerHTML = '';
        
        news.forEach(article => {
            const newsCard = this.createNewsCard(article);
            container.appendChild(newsCard);
        });
    }

    createNewsCard(article) {
        const card = document.createElement('article');
        card.className = 'news-card animate-on-scroll';

        const publishDate = new Date(article.date).toLocaleDateString('de-DE');

        card.innerHTML = `
            <div class="news-image">
                <img src="${article.image}" alt="${article.title}" loading="lazy">
                <div class="news-category">${article.category}</div>
            </div>
            <div class="news-content">
                <div class="news-date">${publishDate}</div>
                <h3>${article.title}</h3>
                <p>${article.excerpt}</p>
                <a href="blog.php?article=${article.slug}" class="news-link">
                    Weiterlesen
                </a>
            </div>
        `;

        return card;
    }

    renderNewsPlaceholders(container) {
        container.innerHTML = `
            <div class="news-placeholder">
                <p>News werden geladen...</p>
                <p><small>Verbindung zu blog.php wird hergestellt...</small></p>
            </div>
        `;
    }
}

// ===== YOUTUBE LIVE STREAM HANDLER =====
class YouTubeLiveStream {
    constructor() {
        this.streamContainer = document.querySelector('.youtube-live-stream');
        this.init();
    }

    init() {
        if (this.streamContainer) {
            this.setupStreamErrorHandling();
        }
    }

    setupStreamErrorHandling() {
        const iframe = this.streamContainer.querySelector('iframe');
        if (iframe) {
            iframe.addEventListener('error', () => {
                this.showStreamFallback();
            });
        }
    }

    showStreamFallback() {
        this.streamContainer.innerHTML = `
            <div class="stream-fallback">
                <div class="stream-fallback-content">
                    <i class="fab fa-youtube" style="font-size: 3rem; color: var(--color-error); margin-bottom: 1rem;"></i>
                    <h3>Live Stream nicht verfügbar</h3>
                    <p>Der 24/7 Live Stream ist momentan offline.</p>
                    <a href="https://www.youtube.com/@4AMTechno" target="_blank" rel="noopener noreferrer" class="btn-secondary">
                        YouTube Kanal besuchen
                    </a>
                </div>
            </div>
        `;
    }
}

// ===== MAIN INITIALIZATION =====
document.addEventListener("DOMContentLoaded", () => {
    // Initialize all components
    new LoadingScreen();
    new Navigation();
    new ScrollAnimations();
    new ScrollToTop();
    new StatisticsCounter();
    new VideoPlayer();
    new ContactForm();
    new DynamicContentLoader();
    new YouTubeLiveStream();
    
    console.log('4AM Techno website initialized');

    // ===== FIX FÜR ANKER-LINKS VON EXTERNEN SEITEN =====
    window.addEventListener('load', () => {
        // Prüft, ob ein Anker in der URL ist (z.B. #contact)
        if (window.location.hash) {
            const targetId = window.location.hash.substring(1);
            const targetElement = document.getElementById(targetId);

            // Wenn das Element auf der Seite existiert...
            if (targetElement) {
                // ...warte einen ganz kleinen Moment, damit alles bereit ist...
                setTimeout(() => {
                    // ...und führe den präzisen Sprung erneut aus.
                    utils.smoothScrollTo(targetElement);
                }, 100); // 100 Millisekunden Verzögerung für absolute Sicherheit
            }
        }
    });
});




// ===== BLOG TEMPLATE FUNCTIONS (Integrated) =====





// ===== NEWS PAGE FUNCTIONS (Integrated) =====



/**
 * Blog Template JavaScript
 * Funktionen für Blog-Seiten
 */

// Blog Template Funktionen
const BlogTemplate = {
    // Initialisierung
    init() {
        this.loadRecentPosts();
        this.initSocialShare();
        this.initScrollProgress();
        this.initReadingTime();
    },

    // Lade aktuelle Blog-Posts für die Sidebar
    async loadRecentPosts() {
        const recentPostsContainer = document.getElementById("recent-posts");
        if (!recentPostsContainer) return;

        try {
            const response = await fetch("../blog.php?limit=18");
            const posts = await response.json();
            
            recentPostsContainer.innerHTML = "";
            posts.slice(0, 4).forEach(post => {
                const postElement = this.createRecentPostElement(post);
                recentPostsContainer.appendChild(postElement);
            });
        } catch (error) {
            console.error("Fehler beim Laden der aktuellen Posts:", error);
            recentPostsContainer.innerHTML = ";Fehler beim Laden der Posts.";
        }
    },

    // Erstelle Element für aktuelle Posts
    createRecentPostElement(post) {
        const postDiv = document.createElement("div");
        postDiv.className = "recent-post";
        
        const publishDate = new Date(post.date).toLocaleDateString("de-DE");
        
        postDiv.innerHTML = `
            <img src="../${post.image}" alt="${post.title}" class="recent-post-image" loading="lazy">
            <div class="recent-post-content">
                <h4><a href="../${post.url}">${post.title}</a></h4>
                <div class="recent-post-date">${publishDate}</div>
            </div>
        `;
        
        return postDiv;
    },

    // Social Share Funktionen
    initSocialShare() {
        // Facebook Share
        window.shareOnFacebook = () => {
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, "_blank", "width=600,height=400");
        };

        // Twitter Share
        window.shareOnTwitter = () => {
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, "_blank", "width=600,height=400");
        };

        // Copy Link
        window.copyToClipboard = async () => {
            try {
                await navigator.clipboard.writeText(window.location.href);
                this.showNotification("Link in Zwischenablage kopiert!", "success");
            } catch (err) {
                // Fallback für ältere Browser
                const textArea = document.createElement("textarea");
                textArea.value = window.location.href;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand("copy");
                document.body.removeChild(textArea);
                this.showNotification("Link in Zwischenablage kopiert!", "success");
            }
        };
    },

    // Scroll Progress Indicator
    initScrollProgress() {
        const progressBar = document.createElement("div");
        progressBar.className = "scroll-progress";
        progressBar.innerHTML = "<div class=\"scroll-progress-bar\"></div>";
        document.body.appendChild(progressBar);

        const progressBarFill = progressBar.querySelector(".scroll-progress-bar");

        window.addEventListener("scroll", () => {
            const scrollTop = window.pageYOffset;
            const docHeight = document.body.scrollHeight - window.innerHeight;
            const scrollPercent = (scrollTop / docHeight) * 100;
            
            progressBarFill.style.width = scrollPercent + "%";
        });

        // CSS für Progress Bar
        const style = document.createElement("style");
        style.textContent = `
            .scroll-progress {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 3px;
                background: rgba(255, 255, 255, 0.1);
                z-index: 9999;
            }
            .scroll-progress-bar {
                height: 100%;
                background: linear-gradient(90deg, #ff6b35, #ff8c42);
                width: 0%;
                transition: width 0.1s ease;
            }
        `;
        document.head.appendChild(style);
    },

    // Lesezeit berechnen
    initReadingTime() {
        const content = document.querySelector(".blog-text");
        if (!content) return;

        const text = content.innerText;
        const wordsPerMinute = 200;
        const words = text.trim().split(/\s+/).length;
        const readingTime = Math.ceil(words / wordsPerMinute);

        // Lesezeit zur Meta-Information hinzufügen
        const blogMeta = document.querySelector(".blog-meta");
        if (blogMeta) {
            const readingTimeElement = document.createElement("div");
            readingTimeElement.className = "meta-item";
            readingTimeElement.innerHTML = `
                <i class="fas fa-clock"></i>
                <span>${readingTime} Min. Lesezeit</span>
            `;
            blogMeta.appendChild(readingTimeElement);
        }
    },

    // Notification anzeigen
    showNotification(message, type = "info") {
        const notification = document.createElement("div");
        notification.className = `notification notification-${type}`;
        notification.textContent = message;

        // CSS für Notifications
        const style = document.createElement("style");
        style.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 1rem 1.5rem;
                border-radius: 8px;
                color: white;
                font-weight: 500;
                z-index: 10000;
                transform: translateX(100%);
                transition: transform 0.3s ease;
            }
            .notification-success {
                background: linear-gradient(135deg, #4CAF50, #45a049);
            }
            .notification-error {
                background: linear-gradient(135deg, #F44336, #d32f2f);
            }
            .notification-info {
                background: linear-gradient(135deg, #2196F3, #1976d2);
            }
            .notification.show {
                transform: translateX(0);
            }
        `;
        
        if (!document.querySelector("style[data-notifications]")) {
            style.setAttribute("data-notifications", "true");
            document.head.appendChild(style);
        }

        document.body.appendChild(notification);

        // Animation
        setTimeout(() => notification.classList.add("show"), 100);
        
        // Auto-remove
        setTimeout(() => {
            notification.classList.remove("show");
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    },

    // Lazy Loading für Bilder
    initLazyLoading() {
        const images = document.querySelectorAll("img[loading=\"lazy\"]");
        
        if ("IntersectionObserver" in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src || img.src;
                        img.classList.remove("lazy");
                        imageObserver.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));
        }
    }
};

// Navigation Enhancement für Blog-Seiten
const BlogNavigation = {
    init() {
        this.handleMobileMenu();
        this.handleSmoothScroll();
    },

    handleMobileMenu() {
        const hamburger = document.querySelector(".hamburger");
        const navMenu = document.querySelector(".nav-menu");

        if (hamburger && navMenu) {
            hamburger.addEventListener("click", () => {
                hamburger.classList.toggle("active");
                navMenu.classList.toggle("active");
            });

            // Close menu when clicking on a link
            document.querySelectorAll(".nav-menu a").forEach(link => {
                link.addEventListener("click", () => {
                    hamburger.classList.remove("active");
                    navMenu.classList.remove("active");
                });
            });
        }
    },

    handleSmoothScroll() {
        document.querySelectorAll("a[href^=\"#\"]").forEach(anchor => {
            anchor.addEventListener("click", function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute("href"));
                if (target) {
                    target.scrollIntoView({
                        behavior: "smooth",
                        block: "start"
                    });
                }
            });
        });
    }
};

// Initialisierung wenn DOM geladen ist
document.addEventListener("DOMContentLoaded", function() {
    BlogTemplate.init();
    BlogNavigation.init();
    
    // Lazy Loading initialisieren
    BlogTemplate.initLazyLoading();
    
    // Scroll-to-Top Button
    const scrollToTopBtn = document.createElement("button");
    scrollToTopBtn.className = "scroll-to-top";
    scrollToTopBtn.innerHTML = "<i class=\"fas fa-arrow-up\"></i>";
    scrollToTopBtn.setAttribute("aria-label", "Nach oben scrollen");
    document.body.appendChild(scrollToTopBtn);

    // CSS für Scroll-to-Top Button
    const style = document.createElement("style");
    style.textContent = `
        .scroll-to-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            border: none;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b35, #ff8c42);
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
        }
        .scroll-to-top.visible {
            opacity: 1;
            visibility: visible;
        }
        .scroll-to-top:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
        }
        @media (max-width: 768px) {
            .scroll-to-top {
                bottom: 1rem;
                right: 1rem;
                width: 45px;
                height: 45px;
            }
        }
    `;
    document.head.appendChild(style);

    // Scroll-to-Top Funktionalität
    window.addEventListener("scroll", () => {
        if (window.pageYOffset > 300) {
            scrollToTopBtn.classList.add("visible");
        } else {
            scrollToTopBtn.classList.remove("visible");
        }
    });

    scrollToTopBtn.addEventListener("click", () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    });
});




/**
 * News Page JavaScript
 * Funktionen für die News-Übersichtsseite
 */

class NewsPage {
    constructor() {
        this.currentFilter = 'all';
        this.articlesLoaded = 6;
        this.articlesPerLoad = 6;
        this.allArticles = [];
        this.filteredArticles = [];
        
        this.init();
    }

    async init() {
        await this.loadAllArticles();
        this.initFilters();
        this.initLoadMore();
        this.initMobileMenu();
        this.displayArticles();
    }

    async loadAllArticles() {
        try {
            const response = await fetch('/blog.php?limit=50');
            this.allArticles = await response.json();
            this.filteredArticles = [...this.allArticles];
        } catch (error) {
            console.error('Fehler beim Laden der Artikel:', error);
            this.showError('Fehler beim Laden der Artikel.');
        }
    }

    initFilters() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        
        filterButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                // Aktiven Button aktualisieren
                filterButtons.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                
                // Filter anwenden
                this.currentFilter = e.target.dataset.category;
                this.applyFilter();
            });
        });
    }

    applyFilter() {
        if (this.currentFilter === 'all') {
            this.filteredArticles = [...this.allArticles];
        } else {
            this.filteredArticles = this.allArticles.filter(article => 
                article.category.toLowerCase().replace(' ', '-') === this.currentFilter
            );
        }
        
        this.articlesLoaded = Math.min(6, this.filteredArticles.length);
        this.displayArticles();
        this.updateLoadMoreButton();
    }

    displayArticles() {
        const newsGrid = document.querySelector('.news-grid-compact');
        if (!newsGrid) return;

        newsGrid.innerHTML = '';
        
        const articlesToShow = this.filteredArticles.slice(0, this.articlesLoaded);
        
        articlesToShow.forEach((article, index) => {
            const articleElement = this.createArticleElement(article);
            articleElement.style.animationDelay = `${index * 0.1}s`;
            newsGrid.appendChild(articleElement);
        });
    }

    createArticleElement(article) {
        const articleDiv = document.createElement('article');
        articleDiv.className = 'news-card fade-in';
        
        const publishDate = new Date(article.date).toLocaleDateString('de-DE');
        
        articleDiv.innerHTML = `
            <div class="news-image">
                <img src="${article.image}" alt="${article.title}" loading="lazy">
                <div class="news-category">${article.category}</div>
            </div>
            <div class="news-content-area">
                <div class="news-date">
                    <i class="fas fa-calendar"></i>
                    ${publishDate}
                </div>
                <h3>${article.title}</h3>
                <p>${article.excerpt}</p>
                <a href="${article.url}" class="news-link">
                    Weiterlesen
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        `;
        
        return articleDiv;
    }

    initLoadMore() {
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        if (!loadMoreBtn) return;

        loadMoreBtn.addEventListener('click', () => {
            this.loadMoreArticles();
        });
        
        this.updateLoadMoreButton();
    }

    loadMoreArticles() {
        const previousCount = this.articlesLoaded;
        this.articlesLoaded = Math.min(
            this.articlesLoaded + this.articlesPerLoad, 
            this.filteredArticles.length
        );
        
        const newsGrid = document.querySelector('.news-grid-compact');
        const newArticles = this.filteredArticles.slice(previousCount, this.articlesLoaded);
        
        newArticles.forEach((article, index) => {
            const articleElement = this.createArticleElement(article);
            articleElement.style.animationDelay = `${index * 0.1}s`;
            newsGrid.appendChild(articleElement);
        });
        
        this.updateLoadMoreButton();
        
        // Smooth scroll zum ersten neuen Artikel
        if (newArticles.length > 0) {
            const firstNewArticle = newsGrid.children[previousCount];
            if (firstNewArticle) {
                setTimeout(() => {
                    firstNewArticle.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                }, 300);
            }
        }
    }

    updateLoadMoreButton() {
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        if (!loadMoreBtn) return;

        if (this.articlesLoaded >= this.filteredArticles.length) {
            loadMoreBtn.style.display = 'none';
        } else {
            loadMoreBtn.style.display = 'inline-flex';
            const remaining = this.filteredArticles.length - this.articlesLoaded;
            loadMoreBtn.innerHTML = `
                <i class="fas fa-plus"></i>
                ${remaining} weitere Artikel laden
            `;
        }
    }

    initMobileMenu() {
        const hamburger = document.querySelector('.hamburger');
        const navMenu = document.querySelector('.nav-menu');

        if (hamburger && navMenu) {
            hamburger.addEventListener('click', () => {
                hamburger.classList.toggle('active');
                navMenu.classList.toggle('active');
                document.body.classList.toggle('menu-open');
            });

            // Menü schließen beim Klick auf einen Link
            document.querySelectorAll('.nav-menu a').forEach(link => {
                link.addEventListener('click', () => {
                    hamburger.classList.remove('active');
                    navMenu.classList.remove('active');
                    document.body.classList.remove('menu-open');
                });
            });

            // Menü schließen beim Klick außerhalb
            document.addEventListener('click', (e) => {
                if (!hamburger.contains(e.target) && !navMenu.contains(e.target)) {
                    hamburger.classList.remove('active');
                    navMenu.classList.remove('active');
                    document.body.classList.remove('menu-open');
                }
            });
        }
    }

    showError(message) {
        const newsGrid = document.querySelector('.news-grid-compact');
        if (newsGrid) {
            newsGrid.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>${message}</p>
                </div>
            `;
        }
    }

    showLoading() {
        const newsGrid = document.querySelector('.news-grid-compact');
        if (newsGrid) {
            newsGrid.innerHTML = `
                <div class="loading">
                    <i class="fas fa-spinner"></i>
                    <p>Artikel werden geladen...</p>
                </div>
            `;
        }
    }
}

// Search Funktionalität
class NewsSearch {
    constructor() {
        this.searchInput = null;
        this.searchResults = [];
        this.init();
    }

    init() {
        this.createSearchBar();
        this.initSearch();
    }

    createSearchBar() {
        const filtersSection = document.querySelector('.news-filters .container');
        if (!filtersSection) return;

        const searchContainer = document.createElement('div');
        searchContainer.className = 'search-container';
        searchContainer.innerHTML = `
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Artikel durchsuchen..." id="newsSearch">
                <button class="search-clear" id="searchClear" style="display: none;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        filtersSection.appendChild(searchContainer);
        this.searchInput = document.getElementById('newsSearch');
        this.searchClear = document.getElementById('searchClear');

        // CSS für Suchleiste
        const style = document.createElement('style');
        style.textContent = `
            .search-container {
                margin-top: 1.5rem;
                display: flex;
                justify-content: center;
            }
            .search-bar {
                position: relative;
                max-width: 400px;
                width: 100%;
            }
            .search-bar i {
                position: absolute;
                left: 1rem;
                top: 50%;
                transform: translateY(-50%);
                color: rgba(255, 255, 255, 0.5);
            }
            .search-bar input {
                width: 100%;
                padding: 0.8rem 1rem 0.8rem 2.5rem;
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 25px;
                color: #ffffff;
                font-size: 0.9rem;
                backdrop-filter: blur(10px);
                transition: all 0.3s ease;
            }
            .search-bar input:focus {
                outline: none;
                border-color: #ff6b35;
                box-shadow: 0 0 10px rgba(255, 107, 53, 0.3);
            }
            .search-bar input::placeholder {
                color: rgba(255, 255, 255, 0.5);
            }
            .search-clear {
                position: absolute;
                right: 0.5rem;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                color: rgba(255, 255, 255, 0.5);
                cursor: pointer;
                padding: 0.5rem;
                border-radius: 50%;
                transition: all 0.3s ease;
            }
            .search-clear:hover {
                color: #ff6b35;
                background: rgba(255, 107, 53, 0.1);
            }
        `;
        document.head.appendChild(style);
    }

    initSearch() {
        if (!this.searchInput) return;

        let searchTimeout;
        
        this.searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            if (query.length > 0) {
                this.searchClear.style.display = 'block';
                searchTimeout = setTimeout(() => {
                    this.performSearch(query);
                }, 300);
            } else {
                this.searchClear.style.display = 'none';
                this.clearSearch();
            }
        });

        this.searchClear.addEventListener('click', () => {
            this.searchInput.value = '';
            this.searchClear.style.display = 'none';
            this.clearSearch();
        });
    }

    performSearch(query) {
        const newsPage = window.newsPageInstance;
        if (!newsPage) return;

        const searchResults = newsPage.allArticles.filter(article => 
            article.title.toLowerCase().includes(query.toLowerCase()) ||
            article.excerpt.toLowerCase().includes(query.toLowerCase()) ||
            article.category.toLowerCase().includes(query.toLowerCase())
        );

        newsPage.filteredArticles = searchResults;
        newsPage.articlesLoaded = Math.min(6, searchResults.length);
        newsPage.displayArticles();
        newsPage.updateLoadMoreButton();

        // Filter-Buttons deaktivieren während Suche
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
    }

    clearSearch() {
        const newsPage = window.newsPageInstance;
        if (!newsPage) return;

        newsPage.currentFilter = 'all';
        newsPage.filteredArticles = [...newsPage.allArticles];
        newsPage.articlesLoaded = Math.min(6, newsPage.filteredArticles.length);
        newsPage.displayArticles();
        newsPage.updateLoadMoreButton();

        // "Alle Artikel" Button aktivieren
        const allBtn = document.querySelector('.filter-btn[data-category="all"]');
        if (allBtn) allBtn.classList.add('active');
    }
}

// Initialisierung
document.addEventListener('DOMContentLoaded', function() {
    window.newsPageInstance = new NewsPage();
    new NewsSearch();
    
    // Smooth Scrolling für Anker-Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});



