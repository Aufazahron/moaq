// KutaBaca Custom JavaScript untuk Anak-Anak
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when dropdown changes
    const dropdowns = document.querySelectorAll('select[onchange="this.form.submit()"]');
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Efek untuk search bar
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
            createSparkles(this.parentElement);
        });

        searchInput.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });

        searchInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                this.style.background = 'linear-gradient(135deg, var(--accent-yellow) 0%, white 100%)';
            } else {
                this.style.background = 'linear-gradient(135deg, white 0%, var(--neutral-gray-light) 100%)';
            }
        });
    }

    // Efek interaktif untuk kartu kategori
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            // Tambahkan efek confetti saat hover
            createConfetti(this);
        });

        card.addEventListener('click', function() {
            // Efek klik yang menyenangkan
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
            
            // Tambahkan efek sparkle saat klik
            createSparkles(this);
        });
    });

    // Efek interaktif untuk kartu buku
    const bookCards = document.querySelectorAll('.book-card');
    bookCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            // Tambahkan efek glow
            this.style.boxShadow = '0 15px 35px rgba(135, 70, 74, 0.4)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.boxShadow = '';
        });

        card.addEventListener('click', function() {
            // Efek klik yang menyenangkan
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 200);
            
            // Tambahkan efek sparkle saat klik
            createSparkles(this);
        });
    });

    // Efek untuk tombol
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            // Efek ripple saat klik
            createRippleEffect(this, event);
        });
    });

    // Animasi loading yang menyenangkan
    const heroSection = document.querySelector('.hero-section');
    if (heroSection) {
        heroSection.addEventListener('animationend', function() {
            // Tambahkan efek floating untuk elemen dalam hero
            const heroElements = heroSection.querySelectorAll('h1, p, a, img');
            heroElements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.2}s`;
                el.style.animation = 'float 3s ease-in-out infinite';
            });
        });
    }

    // Efek parallax sederhana untuk hero section
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const heroSection = document.querySelector('.hero-section');
        if (heroSection) {
            const rate = scrolled * -0.5;
            heroSection.style.transform = `translateY(${rate}px)`;
        }
    });

    // Tambahkan efek hover untuk ikon
    const icons = document.querySelectorAll('.bi');
    icons.forEach(icon => {
        icon.addEventListener('mouseenter', function() {
            this.style.animation = 'bounce 0.6s ease';
        });

        icon.addEventListener('animationend', function() {
            this.style.animation = '';
        });
    });

    // Efek untuk book cover overlay
    const bookOverlays = document.querySelectorAll('.book-overlay');
    bookOverlays.forEach(overlay => {
        overlay.addEventListener('mouseenter', function() {
            this.style.background = 'linear-gradient(135deg, rgba(135, 70, 74, 0.9) 0%, rgba(252, 185, 169, 0.9) 100%)';
        });

        overlay.addEventListener('mouseleave', function() {
            this.style.background = 'linear-gradient(135deg, rgba(135, 70, 74, 0.8) 0%, rgba(252, 185, 169, 0.8) 100%)';
        });
    });

    // Efek untuk search button
    const searchBtn = document.querySelector('.search-btn');
    if (searchBtn) {
        searchBtn.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1) rotate(5deg)';
        });

        searchBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) rotate(0deg)';
        });
    }
});

// Fungsi untuk membuat efek confetti
function createConfetti(element) {
    const colors = ['#FCB9A9', '#87464A', '#FFE5B4', '#B8E6B8', '#E6B8E6'];
    const rect = element.getBoundingClientRect();
    
    for (let i = 0; i < 10; i++) {
        const confetti = document.createElement('div');
        confetti.style.position = 'fixed';
        confetti.style.left = rect.left + Math.random() * rect.width + 'px';
        confetti.style.top = rect.top + Math.random() * rect.height + 'px';
        confetti.style.width = '8px';
        confetti.style.height = '8px';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.borderRadius = '50%';
        confetti.style.pointerEvents = 'none';
        confetti.style.zIndex = '9999';
        confetti.style.animation = 'confettiFall 1s ease-out forwards';
        
        document.body.appendChild(confetti);
        
        setTimeout(() => {
            confetti.remove();
        }, 1000);
    }
}

// Fungsi untuk membuat efek sparkles
function createSparkles(element) {
    const rect = element.getBoundingClientRect();
    
    for (let i = 0; i < 8; i++) {
        const sparkle = document.createElement('div');
        sparkle.innerHTML = '✨';
        sparkle.style.position = 'fixed';
        sparkle.style.left = rect.left + Math.random() * rect.width + 'px';
        sparkle.style.top = rect.top + Math.random() * rect.height + 'px';
        sparkle.style.fontSize = '20px';
        sparkle.style.pointerEvents = 'none';
        sparkle.style.zIndex = '9999';
        sparkle.style.animation = 'sparkleEffect 0.8s ease-out forwards';
        
        document.body.appendChild(sparkle);
        
        setTimeout(() => {
            sparkle.remove();
        }, 800);
    }
}

// Fungsi untuk membuat efek ripple
function createRippleEffect(button, event) {
    const ripple = document.createElement('span');
    const rect = button.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.position = 'absolute';
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';
    ripple.style.borderRadius = '50%';
    ripple.style.background = 'rgba(255, 255, 255, 0.6)';
    ripple.style.transform = 'scale(0)';
    ripple.style.animation = 'ripple 0.6s linear';
    ripple.style.pointerEvents = 'none';
    
    button.style.position = 'relative';
    button.style.overflow = 'hidden';
    button.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 600);
}

// Tambahkan CSS animations ke dalam style
const style = document.createElement('style');
style.textContent = `
    @keyframes confettiFall {
        0% {
            transform: translateY(0) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100px) rotate(360deg);
            opacity: 0;
        }
    }
    
    @keyframes sparkleEffect {
        0% {
            transform: scale(0) rotate(0deg);
            opacity: 1;
        }
        50% {
            transform: scale(1.2) rotate(180deg);
            opacity: 1;
        }
        100% {
            transform: scale(0) rotate(360deg);
            opacity: 0;
        }
    }
    
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }
`;
document.head.appendChild(style); 