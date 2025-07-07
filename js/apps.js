// Skripte für die 4AM TECHNO Apps-Seite

// Floating particles animation
function createParticles() {
    const particlesContainer = document.getElementById('particles');
    if (!particlesContainer) return; // Bricht ab, wenn das Element nicht da ist
    
    for (let i = 0; i < 50; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.width = particle.style.height = Math.random() * 10 + 5 + 'px';
        particle.style.animationDelay = Math.random() * 20 + 's';
        particle.style.animationDuration = (Math.random() * 10 + 20) + 's';
        particlesContainer.appendChild(particle);
    }
}

// BPM Tapper functionality
let taps = [];
let tapCount = 0;

function tap() {
    const now = Date.now();
    taps.push(now);
    tapCount++;
    
    const tapCountEl = document.getElementById('tap-count');
    if(tapCountEl) tapCountEl.textContent = tapCount;
    
    if (taps.length > 1) {
        if (taps.length > 16) {
            taps.shift();
        }
        
        let totalTime = 0;
        for (let i = 1; i < taps.length; i++) {
            totalTime += taps[i] - taps[i-1];
        }
        const avgInterval = totalTime / (taps.length - 1);
        const bpm = Math.round(60000 / avgInterval);
        
        const bpmDisplayEl = document.getElementById('bpm-display');
        if(bpmDisplayEl) bpmDisplayEl.textContent = bpm;
    }
}

function resetTaps() {
    taps = [];
    tapCount = 0;
    const tapCountEl = document.getElementById('tap-count');
    const bpmDisplayEl = document.getElementById('bpm-display');
    if(tapCountEl) tapCountEl.textContent = '0';
    if(bpmDisplayEl) bpmDisplayEl.textContent = '---';
}

// Event Listeners für die App-Seite
document.addEventListener('DOMContentLoaded', function() {
    createParticles();

    const tapBtn = document.getElementById('tap-btn');
    if(tapBtn) tapBtn.addEventListener('click', tap);
    
    const resetBtn = document.querySelector('button[onclick="resetTaps()"]'); // Sicherere Auswahl
    if(resetBtn) resetBtn.addEventListener('click', resetTaps);
    
    document.addEventListener('keydown', function(event) {
        if (event.code === 'Space') {
            const activeEl = document.activeElement;
            if(activeEl.tagName !== 'INPUT' && activeEl.tagName !== 'SELECT') {
                event.preventDefault();
                tap();
            }
        }
    });

    const searchBtn = document.getElementById('search-btn');
    if(searchBtn) searchBtn.addEventListener('click', searchSongs);

    const genreBtn = document.getElementById('genre-btn');
    if(genreBtn) genreBtn.addEventListener('click', analyzeGenre);
});


// Platzhalter-Funktionen für die anderen Tools
function searchSongs() {
    const queryEl = document.getElementById('search-query');
    const resultsDiv = document.getElementById('search-results');
    if (!queryEl || !resultsDiv) return;

    const query = queryEl.value;
    if (query.trim()) {
        resultsDiv.style.display = 'block';
        resultsDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin mr-2"></i>Suche läuft...</div>';
        setTimeout(() => {
            resultsDiv.innerHTML = '<div class="text-center text-gray-400">Demo-Modus: Hier würden Suchergebnisse für "' + query + '" angezeigt.</div>';
        }, 1500);
    }
}

function analyzeGenre() {
    const queryEl = document.getElementById('genre-query');
    const resultsDiv = document.getElementById('genre-results');
    if (!queryEl || !resultsDiv) return;
    
    const query = queryEl.value;
    if (query.trim()) {
        resultsDiv.style.display = 'block';
        resultsDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin mr-2"></i>Analysiere...</div>';
        setTimeout(() => {
            resultsDiv.innerHTML = '<div class="text-center text-gray-400">Demo-Modus: Genre-Analyse für "' + query + '" würde hier angezeigt.</div>';
        }, 1500);
    }
}