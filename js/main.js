/* ==========================================================================
   Apnaa Ghar Real Estate & Interior - PREMIUM JS SYSTEM (GSAP & ScrollTrigger)
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
    'Welcome to Apnaa Ghar Real Estate & Interior Chembur...'
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
              checkPendingInquiry();
            }
          });
        } else {
          // Fallback if GSAP fails to load
          if (preloader) {
            preloader.style.transition = 'opacity 0.4s ease';
            preloader.style.opacity = 0;
            setTimeout(() => {
              preloader.style.display = 'none';
              checkPendingInquiry();
            }, 400);
          }
          document.body.style.overflow = 'auto';
        }
      }, 150);
    } else {
      if (preloaderBar) preloaderBar.style.width = `${progress}%`;
    }
  }, 30);

  function checkPendingInquiry() {
    const pendingInquiry = sessionStorage.getItem('pendingInquiry');
    if (pendingInquiry) {
      const contactSection = document.getElementById('contact');
      const messageTextarea = document.getElementById('client-message');
      if (contactSection && messageTextarea) {
        setTimeout(() => {
          messageTextarea.value = pendingInquiry;
          messageTextarea.dispatchEvent(new Event('input', { bubbles: true }));
          contactSection.scrollIntoView({ behavior: 'smooth' });
          const nameInput = document.querySelector('input[name="name"]');
          if (nameInput) setTimeout(() => nameInput.focus(), 800);
        }, 100);
        sessionStorage.removeItem('pendingInquiry');
      }
    }
  }

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

    // Counter animations
    const counters = document.querySelectorAll('.counter-val');
    counters.forEach(counter => {
      ScrollTrigger.create({
        trigger: counter,
        start: "top 95%",
        once: true,
        onEnter: () => {
          const target = parseFloat(counter.getAttribute('data-target'));
          const isDecimal = target % 1 !== 0 || counter.innerHTML.includes('.');
          
          const duration = 2000;
          const frameRate = 30;
          const totalFrames = Math.round((duration / 1000) * frameRate);
          let currentFrame = 0;
          
          const counterInterval = setInterval(() => {
            currentFrame++;
            const progress = currentFrame / totalFrames;
            // ease-out cubic approximation
            const easeProgress = 1 - Math.pow(1 - progress, 3);
            const currentVal = target * easeProgress;
            
            counter.innerHTML = isDecimal ? currentVal.toFixed(1) : Math.floor(currentVal);
            
            if (currentFrame >= totalFrames) {
              clearInterval(counterInterval);
              counter.innerHTML = isDecimal ? target.toFixed(1) : target;
            }
          }, 1000 / frameRate);
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
  const propertiesData = window.dynamicPropertiesData || {
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
    },
    "property-9": {
      title: "The Grove Residency",
      type: "Residential Tower",
      location: "Tilak Nagar, Chembur, Mumbai",
      price: "₹90 L onwards",
      image: "https://images.unsplash.com/photo-1560184897-ae75f418493e?auto=format&fit=crop&w=800&q=80",
      status: "Possession by June 2027",
      configs: [
        { type: "1 BHK with Deck", size: "On Request", price: "₹90 L onwards" },
        { type: "1.5 BHK with Deck", size: "On Request", price: "₹1.05 Cr onwards" },
        { type: "2 BHK with Balcony", size: "On Request", price: "₹1.23 Cr onwards" },
        { type: "2.5 BHK with Balcony", "size": "On Request", price: "₹1.88 Cr onwards" }
      ],
      highlights: [
        "14-Storey Iconic Tower with Uninterrupted Garden Views",
        "Strategically Positioned Right Outside Tilak Nagar Station",
        "Exclusive Rooftop Amenities for a Luxurious Living Experience",
        "Early Bird Offer — Exclusive Benefits for the First 25 Buyers Only",
        "Deck & Balcony Homes Across All Configurations"
      ],
      connectivity: [
        "Eastern Express Highway – 2 mins",
        "Tilak Nagar Station – 1 min",
        "Vidyavihar Station – 7 mins",
        "Eastern Freeway – 6 mins",
        "Bandra Kurla Complex (BKC) – 12 mins",
        "Mumbai Airport – 25 mins"
      ]
    },
    "property-10": {
      title: "Codename Mangalam",
      type: "Residential Tower",
      location: "Tilak Nagar Station, Chembur, Mumbai",
      price: "Price on Request (Pre-Launch)",
      image: "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80",
      status: "Pre-Launch",
      configs: [
        { type: "1 BHK (6 Variants)", size: "370–434 sq.ft carpet", price: "On Request" },
        { type: "2 BHK (2 Variants)", size: "On Request", price: "On Request" },
        { type: "2 BHK with Car Parking", size: "677 sq.ft carpet", price: "On Request" }
      ],
      highlights: [
        "G+14 storey premium tower with vastu-compliant layouts",
        "Bang outside Tilak Nagar Station",
        "East–West facing, airy residences with open views",
        "Flexible pre-launch payment plans (30:70 / 50:50 / 25:75)",
        "Perfect balance of lifestyle & investment value"
      ],
      connectivity: [
        "Tilak Nagar Station – 1 min",
        "EEH – 2 mins",
        "Chembur Station – 7 mins",
        "Eastern Freeway – 6 mins",
        "BKC – 12 mins",
        "Mumbai Airport – 25 mins"
      ]
    },
    "property-11": {
      title: "Chembur Heights II",
      type: "Residential Apartment",
      location: "Chembur, Mumbai",
      price: "₹2.49 Cr onwards",
      image: "https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80",
      status: "Ready to Move",
      configs: [
        { type: "2 BHK", size: "690 sq.ft", price: "₹2.49 Cr" },
        { type: "2 BHK", size: "767 sq.ft", price: "₹2.69 Cr" },
        { type: "2 BHK", size: "775 sq.ft", price: "₹2.72 Cr" },
        { type: "3 BHK", size: "964 sq.ft", price: "₹3.49 Cr" },
        { type: "3 BHK", size: "1,011 sq.ft", price: "₹3.59 Cr" }
      ],
      highlights: [
        "Spacious ready-to-move-in homes across G + Podium + 19 storeys",
        "Podium level & dedicated clubhouse amenities",
        "6,000 sq.ft clubhouse with well-equipped gym & indoor games",
        "Swimming pool for kids & adults with separate changing rooms",
        "Banquet hall, mini theatre & cafeteria with flexible payment plans"
      ],
      connectivity: [
        "Located in Chembur with easy access to Eastern Express Highway",
        "Close to Chembur & Tilak Nagar railway stations",
        "Well connected to SCLR and BKC Connector"
      ]
    },
    "property-12": {
      title: "Chembur Station East Residences",
      type: "Residential & Commercial Project",
      location: "Near Chembur Station (E), Mumbai",
      price: "₹1.30 Cr onwards",
      image: "https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=800&q=80",
      status: "Under Construction (Floor Band 1–5)",
      configs: [
        { type: "1 BHK", size: "475 sq.ft", price: "₹1.30 Cr All Inclusive" },
        { type: "2 BHK", size: "605 sq.ft", price: "₹1.79 Cr All Inclusive" },
        { type: "3 BHK with Balcony", size: "950 sq.ft", price: "₹2.73 Cr All Inclusive" },
        { type: "3 BHK with Balcony", size: "955 sq.ft", price: "₹2.76 Cr All Inclusive" }
      ],
      highlights: [
        "Luxurious residential & commercial project — 800 mtrs from Chembur Station",
        "2 level basement parking",
        "Spread entrance, double-height lobby for A & B wing",
        "2 levels of dedicated commercial space",
        "Premium location with easy connectivity"
      ],
      connectivity: [
        "800 metres from Chembur Railway Station",
        "Premium location with easy access to Eastern Express Highway"
      ]
    },
    "property-13": {
      title: "Elegance Heights, Nerul",
      type: "Residential Tower",
      location: "Nerul, Navi Mumbai",
      price: "₹1.78 Cr onwards",
      image: "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80",
      status: "CC & RERA Received",
      configs: [
        { type: "2 BHK", size: "725 sq.ft", price: "₹1.78 Cr++" },
        { type: "2 BHK", size: "758 sq.ft", price: "₹1.85 Cr++" },
        { type: "3 BHK", size: "1,054 sq.ft", price: "₹2.64 Cr++" }
      ],
      highlights: [
        "G+19 floors iconic tower on a CIDCO tender plot",
        "Swimming pool, kids play area & fully equipped gym",
        "Yoga room, multipurpose hall & club house",
        "CC & RERA received for added buyer confidence",
        "Builder possession Dec 2027 / RERA possession Nov 2028"
      ],
      connectivity: [
        "5 mins from Nerul Railway Station",
        "5 mins from Sion-Panvel Highway",
        "Premium location in Navi Mumbai"
      ]
    },
    "property-14": {
      title: "Vikhroli East Residences",
      type: "Residential Tower",
      location: "Vikhroli East, Mumbai",
      price: "₹21,000/sq.ft onwards",
      image: "https://images.unsplash.com/photo-1582407947304-fd86f028f716?auto=format&fit=crop&w=800&q=80",
      status: "New Launch",
      configs: [
        { type: "1 BHK", size: "440 sq.ft", price: "₹21,000/sq.ft + charges" },
        { type: "2 BHK", size: "600–650 sq.ft", price: "₹21,000/sq.ft + charges" }
      ],
      highlights: [
        "High-rise G+22 tower with 4 lifts (3+1)",
        "Only 6 flats per floor for more privacy, less crowd",
        "Smart layouts with maximum space utilisation",
        "Gym, rooftop sit-out, kids play area & senior citizen zone",
        "Car parking available at ₹8 Lakhs"
      ],
      connectivity: [
        "Located in Vikhroli East with good social infrastructure"
      ]
    },
    "property-15": {
      title: "The New Landmark, Sion–Chunabhatti",
      type: "Residential Project",
      location: "Sion–Chunabhatti, Mumbai",
      price: "Price on Request",
      image: "https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=800&q=80",
      status: "Under Construction",
      configs: [
        { type: "1 BHK", size: "430 sq.ft", price: "On Request" },
        { type: "2 BHK", size: "625 sq.ft", price: "On Request" },
        { type: "2 BHK", size: "642 sq.ft", price: "On Request" }
      ],
      highlights: [
        "Smartly designed homes at Mumbai's most connected junction",
        "Walking distance to Chunabhatti Station, EEH & Metro access",
        "Minutes from BKC, Sion, Chembur, Kurla, Ghatkopar, Dadar & Lower Parel",
        "Surrounded by shopping malls, hospitals, food hubs & schools",
        "Builder timeline Dec 2029 / RERA possession Dec 2032"
      ],
      connectivity: [
        "Chunabhatti Station",
        "Eastern Express Highway",
        "Metro Access"
      ]
    },
    "property-16": {
      title: "Vile Parle Residences",
      type: "Residential Project",
      location: "Vile Parle, Mumbai",
      price: "₹1.94 Cr onwards",
      image: "https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=800&q=80",
      status: "Under Construction – 2 Basements Completed",
      configs: [
        { type: "1 BHK", size: "495 sq.ft", price: "₹1.94 Cr onwards" },
        { type: "2 BHK", size: "695 sq.ft", price: "₹2.64 Cr onwards" },
        { type: "3 BHK", size: "1,190 sq.ft", price: "₹4.60 Cr onwards" }
      ],
      highlights: [
        "Prime Vile Parle location, ideal for upgraders & NRI families",
        "2 basements completed, plinth targeted by mid-August 2026",
        "Well suited for Gujarati, Jain & Maharashtrian families",
        "Great fit for business owners & investors",
        "Direct developer connect for inventory & pricing"
      ],
      connectivity: [
        "Located in the heart of Vile Parle with excellent social infrastructure"
      ]
    },
    "property-17": {
      title: "Premium 1 & 2 BHK Residences",
      type: "Residential Apartment",
      location: "Location to be confirmed, Mumbai",
      price: "₹73.99 Lacs onwards",
      image: "https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=800&q=80",
      status: "Possession December 2028",
      configs: [
        { type: "1 BHK", size: "365 sq.ft", price: "₹73.99 Lacs onwards" },
        { type: "1 BHK", size: "375 sq.ft", price: "₹76.99 Lacs onwards" },
        { type: "1 BHK Premium", size: "398 sq.ft", price: "₹81.99 Lacs onwards" },
        { type: "2 BHK", size: "519 sq.ft", price: "₹1.05 Cr onwards" }
      ],
      highlights: [
        "Trusted developer with 1 Million+ sq.ft. delivered",
        "20+ successfully completed projects",
        "Premium rooftop amenities",
        "Smart & efficient, future-ready layouts",
        "Commitment to transparency, trust & timely delivery"
      ],
      connectivity: [
        "To be confirmed"
      ]
    },
    "property-18": {
      title: "Fully Furnished Flat, Vikhroli",
      type: "Resale Apartment",
      location: "Vikhroli, Mumbai",
      price: "₹1.25 Cr (Negotiable)",
      image: "https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80",
      status: "Ready to Move / For Sale",
      configs: [
        { type: "Fully Furnished Flat", size: "527 sq.ft carpet", price: "₹1.25 Cr (Negotiable, incl. Car Parking)" }
      ],
      highlights: [
        "Fully furnished — kitchen trolley with cabinets, bed, TV unit",
        "Cabinets in all rooms & water purifier included",
        "Price negotiable, inclusive of car parking",
        "5 minutes walking distance to Vikhroli Station",
        "Schools, hospitals, market & banks within 5 minutes"
      ],
      connectivity: [
        "Vikhroli Station – 5 mins walking",
        "Schools, Hospitals, Market & Banks – within 5 mins"
      ]
    },
    "property-19": {
      title: "SoBo Deck Residences",
      type: "Luxury Residential Tower",
      location: "South Mumbai (SoBo)",
      price: "₹3.47 Cr onwards",
      image: "https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=800&q=80",
      status: "Under Construction",
      configs: [
        { type: "2 BHK Deck (RCA)", size: "890 sq.ft", price: "₹3.47 Cr++" },
        { type: "2 BHK Deck (RCA)", size: "913 sq.ft", price: "₹3.56 Cr++" },
        { type: "3 BHK Deck (RCA)", size: "1,080 sq.ft", price: "₹4.37 Cr++" }
      ],
      highlights: [
        "Supersized 2 & 3 bed deck homes with panoramic views",
        "Double height entrance lobby ready & large sundecks",
        "~14,000 sq.ft. of recreational spaces incl. Jain temple",
        "Swimming pool, terrace garden, yoga room & jogging track",
        "Jodi option available"
      ],
      connectivity: [
        "Prime South Mumbai (SoBo) location"
      ]
    },
    "property-20": {
      title: "Promont, BKC–Sion Connector",
      type: "Residential Tower",
      location: "BKC–Sion Connector, Mumbai",
      price: "Price on Request",
      image: "https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=800&q=80",
      status: "Under Construction, Possession Dec 2027",
      configs: [
        { type: "2 & 3 BHK Majestic Deck Residences", size: "On Request", price: "On Request" }
      ],
      highlights: [
        "A treasured life awaits at the BKC–Sion Connector",
        "Relaxing pool deck & Skyplex",
        "Café lounge, BBQ corner & jacuzzi",
        "Possession by December 2027",
        "Construction in full swing"
      ],
      connectivity: [
        "Located directly on the BKC–Sion Connector"
      ]
    },
    "property-21": {
      title: "Vikhroli Podium Residences",
      type: "Residential Tower",
      location: "Vikhroli, Mumbai",
      price: "₹1.75 Cr onwards",
      image: "https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80",
      status: "Under Construction",
      configs: [
        { type: "1 BHK", size: "On Request", price: "On Request" },
        { type: "2 BHK (Air-Conditioned)", size: "630 sq.ft", price: "₹1.75 Cr onwards, All Inclusive" }
      ],
      highlights: [
        "Ground + 5 Podium + 29 habitable floors",
        "Fully air-conditioned homes with false ceiling & LED lights",
        "Garden, jogging track, fitness centre & zen yoga deck",
        "Open air amphitheatre, swimming pool & kid's pool",
        "24×7 security with video door phone in every home"
      ],
      connectivity: [
        "Eastern Express Highway – 2 mins",
        "Railway Station – 7 mins",
        "Kannamwar Bus Depot – 2 mins",
        "R City Mall – 20 mins",
        "Metro Station – 5 mins"
      ]
    },
    "property-22": {
      title: "Zero-Wastage Residences, Vikhroli",
      type: "Residential Tower",
      location: "Vikhroli, Mumbai",
      price: "₹79 Lacs onwards",
      image: "https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=800&q=80",
      status: "New Launch",
      configs: [
        { type: "1 BHK", size: "359 / 374 sq.ft", price: "₹79 Lacs onwards" },
        { type: "2 BHK", size: "498 sq.ft", price: "₹99 Lacs onwards" }
      ],
      highlights: [
        "G+22 storey tower with spacious zero-wastage layouts",
        "Premium high-end retail outlets & podium level car park",
        "10,000 sq.ft. of dedicated amenities",
        "Sample flat available with unobstructed views",
        "Yoga zone, box cricket & rooftop party lawn"
      ],
      connectivity: [
        "Kannamwar Bus Depot – 2 mins",
        "Eastern Express Highway – 5 mins",
        "Vikhroli Railway Station – 6 mins",
        "International School & College – 8 mins"
      ]
    },
    "property-23": {
      title: "Vikhroli East Gated Community",
      type: "Luxury Residential Tower",
      location: "Vikhroli East, Mumbai",
      price: "₹1.08 Cr onwards",
      image: "https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80",
      status: "Under Construction",
      configs: [
        { type: "1 BHK", size: "426 sq.ft", price: "₹1.08 Cr All Inclusive" },
        { type: "2 BHK", size: "584–638 sq.ft", price: "₹1.56 Cr All Inclusive" }
      ],
      highlights: [
        "31-storey tower on a 1.25 acre gated land parcel",
        "2 levels basement + 4-level podium parking",
        "5 levels of exclusive lifestyle amenities",
        "30+ world-class amenities incl. infinity pool & spa",
        "Habitable residences begin from the 6th floor"
      ],
      connectivity: [
        "Prime highway-touch connectivity in Vikhroli East"
      ]
    },
    "property-24": {
      title: "1 BHK Resale, Tilak Nagar",
      type: "Resale Apartment",
      location: "Near Tilak Nagar Station, Mumbai",
      price: "₹24,000/sq.ft",
      image: "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80",
      status: "New Building",
      configs: [
        { type: "1 BHK", size: "596 sq.ft carpet", price: "₹24,000/sq.ft + Car Parking ₹10 Lakh" }
      ],
      highlights: [
        "New building, ready to move",
        "Car parking available at ₹10 Lakh",
        "Located near Tilak Nagar Station"
      ],
      connectivity: [
        "Close to Tilak Nagar Station"
      ]
    },
    "property-25": {
      title: "1 BHK Resale, Near Tilak Nagar",
      type: "Resale Apartment",
      location: "Near Tilak Nagar, Mumbai",
      price: "₹95 Lakh",
      image: "https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80",
      status: "For Sale",
      configs: [
        { type: "1 BHK", size: "375 sq.ft carpet", price: "₹95 Lakh" }
      ],
      highlights: [
        "Open view apartment",
        "Located near Tilak Nagar"
      ],
      connectivity: [
        "Close to Tilak Nagar"
      ]
    },
    "property-26": {
      title: "1 BHK Resale, Badlapur East",
      type: "Resale Apartment",
      location: "Badlapur East, Thane District",
      price: "₹33 Lakh",
      image: "https://images.unsplash.com/photo-1582407947304-fd86f028f716?auto=format&fit=crop&w=800&q=80",
      status: "Vacant / For Sale",
      configs: [
        { type: "1 BHK", size: "400 sq.ft carpet", price: "₹33 Lakh (Vacant)" }
      ],
      highlights: [
        "Currently vacant, ready for immediate sale",
        "Located in Badlapur East, Thane District"
      ],
      connectivity: [
        "Located in Badlapur East, Thane District"
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
        const inquiryText = `Hi, I am interested in inquiring about "${activePropertyTitle}". Please share more details, pricing brochures, and scheduling visits.`;
        
        if (contactSection && messageTextarea) {
          messageTextarea.value = inquiryText;
          messageTextarea.dispatchEvent(new Event('input', { bubbles: true }));
          contactSection.scrollIntoView({ behavior: 'smooth' });
          const nameInput = document.querySelector('input[name="name"]');
          if (nameInput) setTimeout(() => nameInput.focus(), 800);
        } else {
          sessionStorage.setItem('pendingInquiry', inquiryText);
          window.location.href = 'index.php#contact';
        }
      }, 350);
    });
  }

  /* ------------------------------------------------------------------------
     14.5 PROPERTY FILTERING LOGIC & CUSTOM DROPDOWNS
     ------------------------------------------------------------------------ */
  const filterForm = document.getElementById('property-filter-form');
  if (filterForm) {
    // Custom Dropdown Logic
    const customSelects = document.querySelectorAll('.custom-select-wrapper');
    
    customSelects.forEach(wrapper => {
      const trigger = wrapper.querySelector('.custom-select-trigger');
      const textSpan = trigger.querySelector('.custom-select-text');
      const options = wrapper.querySelectorAll('.custom-option');
      const hiddenInput = wrapper.nextElementSibling; // the hidden input
      
      // Toggle dropdown
      trigger.addEventListener('click', (e) => {
        // Close others
        customSelects.forEach(w => {
          if (w !== wrapper) w.classList.remove('open');
        });
        wrapper.classList.toggle('open');
        e.stopPropagation();
      });
      
      // Select option
      options.forEach(option => {
        option.addEventListener('click', () => {
          // Update visual selected state
          options.forEach(opt => opt.classList.remove('selected'));
          option.classList.add('selected');
          
          // Update text and value
          textSpan.textContent = option.textContent;
          hiddenInput.value = option.getAttribute('data-value');
          
          // Close dropdown
          wrapper.classList.remove('open');
        });
      });
    });

    // Close on click outside
    document.addEventListener('click', () => {
      customSelects.forEach(w => w.classList.remove('open'));
    });

    const locSelect = document.getElementById('filter-location');
    const bhkSelect = document.getElementById('filter-bhk');
    const allCards = document.querySelectorAll('.properties-grid .property-card');
    const noResultsMsg = document.getElementById('filter-no-results');
    const clearFiltersBtn = document.getElementById('clear-filters-btn');

    const applyFilters = () => {
      const locVal = locSelect.value;
      const bhkVal = bhkSelect.value;
      let visibleCount = 0;

      allCards.forEach(card => {
        const cardLoc = card.getAttribute('data-location');
        const cardBhk = card.getAttribute('data-bhk');

        let locMatch = (locVal === 'all' || cardLoc === locVal);
        let bhkMatch = (bhkVal === 'all' || (cardBhk && cardBhk.split(',').includes(bhkVal)));

        if (locMatch && bhkMatch) {
          card.style.display = 'block'; // Cards are display: block normally, image box etc handles layout
          visibleCount++;
        } else {
          card.style.display = 'none';
        }
      });

      if (visibleCount === 0) {
        noResultsMsg.style.display = 'block';
      } else {
        noResultsMsg.style.display = 'none';
      }

      if (typeof ScrollTrigger !== 'undefined') {
        ScrollTrigger.refresh();
      }
    };

    filterForm.addEventListener('submit', (e) => {
      e.preventDefault(); // Prevent page reload for instant frontend filtering
      applyFilters();
    });

    if (clearFiltersBtn) {
      clearFiltersBtn.addEventListener('click', () => {
        locSelect.value = 'all';
        bhkSelect.value = 'all';
        
        // Reset custom dropdown UI visually
        document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
          const textSpan = wrapper.querySelector('.custom-select-text');
          const options = wrapper.querySelectorAll('.custom-option');
          options.forEach(opt => opt.classList.remove('selected'));
          const defaultOpt = wrapper.querySelector('.custom-option[data-value="all"]');
          if (defaultOpt) {
            defaultOpt.classList.add('selected');
            textSpan.textContent = defaultOpt.textContent;
          }
        });
        
        applyFilters();
      });
    }
  }

  /* ------------------------------------------------------------------------
     15. SERVICE DETAILS MODAL SYSTEM
     ------------------------------------------------------------------------ */
  const servicesData = {
    "service-1": {
      title: "Buying Assistance",
      icon: "fa-house-chimney-window",
      tagline: "Secure your dream home with end-to-end expert guidance.",
      desc: "Navigating the Mumbai real estate market can be complex. Our Buying Assistance service ensures a safe, seamless, and transparent journey. We help you search, evaluate, and acquire premium properties in Chembur, Tilak Nagar, Ghatkopar, and wider Mumbai with complete peace of mind.",
      scope: [
        "Access to verified premium listings (residential & commercial).",
        "Rigorous 30-year property title clearance check.",
        "Price negotiation support to secure the best value.",
        "Assistance with loan approvals and financial planning.",
        "Complete coordination for stamp duty and final registration."
      ]
    },
    "service-2": {
      title: "Selling & Marketing Assistance",
      icon: "fa-tags",
      tagline: "Get premium positioning and high-value closures.",
      desc: "Unlock the true value of your luxury residential or commercial property. We use modern digital marketing strategies, targeted local outreach, and our extensive broker network to connect you with qualified buyers and secure faster closures at premium valuations.",
      scope: [
        "Detailed property valuation to set the optimal listing price.",
        "High-definition photography and virtual tour creation.",
        "Premium positioning on leading real estate portals.",
        "Pre-vetting and screening of potential buyers.",
        "End-to-end deal structuring and documentation support."
      ]
    },
    "service-3": {
      title: "Leasing & Rental Services",
      icon: "fa-key",
      tagline: "Smooth rental placements for tenants and landlords.",
      desc: "Whether you are a landlord looking for reliable tenants or a tenant looking for a premium home or commercial space, we handle the entire matching process. We focus on Tilak Nagar, Chembur, and surrounding areas to find safe, respectful, and high-yield rentals.",
      scope: [
        "Extensive database of rental apartments, penthouses, and shops.",
        "Tenant background verification and profile checks.",
        "Customized rental agreement drafting and online registrations.",
        "Cooperative society NOC and police verification processing.",
        "Yearly rental renewals and deposit management support."
      ]
    },
    "service-4": {
      title: "Investment Portfolio Advisory",
      icon: "fa-briefcase",
      tagline: "Maximize capital growth and rental yields.",
      desc: "Make informed real estate investments. We analyze local market micro-trends, localized supply-demand metrics, upcoming infrastructure developments (like SCLR connectors and Metro line 4), and RERA listings to construct high-yield portfolios with minimum risks.",
      scope: [
        "Identifying under-construction projects with high capital growth potential.",
        "Advisory on pre-leased commercial assets with immediate yield.",
        "Guidance on Section 54 EC bonds and capital gain tax planning.",
        "Comprehensive risk assessment and RERA legal checks.",
        "Custom portfolio building for both domestic and NRI investors."
      ]
    },
    "service-5": {
      title: "Property Valuation Services",
      icon: "fa-calculator",
      tagline: "Accurate property valuation based on real-time market data.",
      desc: "Understanding the exact worth of a property is essential for transactions, legal clearances, and taxation. Our valuation team delivers certified valuation reports utilizing current localized pricing indices and real-time transaction data.",
      scope: [
        "Comparative market analysis based on recent localized sales.",
        "Depreciated cost calculation for older structural assets.",
        "Certified valuation reports for bank loan processing.",
        "Capital gains tax assessment support.",
        "Fair market valuation for partition deeds and family settlements."
      ]
    },
    "service-6": {
      title: "RERA Title & Legal Verification",
      icon: "fa-file-contract",
      tagline: "Meticulous legal vetting and RERA title checks.",
      desc: "Real estate transactions involve massive legal documentation. We provide expert legal support, conducting thorough title searches, RERA compliance checks, and drafting custom sale agreements to shield you from litigation and ownership disputes.",
      scope: [
        "30-year search report of the property registry.",
        "Vetting of developer layouts, floor plans, and CC/OC certificates.",
        "Drafting custom Sale Agreements, Gift Deeds, and Power of Attorneys.",
        "Verification of property card status and land records.",
        "Resolution of inheritance disputes and title mutations."
      ]
    },
    "service-7": {
      title: "Home Loan & Finance Liaison",
      icon: "fa-percent",
      tagline: "Get the best loan interest rates and swift disbursements.",
      desc: "Don't let finance hold you back from your dream home. We maintain direct relationships with top public and private banking partners to help you secure home loans at competitive rates, with minimal paperwork and faster approvals.",
      scope: [
        "Comparative analysis of home loan offers from multiple banks.",
        "Step-by-step assistance in loan documentation and submissions.",
        "Swift processing for self-employed and salaried applicants.",
        "Liaison with bank legal and technical evaluation teams.",
        "Support for loan transfers (balance transfer) and top-up loans."
      ]
    },
    "service-8": {
      title: "Stamp Duty & Property Registration",
      icon: "fa-pen-nib",
      tagline: "Seamless stamp duty calculations and sub-registrar slot bookings.",
      desc: "Avoid long waiting hours at government offices. Our team handles the entire sub-registrar registration process, from calculating correct stamp duty values, paying online challans, booking registry slots, to accompanying you to the registrar office for final sign-off.",
      scope: [
        "Precise calculation of stamp duty and registration fees.",
        "Safe online payments of government challans (GRAS).",
        "Online slot booking for registration appointments.",
        "Physical presence and assistance of a coordinator at the Sub-Registrar office.",
        "Timely collection and delivery of registered original deeds."
      ]
    }
  };

  const serviceModal = document.getElementById('service-details-modal');
  const serviceCloseBtn = document.getElementById('service-modal-close-btn');
  const serviceOverlay = document.getElementById('service-modal-overlay');
  const serviceInquireBtn = document.getElementById('service-inquire-btn');
  const serviceWhatsappBtn = document.getElementById('service-whatsapp-btn');
  
  let activeServiceTitle = "";

  function openServiceModal(id) {
    const data = servicesData[id];
    if (!data || !serviceModal) return;

    activeServiceTitle = data.title;

    // Populate header
    document.getElementById('service-modal-title').textContent = data.title;
    document.getElementById('service-modal-tagline').textContent = data.tagline;
    document.getElementById('service-modal-desc').textContent = data.desc;
    
    // Set Icon
    const iconWrapper = document.getElementById('service-modal-icon');
    if (iconWrapper) {
      iconWrapper.innerHTML = `<i class="fa-solid ${data.icon}"></i>`;
    }

    // Populate Scope list
    const scopeUl = document.getElementById('service-modal-scope');
    if (scopeUl) {
      scopeUl.innerHTML = '';
      data.scope.forEach(item => {
        const li = document.createElement('li');
        li.textContent = item;
        scopeUl.appendChild(li);
      });
    }

    // Configure WhatsApp Link
    if (serviceWhatsappBtn) {
      const whatsappText = encodeURIComponent(`Hi Apnaa Ghar Real Estate & Interior Team, I am interested in inquiring about your "${data.title}" service. Please share details on how to proceed.`);
      serviceWhatsappBtn.href = `https://wa.me/917021316956?text=${whatsappText}`;
    }

    // Open Modal
    serviceModal.classList.add('open');
    serviceModal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden'; // Disable scroll

    // GSAP Animation if available
    if (typeof gsap !== 'undefined') {
      gsap.fromTo('.service-modal-wrapper', 
        { scale: 0.9, y: 30, opacity: 0 },
        { scale: 1, y: 0, opacity: 1, duration: 0.5, ease: 'power2.out', overwrite: 'auto' }
      );
    }
  }

  function closeServiceModal() {
    if (!serviceModal) return;
    
    if (typeof gsap !== 'undefined') {
      gsap.to('.service-modal-wrapper', {
        scale: 0.9,
        y: 30,
        opacity: 0,
        duration: 0.3,
        ease: 'power2.in',
        overwrite: 'auto',
        onComplete: () => {
          serviceModal.classList.remove('open');
          serviceModal.setAttribute('aria-hidden', 'true');
          document.body.style.overflow = 'auto'; // Restore scroll
        }
      });
    } else {
      serviceModal.classList.remove('open');
      serviceModal.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = 'auto'; // Restore scroll
    }
  }

  // Bind service card click triggers
  const serviceCards = document.querySelectorAll('.service-card');
  serviceCards.forEach(card => {
    const id = card.getAttribute('data-service-id');
    
    card.addEventListener('click', (e) => {
      e.preventDefault();
      if (id) openServiceModal(id);
    });
  });

  // Bind modal closing triggers
  if (serviceCloseBtn) serviceCloseBtn.addEventListener('click', closeServiceModal);
  if (serviceOverlay) serviceOverlay.addEventListener('click', closeServiceModal);

  // Close on Escape key
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && serviceModal && serviceModal.classList.contains('open')) {
      closeServiceModal();
    }
  });

  // Bind Enquire Button Action
  if (serviceInquireBtn) {
    serviceInquireBtn.addEventListener('click', () => {
      closeServiceModal();
      
      setTimeout(() => {
        const contactSection = document.getElementById('contact');
        const messageTextarea = document.getElementById('client-message');
        
        if (messageTextarea) {
          messageTextarea.value = `Hi, I am interested in inquiring about your "${activeServiceTitle}" service. Please share details on what documents are required and service charges.`;
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
