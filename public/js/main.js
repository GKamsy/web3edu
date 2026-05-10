document.addEventListener('DOMContentLoaded', () => {

    // Mobile menu toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-nav');

    menuToggle.addEventListener('click', () => {
        mainNav.classList.toggle('active');
        menuToggle.classList.toggle('active');
    });
    
    // Close mobile menu when a nav link is clicked
    document.querySelectorAll('.main-nav a').forEach(link => {
        link.addEventListener('click', () => {
            mainNav.classList.remove('active');
            menuToggle.classList.remove('active');
        });
    });

    // This prevents weird states if the phone rotates.
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            mainNav.classList.remove('active');
            menuToggle.classList.remove('active');
        }
    });
    
    const slider = document.querySelector('.hero-slider');
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');

    let current = 0;
    let interval = null;

    function showSlide(index) {
        slides[current].classList.remove('active');
        dots[current].classList.remove('active');

        current = index;

        slides[current].classList.add('active');
        dots[current].classList.add('active');
    }

    function nextSlide() {
        showSlide((current + 1) % slides.length);
    }

    function prevSlide() {
        showSlide((current - 1 + slides.length) % slides.length);
    }

    function startAutoSlide() {
        interval = setInterval(nextSlide, 5000);
    }

    function resetAutoSlide() {
        clearInterval(interval);
        startAutoSlide();
    }

    // Dot navigation
    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            showSlide(parseInt(dot.dataset.slide, 10));
            resetAutoSlide();
        });
    });

    // ----------------------
    // 👉 TOUCH / SWIPE LOGIC
    // ----------------------

    let touchStartX = 0;
    let touchEndX = 0;

    slider.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    slider.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });

    function handleSwipe() {
        const threshold = 50; // minimum swipe distance (px)
        const deltaX = touchEndX - touchStartX;

        if (Math.abs(deltaX) < threshold) return;

        if (deltaX < 0) {
            nextSlide();   // swipe left
        } else {
            prevSlide();   // swipe right
        }

        resetAutoSlide();
    }

    // Init
    slides[0].classList.add('active');
    dots[0].classList.add('active');
    startAutoSlide();

    // ----------------------
    // Scroll fade-in effects
    // ----------------------

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.15 });

    document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
});
