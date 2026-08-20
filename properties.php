<?php
require_once 'includes/db.php';

// Fetch All Properties for the properties page
$locParam = isset($_GET['location']) ? strtolower(trim($_GET['location'])) : 'all';
$bhkParam = isset($_GET['bhk']) ? strtolower(trim($_GET['bhk'])) : 'all';

$sql = "SELECT * FROM properties WHERE 1=1";
$params = [];

// Filter by location if specified (and not 'all')
if ($locParam !== 'all' && !empty($locParam)) {
    // Map the dropdown values to actual database search patterns
    $locSearch = str_replace('-', ' ', $locParam); 
    $sql .= " AND LOWER(location) LIKE ?";
    $params[] = "%{$locSearch}%";
}

// Filter by BHK/Type if specified (and not 'all')
if ($bhkParam !== 'all' && !empty($bhkParam)) {
    if ($bhkParam === '4+') {
        $sql .= " AND (bhk LIKE '%4%' OR bhk LIKE '%5%' OR bhk LIKE '%6%' OR LOWER(type) LIKE '%villa%')";
    } elseif ($bhkParam === 'commercial') {
        $sql .= " AND (LOWER(type) LIKE '%commercial%' OR LOWER(type) LIKE '%office%' OR LOWER(type) LIKE '%shop%')";
    } else {
        $sql .= " AND bhk LIKE ?";
        $params[] = "%{$bhkParam}%";
    }
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$properties = $stmt->fetchAll();

// Build JSON object for the GSAP frontend modals
$propertiesJsonObj = [];
foreach ($properties as $prop) {
    $propId = 'property-' . $prop['id'];
    $images = ['image.php?id=' . $prop['id']];
    $extra = !empty($prop['images_json']) ? json_decode($prop['images_json'], true) : [];
    if (is_array($extra)) {
        foreach ($extra as $idx => $img) {
            $images[] = 'image.php?id='.$prop['id'].'&idx='.$idx;
        }
    }
    
    $propertiesJsonObj[$propId] = [
        'title' => $prop['title'],
        'type' => $prop['type'],
        'location' => $prop['location'],
        'price' => $prop['price'],
        'image' => 'image.php?id=' . $prop['id'],
        'images' => $images,
        'status' => $prop['status'],
        'description' => $prop['description'] ?? '',
        'configs' => [
            [
                'type' => $prop['bhk'],
                'size' => $prop['size'],
                'price' => $prop['price']
            ]
        ],
        'highlights' => !empty($prop['highlights_json']) ? json_decode($prop['highlights_json'], true) : [],
        'connectivity' => !empty($prop['connectivity_json']) ? json_decode($prop['connectivity_json'], true) : []
    ];
}
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
  
  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
  
  <!-- Stylesheets -->
  <link rel="stylesheet" href="css/style_v2.css">

  <script>
    // Inject dynamic properties data for GSAP modals
    
  </script>

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
  <style>
    /* Force dark background on header when not scrolled on this page */
    .header:not(.scrolled) {
      background-color: var(--secondary-black) !important;
    }
  </style>
</head>
<body>

  <!-- 1. PRELOADER -->
  <div id="preloader" class="preloader">
    <div class="preloader-content">
      <div class="preloader-logo" style="margin-bottom: 1.5rem;">
        <img src="logo.png" alt="Apnaa Ghar" style="height: 120px; padding: 15px; background: #ffffff; border-radius: 12px; object-fit: contain; box-shadow: 0 8px 32px rgba(0,0,0,0.3); margin-bottom: 10px;">
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
      <a href="index.php#home" class="logo magnetic" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
        <img src="logo.png" alt="Apnaa Ghar Real Estate" style="height: 55px; max-width: 100%; object-fit: contain; mix-blend-mode: multiply;">
      </a>
      
      <nav id="nav-menu" class="nav-menu">
        <a href="index.php#home" class="nav-link">Home</a>
        <a href="index.php#properties" class="nav-link">Properties</a>
        <a href="index.php#about" class="nav-link">About Us</a>
        <a href="index.php#categories" class="nav-link">Categories</a>
        <a href="index.php#services" class="nav-link">Services</a>
        <a href="index.php#testimonials" class="nav-link">Reviews</a>
        <a href="index.php#gallery" class="nav-link">Gallery</a>
        <a href="index.php#contact" class="nav-link">Contact</a>
      </nav>

      <div class="nav-cta">
        <a href="index.php#contact" class="btn btn-gold btn-navbar-cta magnetic">
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
      <a href="index.php#home" class="mobile-link">Home</a>
      <a href="index.php#properties" class="mobile-link">Properties</a>
      <a href="index.php#about" class="mobile-link">About Us</a>
      <a href="index.php#categories" class="mobile-link">Categories</a>
      <a href="index.php#services" class="mobile-link">Services</a>
      <a href="index.php#testimonials" class="mobile-link">Reviews</a>
      <a href="index.php#gallery" class="mobile-link">Gallery</a>
      <a href="index.php#contact" class="mobile-link">Contact</a>
    </nav>
    <div class="mobile-drawer-footer">
      <a href="tel:+917021316956" class="mobile-footer-btn"><i class="fa-solid fa-phone"></i> Call Now</a>
      <a href="index.php#contact" class="mobile-footer-btn outline" id="drawer-cta-btn">Book Consultation</a>
    </div>
  </div>

  <main style="padding-top: 100px;">
    <!-- ALL PROPERTIES SECTION -->
    <section id="all-properties" class="properties-section" style="padding-bottom: 8rem;">
      <div class="container">
        <div class="section-header text-center reveal-el">
          <span class="section-tag">Comprehensive Portfolio</span>
          <h2 class="section-title text-cormorant">All Properties</h2>
          <div class="title-underline"></div>
          <p class="section-desc">Explore our complete collection of premium residences and commercial spaces.</p>
        </div>

        <div class="filter-wrapper reveal-el">
          <form id="property-filter-form" class="property-filter-bar" action="" method="GET">
            <div class="filter-group">
              <i class="fa-solid fa-location-dot filter-icon"></i>
              <div class="custom-select-wrapper" id="loc-select-wrapper">
                <div class="custom-select-trigger">
                  <span class="custom-select-text">All Locations</span>
                  <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
                </div>
                <div class="custom-options">
                  <div class="custom-option selected" data-value="all">All Locations</div>
                  <div class="custom-option" data-value="chembur">Chembur</div>
                  <div class="custom-option" data-value="tilak-nagar">Tilak Nagar</div>
                  <div class="custom-option" data-value="vikhroli">Vikhroli</div>
                  <div class="custom-option" data-value="ghatkopar">Ghatkopar</div>
                  <div class="custom-option" data-value="kurla">Kurla</div>
                  <div class="custom-option" data-value="sion-bkc">Sion & BKC</div>
                  <div class="custom-option" data-value="vile-parle">Vile Parle</div>
                  <div class="custom-option" data-value="south-mumbai">South Mumbai</div>
                  <div class="custom-option" data-value="navi-mumbai">Navi Mumbai</div>
                  <div class="custom-option" data-value="other">Rest of MMR</div>
                </div>
              </div>
              <input type="hidden" name="location" id="filter-location" value="all">
            </div>
            <div class="filter-divider"></div>
            <div class="filter-group">
              <i class="fa-solid fa-bed filter-icon"></i>
              <div class="custom-select-wrapper" id="bhk-select-wrapper">
                <div class="custom-select-trigger">
                  <span class="custom-select-text">All Types</span>
                  <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
                </div>
                <div class="custom-options">
                  <div class="custom-option selected" data-value="all">All Types</div>
                  <div class="custom-option" data-value="1">1 BHK</div>
                  <div class="custom-option" data-value="2">2 BHK</div>
                  <div class="custom-option" data-value="3">3 BHK</div>
                  <div class="custom-option" data-value="4+">4+ BHK</div>
                  <div class="custom-option" data-value="commercial">Commercial</div>
                </div>
              </div>
              <input type="hidden" name="bhk" id="filter-bhk" value="all">
            </div>
            <div class="filter-divider"></div>
            <div class="filter-group price-filter-group" style="flex-grow: 1.5; min-width: 200px;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                 <span style="font-weight: 600; font-size: 0.85rem; color: var(--text-charcoal);"><i class="fa-solid fa-indian-rupee-sign filter-icon" style="margin-right: 5px;"></i> Max Price</span>
                 <span id="price-slider-val" style="font-weight: 700; color: var(--primary-gold); font-size: 0.9rem;">50 Cr</span>
              </div>
              <input type="range" id="filter-price" min="0.4" max="50" step="0.1" value="50" style="width: 100%; cursor: pointer; accent-color: var(--primary-gold);">
            </div>
            <button type="submit" class="btn btn-gold filter-submit-btn">Search</button>
          </form>
          <div id="filter-no-results" class="no-results-msg" style="display: none;">
            <p>No properties match your current filters.</p>
            <button type="button" class="btn btn-outline reset-filters-btn" id="clear-filters-btn">Clear Filters</button>
          </div>
        </div>

        <div class="properties-grid" id="properties-grid-container">
          <?php foreach($properties as $prop): 
              $bhk_str = strtolower($prop['bhk'] . ' ' . $prop['type']);
              $bhk_matches = [];
              if (strpos($bhk_str, '1') !== false) $bhk_matches[] = '1';
              if (strpos($bhk_str, '2') !== false) $bhk_matches[] = '2';
              if (strpos($bhk_str, '3') !== false) $bhk_matches[] = '3';
              if (strpos($bhk_str, '4') !== false || strpos($bhk_str, '5') !== false || strpos($bhk_str, '6') !== false) $bhk_matches[] = '4+';
              if (strpos($bhk_str, 'commercial') !== false || strpos($bhk_str, 'office') !== false || strpos($bhk_str, 'shop') !== false) $bhk_matches[] = 'commercial';
              
              $data_bhk = implode(',', $bhk_matches);
              if (empty($data_bhk)) {
                  if (strpos($bhk_str, 'villa') !== false) $data_bhk = '4+';
                  else $data_bhk = 'commercial';
              }
              
              $price_str = strtolower(trim($prop['price']));
              preg_match('/([\d\.]+)/', $price_str, $matches);
              $price_cr = 50; // default
              if (!empty($matches)) {
                  $val = (float)$matches[1];
                  if (strpos($price_str, 'cr') !== false) {
                      $price_cr = $val;
                  } elseif (strpos($price_str, 'lakh') !== false || strpos($price_str, 'lac') !== false) {
                      $price_cr = $val / 100;
                  } elseif ($val > 1000) { // raw number
                      $price_cr = $val / 10000000;
                  } else {
                      $price_cr = $val;
                  }
              }
            ?>
          <div class="property-card reveal-el" data-property-id="property-<?php echo $prop['id']; ?>" data-location="<?php echo strtolower(str_replace([' ', ','], ['-', ''], $prop['location'])); ?>" data-bhk="<?php echo $data_bhk; ?>" data-price="<?php echo $price_cr; ?>">
            <div class="property-image-box swiper property-card-slider">
              <div class="swiper-wrapper">
                <div class="swiper-slide">
                  <img src="image.php?id=<?php echo $prop['id']; ?>" alt="<?php echo htmlspecialchars($prop['title']); ?>" class="property-img">
                </div>
                <?php
                if (!empty($prop['images_json'])) {
                    $extra_images = json_decode($prop['images_json'], true);
                    if (is_array($extra_images)) {
                        foreach ($extra_images as $idx => $img) {
                            echo '<div class="swiper-slide">';
                            echo '<img src="image.php?id='.$prop['id'].'&idx='.$idx.'" alt="'.htmlspecialchars($prop['title']).'" class="property-img">';
                            echo '</div>';
                        }
                    }
                }
                ?>
              </div>
              <!-- Swiper Navigation -->
              <div class="swiper-button-next property-swiper-next"></div>
              <div class="swiper-button-prev property-swiper-prev"></div>
              
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
                <a href="#" class="btn-card-link magnetic">Know More <i class="fa-solid fa-arrow-right"></i></a>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  </main>

  <!-- 14. FOOTER -->
  <footer class="footer-luxury">
    <div class="container footer-top-grid">
      <!-- Footer Logo & Intro -->
      <div class="footer-col-desc">
        <a href="index.php#home" class="footer-logo" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
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
          <li><a href="index.php#home">Home</a></li>
          <li><a href="index.php#properties">Featured Properties</a></li>
          <li><a href="index.php#about">About Our Agency</a></li>
          <li><a href="index.php#categories">Property Types</a></li>
          <li><a href="index.php#services">Consulting Services</a></li>
          <li><a href="index.php#gallery">Masonry Gallery</a></li>
        </ul>
      </div>

      <!-- Services List -->
      <div class="footer-col-nav">
        <h3>Services</h3>
        <ul>
          <li><a href="index.php#services">Buying Properties</a></li>
          <li><a href="index.php#services">Selling Assistance</a></li>
          <li><a href="index.php#services">Rental Portals</a></li>
          <li><a href="index.php#services">RERA Title Search</a></li>
          <li><a href="index.php#services">Stamp Duty & Registry</a></li>
          <li><a href="index.php#services">Home Loan Support</a></li>
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
          <a href="index.php#home">Privacy Policy</a>
          <span class="footer-dot">•</span>
          <a href="index.php#home">Terms of Service</a>
          <span class="footer-dot">•</span>
          <a href="index.php#home">RERA Guidelines</a>
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
            <div class="modal-image-wrapper swiper" id="modal-property-slider">
              <div class="swiper-wrapper" id="modal-property-swiper-wrapper">
                <!-- Slides injected via JS -->
                <div class="swiper-slide">
                  <img src="" alt="" id="modal-property-img" class="modal-property-img">
                </div>
              </div>
              <div class="swiper-button-next modal-swiper-next"></div>
              <div class="swiper-button-prev modal-swiper-prev"></div>
            </div>
            
            <div class="modal-description-section" style="margin-bottom: 2rem;">
              <h3 class="modal-section-title">Property Description</h3>
              <p id="modal-property-desc" style="color: var(--text-gray); font-size: 0.95rem; line-height: 1.6;"></p>
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
              <h3 class="modal-section-title"><i class="fa-solid fa-star"></i> Amenities</h3>
              <ul class="modal-list amenities-list" id="modal-property-highlights">
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
  <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
  
  <script>
    // Inject dynamic properties data from PHP for the modals
    window.dynamicPropertiesData = <?php echo json_encode($propertiesJsonObj); ?>;

    document.addEventListener("DOMContentLoaded", function() {
      // Property Card Sliders
      const propertySwipers = document.querySelectorAll('.property-card-slider');
      propertySwipers.forEach(function(slider) {
        new Swiper(slider, {
          loop: true,
          autoplay: {
            delay: 1800,
            disableOnInteraction: false,
          },
          speed: 800,
          navigation: {
            nextEl: slider.querySelector('.swiper-button-next'),
            prevEl: slider.querySelector('.swiper-button-prev'),
          },
        });
      });
    });
  </script>

  <!-- Main Script -->
  <script src="js/main_v2.js?v=<?php echo time(); ?>"></script>
  <script>
    // Handle URL parameters for filtering on properties page
    document.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      const bhkParam = urlParams.get('bhk');
      const locParam = urlParams.get('location');
      
      // Restore Location Dropdown state
      if (locParam) {
        const locOptions = document.querySelectorAll('#loc-select-wrapper .custom-option');
        locOptions.forEach(opt => {
          if (opt.getAttribute('data-value') === locParam) {
            opt.click();
          }
        });
      }

      // Restore BHK Dropdown state
      if (bhkParam) {
        const bhkOptions = document.querySelectorAll('#bhk-select-wrapper .custom-option');
        bhkOptions.forEach(opt => {
          if (opt.getAttribute('data-value') === bhkParam) {
            opt.click();
          }
        });
      }
    });
  </script>
</body>
</html>
