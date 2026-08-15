<?php
require_once 'includes/db.php';

// Fetch Properties (Limit to 3 for Homepage)
$stmt = $pdo->query("SELECT * FROM properties ORDER BY created_at DESC LIMIT 3");
$properties = $stmt->fetchAll();

// Build JSON object for the GSAP frontend modals
$propertiesJsonObj = [];
foreach ($properties as $prop) {
    $propId = 'property-' . $prop['id'];
    $propertiesJsonObj[$propId] = [
        'title' => $prop['title'],
        'type' => $prop['type'],
        'location' => $prop['location'],
        'price' => $prop['price'],
        'image' => (strpos($prop['image_url'], 'http') === 0) ? $prop['image_url'] : $prop['image_url'],
        'status' => $prop['status'],
        'configs' => [
            [
                'type' => $prop['bhk'],
                'size' => $prop['size'],
                'price' => $prop['price']
            ]
        ],
        'highlights' => json_decode($prop['highlights_json'], true) ?: [],
        'connectivity' => json_decode($prop['connectivity_json'], true) ?: []
    ];
}

// Fetch Gallery
$g_stmt = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC");
$gallery_items = $g_stmt->fetchAll();

// Fetch Category Counts
$stmtApt = $pdo->query("SELECT COUNT(*) FROM properties WHERE type LIKE '%Apartment%' OR type LIKE '%Tower%' OR type LIKE '%Project%' OR type LIKE '%Residences%' OR type LIKE '%Duplex%'");
$aptCount = $stmtApt->fetchColumn();

$stmtComm = $pdo->query("SELECT COUNT(*) FROM properties WHERE type LIKE '%Commercial%' OR type LIKE '%Office%'");
$commCount = $stmtComm->fetchColumn();

$stmtVilla = $pdo->query("SELECT COUNT(*) FROM properties WHERE type LIKE '%Villa%' OR type LIKE '%Bungalow%'");
$villaCount = $stmtVilla->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Primary Meta Tags -->
  <title>Apnaa Ghar Real Estate | Premium Properties in Chembur, Mumbai</title>
  <meta name="title" content="Apnaa Ghar Real Estate | Premium Properties in Chembur, Mumbai">
  <meta name="description" content="Discover luxury apartments, penthouses, commercial spaces, and villas in Chembur, Mumbai. Guided by trust, integrity, and transparency. 4.9 rated agency with 58+ reviews.">
  <meta name="keywords" content="Apnaa Ghar Real Estate, Chembur Real Estate, Mumbai Luxury Properties, Buy Flat Chembur, Real Estate Agent Tilak Nagar, Kurla, Ghatkopar, Mumbai">
  
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://apnaagharrealestate.in/">
  <meta property="og:title" content="Apnaa Ghar Real Estate | Luxury Homes in Mumbai">
  <meta property="og:description" content="Discover premium apartments, modern interiors, and investment properties in Chembur, Ghatkopar, and Tilak Nagar. 4.9 Google rating.">
  <meta property="og:image" content="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&h=630&q=80">

  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="https://apnaagharrealestate.in/">
  <meta property="twitter:title" content="Apnaa Ghar Real Estate | Luxury Homes in Mumbai">
  <meta property="twitter:description" content="Discover premium apartments, modern interiors, and investment properties in Chembur, Ghatkopar, and Tilak Nagar. 4.9 Google rating.">
  <meta property="twitter:image" content="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&h=630&q=80">

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="favicon.png">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/style_v2.css">



  <!-- SEO Structured Data Schema (JSON-LD) -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "RealEstateAgent",
    "name": "Apnaa Ghar Real Estate",
    "image": "https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80",
    "@id": "https://apnaagharrealestate.in/#agent",
    "url": "https://apnaagharrealestate.in/",
    "telephone": "+917021316956",
    "priceRange": "$$$",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "Building No.143, New Tilak Nagar, Near Tilak Nagar Railway Station, Chembur",
      "addressLocality": "Mumbai",
      "addressRegion": "Maharashtra",
      "postalCode": "400089",
      "addressCountry": "IN"
    },
    "geo": {
      "@type": "GeoCoordinates",
      "latitude": 19.0712,
      "longitude": 72.8984
    },
    "openingHoursSpecification": {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": [
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
        "Sunday"
      ],
      "opens": "09:00",
      "closes": "21:00"
    },
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "4.9",
      "reviewCount": "58"
    },
    "knowsLanguage": ["English", "Hindi", "Marathi"],
    "areaServed": ["Chembur", "Tilak Nagar", "Kurla", "Ghatkopar", "Mumbai"],
    "additionalType": "http://www.productontology.org/doc/Real_estate_broker"
  }
  </script>
</head>
<body>

  <!-- 1. PRELOADER -->
  <div id="preloader" class="preloader">
    <div class="preloader-content">
      <div class="preloader-logo" style="margin-bottom: 1.5rem;">
        <img src="logo.png" alt="Apnaa Ghar" style="height: 100px; object-fit: contain; mix-blend-mode: multiply;">
        <span class="logo-text-cormorant" style="margin-top: 15px; display: block;">Apnaa Ghar</span>
        <span class="logo-subtext">REAL ESTATE</span>
      </div>
      <div class="preloader-bar-container">
        <div class="preloader-bar"></div>
      </div>
      <p class="preloader-status">Crafting Premium Space...</p>
    </div>
  </div>

  <!-- CUSTOM CURSOR -->
  <div class="custom-cursor" id="custom-cursor"></div>
  <div class="custom-cursor-follower" id="custom-cursor-follower"></div>

  <!-- 2. STICKY NAVBAR -->
  <header id="header" class="header">
    <div class="container navbar-container">
      <a href="#home" class="logo magnetic" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
        <img src="logo.png" alt="Apnaa Ghar Real Estate" style="height: 55px; max-width: 100%; object-fit: contain; mix-blend-mode: multiply;">
      </a>
      
      <nav id="nav-menu" class="nav-menu">
        <a href="#home" class="nav-link active">Home</a>
        <a href="#properties" class="nav-link">Properties</a>
        <a href="#about" class="nav-link">About Us</a>
        <a href="#categories" class="nav-link">Categories</a>
        <a href="#services" class="nav-link">Services</a>
        <a href="#testimonials" class="nav-link">Reviews</a>
        <a href="#gallery" class="nav-link">Gallery</a>
        <a href="#contact" class="nav-link">Contact</a>
      </nav>

      <div class="nav-cta">
        <a href="#contact" class="btn btn-gold btn-navbar-cta magnetic">
          Book Consultation <i class="fa-solid fa-calendar-alt"></i>
        </a>
        <button id="hamburger" class="hamburger" aria-label="Toggle menu">
          <span></span>
          <span></span>
          <span></span>
        </button>
      </div>
    </div>
  </header>

  <!-- MOBILE NAVIGATION DRAWER -->
  <div class="mobile-drawer" id="mobile-drawer">
    <div class="mobile-drawer-header" style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem 2rem;">
      <img src="logo.png" alt="Apnaa Ghar" style="height: 40px; object-fit: contain; mix-blend-mode: multiply;">
      <button class="drawer-close" id="drawer-close">&times;</button>
    </div>
    <nav class="mobile-drawer-nav">
      <a href="#home" class="mobile-link active">Home</a>
      <a href="#properties" class="mobile-link">Properties</a>
      <a href="#about" class="mobile-link">About Us</a>
      <a href="#categories" class="mobile-link">Categories</a>
      <a href="#services" class="mobile-link">Services</a>
      <a href="#testimonials" class="mobile-link">Reviews</a>
      <a href="#gallery" class="mobile-link">Gallery</a>
      <a href="#contact" class="mobile-link">Contact</a>
    </nav>
    <div class="mobile-drawer-footer">
      <a href="tel:+917021316956" class="mobile-footer-btn"><i class="fa-solid fa-phone"></i> Call Now</a>
      <a href="#contact" class="mobile-footer-btn outline" id="drawer-cta-btn">Book Consultation</a>
    </div>
  </div>

  <main>
    <!-- 3. HERO SECTION -->
    <section id="home" class="hero">
      <div class="hero-bg-zoom">
        <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1920&q=80" alt="Apnaa Ghar Luxury Villa Background" class="hero-img">
      </div>
      <div class="hero-overlay"></div>
      
      <div class="container hero-content">
        <div class="hero-texts">
          <!-- Floating Google Rating Badge -->
          <div class="hero-rating-badge magnetic">
            <div class="rating-badge-stars">★★★★★</div>
            <div class="rating-badge-txt">
              <strong>4.9</strong> Google Rating <span>(58+ Reviews)</span>
            </div>
            <div class="badge-tag">LGBTQ+ Friendly</div>
          </div>
          
          <h1 class="hero-title reveal-text">
            Find Your Dream Home <br><span class="accent-gold text-cormorant">In Mumbai</span>
          </h1>
          <p class="hero-subtitle">
            Helping families discover premium homes, apartments and investment properties with complete transparency and trusted guidance. Serving Chembur, Tilak Nagar, Kurla, Ghatkopar & Mumbai.
          </p>
          
          <div class="hero-actions">
            <a href="#properties" class="btn btn-gold btn-large magnetic">Explore Properties <i class="fa-solid fa-arrow-right"></i></a>
            <a href="#contact" class="btn btn-outline btn-large magnetic">Schedule Visit <i class="fa-solid fa-clock"></i></a>
          </div>
        </div>
      </div>

      <!-- Scroll Down Indicator -->
      <a href="#properties" class="scroll-down-indicator" aria-label="Scroll to properties">
        <span class="mouse-icon">
          <span class="mouse-wheel"></span>
        </span>
        <span class="scroll-text">Scroll Down</span>
      </a>
    </section>

    <!-- 4. PARALLAX SECTION BANNER -->
    <section class="parallax-quote-section">
      <div class="parallax-bg-wrapper" style="background-image: url('https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1920&q=80')">
        <div class="parallax-overlay"></div>
      </div>
      <div class="container relative z-index-2">
        <div class="parallax-quote-box text-center">
          <span class="quote-eyebrow">Exclusive Living Spaces</span>
          <h2 class="quote-text text-cormorant">"Home is not a place, it's a premium feeling of comfort, luxury, and belonging."</h2>
          <div class="divider-gold"></div>
        </div>
      </div>
    </section>

    <!-- 5. FEATURED PROPERTIES -->
    <section id="properties" class="properties-section section-padding">
      <div class="container">
        <div class="section-header text-center reveal-el">
          <span class="section-tag">Curated Collections</span>
          <h2 class="section-title text-cormorant">Featured Properties</h2>
          <div class="title-underline"></div>
          <p class="section-desc">Handpicked architectural masterpieces and premium residences offering world-class amenities in Mumbai.</p>
        </div>

        <div class="properties-grid">
          <?php foreach($properties as $prop): ?>
          <div class="property-card reveal-el" data-property-id="property-<?php echo $prop['id']; ?>">
            <div class="property-image-box">
              <img src="<?php echo strpos($prop['image_url'], 'http') === 0 ? $prop['image_url'] : $prop['image_url']; ?>" alt="<?php echo htmlspecialchars($prop['title']); ?>" class="property-img">
              <div class="property-badge-group">
                <?php if(!empty($prop['badge_status'])): ?>
                <span class="badge-status <?php echo $prop['badge_status'] == 'FOR RENT' ? 'for-rent' : 'for-sale'; ?>"><?php echo htmlspecialchars($prop['badge_status']); ?></span>
                <?php endif; ?>
                <?php if(!empty($prop['badge_featured'])): ?>
                <span class="badge-featured"><?php echo htmlspecialchars($prop['badge_featured']); ?></span>
                <?php endif; ?>
              </div>
              <button class="bookmark-btn" aria-label="Bookmark property">
                <i class="fa-regular fa-bookmark"></i>
              </button>
            </div>
            <div class="property-content">
              <div class="property-type-tag"><?php echo htmlspecialchars($prop['type']); ?></div>
              <h3 class="property-title"><?php echo htmlspecialchars($prop['title']); ?></h3>
              <p class="property-location"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($prop['location']); ?></p>
              <div class="property-features">
                <span><i class="fa-solid fa-bed"></i> <?php echo htmlspecialchars($prop['bhk']); ?></span>
                <span><i class="fa-solid fa-ruler-combined"></i> <?php echo htmlspecialchars($prop['size']); ?></span>
              </div>
              <div class="property-card-footer">
                <div class="property-price"><?php echo htmlspecialchars($prop['price']); ?></div>
                <a href="#contact" class="btn-card-link magnetic">Know More <i class="fa-solid fa-arrow-right"></i></a>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        
        <div class="text-center reveal-el" style="margin-top: 40px;">
          <a href="properties.php" class="btn btn-gold btn-large magnetic">
            View All Properties <i class="fa-solid fa-arrow-right"></i>
          </a>
        </div>
      </div>
    </section>

    <!-- 6. ABOUT US SECTION -->
    <section id="about" class="about-section section-padding">
      <div class="container">
        <div class="grid grid-2 about-grid-layout">
          <!-- Left: Split Luxury Photography with Parallax Elements -->
          <div class="about-visuals reveal-el">
            <div class="about-img-main-wrapper">
              <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80" alt="Apnaa Ghar Premium Office Interaction" class="about-img-main">
            </div>
            <div class="about-img-secondary-wrapper">
              <img src="https://images.unsplash.com/photo-1582407947304-fd86f028f716?auto=format&fit=crop&w=800&q=80" alt="Mumbai Skyline Luxury Architecture" class="about-img-secondary">
            </div>
            <!-- Counter Badges -->
            <div class="stats-badge-floating magnetic">
              <div class="stats-icon"><i class="fa-solid fa-handshake"></i></div>
              <h4 class="counter-val" data-target="1250">0</h4>
              <span>Happy Families Served</span>
            </div>
          </div>

          <!-- Right: Narrative Story, Mission, Counters -->
          <div class="about-texts-column reveal-el">
            <span class="section-tag">About Apnaa Ghar</span>
            <h2 class="section-title text-cormorant">Crafting Lifestyles & Securing Legacies</h2>
            <div class="title-underline left"></div>
            
            <!-- Stats Grid with Counter Animations -->
            <div class="about-stats-grid">
              <div class="stat-card-box">
                <h3 class="stat-num"><span class="counter-val" data-target="15">0</span>+</h3>
                <p class="stat-lbl">Years Experience</p>
              </div>
              <div class="stat-card-box">
                <h3 class="stat-num"><span class="counter-val" data-target="350">0</span>+</h3>
                <p class="stat-lbl">Properties Sold</p>
              </div>
              <div class="stat-card-box">
                <h3 class="stat-num"><span class="counter-val" data-target="4.9">0.0</span>/5</h3>
                <p class="stat-lbl">Google Rating</p>
              </div>
            </div>
            
            <p class="about-narrative">
              With deep-rooted expertise in Chembur and surrounding suburbs of Mumbai, Apnaa Ghar Real Estate has established a benchmark of absolute transparency, personalized advice, and customer-first guidance.
            </p>
            
            <div class="about-tabs-container">
              <div class="about-tab-buttons">
                <button class="about-tab-btn active" data-tab="story">Our Story</button>
                <button class="about-tab-btn" data-tab="mission">Mission & Vision</button>
                <button class="about-tab-btn" data-tab="lgbtq">Inclusivity</button>
              </div>
              
              <div class="about-tab-contents">
                <div class="tab-content active" id="tab-story">
                  For over a decade, we have navigated Mumbai's dynamic real estate landscape, helping individuals buy, sell, and rent spaces that represent their dreams. We take pride in building lifetime partnerships rather than transacting properties.
                </div>
                <div class="tab-content" id="tab-mission">
                  Our mission is to empower families with accurate property guidance, legal clarity, and transparent deals. We envision Apnaa Ghar as Mumbai's most trusted premium real estate advisor, setting new records in client satisfaction.
                </div>
                <div class="tab-content" id="tab-lgbtq">
                  As an officially certified LGBTQ+ friendly real estate agency, we proudly offer a safe, respectful, and highly inclusive environment. Finding your home should be an empowering experience for everyone, without prejudice.
                </div>
              </div>
            </div>


            
            <a href="#contact" class="btn btn-gold btn-inline-link magnetic">Consult Our Expert <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </section>

    <!-- 7. WHY CHOOSE APNAA GHAR -->
    <section class="why-choose-section section-padding bg-dark text-white">
      <div class="container">
        <div class="section-header text-center reveal-el">
          <span class="section-tag gold">Why Apnaa Ghar</span>
          <h2 class="section-title text-cormorant text-white">Uncompromising Trust, Absolute Transparency</h2>
          <div class="title-underline"></div>
          <p class="section-desc text-secondary">Why discerning home buyers and elite investors choose Apnaa Ghar for property consulting in Mumbai suburbs.</p>
        </div>

        <div class="why-grid-layout">
          <!-- Glass Card 1 -->
          <div class="why-glass-card reveal-el">
            <div class="why-icon"><i class="fa-solid fa-shield-halved"></i></div>
            <h3>Verified Listings Only</h3>
            <p>Every flat, penthouse, or plot undergoes detailed cross-verifications before reaching our buyers.</p>
          </div>
          <!-- Glass Card 2 -->
          <div class="why-glass-card reveal-el">
            <div class="why-icon"><i class="fa-solid fa-user-tie"></i></div>
            <h3>Trusted Advisors</h3>
            <p>Our advisors have deep local market knowledge, identifying upcoming appreciation hotspots.</p>
          </div>
          <!-- Glass Card 3 -->
          <div class="why-glass-card reveal-el">
            <div class="why-icon"><i class="fa-solid fa-handshake-angle"></i></div>
            <h3>Transparent Deals</h3>
            <p>Zero hidden charges. Complete clarity on brokerages, maintenance costs, and builder clauses.</p>
          </div>
          <!-- Glass Card 4 -->
          <div class="why-glass-card reveal-el">
            <div class="why-icon"><i class="fa-solid fa-scale-balanced"></i></div>
            <h3>Legal Assistance</h3>
            <p>In-house legal experts specializing in property titles, RERA guidelines, and developer agreements.</p>
          </div>
          <!-- Glass Card 5 -->
          <div class="why-glass-card reveal-el">
            <div class="why-icon"><i class="fa-solid fa-car"></i></div>
            <h3>Site Visits Provided</h3>
            <p>Private luxury car site viewings scheduled at your absolute convenience with premium safety.</p>
          </div>
          <!-- Glass Card 6 -->
          <div class="why-glass-card reveal-el">
            <div class="why-icon"><i class="fa-solid fa-landmark"></i></div>
            <h3>Loan Assistance</h3>
            <p>Tie-ups with leading national and private banks (SBI, HDFC, ICICI) for fast home loan processing.</p>
          </div>
          <!-- Glass Card 7 -->
          <div class="why-glass-card reveal-el">
            <div class="why-icon"><i class="fa-solid fa-file-signature"></i></div>
            <h3>Property Documentation</h3>
            <p>Hassle-free registration support, index-2 processing, stamp duty handling, and title clearance.</p>
          </div>
          <!-- Glass Card 8 -->
          <div class="why-glass-card reveal-el">
            <div class="why-icon"><i class="fa-solid fa-chart-line"></i></div>
            <h3>Investment Guidance</h3>
            <p>Strategic counseling on capital gains, high-yield commercial rentals, and pre-launch benefits.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 8. PROPERTY CATEGORIES -->
    <section id="categories" class="categories-section section-padding">
      <div class="container">
        <div class="section-header text-center reveal-el">
          <span class="section-tag">Tailored Options</span>
          <h2 class="section-title text-cormorant">Property Categories</h2>
          <div class="title-underline"></div>
          <p class="section-desc">Select from our extensive portfolio of high-grade properties matching your lifestyle or corporate budget.</p>
        </div>

        <div class="categories-grid">
          <!-- Cat 1 -->
          <div class="category-box reveal-el">
            <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=600&q=80" alt="Premium Apartments" class="cat-img">
            <div class="cat-overlay">
              <h3 class="cat-title">Apartments</h3>
              <p class="cat-count"><?php echo $aptCount; ?>+ Listed</p>
              <a href="properties.php?bhk=all" class="cat-link magnetic"><i class="fa-solid fa-arrow-right"></i></a>
            </div>
          </div>
          <!-- Cat 2 -->
          <div class="category-box reveal-el">
            <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=600&q=80" alt="Commercial Offices" class="cat-img">
            <div class="cat-overlay">
              <h3 class="cat-title">Commercial</h3>
              <p class="cat-count"><?php echo $commCount; ?> Listed</p>
              <a href="properties.php?bhk=commercial" class="cat-link magnetic"><i class="fa-solid fa-arrow-right"></i></a>
            </div>
          </div>
          <!-- Cat 3 -->
          <div class="category-box reveal-el">
            <img src="https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=600&q=80" alt="Luxury Villas" class="cat-img">
            <div class="cat-overlay">
              <h3 class="cat-title">Luxury Villas</h3>
              <p class="cat-count"><?php echo $villaCount; ?> Listed</p>
              <a href="properties.php?bhk=all" class="cat-link magnetic"><i class="fa-solid fa-arrow-right"></i></a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 9. SERVICES -->
    <section id="services" class="services-section section-padding bg-accent">
      <div class="container">
        <div class="section-header text-center reveal-el">
          <span class="section-tag">Core Capabilities</span>
          <h2 class="section-title text-cormorant">Comprehensive Real Estate Services</h2>
          <div class="title-underline"></div>
          <p class="section-desc">From initial site browsing to handling local government registrations, we support your property journey from start to finish.</p>
        </div>

        <div class="services-grid">
          <!-- Service 1 -->
          <div class="service-card reveal-el" data-service-id="service-1">
            <div class="service-icon"><i class="fa-solid fa-house-chimney-window"></i></div>
            <h3 class="service-name">Buying</h3>
            <p class="service-text">Access exclusive listings in Chembur, Tilak Nagar, and wider Mumbai with verified title clearance.</p>
          </div>
          <!-- Service 2 -->
          <div class="service-card reveal-el" data-service-id="service-2">
            <div class="service-icon"><i class="fa-solid fa-tags"></i></div>
            <h3 class="service-name">Selling</h3>
            <p class="service-text">List your luxury property with us to gain premium digital positioning and secure high-value transactions.</p>
          </div>
          <!-- Service 3 -->
          <div class="service-card reveal-el" data-service-id="service-3">
            <div class="service-icon"><i class="fa-solid fa-key"></i></div>
            <h3 class="service-name">Renting</h3>
            <p class="service-text">Premium leasing support for residential apartments, villas, and commercial shops with customized agreements.</p>
          </div>
          <!-- Service 4 -->
          <div class="service-card reveal-el" data-service-id="service-4">
            <div class="service-icon"><i class="fa-solid fa-briefcase"></i></div>
            <h3 class="service-name">Investment Consulting</h3>
            <p class="service-text">Consultations on commercial high-yield investments, pre-RERA listings, and capital gain reinvestment plans.</p>
          </div>
          <!-- Service 5 -->
          <div class="service-card reveal-el" data-service-id="service-5">
            <div class="service-icon"><i class="fa-solid fa-calculator"></i></div>
            <h3 class="service-name">Property Valuation</h3>
            <p class="service-text">Accurate property evaluation reports utilizing current localized pricing indices and supply-demand trends.</p>
          </div>
          <!-- Service 6 -->
          <div class="service-card reveal-el" data-service-id="service-6">
            <div class="service-icon"><i class="fa-solid fa-file-contract"></i></div>
            <h3 class="service-name">Legal Documentation</h3>
            <p class="service-text">Complete administrative support for property title searches, deeds, mutations, and structural audits.</p>
          </div>
          <!-- Service 7 -->
          <div class="service-card reveal-el" data-service-id="service-7">
            <div class="service-icon"><i class="fa-solid fa-percent"></i></div>
            <h3 class="service-name">Home Loan Support</h3>
            <p class="service-text">End-to-end liaison with top banking partners to process and sanction loans at competitive interest rates.</p>
          </div>
          <!-- Service 8 -->
          <div class="service-card reveal-el" data-service-id="service-8">
            <div class="service-icon"><i class="fa-solid fa-pen-nib"></i></div>
            <h3 class="service-name">Registration Assistance</h3>
            <p class="service-text">Smooth stamp duty estimation, online payment tracking, slot bookings, and registration office assistance.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 10. CLIENT TESTIMONIALS -->
    <section id="testimonials" class="testimonials-section section-padding">
      <div class="container">
        <div class="section-header text-center reveal-el">
          <span class="section-tag">Google Rating 4.9 ★★★★★</span>
          <h2 class="section-title text-cormorant">Client Testimonials</h2>
          <div class="title-underline"></div>
          <p class="section-desc">Read reviews left on Google by families and investors who trusted Apnaa Ghar Real Estate.</p>
        </div>

        <div class="testimonial-slider-container reveal-el">
          <div class="testimonial-track" id="testimonial-track">
            
            <!-- Slide 1 -->
            <div class="testimonial-slide">
              <div class="testi-header">
                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&h=150&q=80" alt="Rahul Sharma" class="testi-avatar">
                <div class="testi-info">
                  <h4>Rahul Sharma</h4>
                  <div class="testi-stars">★★★★★</div>
                  <span class="testi-relation">Bought 3BHK in Chembur</span>
                </div>
              </div>
              <p class="testi-text">
                "Excellent service and smooth experience with the Apnaa Ghar team. They simplified the entire legal documentation, verified the title, and guided us with complete transparency. Highly cooperative and professional."
              </p>
              <div class="review-highlight-tag">Excellent service and smooth experience</div>
            </div>

            <!-- Slide 2 -->
            <div class="testimonial-slide">
              <div class="testi-header">
                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&h=150&q=80" alt="Priya Kadam" class="testi-avatar">
                <div class="testi-info">
                  <h4>Priya Kadam</h4>
                  <div class="testi-stars">★★★★★</div>
                  <span class="testi-relation">Rented Penthouse in Tilak Nagar</span>
                </div>
              </div>
              <p class="testi-text">
                "Finding an inclusive and safe environment as a minority couple was extremely important to us. Apnaa Ghar is truly friendly and respectful. They found us a beautiful home without any judgment."
              </p>
              <div class="review-highlight-tag">Friendly environment</div>
            </div>

            <!-- Slide 3 -->
            <div class="testimonial-slide">
              <div class="testi-header">
                <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&h=150&q=80" alt="Devendra Mehta" class="testi-avatar">
                <div class="testi-info">
                  <h4>Devendra Mehta</h4>
                  <div class="testi-stars">★★★★★</div>
                  <span class="testi-relation">Commercial Space Owner</span>
                </div>
              </div>
              <p class="testi-text">
                "Great communication and regular updates during our commercial shop sale. Their local networks in Kurla and Ghatkopar are outstanding. We closed the transaction well within our targeted timeline."
              </p>
              <div class="review-highlight-tag">Great communication</div>
            </div>

            <!-- Slide 4 -->
            <div class="testimonial-slide">
              <div class="testi-header">
                <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=150&h=150&q=80" alt="Smita Deshmukh" class="testi-avatar">
                <div class="testi-info">
                  <h4>Smita Deshmukh</h4>
                  <div class="testi-stars">★★★★★</div>
                  <span class="testi-relation">Plot Buyer</span>
                </div>
              </div>
              <p class="testi-text">
                "Time-saving support for registrations and title transfers. We didn't have to stand in queues at the registrar office—Apnaa Ghar team handled stamp duty payments, slot verification, and documentation seamlessly."
              </p>
              <div class="review-highlight-tag">Time-saving support</div>
            </div>

          </div>
          
          <div class="slider-controls">
            <button class="slider-arrow prev" id="slider-prev" aria-label="Previous review"><i class="fa-solid fa-arrow-left"></i></button>
            <div class="slider-dots" id="slider-dots"></div>
            <button class="slider-arrow next" id="slider-next" aria-label="Next review"><i class="fa-solid fa-arrow-right"></i></button>
          </div>
        </div>
      </div>
    </section>

    <!-- 11. GALLERY -->
    <section id="gallery" class="gallery-section section-padding bg-accent">
      <div class="container">
        <div class="section-header text-center reveal-el">
          <span class="section-tag">Visual Portfolios</span>
          <h2 class="section-title text-cormorant">Luxury Masonry Gallery</h2>
          <div class="title-underline"></div>
          <p class="section-desc">Explore inside views of our elite real estate office, premium properties, happy client interactions, and property site visits.</p>
        </div>

        <!-- Masonry Grid -->
        <div class="gallery-masonry reveal-el" id="gallery-masonry" style="margin-top: 2rem;">
          <?php foreach($gallery_items as $item): ?>
          <div class="gallery-item" data-category="<?php echo strtolower($item['category'] == 'Site Visits' ? 'visits' : $item['category']); ?>">
            <div class="gallery-img-box">
              <img src="<?php echo strpos($item['image_url'], 'http') === 0 ? $item['image_url'] : $item['image_url']; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="gallery-img">
              <div class="gallery-item-overlay">
                <span class="gallery-cat"><?php echo htmlspecialchars($item['category']); ?></span>
                <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                <div class="lightbox-trigger-btn"><i class="fa-solid fa-expand"></i></div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- LIGHTBOX MODAL FOR GALLERY -->
    <div id="lightbox" class="lightbox" role="dialog" aria-modal="true" aria-label="Image Lightbox">
      <button id="lightbox-close" class="lightbox-close" aria-label="Close lightbox">&times;</button>
      <button id="lightbox-prev" class="lightbox-nav lightbox-prev" aria-label="Previous image">&#10094;</button>
      <button id="lightbox-next" class="lightbox-nav lightbox-next" aria-label="Next image">&#10095;</button>
      <div class="lightbox-content">
        <img id="lightbox-img" src="" alt="Enlarged gallery photo" class="lightbox-img">
        <div id="lightbox-caption" class="lightbox-caption"></div>
      </div>
    </div>

    <!-- 12. CONTACT FORM SECTION -->
    <section id="contact" class="contact-section section-padding">
      <div class="container">
        <div class="section-header text-center reveal-el">
          <span class="section-tag">Secure Consultation</span>
          <h2 class="section-title text-cormorant">Connect With Our Property Advisors</h2>
          <div class="title-underline"></div>
          <p class="section-desc">Schedule a private site visit, seek loan approvals, or discuss commercial investments in Mumbai with our experts.</p>
        </div>

        <div class="grid grid-2 contact-grid-layout">
          <!-- Left: Agency Details, Reviews summary, Google Maps embed -->
          <div class="contact-details-column reveal-el">
            <div class="agency-contact-card">
              <h3>Apnaa Ghar Real Estate</h3>
              <p class="agency-address">
                <i class="fa-solid fa-map-location-dot"></i> 
                Building No.143, New Tilak Nagar, Near Tilak Nagar Railway Station, Chembur, Mumbai, Maharashtra 400089
              </p>
              <div class="contact-phone-links">
                <a href="tel:+917021316956" class="contact-info-link magnetic"><i class="fa-solid fa-phone"></i> +91 70213 16956</a>
                <a href="https://wa.me/917021316956" class="contact-info-link whatsapp magnetic"><i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp</a>
              </div>
              <div class="contact-email">
                <i class="fa-solid fa-envelope"></i> contact@apnaagharrealestate.com
              </div>
            </div>

            <!-- 13. GOOGLE MAPS EMBED -->
            <div class="map-embed-wrapper">
              <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3771.189722748805!2d72.89599547596041!3d19.055452252631583!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7c8ad81d4a04b%3A0x7d6a5759714856f7!2sTilak%20Nagar%20Railway%20Station!5e0!3m2!1sen!2sin!4v1719688402511!5m2!1sen!2sin" 
                width="100%" 
                height="320" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade"
                title="Apnaa Ghar Real Estate Location Map">
              </iframe>
              <a href="https://maps.google.com/?q=Building+No.143,+New+Tilak+Nagar,+Near+Tilak+Nagar+Railway+Station,+Chembur,+Mumbai" 
                 target="_blank" 
                 rel="noopener"
                 class="btn-directions magnetic">
                Get Directions <i class="fa-solid fa-location-arrow"></i>
              </a>
            </div>
          </div>

          <!-- Right: Contact Form -->
          <div class="contact-form-card reveal-el">
            <form id="realEstateForm" class="luxury-form" action="api/submit_enquiry.php" method="POST">
              <div id="form-alert-success" class="alert-box alert-success hidden" style="margin-top: 0; margin-bottom: 2rem;">
                <div class="alert-icon"><i class="fa-solid fa-circle-check"></i></div>
                <div class="alert-text">
                  <h4>Inquiry Submitted Successfully</h4>
                  <p>Our property consultant will contact you within the next 2 hours.</p>
                </div>
                <button type="button" class="alert-close-btn">&times;</button>
              </div>

              <div id="form-alert-error" class="alert-box alert-danger hidden" style="margin-top: 0; margin-bottom: 2rem;">
                <div class="alert-icon"><i class="fa-solid fa-circle-xmark"></i></div>
                <div class="alert-text">
                  <h4>Submission Failed</h4>
                  <p>Please double-check your connection or inputs and try again.</p>
                </div>
                <button type="button" class="alert-close-btn">&times;</button>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label for="client-name">Your Full Name <span class="required">*</span></label>
                  <input type="text" id="client-name" name="name" placeholder="Enter your full name" required>
                </div>
              </div>

              <div class="form-row grid-2-col">
                <div class="form-group">
                  <label for="client-phone">Phone Number <span class="required">*</span></label>
                  <input type="tel" id="client-phone" name="phone" placeholder="10-Digit Mobile" pattern="[0-9]{10}" required>
                </div>
                <div class="form-group">
                  <label for="client-email">Email Address <span class="required">*</span></label>
                  <input type="email" id="client-email" name="email" placeholder="example@email.com" required>
                </div>
              </div>

              <div class="form-row grid-2-col">
                <div class="form-group">
                  <label for="client-property-type">Interested Property</label>
                  <select id="client-property-type" name="interested_property">
                    <option value="" disabled selected>Choose type...</option>
                    <option value="Apartment">Apartment</option>
                    <option value="Commercial Space">Commercial Space</option>
                    <option value="Luxury Villa">Luxury Villa</option>
                    <option value="Builder Floor">Builder Floor</option>
                    <option value="Plot">Plot</option>
                    <option value="Rental Flat">Rental Home</option>
                    <option value="Investment Property">Investment Property</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="client-budget">Investment Budget</label>
                  <select id="client-budget" name="budget">
                    <option value="" disabled selected>Select range...</option>
                    <option value="Under 50 Lakhs">Under ₹50 Lakhs</option>
                    <option value="50 Lakhs - 1 Crore">₹50 Lakhs - ₹1 Crore</option>
                    <option value="1 Crore - 3 Crore">₹1 Crore - ₹3 Crore</option>
                    <option value="3 Crore - 5 Crore">₹3 Crore - ₹5 Crore</option>
                    <option value="Above 5 Crore">Above ₹5 Crore</option>
                  </select>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label for="client-message">Specific Requirements / Message</label>
                  <textarea id="client-message" name="message" rows="4" placeholder="Tell us about your preferences..."></textarea>
                </div>
              </div>

              <button type="submit" id="form-submit-btn" class="btn btn-gold btn-full magnetic">
                Send Inquiry <i class="fa-solid fa-paper-plane"></i>
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- 14. FOOTER -->
  <footer class="footer-luxury">
    <div class="container footer-top-grid">
      <!-- Footer Logo & Intro -->
      <div class="footer-col-desc">
        <a href="#home" class="footer-logo" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
          <img src="logo.png" alt="Apnaa Ghar" style="height: 50px; object-fit: contain; mix-blend-mode: multiply;">
        </a>
        <p class="footer-about-text">
          Mumbai's premium real estate consultancy serving Chembur, Tilak Nagar, Ghatkopar, Kurla and surrounding micro-markets since 2015. 4.9 rated.
        </p>
        <div class="footer-google-badges">
          <div class="stars">★★★★★ 4.9 Rating</div>
          <div class="badge-txt">58+ Reviews on Google</div>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="footer-col-nav">
        <h3>Quick Navigation</h3>
        <ul>
          <li><a href="#home">Home</a></li>
          <li><a href="#properties">Featured Properties</a></li>
          <li><a href="#about">About Our Agency</a></li>
          <li><a href="#categories">Property Types</a></li>
          <li><a href="#services">Consulting Services</a></li>
          <li><a href="#gallery">Masonry Gallery</a></li>
        </ul>
      </div>

      <!-- Services List -->
      <div class="footer-col-nav">
        <h3>Services</h3>
        <ul>
          <li><a href="#services">Buying Properties</a></li>
          <li><a href="#services">Selling Assistance</a></li>
          <li><a href="#services">Rental Portals</a></li>
          <li><a href="#services">RERA Title Search</a></li>
          <li><a href="#services">Stamp Duty & Registry</a></li>
          <li><a href="#services">Home Loan Support</a></li>
        </ul>
      </div>

      <!-- Contact Details -->
      <div class="footer-col-contact">
        <h3>Contact Agency</h3>
        <p><i class="fa-solid fa-location-dot"></i> Building No.143, New Tilak Nagar, near Railway Station, Chembur, Mumbai 400089</p>
        <p><i class="fa-solid fa-phone"></i> <a href="tel:+917021316956" class="footer-phone-link">+91 70213 16956</a></p>
        <p><i class="fa-solid fa-envelope"></i> contact@apnaagharrealestate.com</p>
        
        <!-- Social Icons -->
        <div class="footer-social-links">
          <a href="https://facebook.com" class="social-icon magnetic" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="https://instagram.com" class="social-icon magnetic" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="https://linkedin.com" class="social-icon magnetic" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
          <a href="https://wa.me/917021316956" class="social-icon magnetic" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
        </div>
      </div>
    </div>

    <!-- Footer Copyright -->
    <div class="footer-bottom-bar">
      <div class="container footer-bottom-flex">
        <p class="copyright-txt">&copy; 2026 Apnaa Ghar Real Estate. All rights reserved. Designed with premium craft. LGBTQ+ Friendly.</p>
        <div class="footer-legal-links">
          <a href="#home">Privacy Policy</a>
          <span>&middot;</span>
          <a href="#home">Terms of Service</a>
          <span>&middot;</span>
          <a href="#home">RERA Guidelines</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- FLOATING INTERACTION BUTTONS -->
  <div class="floating-actions-container">
    <a href="https://wa.me/917021316956" class="floating-btn whatsapp-btn magnetic" target="_blank" rel="noopener" aria-label="WhatsApp Agent">
      <i class="fa-brands fa-whatsapp"></i>
    </a>
    <a href="tel:+917021316956" class="floating-btn call-btn magnetic" aria-label="Call Agent">
      <i class="fa-solid fa-phone"></i>
    </a>
    <button class="floating-btn scroll-top-btn magnetic" id="back-to-top" aria-label="Back to top">
      <i class="fa-solid fa-arrow-up"></i>
    </button>
  </div>

  <!-- PROPERTY DETAILS MODAL -->
  <div class="property-details-modal" id="property-details-modal" aria-hidden="true" role="dialog">
    <div class="modal-overlay" id="modal-overlay"></div>
    <div class="modal-wrapper">
      <button class="modal-close-btn" id="modal-close-btn" aria-label="Close modal">&times;</button>
      <div class="modal-container">
        
        <div class="modal-header">
          <div class="modal-header-meta">
            <span class="modal-type-tag" id="modal-property-type">Apartment</span>
            <span class="modal-status-tag" id="modal-property-status">OC Received</span>
          </div>
          <h2 class="modal-title text-cormorant" id="modal-property-title">The Grand Horizon Residency</h2>
          <p class="modal-location" id="modal-property-location"><i class="fa-solid fa-location-dot"></i> Shell Colony, Chembur, Mumbai</p>
        </div>

        <div class="modal-grid">
          <!-- Left Column: Visuals & Pricing -->
          <div class="modal-visuals-panel">
            <div class="modal-image-wrapper">
              <img src="" alt="" id="modal-property-img" class="modal-property-img">
            </div>
            
            <div class="modal-pricing-section">
              <h3 class="modal-section-title">Configurations & Pricing</h3>
              <div class="table-responsive">
                <table class="modal-pricing-table">
                  <thead>
                    <tr>
                      <th>Configuration</th>
                      <th>Area (Carpet)</th>
                      <th>Pricing (All-Incl.)</th>
                    </tr>
                  </thead>
                  <tbody id="modal-pricing-tbody">
                    <!-- Dynamic Rows -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Right Column: Description & Perks -->
          <div class="modal-details-panel">
            <div class="modal-perks-section">
              <h3 class="modal-section-title"><i class="fa-solid fa-star"></i> Project Highlights</h3>
              <ul class="modal-list" id="modal-property-highlights">
                <!-- Dynamic List Items -->
              </ul>
            </div>

            <div class="modal-perks-section">
              <h3 class="modal-section-title"><i class="fa-solid fa-route"></i> Connectivity & Location</h3>
              <ul class="modal-list connectivity-list" id="modal-property-connectivity">
                <!-- Dynamic List Items -->
              </ul>
            </div>

            <div class="modal-cta-box">
              <div class="price-summary-box">
                <span class="price-label">Starting Price</span>
                <span class="price-value" id="modal-property-price">₹3.45 Cr</span>
              </div>
              <button class="btn btn-gold btn-large magnetic" id="modal-inquire-btn">
                Inquire Now <i class="fa-solid fa-paper-plane"></i>
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- SERVICE DETAILS MODAL -->
  <div class="service-details-modal" id="service-details-modal" aria-hidden="true" role="dialog">
    <div class="service-modal-overlay" id="service-modal-overlay"></div>
    <div class="service-modal-wrapper">
      <button class="service-modal-close-btn" id="service-modal-close-btn" aria-label="Close modal">&times;</button>
      <div class="service-modal-container">
        
        <div class="service-modal-header">
          <div class="service-modal-icon-wrapper" id="service-modal-icon">
            <i class="fa-solid fa-house-chimney-window"></i>
          </div>
          <div class="service-modal-title-group">
            <h2 class="service-modal-title text-cormorant" id="service-modal-title">Buying Assistance</h2>
            <p class="service-modal-tagline" id="service-modal-tagline">Secure your dream home with expert guidance.</p>
          </div>
        </div>

        <div class="service-modal-grid">
          <!-- Overview -->
          <div class="service-modal-overview-panel">
            <h3 class="service-modal-section-title"><i class="fa-solid fa-circle-info"></i> Service Overview</h3>
            <p class="service-modal-desc" id="service-modal-desc">
              Detailed description of the service goes here...
            </p>
          </div>

          <!-- Scope -->
          <div class="service-modal-scope-panel">
            <h3 class="service-modal-section-title"><i class="fa-solid fa-list-check"></i> What We Cover</h3>
            <ul class="service-modal-list" id="service-modal-scope">
              <!-- Scope items list -->
            </ul>
          </div>
        </div>

        <div class="service-modal-footer">
          <button class="btn btn-gold btn-large magnetic" id="service-inquire-btn">
            Enquire Now <i class="fa-solid fa-paper-plane"></i>
          </button>
          <a href="#" class="btn btn-whatsapp-modal btn-large magnetic" id="service-whatsapp-btn" target="_blank" rel="noopener">
            WhatsApp Us <i class="fa-brands fa-whatsapp"></i>
          </a>
        </div>

      </div>
    </div>
  </div>

  <!-- CDN scripts for high-end animations -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
  
  <script>
    // Inject dynamic properties data for GSAP modals
    window.dynamicPropertiesData = <?php echo json_encode($propertiesJsonObj, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>;
  </script>

  <!-- Main Script -->
  <script src="js/main_v2.js?v=<?php echo time(); ?>"></script>
</body>
</html>
