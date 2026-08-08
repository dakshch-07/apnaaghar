import re
import json

html_template = """          <!-- Property Card {id} -->
          <div class="property-card reveal-el" data-property-id="property-{id}">
            <div class="property-image-box">
              <img src="{image}" alt="{alt}" class="property-img">
              <div class="property-badge-group">
                <span class="badge-status for-sale">FOR SALE</span>
                <span class="badge-featured">{badge}</span>
              </div>
              <button class="bookmark-btn" aria-label="Bookmark property">
                <i class="fa-regular fa-bookmark"></i>
              </button>
            </div>
            <div class="property-content">
              <div class="property-type-tag">{type}</div>
              <h3 class="property-title">{title}</h3>
              <p class="property-location"><i class="fa-solid fa-location-dot"></i> {location}</p>
              <div class="property-features">
                <span><i class="fa-solid fa-bed"></i> {bed}</span>
                <span><i class="fa-solid fa-ruler-combined"></i> {size}</span>
                <span><i class="fa-solid fa-building"></i> {status_short}</span>
              </div>
              <div class="property-card-footer">
                <div class="property-price">{price}</div>
                <a href="#" class="btn-card-link magnetic">Know More <i class="fa-solid fa-arrow-right"></i></a>
              </div>
            </div>
          </div>"""

# Note on images: provided random distinct unsplash architectural/building images
data = [
    {
        "id": "9",
        "title": "The Grove Residency",
        "type": "Residential Tower",
        "location": "Tilak Nagar, Chembur, Mumbai",
        "price": "₹90 L onwards",
        "image": "https://images.unsplash.com/photo-1560184897-ae75f418493e?auto=format&fit=crop&w=800&q=80",
        "status": "Possession by June 2027",
        "status_short": "Poss. Jun 2027",
        "badge": "PRE-LAUNCH",
        "bed": "1 - 2.5 BHK",
        "size": "Deck & Balcony Homes",
        "configs": [
            { "type": "1 BHK with Deck", "size": "On Request", "price": "₹90 L onwards" },
            { "type": "1.5 BHK with Deck", "size": "On Request", "price": "₹1.05 Cr onwards" },
            { "type": "2 BHK with Balcony", "size": "On Request", "price": "₹1.23 Cr onwards" },
            { "type": "2.5 BHK with Balcony", "size": "On Request", "price": "₹1.88 Cr onwards" }
        ],
        "highlights": [
            "14-Storey Iconic Tower with Uninterrupted Garden Views",
            "Strategically Positioned Right Outside Tilak Nagar Station",
            "Exclusive Rooftop Amenities for a Luxurious Living Experience",
            "Early Bird Offer — Exclusive Benefits for the First 25 Buyers Only",
            "Deck & Balcony Homes Across All Configurations"
        ],
        "connectivity": [
            "Eastern Express Highway – 2 mins",
            "Tilak Nagar Station – 1 min",
            "Vidyavihar Station – 7 mins",
            "Eastern Freeway – 6 mins",
            "Bandra Kurla Complex (BKC) – 12 mins",
            "Mumbai Airport – 25 mins"
        ]
    },
    {
        "id": "10",
        "title": "Codename Mangalam",
        "type": "Residential Tower",
        "location": "Tilak Nagar Station, Chembur, Mumbai",
        "price": "Price on Request (Pre-Launch)",
        "image": "https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80",
        "status": "Pre-Launch",
        "status_short": "Pre-Launch",
        "badge": "PRE-LAUNCH",
        "bed": "1 - 2 BHK",
        "size": "370 - 677 sq.ft",
        "configs": [
            { "type": "1 BHK (6 Variants)", "size": "370–434 sq.ft carpet", "price": "On Request" },
            { "type": "2 BHK (2 Variants)", "size": "On Request", "price": "On Request" },
            { "type": "2 BHK with Car Parking", "size": "677 sq.ft carpet", "price": "On Request" }
        ],
        "highlights": [
            "G+14 storey premium tower with vastu-compliant layouts",
            "Bang outside Tilak Nagar Station",
            "East–West facing, airy residences with open views",
            "Flexible pre-launch payment plans (30:70 / 50:50 / 25:75)",
            "Perfect balance of lifestyle & investment value"
        ],
        "connectivity": [
            "Tilak Nagar Station – 1 min",
            "EEH – 2 mins",
            "Chembur Station – 7 mins",
            "Eastern Freeway – 6 mins",
            "BKC – 12 mins",
            "Mumbai Airport – 25 mins"
        ]
    },
    {
        "id": "11",
        "title": "Chembur Heights II",
        "type": "Residential Apartment",
        "location": "Chembur, Mumbai",
        "price": "₹2.49 Cr onwards",
        "image": "https://images.unsplash.com/photo-1515263487990-61b07816b324?auto=format&fit=crop&w=800&q=80",
        "status": "Ready to Move",
        "status_short": "Ready to Move",
        "badge": "READY TO MOVE",
        "bed": "2 - 3 BHK",
        "size": "690 - 1,011 sq.ft",
        "configs": [
            { "type": "2 BHK", "size": "690 sq.ft", "price": "₹2.49 Cr" },
            { "type": "2 BHK", "size": "767 sq.ft", "price": "₹2.69 Cr" },
            { "type": "2 BHK", "size": "775 sq.ft", "price": "₹2.72 Cr" },
            { "type": "3 BHK", "size": "964 sq.ft", "price": "₹3.49 Cr" },
            { "type": "3 BHK", "size": "1,011 sq.ft", "price": "₹3.59 Cr" }
        ],
        "highlights": [
            "Spacious ready-to-move-in homes across G + Podium + 19 storeys",
            "Podium level & dedicated clubhouse amenities",
            "6,000 sq.ft clubhouse with well-equipped gym & indoor games",
            "Swimming pool for kids & adults with separate changing rooms",
            "Banquet hall, mini theatre & cafeteria with flexible payment plans"
        ],
        "connectivity": [
            "Located in Chembur with easy access to Eastern Express Highway",
            "Close to Chembur & Tilak Nagar railway stations",
            "Well connected to SCLR and BKC Connector"
        ]
    },
    {
        "id": "12",
        "title": "Chembur Station East Residences",
        "type": "Residential & Commercial Project",
        "location": "Near Chembur Station (E), Mumbai",
        "price": "₹1.30 Cr onwards",
        "image": "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80",
        "status": "Under Construction (Floor Band 1–5)",
        "status_short": "Under Construction",
        "badge": "NEW",
        "bed": "1 - 3 BHK",
        "size": "475 - 955 sq.ft",
        "configs": [
            { "type": "1 BHK", "size": "475 sq.ft", "price": "₹1.30 Cr All Inclusive" },
            { "type": "2 BHK", "size": "605 sq.ft", "price": "₹1.79 Cr All Inclusive" },
            { "type": "3 BHK with Balcony", "size": "950 sq.ft", "price": "₹2.73 Cr All Inclusive" },
            { "type": "3 BHK with Balcony", "size": "955 sq.ft", "price": "₹2.76 Cr All Inclusive" }
        ],
        "highlights": [
            "Luxurious residential & commercial project — 800 mtrs from Chembur Station",
            "2 level basement parking",
            "Spread entrance, double-height lobby for A & B wing",
            "2 levels of dedicated commercial space",
            "Premium location with easy connectivity"
        ],
        "connectivity": [
            "800 metres from Chembur Railway Station",
            "Premium location with easy access to Eastern Express Highway"
        ]
    },
    {
        "id": "13",
        "title": "Elegance Heights, Nerul",
        "type": "Residential Tower",
        "location": "Nerul, Navi Mumbai",
        "price": "₹1.78 Cr onwards",
        "image": "https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=800&q=80",
        "status": "CC & RERA Received",
        "status_short": "Poss. Dec 2027",
        "badge": "RERA APPROVED",
        "bed": "2 - 3 BHK",
        "size": "725 - 1,054 sq.ft",
        "configs": [
            { "type": "2 BHK", "size": "725 sq.ft", "price": "₹1.78 Cr++" },
            { "type": "2 BHK", "size": "758 sq.ft", "price": "₹1.85 Cr++" },
            { "type": "3 BHK", "size": "1,054 sq.ft", "price": "₹2.64 Cr++" }
        ],
        "highlights": [
            "G+19 floors iconic tower on a CIDCO tender plot",
            "Swimming pool, kids play area & fully equipped gym",
            "Yoga room, multipurpose hall & club house",
            "CC & RERA received for added buyer confidence",
            "Builder possession Dec 2027 / RERA possession Nov 2028"
        ],
        "connectivity": [
            "5 mins from Nerul Railway Station",
            "5 mins from Sion-Panvel Highway",
            "Premium location in Navi Mumbai"
        ]
    },
    {
        "id": "14",
        "title": "Vikhroli East Residences",
        "type": "Residential Tower",
        "location": "Vikhroli East, Mumbai",
        "price": "₹21,000/sq.ft onwards",
        "image": "https://images.unsplash.com/photo-1574362848149-11496d93a7c7?auto=format&fit=crop&w=800&q=80",
        "status": "New Launch",
        "status_short": "New Launch",
        "badge": "NEW",
        "bed": "1 - 2 BHK",
        "size": "440 - 650 sq.ft",
        "configs": [
            { "type": "1 BHK", "size": "440 sq.ft", "price": "₹21,000/sq.ft + charges" },
            { "type": "2 BHK", "size": "600–650 sq.ft", "price": "₹21,000/sq.ft + charges" }
        ],
        "highlights": [
            "High-rise G+22 tower with 4 lifts (3+1)",
            "Only 6 flats per floor for more privacy, less crowd",
            "Smart layouts with maximum space utilisation",
            "Gym, rooftop sit-out, kids play area & senior citizen zone",
            "Car parking available at ₹8 Lakhs"
        ],
        "connectivity": [
            "Located in Vikhroli East with good social infrastructure"
        ]
    },
    {
        "id": "15",
        "title": "The New Landmark, Sion–Chunabhatti",
        "type": "Residential Project",
        "location": "Sion–Chunabhatti, Mumbai",
        "price": "Price on Request",
        "image": "https://images.unsplash.com/photo-1460317442991-0ec209397118?auto=format&fit=crop&w=800&q=80",
        "status": "Under Construction",
        "status_short": "Poss. Dec 2029",
        "badge": "NEW LAUNCH",
        "bed": "1 - 2 BHK",
        "size": "430 - 642 sq.ft",
        "configs": [
            { "type": "1 BHK", "size": "430 sq.ft", "price": "On Request" },
            { "type": "2 BHK", "size": "625 sq.ft", "price": "On Request" },
            { "type": "2 BHK", "size": "642 sq.ft", "price": "On Request" }
        ],
        "highlights": [
            "Smartly designed homes at Mumbai's most connected junction",
            "Walking distance to Chunabhatti Station, EEH & Metro access",
            "Minutes from BKC, Sion, Chembur, Kurla, Ghatkopar, Dadar & Lower Parel",
            "Surrounded by shopping malls, hospitals, food hubs & schools",
            "Builder timeline Dec 2029 / RERA possession Dec 2032"
        ],
        "connectivity": [
            "Chunabhatti Station",
            "Eastern Express Highway",
            "Metro Access"
        ]
    },
    {
        "id": "16",
        "title": "Vile Parle Residences",
        "type": "Residential Project",
        "location": "Vile Parle, Mumbai",
        "price": "₹1.94 Cr onwards",
        "image": "https://images.unsplash.com/photo-1515263487990-61b07816b324?auto=format&fit=crop&w=800&q=80",
        "status": "Under Construction – 2 Basements Completed",
        "status_short": "Under Construction",
        "badge": "NEW",
        "bed": "1 - 3 BHK",
        "size": "495 - 1,190 sq.ft",
        "configs": [
            { "type": "1 BHK", "size": "495 sq.ft", "price": "₹1.94 Cr onwards" },
            { "type": "2 BHK", "size": "695 sq.ft", "price": "₹2.64 Cr onwards" },
            { "type": "3 BHK", "size": "1,190 sq.ft", "price": "₹4.60 Cr onwards" }
        ],
        "highlights": [
            "Prime Vile Parle location, ideal for upgraders & NRI families",
            "2 basements completed, plinth targeted by mid-August 2026",
            "Well suited for Gujarati, Jain & Maharashtrian families",
            "Great fit for business owners & investors",
            "Direct developer connect for inventory & pricing"
        ],
        "connectivity": [
            "Located in the heart of Vile Parle with excellent social infrastructure"
        ]
    },
    {
        "id": "17",
        "title": "Premium 1 & 2 BHK Residences",
        "type": "Residential Apartment",
        "location": "TBC, Mumbai",
        "price": "₹73.99 Lacs onwards",
        "image": "https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80",
        "status": "Possession December 2028",
        "status_short": "Poss. Dec 2028",
        "badge": "TRUSTED DEVELOPER",
        "bed": "1 - 2 BHK",
        "size": "365 - 519 sq.ft",
        "configs": [
            { "type": "1 BHK", "size": "365 sq.ft", "price": "₹73.99 Lacs onwards" },
            { "type": "1 BHK", "size": "375 sq.ft", "price": "₹76.99 Lacs onwards" },
            { "type": "1 BHK Premium", "size": "398 sq.ft", "price": "₹81.99 Lacs onwards" },
            { "type": "2 BHK", "size": "519 sq.ft", "price": "₹1.05 Cr onwards" }
        ],
        "highlights": [
            "Trusted developer with 1 Million+ sq.ft. delivered",
            "20+ successfully completed projects",
            "Premium rooftop amenities",
            "Smart & efficient, future-ready layouts",
            "Commitment to transparency, trust & timely delivery"
        ],
        "connectivity": [
            "To be confirmed"
        ]
    },
    {
        "id": "18",
        "title": "Fully Furnished Flat, Vikhroli",
        "type": "Resale Apartment",
        "location": "Vikhroli, Mumbai",
        "price": "₹1.25 Cr (Negotiable)",
        "image": "https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=800&q=80",
        "status": "Ready to Move / For Sale",
        "status_short": "Ready to Move",
        "badge": "RESALE",
        "bed": "Fully Furnished",
        "size": "527 sq.ft",
        "configs": [
            { "type": "Fully Furnished Flat", "size": "527 sq.ft carpet", "price": "₹1.25 Cr (Negotiable, incl. Car Parking)" }
        ],
        "highlights": [
            "Fully furnished — kitchen trolley with cabinets, bed, TV unit",
            "Cabinets in all rooms & water purifier included",
            "Price negotiable, inclusive of car parking",
            "5 minutes walking distance to Vikhroli Station",
            "Schools, hospitals, market & banks within 5 minutes"
        ],
        "connectivity": [
            "Vikhroli Station – 5 mins walking",
            "Schools, Hospitals, Market & Banks – within 5 mins"
        ]
    },
    {
        "id": "19",
        "title": "SoBo Deck Residences",
        "type": "Luxury Residential Tower",
        "location": "South Mumbai (SoBo)",
        "price": "₹3.47 Cr onwards",
        "image": "https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80",
        "status": "Under Construction",
        "status_short": "Under Construction",
        "badge": "LUXURY",
        "bed": "2 - 3 BHK",
        "size": "890 - 1,080 sq.ft",
        "configs": [
            { "type": "2 BHK Deck (RCA)", "size": "890 sq.ft", "price": "₹3.47 Cr++" },
            { "type": "2 BHK Deck (RCA)", "size": "913 sq.ft", "price": "₹3.56 Cr++" },
            { "type": "3 BHK Deck (RCA)", "size": "1,080 sq.ft", "price": "₹4.37 Cr++" }
        ],
        "highlights": [
            "Supersized 2 & 3 bed deck homes with panoramic views",
            "Double height entrance lobby ready & large sundecks",
            "~14,000 sq.ft. of recreational spaces incl. Jain temple",
            "Swimming pool, terrace garden, yoga room & jogging track",
            "Jodi option available"
        ],
        "connectivity": [
            "Prime South Mumbai (SoBo) location"
        ]
    },
    {
        "id": "20",
        "title": "Promont, BKC–Sion Connector",
        "type": "Residential Tower",
        "location": "BKC–Sion Connector, Mumbai",
        "price": "Price on Request",
        "image": "https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80",
        "status": "Under Construction, Possession Dec 2027",
        "status_short": "Poss. Dec 2027",
        "badge": "UNDER CONSTRUCTION",
        "bed": "2 - 3 BHK",
        "size": "On Request",
        "configs": [
            { "type": "2 & 3 BHK Majestic Deck Residences", "size": "On Request", "price": "On Request" }
        ],
        "highlights": [
            "A treasured life awaits at the BKC–Sion Connector",
            "Relaxing pool deck & Skyplex",
            "Café lounge, BBQ corner & jacuzzi",
            "Possession by December 2027",
            "Construction in full swing"
        ],
        "connectivity": [
            "Located directly on the BKC–Sion Connector"
        ]
    },
    {
        "id": "21",
        "title": "Vikhroli Podium Residences",
        "type": "Residential Tower",
        "location": "Vikhroli, Mumbai",
        "price": "₹1.75 Cr onwards",
        "image": "https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80",
        "status": "Under Construction",
        "status_short": "Under Construction",
        "badge": "NEW",
        "bed": "1 - 2 BHK",
        "size": "630 sq.ft",
        "configs": [
            { "type": "1 BHK", "size": "On Request", "price": "On Request" },
            { "type": "2 BHK (Air-Conditioned)", "size": "630 sq.ft", "price": "₹1.75 Cr onwards, All Inclusive" }
        ],
        "highlights": [
            "Ground + 5 Podium + 29 habitable floors",
            "Fully air-conditioned homes with false ceiling & LED lights",
            "Garden, jogging track, fitness centre & zen yoga deck",
            "Open air amphitheatre, swimming pool & kid's pool",
            "24×7 security with video door phone in every home"
        ],
        "connectivity": [
            "Eastern Express Highway – 2 mins",
            "Railway Station – 7 mins",
            "Kannamwar Bus Depot – 2 mins",
            "R City Mall – 20 mins",
            "Metro Station – 5 mins"
        ]
    },
    {
        "id": "22",
        "title": "Zero-Wastage Residences, Vikhroli",
        "type": "Residential Tower",
        "location": "Vikhroli, Mumbai",
        "price": "₹79 Lacs onwards",
        "image": "https://images.unsplash.com/photo-1582407947304-fd86f028f716?auto=format&fit=crop&w=800&q=80",
        "status": "New Launch",
        "status_short": "New Launch",
        "badge": "NEW",
        "bed": "1 - 2 BHK",
        "size": "359 - 498 sq.ft",
        "configs": [
            { "type": "1 BHK", "size": "359 / 374 sq.ft", "price": "₹79 Lacs onwards" },
            { "type": "2 BHK", "size": "498 sq.ft", "price": "₹99 Lacs onwards" }
        ],
        "highlights": [
            "G+22 storey tower with spacious zero-wastage layouts",
            "Premium high-end retail outlets & podium level car park",
            "10,000 sq.ft. of dedicated amenities",
            "Sample flat available with unobstructed views",
            "Yoga zone, box cricket & rooftop party lawn"
        ],
        "connectivity": [
            "Kannamwar Bus Depot – 2 mins",
            "Eastern Express Highway – 5 mins",
            "Vikhroli Railway Station – 6 mins",
            "International School & College – 8 mins"
        ]
    },
    {
        "id": "23",
        "title": "Vikhroli East Gated Community",
        "type": "Luxury Residential Tower",
        "location": "Vikhroli East, Mumbai",
        "price": "₹1.08 Cr onwards",
        "image": "https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80",
        "status": "Under Construction",
        "status_short": "Under Construction",
        "badge": "LUXURY",
        "bed": "1 - 2 BHK",
        "size": "426 - 638 sq.ft",
        "configs": [
            { "type": "1 BHK", "size": "426 sq.ft", "price": "₹1.08 Cr All Inclusive" },
            { "type": "2 BHK", "size": "584–638 sq.ft", "price": "₹1.56 Cr All Inclusive" }
        ],
        "highlights": [
            "31-storey tower on a 1.25 acre gated land parcel",
            "2 levels basement + 4-level podium parking",
            "5 levels of exclusive lifestyle amenities",
            "30+ world-class amenities incl. infinity pool & spa",
            "Habitable residences begin from the 6th floor"
        ],
        "connectivity": [
            "Prime highway-touch connectivity in Vikhroli East"
        ]
    },
    {
        "id": "24",
        "title": "1 BHK Resale, Tilak Nagar",
        "type": "Resale Apartment",
        "location": "Near Tilak Nagar Station, Mumbai",
        "price": "₹24,000/sq.ft",
        "image": "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80",
        "status": "New Building",
        "status_short": "New Building",
        "badge": "RESALE",
        "bed": "1 BHK",
        "size": "596 sq.ft",
        "configs": [
            { "type": "1 BHK", "size": "596 sq.ft carpet", "price": "₹24,000/sq.ft + Car Parking ₹10 Lakh" }
        ],
        "highlights": [
            "New building, ready to move",
            "Car parking available at ₹10 Lakh",
            "Located near Tilak Nagar Station"
        ],
        "connectivity": [
            "Close to Tilak Nagar Station"
        ]
    },
    {
        "id": "25",
        "title": "1 BHK Resale, Near Tilak Nagar",
        "type": "Resale Apartment",
        "location": "Near Tilak Nagar, Mumbai",
        "price": "₹95 Lakh",
        "image": "https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=800&q=80",
        "status": "For Sale",
        "status_short": "For Sale",
        "badge": "RESALE",
        "bed": "1 BHK",
        "size": "375 sq.ft",
        "configs": [
            { "type": "1 BHK", "size": "375 sq.ft carpet", "price": "₹95 Lakh" }
        ],
        "highlights": [
            "Open view apartment",
            "Located near Tilak Nagar"
        ],
        "connectivity": [
            "Close to Tilak Nagar"
        ]
    },
    {
        "id": "26",
        "title": "1 BHK Resale, Badlapur East",
        "type": "Resale Apartment",
        "location": "Badlapur East, Thane District",
        "price": "₹33 Lakh",
        "image": "https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=800&q=80",
        "status": "Vacant / For Sale",
        "status_short": "Vacant / For Sale",
        "badge": "FOR SALE",
        "bed": "1 BHK",
        "size": "400 sq.ft",
        "configs": [
            { "type": "1 BHK", "size": "400 sq.ft carpet", "price": "₹33 Lakh (Vacant)" }
        ],
        "highlights": [
            "Currently vacant, ready for immediate sale",
            "Located in Badlapur East, Thane District"
        ],
        "connectivity": [
            "Located in Badlapur East, Thane District"
        ]
    }
]

# Generate properties HTML
html_output = "\\n".join([html_template.format(
    id=d["id"],
    image=d["image"],
    alt=d["title"],
    badge=d["badge"],
    type=d["type"],
    title=d["title"],
    location=d["location"],
    bed=d["bed"],
    size=d["size"],
    status_short=d["status_short"],
    price=d["price"]
) for d in data])

# Read index.html
with open(r'c:\xampp_new\htdocs\apnaaghar\index.html', 'r', encoding='utf-8') as f:
    index_content = f.read()

# Make properties.html content
# 1. Update navigation for both
# In index.html:
# <a href="#properties" class="nav-link">Properties</a>
# Replace with:
# <a href="#properties" class="nav-link">Properties</a>
# <a href="properties.html" class="nav-link">All Properties</a>

index_nav_replacement = '''<a href="#home" class="nav-link active">Home</a>
        <a href="#properties" class="nav-link">Properties</a>
        <a href="properties.html" class="nav-link">All Properties</a>
        <a href="#about" class="nav-link">About Us</a>
        <a href="#categories" class="nav-link">Categories</a>
        <a href="#services" class="nav-link">Services</a>
        <a href="#testimonials" class="nav-link">Reviews</a>
        <a href="#gallery" class="nav-link">Gallery</a>
        <a href="#contact" class="nav-link">Contact</a>'''

index_mobile_nav_replacement = '''<a href="#home" class="mobile-link">Home</a>
      <a href="#properties" class="mobile-link">Properties</a>
      <a href="properties.html" class="mobile-link">All Properties</a>
      <a href="#about" class="mobile-link">About Us</a>
      <a href="#categories" class="mobile-link">Categories</a>
      <a href="#services" class="mobile-link">Services</a>
      <a href="#testimonials" class="mobile-link">Reviews</a>
      <a href="#gallery" class="mobile-link">Gallery</a>
      <a href="#contact" class="mobile-link">Contact</a>'''

properties_nav_replacement = '''<a href="index.html#home" class="nav-link">Home</a>
        <a href="index.html#properties" class="nav-link">Properties</a>
        <a href="properties.html" class="nav-link active">All Properties</a>
        <a href="index.html#about" class="nav-link">About Us</a>
        <a href="index.html#categories" class="nav-link">Categories</a>
        <a href="index.html#services" class="nav-link">Services</a>
        <a href="index.html#testimonials" class="nav-link">Reviews</a>
        <a href="index.html#gallery" class="nav-link">Gallery</a>
        <a href="index.html#contact" class="nav-link">Contact</a>'''

properties_mobile_nav_replacement = '''<a href="index.html#home" class="mobile-link">Home</a>
      <a href="index.html#properties" class="mobile-link">Properties</a>
      <a href="properties.html" class="mobile-link">All Properties</a>
      <a href="index.html#about" class="mobile-link">About Us</a>
      <a href="index.html#categories" class="mobile-link">Categories</a>
      <a href="index.html#services" class="mobile-link">Services</a>
      <a href="index.html#testimonials" class="mobile-link">Reviews</a>
      <a href="index.html#gallery" class="mobile-link">Gallery</a>
      <a href="index.html#contact" class="mobile-link">Contact</a>'''

# 2. Extract original properties 1-8
prop_grid_match = re.search(r'<div class="properties-grid">(.*?)</div>\s*</div>\s*</section>', index_content, re.DOTALL)
if prop_grid_match:
    original_grid = prop_grid_match.group(1)
    
    # Split the original grid into individual cards
    cards = re.findall(r'<!-- Property Card \d+ -->.*?</div>\s*</div>\s*</div>', original_grid, re.DOTALL)
    
    # properties.html needs all cards (1-8 + 9-26)
    all_cards_html = "\\n".join(cards) + "\\n" + html_output
    
    # index.html needs only first 3 cards + View all button
    index_cards_html = "\\n".join(cards[:3])
else:
    print("Could not find properties grid")

# Create properties.html from index.html template
props_content = index_content

# Replace title in properties.html
props_content = re.sub(r'<h2 class="section-title text-cormorant">Featured Properties</h2>', r'<h2 class="section-title text-cormorant">All Properties</h2>', props_content)

# Replace properties grid in properties.html
props_content = props_content.replace(original_grid, "\\n" + all_cards_html + "\\n        ")

# Update navs in properties.html
props_content = re.sub(r'<a href="#home" class="nav-link active">Home</a>.*?<a href="#contact" class="nav-link">Contact</a>', properties_nav_replacement, props_content, flags=re.DOTALL)
props_content = re.sub(r'<a href="#home" class="mobile-link">Home</a>.*?<a href="#contact" class="mobile-link">Contact</a>', properties_mobile_nav_replacement, props_content, flags=re.DOTALL)

# Update nav CTA links to absolute in properties.html
props_content = props_content.replace('href="#contact"', 'href="index.html#contact"')
props_content = props_content.replace('href="#properties"', 'href="index.html#properties"')

# Write properties.html
with open(r'c:\xampp_new\htdocs\apnaaghar\properties.html', 'w', encoding='utf-8') as f:
    f.write(props_content)

# 3. Update index.html
# Replace properties grid
view_all_btn = '''
        </div>
        <div class="text-center reveal-el" style="margin-top: 40px;">
          <a href="properties.html" class="btn btn-gold btn-large magnetic">
            View All Properties <i class="fa-solid fa-arrow-right"></i>
          </a>
        </div>'''
new_index_grid = "\\n" + index_cards_html + "\\n" + view_all_btn
index_content = index_content.replace('<div class="properties-grid">' + original_grid + '</div>', '<div class="properties-grid">' + new_index_grid + '</div>')

# Update navs in index.html
index_content = re.sub(r'<a href="#home" class="nav-link active">Home</a>.*?<a href="#contact" class="nav-link">Contact</a>', index_nav_replacement, index_content, flags=re.DOTALL)
index_content = re.sub(r'<a href="#home" class="mobile-link">Home</a>.*?<a href="#contact" class="mobile-link">Contact</a>', index_mobile_nav_replacement, index_content, flags=re.DOTALL)

with open(r'c:\xampp_new\htdocs\apnaaghar\index.html', 'w', encoding='utf-8') as f:
    f.write(index_content)

# 4. Update js/main.js
with open(r'c:\xampp_new\htdocs\apnaaghar\js\main.js', 'r', encoding='utf-8') as f:
    js_content = f.read()

# Generate JS data for new properties
js_additions = []
for d in data:
    configs_str = ",\\n        ".join([json.dumps(c) for c in d["configs"]])
    highlights_str = ",\\n        ".join([json.dumps(h) for h in d["highlights"]])
    conn_str = ",\\n        ".join([json.dumps(c) for c in d["connectivity"]])
    
    js_str = f'''"{d["id"]}" : {{
      title: "{d["title"]}",
      type: "{d["type"]}",
      location: "{d["location"]}",
      price: "{d["price"]}",
      image: "{d["image"]}",
      status: "{d["status"]}",
      configs: [
        {configs_str}
      ],
      highlights: [
        {highlights_str}
      ],
      connectivity: [
        {conn_str}
      ]
    }}'''
    # Fix the key to have property- prefix
    js_str = js_str.replace(f'"{d["id"]}" :', f'"property-{d["id"]}":')
    js_additions.append(js_str)

js_new_entries = ",\\n    " + ",\\n    ".join(js_additions)

# Replace in js/main.js
# Look for the end of property-8 entry which looks like:
#         "Located near SCLR flyover, SCLR connector, and Krushal Towers"
#       ]
#     }
#   };

js_content = js_content.replace('''"Located near SCLR flyover, SCLR connector, and Krushal Towers"
      ]
    }
  };''', '''"Located near SCLR flyover, SCLR connector, and Krushal Towers"
      ]
    }''' + js_new_entries + '''
  };''')

with open(r'c:\xampp_new\htdocs\apnaaghar\js\main.js', 'w', encoding='utf-8') as f:
    f.write(js_content)

print("Done updating files.")
