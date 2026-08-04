/* ==========================================================================
   APNAA GHAR REAL ESTATE - PREMIUM JS SYSTEM (GSAP & ScrollTrigger)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
  
  // Register GSAP ScrollTrigger
  if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
    gsap.registerPlugin(ScrollTrigger);
  }

  /* ------------------------------------------------------------------------
     1. ANIMATED PRELOADER & LOADING BAR
     ------------------------------------------------------------------------ */
  const preloader = document.getElementById('preloader');
  const preloaderBar = document.querySelector('.preloader-bar');
  const statusTexts = [
    'Curating modern blueprints...',
    'Verifying premium listings...',
    'Structuring transparent deals...',
    'Welcome to Apnaa Ghar Chembur...'
  ];
  const preloaderStatusText = document.querySelector('.preloader-status');
  
  let progress = 0;
  let statusIndex = 0;
  
  // Cycle preloader texts
  const statusInterval = setInterval(() => {
    if (preloaderStatusText && statusIndex < statusTexts.length) {
      preloaderStatusText.textContent = statusTexts[statusIndex];
      statusIndex++;
    }
  }, 600);

  // Animate preloader loading progress
  const progressInterval = setInterval(() => {
    progress += Math.floor(Math.random() * 15) + 5;
    if (progress >= 100) {
      progress = 100;
      clearInterval(progressInterval);
      clearInterval(statusInterval);
      
      // Complete loading & fade out
      if (preloaderBar) preloaderBar.style.width = '100%';
      
      // Snappy delay (150ms) matching the CSS transition for the progress bar to reach 100%
      setTimeout(() => {
        // Trigger entrance animations concurrently with the fade out
        runEntranceAnimations();

        if (typeof gsap !== 'undefined') {
          // Fade out animation
          gsap.to(preloader, {
            opacity: 0,
            duration: 0.4,
            ease: "power2.out",
            onComplete: () => {
              if (preloader) preloader.style.display = 'none';
              document.body.style.overflow = 'auto';
            }
          });
        } else {
          // Fallback if GSAP fails to load
          if (preloader) {
            preloader.style.transition = 'opacity 0.4s ease';
            preloader.style.opacity = 0;
            setTimeout(() => {
              preloader.style.display = 'none';
            }, 400);
          }
          document.body.style.overflow = 'auto';
        }
      }, 150);
    } else {
      if (preloaderBar) preloaderBar.style.width = `${progress}%`;
    }
  }, 80);

  // Prevent scroll during loading
  document.body.style.overflow = 'hidden';

  /* ------------------------------------------------------------------------
     2. LUXURY CUSTOM CURSOR TRACKING
     ------------------------------------------------------------------------ */
  const cursor = document.getElementById('custom-cursor');
  const follower = document.getElementById('custom-cursor-follower');
  
  let mouseX = 0;
  let mouseY = 0;
  let posX = 0;
  let posY = 0;
  
  window.addEventListener('mousemove', (e) => {
    mouseX = e.clientX;
    mouseY = e.clientY;
    
    // Quick movement for dot cursor
    if (cursor) {
      gsap.to(cursor, {
        x: mouseX,
        y: mouseY,
        duration: 0.1,
        ease: "power2.out"
      });
    }
  });

  // Slow smooth movement for follower ring
  gsap.ticker.add(() => {
    posX += (mouseX - posX) * 0.15;
    posY += (mouseY - posY) * 0.15;
    
    if (follower) {
      gsap.set(follower, {
        x: posX,
        y: posY
      });
    }
  });

  // Hover animations for cursor
  const hoverElements = document.querySelectorAll('a, button, select, input, textarea, .bookmark-btn, .filter-btn');
  hoverElements.forEach((el) => {
    el.addEventListener('mouseenter', () => {
      if (cursor) cursor.classList.add('hovered');
      if (follower) follower.classList.add('hovered');
    });
    el.addEventListener('mouseleave', () => {
      if (cursor) cursor.classList.remove('hovered');
      if (follower) follower.classList.remove('hovered');
    });
  });

  /* ------------------------------------------------------------------------
     3. MAGNETIC ATTRACTION ON PREMIUM CTA BUTTONS & LOGO
     ------------------------------------------------------------------------ */
  const magnetics = document.querySelectorAll('.magnetic');
  magnetics.forEach((el) => {
    el.addEventListener('mousemove', function(e) {
      const pos = el.getBoundingClientRect();
      const x = e.clientX - pos.left - pos.width / 2;
      const y = e.clientY - pos.top - pos.height / 2;
      
      // Pull button slightly towards mouse cursor
      gsap.to(el, {
        x: x * 0.35,
        y: y * 0.35,
        duration: 0.3,
        ease: "power2.out"
      });

      // Snapping cursor follower to magnetic elements
      if (follower) {
        follower.classList.add('magnetic-snap');
        gsap.to(follower, {
          x: pos.left + pos.width / 2 + x * 0.2,
          y: pos.top + pos.height / 2 + y * 0.2,
          duration: 0.2
        });
      }
    });

    el.addEventListener('mouseleave', function() {
      // Return button to default position
      gsap.to(el, {
        x: 0,
        y: 0,
        duration: 0.6,
        ease: "elastic.out(1, 0.3)"
      });
      
      if (follower) {
        follower.classList.remove('magnetic-snap');
      }
    });
  });

  /* ------------------------------------------------------------------------
     4. GLASSMORPHIC NAVBAR SCROLL TRANSITION
     ------------------------------------------------------------------------ */
  const header = document.getElementById('header');
  const backToTop = document.getElementById('back-to-top');
  
  window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
      if (header) header.classList.add('scrolled');
      if (backToTop) backToTop.classList.add('show');
    } else {
      if (header) header.classList.remove('scrolled');
      if (backToTop) backToTop.classList.remove('show');
    }
    
    // Highlight Active Link on Scroll
    highlightNavLink();
  });

  // Back to Top functionality
  if (backToTop) {
    backToTop.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }

  // Active Link Highlighter logic
  const sections = document.querySelectorAll('section, header, footer');
  const navLinks = document.querySelectorAll('.nav-link');

  function highlightNavLink() {
    let scrollPos = window.scrollY + 200;
    
    sections.forEach((section) => {
      if (section.id) {
        let top = section.offsetTop;
        let height = section.offsetHeight;
        
        if (scrollPos >= top && scrollPos < top + height) {
          navLinks.forEach((link) => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${section.id}`) {
              link.classList.add('active');
            }
          });
        }
      }
    });
  }

  /* ------------------------------------------------------------------------
     5. MOBILE MENU DRAWER TOGGLE
     ------------------------------------------------------------------------ */
  const hamburger = document.getElementById('hamburger');
  const drawer = document.getElementById('mobile-drawer');
  const drawerClose = document.getElementById('drawer-close');
  const drawerLinks = document.querySelectorAll('.mobile-link, #drawer-cta-btn');

  if (hamburger && drawer && drawerClose) {
    hamburger.addEventListener('click', () => {
      drawer.classList.add('open');
    });

    drawerClose.addEventListener('click', () => {
      drawer.classList.remove('open');
    });

    // Close drawer when link clicked
    drawerLinks.forEach((link) => {
      link.addEventListener('click', () => {
        drawer.classList.remove('open');
      });
    });
  }

  /* ------------------------------------------------------------------------
     6. ENTRANCE & SCROLL ANIMATIONS (GSAP)
     ------------------------------------------------------------------------ */
  function runEntranceAnimations() {
    if (typeof gsap === 'undefined') return;
    // 1. Ken Burns slow zoom effect on Hero Image
    gsap.to(".hero-img", {
      scale: 1.0,
      duration: 12,
      ease: "power1.inOut",
      repeat: -1,
      yoyo: true
    });

    // 2. Reveal text in Hero
    gsap.from(".hero-rating-badge", {
      y: 20,
      opacity: 0,
      duration: 0.8,
      ease: "power2.out"
    });

    gsap.from(".hero-title", {
      y: 40,
      opacity: 0,
      duration: 1,
      delay: 0.2,
      ease: "power3.out"
    });

    gsap.from(".hero-subtitle", {
      y: 30,
      opacity: 0,
      duration: 1,
      delay: 0.4,
      ease: "power3.out"
    });

    gsap.from(".hero-actions", {
      y: 20,
      opacity: 0,
      duration: 1,
      delay: 0.6,
      ease: "power3.out"
    });

    // Scroll trigger reveals for each section's elements
    const revealElements = document.querySelectorAll('.reveal-el');
    revealElements.forEach((el) => {
      gsap.from(el, {
        opacity: 0,
        y: 40,
        duration: 1,
        ease: "power2.out",
        scrollTrigger: {
          trigger: el,
          start: "top 88%",
          toggleActions: "play none none none"
        }
      });
    });

    // About Us visuals parallax depth
    if (document.querySelector('.about-visuals')) {
      gsap.to(".about-img-main-wrapper", {
        y: -40,
        scrollTrigger: {
          trigger: ".about-visuals",
          start: "top bottom",
          end: "bottom top",
          scrub: true
        }
      });

      gsap.to(".about-img-secondary-wrapper", {
        y: 30,
        scrollTrigger: {
          trigger: ".about-visuals",
          start: "top bottom",
          end: "bottom top",
          scrub: true
        }
      });
    }

    // Parallax background Quote banner
    if (document.querySelector('.parallax-bg-wrapper')) {
      gsap.to(".parallax-bg-wrapper", {
        yPercent: 15,
        ease: "none",
        scrollTrigger: {
          trigger: ".parallax-quote-section",
          start: "top bottom",
          end: "bottom top",
          scrub: true
        }
      });
    }

    // Stats counter trigger
    initCounterAnimations();
  }

  /* ------------------------------------------------------------------------
     7. STATISTICS COUNT-UP COUNTERS
     ------------------------------------------------------------------------ */
  function initCounterAnimations() {
    const counters = document.querySelectorAll('.counter-val');
    counters.forEach((counter) => {
      const target = parseInt(counter.getAttribute('data-target'), 10);
      
      gsap.from(counter, {
        textContent: 0,
        duration: 2.5,
        ease: "power2.out",
        snap: { textContent: 1 },
        scrollTrigger: {
          trigger: counter,
          start: "top 92%",
          toggleActions: "play none none none"
        },
        onUpdate: function() {
          // Add decimals back for 4.9 rating
          if (target === 49) {
            let val = parseFloat(counter.textContent) / 10;
            counter.textContent = val.toFixed(1);
          }
        }
      });
    });
  }

  /* ------------------------------------------------------------------------
     8. ABOUT US TABS SWITCHER
     ------------------------------------------------------------------------ */
  const tabButtons = document.querySelectorAll('.about-tab-btn');
  const tabContents = document.querySelectorAll('.tab-content');

  tabButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      // Remove active states
      tabButtons.forEach(b => b.classList.remove('active'));
      tabContents.forEach(c => c.classList.remove('active'));
      
      // Activate clicked
      btn.classList.add('active');
      const tabId = btn.getAttribute('data-tab');
      const targetContent = document.getElementById(`tab-${tabId}`);
      if (targetContent) targetContent.classList.add('active');
    });
  });

  /* ------------------------------------------------------------------------
     9. LUXURY PROPERTIES BOOKMARK BUTTONS
     ------------------------------------------------------------------------ */
  const bookmarkButtons = document.querySelectorAll('.bookmark-btn');
  bookmarkButtons.forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      btn.classList.toggle('active');
      const icon = btn.querySelector('i');
      if (icon) {
        if (btn.classList.contains('active')) {
          icon.className = 'fa-solid fa-bookmark';
          gsap.to(btn, { scale: 1.2, duration: 0.1, yoyo: true, repeat: 1 });
        } else {
          icon.className = 'fa-regular fa-bookmark';
        }
      }
    });
  });

  /* ------------------------------------------------------------------------
     10. CLIENT TESTIMONIALS SLIDER (AUTOPLAY & SWIPE)
     ------------------------------------------------------------------------ */
  const testimonialTrack = document.getElementById('testimonial-track');
  const testimonialSlides = document.querySelectorAll('.testimonial-slide');
  const sliderPrev = document.getElementById('slider-prev');
  const sliderNext = document.getElementById('slider-next');
  const dotsContainer = document.getElementById('slider-dots');
  
  let currentSlide = 0;
  const slideCount = testimonialSlides.length;
  let autoplayInterval;

  if (testimonialTrack && slideCount > 0) {
    // Generate dots
    for (let i = 0; i < slideCount; i++) {
      const dot = document.createElement('div');
      dot.className = i === 0 ? 'dot active' : 'dot';
      dot.addEventListener('click', () => {
        goToSlide(i);
        resetAutoplay();
      });
      if (dotsContainer) dotsContainer.appendChild(dot);
    }
    
    const dots = document.querySelectorAll('.dot');
    
    function updateSlider() {
      gsap.to(testimonialTrack, {
        x: `-${currentSlide * 100}%`,
        duration: 0.8,
        ease: "power3.inOut"
      });
      
      dots.forEach((dot, index) => {
        dot.className = index === currentSlide ? 'dot active' : 'dot';
      });
    }

    function goToSlide(index) {
      currentSlide = index;
      updateSlider();
    }

    function nextSlide() {
      currentSlide = (currentSlide + 1) % slideCount;
      updateSlider();
    }

    function prevSlide() {
      currentSlide = (currentSlide - 1 + slideCount) % slideCount;
      updateSlider();
    }

    if (sliderNext) {
      sliderNext.addEventListener('click', () => {
        nextSlide();
        resetAutoplay();
      });
    }

    if (sliderPrev) {
      sliderPrev.addEventListener('click', () => {
        prevSlide();
        resetAutoplay();
      });
    }

    // Autoplay setup
    function startAutoplay() {
      autoplayInterval = setInterval(nextSlide, 6000);
    }

    function resetAutoplay() {
      clearInterval(autoplayInterval);
      startAutoplay();
    }

    startAutoplay();
  }

  /* ------------------------------------------------------------------------
     11. LUXURY GALLERY FILTER & MASONRY
     ------------------------------------------------------------------------ */
  const filterButtons = document.querySelectorAll('.filter-btn');
  const galleryItems = document.querySelectorAll('.gallery-item');

  filterButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      // Toggle button states
      filterButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      
      const filterValue = btn.getAttribute('data-filter');
      
      galleryItems.forEach((item) => {
        const itemCategory = item.getAttribute('data-category');
        
        if (filterValue === 'all' || itemCategory === filterValue) {
          item.classList.remove('filtered-out');
          gsap.to(item, {
            opacity: 1,
            scale: 1,
            duration: 0.4,
            ease: "power2.out",
            overwrite: "auto"
          });
        } else {
          gsap.to(item, {
            opacity: 0,
            scale: 0.95,
            duration: 0.3,
            ease: "power2.inOut",
            overwrite: "auto",
            onComplete: () => {
              item.classList.add('filtered-out');
            }
          });
        }
      });
    });
  });

  /* ------------------------------------------------------------------------
     12. ACCESSIBLE GALLERY LIGHTBOX
     ------------------------------------------------------------------------ */
  const lightbox = document.getElementById('lightbox');
  const lightboxImg = document.getElementById('lightbox-img');
  const lightboxCaption = document.getElementById('lightbox-caption');
  const lightboxClose = document.getElementById('lightbox-close');
  const lightboxPrev = document.getElementById('lightbox-prev');
  const lightboxNext = document.getElementById('lightbox-next');
  
  let visibleItemsList = [];
  let currentLightboxIndex = 0;

  // Click on items opens Lightbox
  galleryItems.forEach((item) => {
    item.addEventListener('click', () => {
      // Grab only currently visible items in masonry
      visibleItemsList = Array.from(galleryItems).filter(el => !el.classList.contains('filtered-out'));
      currentLightboxIndex = visibleItemsList.indexOf(item);
      
      openLightbox();
    });
  });

  function openLightbox() {
    if (!lightbox || visibleItemsList.length === 0) return;
    
    const targetItem = visibleItemsList[currentLightboxIndex];
    const imgElement = targetItem.querySelector('.gallery-img');
    const captionText = targetItem.querySelector('h4').textContent;
    
    if (lightboxImg && imgElement) {
      lightboxImg.src = imgElement.src;
      lightboxImg.alt = imgElement.alt;
    }
    
    if (lightboxCaption) {
      lightboxCaption.textContent = captionText;
    }
    
    lightbox.classList.add('open');
    document.body.style.overflow = 'hidden'; // Lock scrolling
  }

  function closeLightbox() {
    if (lightbox) {
      lightbox.classList.remove('open');
      document.body.style.overflow = 'auto'; // Unlock scrolling
    }
  }

  function showNextLightboxImage() {
    if (visibleItemsList.length === 0) return;
    currentLightboxIndex = (currentLightboxIndex + 1) % visibleItemsList.length;
    openLightbox();
  }

  function showPrevLightboxImage() {
    if (visibleItemsList.length === 0) return;
    currentLightboxIndex = (currentLightboxIndex - 1 + visibleItemsList.length) % visibleItemsList.length;
    openLightbox();
  }

  if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
  if (lightboxNext) lightboxNext.addEventListener('click', showNextLightboxImage);
  if (lightboxPrev) lightboxPrev.addEventListener('click', showPrevLightboxImage);
  
  // Close on backdrop click
  if (lightbox) {
    lightbox.addEventListener('click', (e) => {
      if (e.target === lightbox) closeLightbox();
    });
  }

  // Keyboard navigation for Lightbox
  window.addEventListener('keydown', (e) => {
    if (lightbox && lightbox.classList.contains('open')) {
      if (e.key === 'Escape') closeLightbox();
      if (e.key === 'ArrowRight') showNextLightboxImage();
      if (e.key === 'ArrowLeft') showPrevLightboxImage();
    }
  });

  /* ------------------------------------------------------------------------
     13. WEB3FORMS CONTACT FORM HANDLER WITH visual STATUS popup
     ------------------------------------------------------------------------ */
  const realEstateForm = document.getElementById('realEstateForm');
  const alertSuccess = document.getElementById('form-alert-success');
  const alertError = document.getElementById('form-alert-error');
  const formSubmitBtn = document.getElementById('form-submit-btn');

  if (realEstateForm) {
    realEstateForm.addEventListener('submit', (e) => {
      e.preventDefault();
      
      // Disable button and update text
      if (formSubmitBtn) {
        formSubmitBtn.disabled = true;
        formSubmitBtn.innerHTML = 'Submitting... <i class="fa-solid fa-spinner fa-spin"></i>';
      }

      // Hide alerts
      if (alertSuccess) alertSuccess.classList.add('hidden');
      if (alertError) alertError.classList.add('hidden');

      const formData = new FormData(realEstateForm);
      
      // Perform submission via Ajax
      fetch(realEstateForm.action, {
        method: 'POST',
        body: formData,
        headers: {
          'Accept': 'application/json'
        }
      })
      .then(async (response) => {
        let json = await response.json();
        if (response.status == 200) {
          // Success
          if (alertSuccess) alertSuccess.classList.remove('hidden');
          realEstateForm.reset();
        } else {
          // Error
          console.error(json);
          if (alertError) alertError.classList.remove('hidden');
        }
      })
      .catch((error) => {
        console.error(error);
        if (alertError) alertError.classList.remove('hidden');
      })
      .finally(() => {
        // Reset submit button state
        if (formSubmitBtn) {
          formSubmitBtn.disabled = false;
          formSubmitBtn.innerHTML = 'Send Inquiry <i class="fa-solid fa-paper-plane"></i>';
        }
      });
    });

    // Alert close buttons trigger
    const alertCloseBtns = document.querySelectorAll('.alert-close-btn');
    alertCloseBtns.forEach((btn) => {
      btn.addEventListener('click', () => {
        const parentAlert = btn.parentElement;
        if (parentAlert) parentAlert.classList.add('hidden');
      });
    });
  }

  /* ------------------------------------------------------------------------
     14. DETAILED PROPERTY OVERVIEW MODAL SYSTEM
     ------------------------------------------------------------------------ */
  const propertiesData = {
    "property-1": {
      title: "The Grand Horizon Residency",
      type: "Luxury Tower",
      location: "Shell Colony, Chembur, Mumbai",
      price: "₹3.45 Cr",
      image: "https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80",
      status: "OC Received",
      configs: [
        { type: "3 BHK", size: "1,450 sq.ft", price: "₹3.45 Cr" }
      ],
      highlights: [
        "Double Height Grand Entrance Lobby (Air-Conditioned)",
        "Fully Equipped Modern Gymnasium",
        "Beautiful Rooftop Garden & Lounge Area",
        "High-speed Passenger Elevators",
        "24/7 Security Surveillance & Intercom System"
      ],
      connectivity: [
        "5 mins from Chembur Railway Station",
        "2 mins drive from Eastern Express Highway",
        "10 mins to Bandra Kurla Complex (BKC) via connector",
        "Conveniently close to upcoming Metro Line 4"
      ]
    },
    "property-2": {
      title: "Symphony Sky Villa",
      type: "Luxury Penthouse",
      location: "Tilak Nagar, Chembur, Mumbai",
      price: "₹5.20 Cr",
      image: "https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=800&q=80",
      status: "OC Received",
      configs: [
        { type: "4 BHK", size: "2,200 sq.ft", price: "₹5.20 Cr" }
      ],
      highlights: [
        "Exclusive Private High-Speed Elevator Access",
        "Infinity Edge Rooftop Swimming Pool",
        "3 Reserved Private Covered Car Parks",
        "Advanced Smart Home Automation System",
        "360-degree Panoramic Mumbai Skyline View"
      ],
      connectivity: [
        "2 mins walking distance to Tilak Nagar Railway Station",
        "5 mins drive to SCLR & Kurla area",
        "12 mins drive to BKC via Connector",
        "Easy connection to the Eastern Freeway"
      ]
    },
    "property-3": {
      title: "Elegance Court Duplex",
      type: "Builder Floor",
      location: "Union Park, Chembur, Mumbai",
      price: "₹1.25 L/Mo",
      image: "https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=800&q=80",
      status: "Available immediately",
      configs: [
        { type: "3 BHK Duplex", size: "1,850 sq.ft", price: "₹1.25 Lakh / Month" }
      ],
      highlights: [
        "Fully Furnished and Tastefully Designed Modern Interiors",
        "Double Height Living Room Ceiling for Airier Spaces",
        "Private Open-Air Terrace with Sit-out Spaces",
        "Dedicated Separate Servant Room & Washroom",
        "Nestled in the quietest, most elite neighborhood of Union Park"
      ],
      connectivity: [
        "Walkable distance to famous Union Park cafes & restaurants",
        "5 mins from the historical Ambedkar Garden",
        "8 mins access to the Eastern Freeway entry point",
        "Quick connectivity to Sion & Chembur SCLR junction"
      ]
    },
    "property-4": {
      title: "Emerald Gardens Heights",
      type: "Apartment",
      location: "Ghatkopar East, Mumbai",
      price: "₹2.10 Cr",
      image: "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80",
      status: "OC Expected Dec 2026",
      configs: [
        { type: "2 BHK", size: "950 sq.ft", price: "₹2.10 Cr" }
      ],
      highlights: [
        "Premium State-of-the-Art Clubhouse",
        "Lush Landscaped Gardens & Jogging Tracks",
        "Dedicated Safe Children's Play Area",
        "Modern Modular Kitchen & Premium Bath Fittings",
        "Multi-level Security Control Room with 24/7 Patrols"
      ],
      connectivity: [
        "5 mins from Ghatkopar Metro & Railway Stations",
        "3 mins from Eastern Express Highway (EEH)",
        "8 mins to LBS Road & Ghatkopar commercial zones",
        "Direct access to Upcoming Metro Line 4"
      ]
    },
    "property-5": {
      title: "Urban Retreat Penthouse",
      type: "Luxury Penthouse",
      location: "Kurla West, Mumbai",
      price: "₹4.85 Cr",
      image: "https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=800&q=80",
      status: "Ready Possession",
      configs: [
        { type: "4 BHK", size: "2,500 sq.ft", price: "₹4.85 Cr" }
      ],
      highlights: [
        "Private Open Deck with Premium Jacuzzi Setup",
        "Rooftop Open-to-Sky Private Lounge Area",
        "Complete Smart Home Integration & Mood Lighting",
        "Premium Selected Marble & Sanitary Fittings",
        "Walk-in Wardrobe Spaces in Master Suites"
      ],
      connectivity: [
        "5 mins drive to SCLR (BKC Link SCLR)",
        "7 mins from Kurla Junction Railway Station",
        "10 mins to Phoenix Marketcity Mall",
        "Direct connection routes to LBS Road & EEH"
      ]
    },
    "property-6": {
      title: "Charming Colonial Bungalow",
      type: "Luxury Villa",
      location: "Deonar, Chembur, Mumbai",
      price: "₹12.00 Cr",
      image: "https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=800&q=80",
      status: "Ready Possession",
      configs: [
        { type: "5 BHK", size: "4,500 sq.ft", price: "₹12.00 Cr" }
      ],
      highlights: [
        "Lush Private Landscaped Gardens",
        "Private Swimming Pool with Lounge Deck",
        "Classic Colonial/Heritage Aesthetics",
        "5 Dedicated Covered Parking Bays",
        "Nestled in a highly secure elite Bungalow Gated Compound"
      ],
      connectivity: [
        "Peaceful and green residential lane of Deonar",
        "3 mins from Tata Institute of Social Sciences (TISS)",
        "5 mins drive to Eastern Freeway entrance",
        "10 mins drive to Chembur Naka crossroads"
      ]
    },
    "property-7": {
      title: "The Signature Aura (Chembur)",
      type: "Residential Project",
      location: "Near SCLR & Freeway, Chembur, Mumbai",
      price: "₹1.94 Cr onwards",
      image: "https://images.unsplash.com/photo-1582407947304-fd86f028f716?auto=format&fit=crop&w=800&q=80",
      status: "OC Received",
      configs: [
        { type: "2 BHK", size: "611 to 829 sq.ft", price: "₹1.94 Cr onwards" },
        { type: "2.5 BHK", size: "834 sq.ft", price: "Sold out" }
      ],
      highlights: [
        "Premium Ready-to-Move Residences with OC Received",
        "Assured Rental Scheme Available (Ideal for Investors)",
        "Immediate Payouts & Faster Property Closures",
        "AC Grand Entrance Lobby & Fully Equipped Gym",
        "Rooftop Party Lawn, Open Air Lounge & Play Area"
      ],
      connectivity: [
        "Direct Entry to Eastern Freeway & SCLR",
        "Connected to Eastern Express Highway (EEH)",
        "Quick connectivity to BKC Connector (5 mins to BKC)",
        "Walkable to Monorail and upcoming Metro Line 4",
        "Excellent rail connectivity via Chembur & Kurla stations"
      ]
    },
    "property-8": {
      title: "Hyper-Connected Offices",
      type: "Commercial Project",
      location: "Near Krushal Towers, SCLR & EEH Junction, Chembur-Ghatkopar, Mumbai",
      price: "₹73 Lakh onwards",
      image: "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80",
      status: "CC Received (OC Dec 2026)",
      configs: [
        { type: "Office Unit", size: "245 sq.ft", price: "₹73 Lakh onwards" },
        { type: "Office Unit", size: "323 sq.ft", price: "₹96 Lakh onwards" },
        { type: "Office Unit", size: "415 sq.ft", price: "₹1.16 Cr onwards" },
        { type: "Office Unit", size: "518 sq.ft", price: "₹1.45 Cr onwards" },
        { type: "Office Unit", size: "617 sq.ft", price: "₹1.72 Cr onwards" },
        { type: "Office Unit", size: "817 sq.ft", price: "₹2.28 Cr onwards" },
        { type: "Jodi Office Option", size: "Up to 7,200 sq.ft", price: "On Request" }
      ],
      highlights: [
        "15-Story Premium Commercial Glass Architecture",
        "10 ft. Clear Ceiling Height for Spacious Offices",
        "Grand AC Double-Height Entrance Lobby",
        "Self-Contained Executive Office Units",
        "Assured High Rental Yields Upto 7% YoY",
        "Ever Growing Cash Flow & Rental appreciation Upto 8% YoY",
        "Executive Jain Temple (Derasar) inside Building Premises",
        "2 Levels of Basement Car Parking with Ramp Access"
      ],
      connectivity: [
        "5 mins from Ghatkopar, Chembur & Tilak Nagar stations",
        "1 min from SCLR and Eastern Express Highway (EEH)",
        "5 mins to Bandra Kurla Complex (BKC)",
        "Directly next to upcoming Metro Line 4",
        "Located near SCLR flyover, SCLR connector, and Krushal Towers"
      ]
    }
  };

  const detailsModal = document.getElementById('property-details-modal');
  const modalCloseBtn = document.getElementById('modal-close-btn');
  const modalOverlay = document.getElementById('modal-overlay');
  const modalInquireBtn = document.getElementById('modal-inquire-btn');
  
  let activePropertyTitle = "";

  function openPropertyModal(id) {
    const data = propertiesData[id];
    if (!data || !detailsModal) return;

    activePropertyTitle = data.title;

    // Populating header
    document.getElementById('modal-property-title').textContent = data.title;
    document.getElementById('modal-property-type').textContent = data.type;
    document.getElementById('modal-property-status').textContent = data.status;
    document.getElementById('modal-property-location').innerHTML = `<i class="fa-solid fa-location-dot"></i> ${data.location}`;
    document.getElementById('modal-property-price').textContent = data.price;
    
    // Set image
    const imgEl = document.getElementById('modal-property-img');
    if (imgEl) {
      imgEl.src = data.image;
      imgEl.alt = data.title;
    }

    // Populate Pricing Table
    const tbody = document.getElementById('modal-pricing-tbody');
    if (tbody) {
      tbody.innerHTML = '';
      data.configs.forEach(conf => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td><strong>${conf.type}</strong></td>
          <td>${conf.size}</td>
          <td><span class="accent-gold" style="font-weight: 700;">${conf.price}</span></td>
        `;
        tbody.appendChild(tr);
      });
    }

    // Populate Highlights
    const highlightsUl = document.getElementById('modal-property-highlights');
    if (highlightsUl) {
      highlightsUl.innerHTML = '';
      data.highlights.forEach(hl => {
        const li = document.createElement('li');
        li.textContent = hl;
        highlightsUl.appendChild(li);
      });
    }

    // Populate Connectivity
    const connectivityUl = document.getElementById('modal-property-connectivity');
    if (connectivityUl) {
      connectivityUl.innerHTML = '';
      data.connectivity.forEach(con => {
        const li = document.createElement('li');
        li.textContent = con;
        connectivityUl.appendChild(li);
      });
    }

    // Show modal
    detailsModal.classList.add('open');
    detailsModal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden'; // Disable scroll

    // Entrance animation using GSAP if available
    if (typeof gsap !== 'undefined') {
      gsap.fromTo('.modal-wrapper', 
        { scale: 0.9, y: 30, opacity: 0 },
        { scale: 1, y: 0, opacity: 1, duration: 0.5, ease: 'power2.out', overwrite: 'auto' }
      );
    }
  }

  function closePropertyModal() {
    if (!detailsModal) return;
    
    if (typeof gsap !== 'undefined') {
      gsap.to('.modal-wrapper', {
        scale: 0.9,
        y: 30,
        opacity: 0,
        duration: 0.3,
        ease: 'power2.in',
        overwrite: 'auto',
        onComplete: () => {
          detailsModal.classList.remove('open');
          detailsModal.setAttribute('aria-hidden', 'true');
          document.body.style.overflow = 'auto'; // Restore scroll
        }
      });
    } else {
      detailsModal.classList.remove('open');
      detailsModal.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = 'auto'; // Restore scroll
    }
  }

  // Card click triggers modal
  const propertyCards = document.querySelectorAll('.property-card');
  propertyCards.forEach(card => {
    const id = card.getAttribute('data-property-id');
    
    card.addEventListener('click', (e) => {
      // Prevent opening if clicking bookmark
      if (e.target.closest('.bookmark-btn')) return;
      
      e.preventDefault();
      if (id) openPropertyModal(id);
    });
  });

  // Modal close event handlers
  if (modalCloseBtn) modalCloseBtn.addEventListener('click', closePropertyModal);
  if (modalOverlay) modalOverlay.addEventListener('click', closePropertyModal);

  // Close on Escape key
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && detailsModal && detailsModal.classList.contains('open')) {
      closePropertyModal();
    }
  });

  // Modal Inquiry CTAs Link to Contact Form
  if (modalInquireBtn) {
    modalInquireBtn.addEventListener('click', () => {
      closePropertyModal();
      
      setTimeout(() => {
        const contactSection = document.getElementById('contact');
        const messageTextarea = document.getElementById('client-message');
        
        if (messageTextarea) {
          messageTextarea.value = `Hi, I am interested in inquiring about "${activePropertyTitle}". Please share more details, pricing brochures, and scheduling visits.`;
          messageTextarea.dispatchEvent(new Event('input', { bubbles: true }));
        }

        if (contactSection) {
          contactSection.scrollIntoView({ behavior: 'smooth' });
          const nameInput = document.querySelector('input[name="name"]');
          if (nameInput) setTimeout(() => nameInput.focus(), 800);
        }
      }, 350);
    });
  }
});
