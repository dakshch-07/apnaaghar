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

  // Animate preloader loading progress (Slowed down to take ~2 seconds)
  const progressInterval = setInterval(() => {
    progress += Math.floor(Math.random() * 2) + 1;
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
          // Slowed down fade out animation (0.8s)
          gsap.to(preloader, {
            opacity: 0,
            duration: 0.8,
            ease: "power2.out",
            onComplete: () => {
              if (preloader) preloader.style.display = 'none';
              document.body.style.overflow = 'auto';
              checkPendingInquiry();
            }
          });
        } else {
          // Fallback if GSAP fails to load (0.8s)
          if (preloader) {
            preloader.style.transition = 'opacity 0.8s ease';
            preloader.style.opacity = 0;
            setTimeout(() => {
              preloader.style.display = 'none';
              checkPendingInquiry();
            }, 800);
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
    el.addEventListener('mousemove', function (e) {
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

    el.addEventListener('mouseleave', function () {
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

    if (document.querySelector('.hero')) {
      // 1. Zoom effect for hero image
      gsap.from(".hero-img", {
        scale: 1.15,
        opacity: 0,
        duration: 1.5,
        ease: "power2.out"
      });

      // 2. Reveal text in Hero
      gsap.from(".hero-rating-badge", {
        y: 20,
        opacity: 0,
        duration: 0.8,
        ease: "power2.out",
        delay: 0.3
      });

      gsap.from(".hero-title", {
        y: 40,
        opacity: 0,
        duration: 1,
        ease: "power3.out",
        delay: 0.5
      });

      gsap.from(".hero-subtitle", {
        y: 30,
        opacity: 0,
        duration: 1,
        ease: "power3.out",
        delay: 0.7
      });

      gsap.from(".hero-actions", {
        y: 20,
        opacity: 0,
        duration: 1,
        ease: "power3.out",
        delay: 0.9
      });
    }

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


  const detailsModal = document.getElementById('property-details-modal');
  const modalCloseBtn = document.getElementById('modal-close-btn');
  const modalOverlay = document.getElementById('modal-overlay');
  const modalInquireBtn = document.getElementById('modal-inquire-btn');

  let activePropertyTitle = "";

  function openPropertyModal(id) {
    const data = (typeof window.dynamicPropertiesData !== 'undefined' && window.dynamicPropertiesData[id])
      ? window.dynamicPropertiesData[id]
      : propertiesData[id];
    if (!data || !detailsModal) return;

    activePropertyTitle = data.title;

    // Populating header
    document.getElementById('modal-property-title').textContent = data.title;
    document.getElementById('modal-property-type').textContent = data.type;
    document.getElementById('modal-property-status').textContent = data.status;
    document.getElementById('modal-property-location').innerHTML = `<i class="fa-solid fa-location-dot"></i> ${data.location}`;
    document.getElementById('modal-property-price').textContent = data.price;

    // Set images in Swiper
    const swiperWrapper = document.getElementById('modal-property-swiper-wrapper');
    if (swiperWrapper) {
      swiperWrapper.innerHTML = '';
      if (data.images && data.images.length > 0) {
        data.images.forEach(img => {
          const slide = document.createElement('div');
          slide.className = 'swiper-slide';
          slide.innerHTML = `<img src="${img}" alt="${data.title}" class="modal-property-img">`;
          swiperWrapper.appendChild(slide);
        });
      } else {
        const slide = document.createElement('div');
        slide.className = 'swiper-slide';
        slide.innerHTML = `<img src="${data.image}" alt="${data.title}" class="modal-property-img">`;
        swiperWrapper.appendChild(slide);
      }
      
      // Initialize or update Swiper
      if (window.modalPropertySwiper) {
        window.modalPropertySwiper.destroy(true, true);
      }
      window.modalPropertySwiper = new Swiper('#modal-property-slider', {
        loop: false,
        navigation: {
          nextEl: '.modal-swiper-next',
          prevEl: '.modal-swiper-prev',
        },
        observer: true,
        observeParents: true
      });
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
      // Prevent opening if clicking bookmark or slider arrows
      if (e.target.closest('.bookmark-btn') || e.target.closest('.swiper-button-next') || e.target.closest('.swiper-button-prev')) return;

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
    const priceSlider = document.getElementById('filter-price');
    const priceSliderVal = document.getElementById('price-slider-val');
    const allCards = document.querySelectorAll('.properties-grid .property-card');
    const noResultsMsg = document.getElementById('filter-no-results');
    const clearFiltersBtn = document.getElementById('clear-filters-btn');

    if (priceSlider) {
      priceSlider.addEventListener('input', (e) => {
        let val = parseFloat(e.target.value);
        if (val === 50) {
           priceSliderVal.textContent = '50+ Cr';
        } else if (val < 1) {
           priceSliderVal.textContent = Math.round(val * 100) + ' Lakhs';
        } else {
           priceSliderVal.textContent = val.toFixed(2) + ' Cr';
        }
      });
      priceSlider.addEventListener('change', () => {
         applyFilters();
      });
    }

    const applyFilters = () => {
      const locVal = locSelect ? locSelect.value : 'all';
      const bhkVal = bhkSelect ? bhkSelect.value : 'all';
      const maxPrice = priceSlider ? parseFloat(priceSlider.value) : 50;
      let visibleCount = 0;

      allCards.forEach(card => {
        const cardLoc = card.getAttribute('data-location');
        const cardBhk = card.getAttribute('data-bhk');
        const cardPrice = parseFloat(card.getAttribute('data-price') || 50);

        let locMatch = (locVal === 'all' || (cardLoc && cardLoc.includes(locVal)));
        let bhkMatch = (bhkVal === 'all' || (cardBhk && cardBhk.split(',').includes(bhkVal)));
        let priceMatch = (cardPrice <= maxPrice || maxPrice === 50);

        if (locMatch && bhkMatch && priceMatch) {
          card.style.display = 'block'; // Cards are display: block normally, image box etc handles layout
          visibleCount++;
        } else {
          card.style.display = 'none';
        }
      });

      if (noResultsMsg) {
        noResultsMsg.style.display = visibleCount === 0 ? 'block' : 'none';
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
        if (priceSlider) {
            priceSlider.value = 50;
            priceSliderVal.textContent = '50+ Cr';
        }

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
      const whatsappText = encodeURIComponent(`Hi Apnaa Ghar Team, I am interested in inquiring about your "${data.title}" service. Please share details on how to proceed.`);
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
  // Bind "Why Choose Us" cards to WhatsApp enquiry action
  const whyCards = document.querySelectorAll('.why-glass-card');
  whyCards.forEach(card => {
    card.addEventListener('click', (e) => {
      e.preventDefault();
      const title = card.querySelector('h3').textContent;
      const whatsappText = encodeURIComponent(`Hi Apnaa Ghar Team, I am interested in your service/feature: "${title}". Please share more details.`);
      const whatsappUrl = `https://wa.me/917021316956?text=${whatsappText}`;
      window.open(whatsappUrl, '_blank');
    });
  });
});

