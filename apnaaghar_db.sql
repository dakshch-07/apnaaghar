-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 10, 2026 at 02:59 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `apnaaghar_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin@apnaghar', '$2y$10$UtxElwzjz.ElAraxna8JHusUjG7uvf885fslqoNF08YmEciy/lMmK', '2026-08-08 17:52:27');

-- --------------------------------------------------------

--
-- Table structure for table `enquiries`
--

CREATE TABLE `enquiries` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `property_type` varchar(255) DEFAULT NULL,
  `budget` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `category`, `image_url`, `created_at`) VALUES
(2, 'Modern Kitchen', 'Interiors', 'https://images.unsplash.com/photo-1556912172-45b7abe8b7e1?auto=format&fit=crop&w=800&q=80', '2026-08-08 17:52:27'),
(3, 'Grand Lobby', 'Amenities', 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=800&q=80', '2026-08-08 17:52:27'),
(4, 'Gymnasium', 'Amenities', 'https://images.unsplash.com/photo-1540497077202-7c8a3999166f?auto=format&fit=crop&w=800&q=80', '2026-08-08 17:52:27'),
(5, 'Elevation View', 'Exteriors', 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80', '2026-08-08 17:52:27'),
(6, 'Living Room', 'Interiors', 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=800&q=80', '2026-08-08 17:52:27'),
(7, 'Swimming Pool', 'Amenities', 'https://images.unsplash.com/photo-1576013551627-0cc20b96c2a7?auto=format&fit=crop&w=800&q=80', '2026-08-08 17:52:27'),
(10, 'Gym', 'Amenities', 'uploads/gallery/6a79c69e40e3d.jpg', '2026-08-10 12:39:58');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `location` varchar(255) NOT NULL,
  `price` varchar(50) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `status` varchar(100) NOT NULL,
  `badge_status` varchar(50) DEFAULT NULL,
  `badge_featured` varchar(50) DEFAULT NULL,
  `bhk` varchar(50) NOT NULL,
  `size` varchar(100) NOT NULL,
  `highlights_json` text NOT NULL,
  `connectivity_json` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `title`, `type`, `location`, `price`, `image_url`, `status`, `badge_status`, `badge_featured`, `bhk`, `size`, `highlights_json`, `connectivity_json`, `created_at`) VALUES
(1, 'The Grand Horizon Residency', 'Luxury Tower', 'Shell Colony, Chembur, Mumbai', '₹3.45 Cr', 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80', 'OC Received', 'FOR SALE', 'FEATURED', '3 BHK', '1,450 sq.ft', '[\"Double Height Grand Entrance Lobby (Air-Conditioned)\", \"Fully Equipped Modern Gymnasium\", \"Beautiful Rooftop Garden & Lounge Area\", \"High-speed Passenger Elevators\", \"24/7 Security Surveillance & Intercom System\"]', '[\"5 mins from Chembur Railway Station\", \"2 mins drive from Eastern Express Highway\", \"10 mins to Bandra Kurla Complex (BKC) via connector\", \"Conveniently close to upcoming Metro Line 4\"]', '2026-08-08 17:52:27'),
(2, 'Symphony Sky Villa', 'Luxury Penthouse', 'Tilak Nagar, Chembur, Mumbai', '₹5.20 Cr', 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=800&q=80', 'OC Received', 'FOR SALE', '', '4 BHK', '2,200 sq.ft', '[\"Exclusive Private High-Speed Elevator Access\", \"Infinity Edge Rooftop Swimming Pool\", \"3 Reserved Private Covered Car Parks\", \"Advanced Smart Home Automation System\", \"360-degree Panoramic Mumbai Skyline View\"]', '[\"2 mins walking distance to Tilak Nagar Railway Station\", \"5 mins drive to SCLR & Kurla area\", \"12 mins drive to BKC via Connector\", \"Easy connection to the Eastern Freeway\"]', '2026-08-08 17:52:27'),
(3, 'Elegance Court Duplex', 'Builder Floor', 'Union Park, Chembur, Mumbai', '₹1.25 L/Mo', 'https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=800&q=80', 'Available immediately', 'FOR RENT', '', '4 BHK', '1,950 sq.ft', '[\"Massive Private Terrace Area\", \"Fully Furnished with Premium Imported Fittings\", \"Pet-Friendly Building\", \"Designated Servant Quarters\", \"24-hour uninterrupted Power Backup\"]', '[\"In the heart of Chembur\'s most premium residential zone\", \"Walking distance to Gymkhana and fine-dining\", \"Quick access to Eastern Freeway\", \"15 mins to Navi Mumbai\"]', '2026-08-08 17:52:27'),
(4, 'The Grand Horizon Residency', 'Luxury Tower', 'Shell Colony, Chembur, Mumbai', '₹3.45 Cr', 'uploads/properties/6a77755e4b45e.png', 'OC Received', '', '', '3 BHK', '1,450 sq.ft', '[\"Double Height Grand Entrance Lobby (Air-Conditioned)\",\"Fully Equipped Modern Gymnasium\",\"Beautiful Rooftop Garden & Lounge Area\",\"High-speed Passenger Elevators\",\"24\\/7 Security Surveillance & Intercom System\"]', '[\"5 mins from Chembur Railway Station\",\"2 mins drive from Eastern Express Highway\",\"10 mins to Bandra Kurla Complex (BKC) via connector\",\"Conveniently close to upcoming Metro Line 4\"]', '2026-08-08 18:08:30'),
(5, 'Symphony Sky Villa', 'Luxury Penthouse', 'Tilak Nagar, Chembur, Mumbai', '₹5.20 Cr', 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=800&q=80', 'OC Received', NULL, NULL, '4 BHK', '2,200 sq.ft', '[\"Exclusive Private High-Speed Elevator Access\",\"Infinity Edge Rooftop Swimming Pool\",\"3 Reserved Private Covered Car Parks\",\"Advanced Smart Home Automation System\",\"360-degree Panoramic Mumbai Skyline View\"]', '[\"2 mins walking distance to Tilak Nagar Railway Station\",\"5 mins drive to SCLR & Kurla area\",\"12 mins drive to BKC via Connector\",\"Easy connection to the Eastern Freeway\"]', '2026-08-08 18:08:30'),
(6, 'Elegance Court Duplex', 'Builder Floor', 'Union Park, Chembur, Mumbai', '₹1.25 L/Mo', 'https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=800&q=80', 'Available immediately', NULL, NULL, '3 BHK Duplex', '1,850 sq.ft', '[\"Fully Furnished and Tastefully Designed Modern Interiors\",\"Double Height Living Room Ceiling for Airier Spaces\",\"Private Open-Air Terrace with Sit-out Spaces\",\"Dedicated Separate Servant Room & Washroom\",\"Nestled in the quietest, most elite neighborhood of Union Park\"]', '[\"Walkable distance to famous Union Park cafes & restaurants\",\"5 mins from the historical Ambedkar Garden\",\"8 mins access to the Eastern Freeway entry point\",\"Quick connectivity to Sion & Chembur SCLR junction\"]', '2026-08-08 18:08:30'),
(7, 'Emerald Gardens Heights', 'Apartment', 'Ghatkopar East, Mumbai', '₹2.10 Cr', 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80', 'OC Expected Dec 2026', NULL, NULL, '2 BHK', '950 sq.ft', '[\"Premium State-of-the-Art Clubhouse\",\"Lush Landscaped Gardens & Jogging Tracks\",\"Dedicated Safe Children\'s Play Area\",\"Modern Modular Kitchen & Premium Bath Fittings\",\"Multi-level Security Control Room with 24\\/7 Patrols\"]', '[\"5 mins from Ghatkopar Metro & Railway Stations\",\"3 mins from Eastern Express Highway (EEH)\",\"8 mins to LBS Road & Ghatkopar commercial zones\",\"Direct access to Upcoming Metro Line 4\"]', '2026-08-08 18:08:30'),
(8, 'Urban Retreat Penthouse', 'Luxury Penthouse', 'Kurla West, Mumbai', '₹4.85 Cr', 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=800&q=80', 'Ready Possession', NULL, NULL, '4 BHK', '2,500 sq.ft', '[\"Private Open Deck with Premium Jacuzzi Setup\",\"Rooftop Open-to-Sky Private Lounge Area\",\"Complete Smart Home Integration & Mood Lighting\",\"Premium Selected Marble & Sanitary Fittings\",\"Walk-in Wardrobe Spaces in Master Suites\"]', '[\"5 mins drive to SCLR (BKC Link SCLR)\",\"7 mins from Kurla Junction Railway Station\",\"10 mins to Phoenix Marketcity Mall\",\"Direct connection routes to LBS Road & EEH\"]', '2026-08-08 18:08:30'),
(9, 'Charming Colonial Bungalow', 'Luxury Villa', 'Deonar, Chembur, Mumbai', '₹12.00 Cr', 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=800&q=80', 'Ready Possession', NULL, NULL, '5 BHK', '4,500 sq.ft', '[\"Lush Private Landscaped Gardens\",\"Private Swimming Pool with Lounge Deck\",\"Classic Colonial\\/Heritage Aesthetics\",\"5 Dedicated Covered Parking Bays\",\"Nestled in a highly secure elite Bungalow Gated Compound\"]', '[\"Peaceful and green residential lane of Deonar\",\"3 mins from Tata Institute of Social Sciences (TISS)\",\"5 mins drive to Eastern Freeway entrance\",\"10 mins drive to Chembur Naka crossroads\"]', '2026-08-08 18:08:30'),
(10, 'The Signature Aura (Chembur)', 'Residential Project', 'Near SCLR & Freeway, Chembur, Mumbai', '₹1.94 Cr onwards', 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?auto=format&fit=crop&w=800&q=80', 'OC Received', NULL, NULL, '2 BHK', '611 to 829 sq.ft', '[\"Premium Ready-to-Move Residences with OC Received\",\"Assured Rental Scheme Available (Ideal for Investors)\",\"Immediate Payouts & Faster Property Closures\",\"AC Grand Entrance Lobby & Fully Equipped Gym\",\"Rooftop Party Lawn, Open Air Lounge & Play Area\"]', '[\"Direct Entry to Eastern Freeway & SCLR\",\"Connected to Eastern Express Highway (EEH)\",\"Quick connectivity to BKC Connector (5 mins to BKC)\",\"Walkable to Monorail and upcoming Metro Line 4\",\"Excellent rail connectivity via Chembur & Kurla stations\"]', '2026-08-08 18:08:30'),
(11, 'Hyper-Connected Offices', 'Commercial Project', 'Near Krushal Towers, SCLR & EEH Junction, Chembur-Ghatkopar, Mumbai', '₹73 Lakh onwards', 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80', 'CC Received (OC Dec 2026)', NULL, NULL, 'Office Unit', '245 sq.ft', '[\"15-Story Premium Commercial Glass Architecture\",\"10 ft. Clear Ceiling Height for Spacious Offices\",\"Grand AC Double-Height Entrance Lobby\",\"Self-Contained Executive Office Units\",\"Assured High Rental Yields Upto 7% YoY\",\"Ever Growing Cash Flow & Rental appreciation Upto 8% YoY\",\"Executive Jain Temple (Derasar) inside Building Premises\",\"2 Levels of Basement Car Parking with Ramp Access\"]', '[\"5 mins from Ghatkopar, Chembur & Tilak Nagar stations\",\"1 min from SCLR and Eastern Express Highway (EEH)\",\"5 mins to Bandra Kurla Complex (BKC)\",\"Directly next to upcoming Metro Line 4\",\"Located near SCLR flyover, SCLR connector, and Krushal Towers\"]', '2026-08-08 18:08:30'),
(12, 'The Grove Residency', 'Residential Tower', 'Tilak Nagar, Chembur, Mumbai', '₹90 L onwards', 'https://images.unsplash.com/photo-1560184897-ae75f418493e?auto=format&fit=crop&w=800&q=80', 'Possession by June 2027', NULL, NULL, '1 BHK with Deck', 'On Request', '[\"14-Storey Iconic Tower with Uninterrupted Garden Views\",\"Strategically Positioned Right Outside Tilak Nagar Station\",\"Exclusive Rooftop Amenities for a Luxurious Living Experience\",\"Early Bird Offer \\u2014 Exclusive Benefits for the First 25 Buyers Only\",\"Deck & Balcony Homes Across All Configurations\"]', '[\"Eastern Express Highway \\u2013 2 mins\",\"Tilak Nagar Station \\u2013 1 min\",\"Vidyavihar Station \\u2013 7 mins\",\"Eastern Freeway \\u2013 6 mins\",\"Bandra Kurla Complex (BKC) \\u2013 12 mins\",\"Mumbai Airport \\u2013 25 mins\"]', '2026-08-08 18:08:30'),
(13, 'Codename Mangalam', 'Residential Tower', 'Tilak Nagar Station, Chembur, Mumbai', 'Price on Request (Pre-Launch)', 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80', 'Pre-Launch', NULL, NULL, '1 BHK (6 Variants)', '370–434 sq.ft carpet', '[\"G+14 storey premium tower with vastu-compliant layouts\",\"Bang outside Tilak Nagar Station\",\"East\\u2013West facing, airy residences with open views\",\"Flexible pre-launch payment plans (30:70 \\/ 50:50 \\/ 25:75)\",\"Perfect balance of lifestyle & investment value\"]', '[\"Tilak Nagar Station \\u2013 1 min\",\"EEH \\u2013 2 mins\",\"Chembur Station \\u2013 7 mins\",\"Eastern Freeway \\u2013 6 mins\",\"BKC \\u2013 12 mins\",\"Mumbai Airport \\u2013 25 mins\"]', '2026-08-08 18:08:30'),
(14, 'Chembur Heights II', 'Residential Apartment', 'Chembur, Mumbai', '₹2.49 Cr onwards', 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80', 'Ready to Move', NULL, NULL, '2 BHK', '690 sq.ft', '[\"Spacious ready-to-move-in homes across G + Podium + 19 storeys\",\"Podium level & dedicated clubhouse amenities\",\"6,000 sq.ft clubhouse with well-equipped gym & indoor games\",\"Swimming pool for kids & adults with separate changing rooms\",\"Banquet hall, mini theatre & cafeteria with flexible payment plans\"]', '[\"Located in Chembur with easy access to Eastern Express Highway\",\"Close to Chembur & Tilak Nagar railway stations\",\"Well connected to SCLR and BKC Connector\"]', '2026-08-08 18:08:30'),
(15, 'Chembur Station East Residences', 'Residential & Commercial Project', 'Near Chembur Station (E), Mumbai', '₹1.30 Cr onwards', 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=800&q=80', 'Under Construction (Floor Band 1–5)', NULL, NULL, '1 BHK', '475 sq.ft', '[\"Luxurious residential & commercial project \\u2014 800 mtrs from Chembur Station\",\"2 level basement parking\",\"Spread entrance, double-height lobby for A & B wing\",\"2 levels of dedicated commercial space\",\"Premium location with easy connectivity\"]', '[\"800 metres from Chembur Railway Station\",\"Premium location with easy access to Eastern Express Highway\"]', '2026-08-08 18:08:30'),
(16, 'Elegance Heights, Nerul', 'Residential Tower', 'Nerul, Navi Mumbai', '₹1.78 Cr onwards', 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80', 'CC & RERA Received', NULL, NULL, '2 BHK', '725 sq.ft', '[\"G+19 floors iconic tower on a CIDCO tender plot\",\"Swimming pool, kids play area & fully equipped gym\",\"Yoga room, multipurpose hall & club house\",\"CC & RERA received for added buyer confidence\",\"Builder possession Dec 2027 \\/ RERA possession Nov 2028\"]', '[\"5 mins from Nerul Railway Station\",\"5 mins from Sion-Panvel Highway\",\"Premium location in Navi Mumbai\"]', '2026-08-08 18:08:30'),
(17, 'Vikhroli East Residences', 'Residential Tower', 'Vikhroli East, Mumbai', '₹21,000/sq.ft onwards', 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?auto=format&fit=crop&w=800&q=80', 'New Launch', NULL, NULL, '1 BHK', '440 sq.ft', '[\"High-rise G+22 tower with 4 lifts (3+1)\",\"Only 6 flats per floor for more privacy, less crowd\",\"Smart layouts with maximum space utilisation\",\"Gym, rooftop sit-out, kids play area & senior citizen zone\",\"Car parking available at \\u20b98 Lakhs\"]', '[\"Located in Vikhroli East with good social infrastructure\"]', '2026-08-08 18:08:30'),
(18, 'The New Landmark, Sion–Chunabhatti', 'Residential Project', 'Sion–Chunabhatti, Mumbai', 'Price on Request', 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=800&q=80', 'Under Construction', NULL, NULL, '1 BHK', '430 sq.ft', '[\"Smartly designed homes at Mumbai\'s most connected junction\",\"Walking distance to Chunabhatti Station, EEH & Metro access\",\"Minutes from BKC, Sion, Chembur, Kurla, Ghatkopar, Dadar & Lower Parel\",\"Surrounded by shopping malls, hospitals, food hubs & schools\",\"Builder timeline Dec 2029 \\/ RERA possession Dec 2032\"]', '[\"Chunabhatti Station\",\"Eastern Express Highway\",\"Metro Access\"]', '2026-08-08 18:08:30'),
(19, 'Vile Parle Residences', 'Residential Project', 'Vile Parle, Mumbai', '₹1.94 Cr onwards', 'https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=800&q=80', 'Under Construction – 2 Basements Completed', NULL, NULL, '1 BHK', '495 sq.ft', '[\"Prime Vile Parle location, ideal for upgraders & NRI families\",\"2 basements completed, plinth targeted by mid-August 2026\",\"Well suited for Gujarati, Jain & Maharashtrian families\",\"Great fit for business owners & investors\",\"Direct developer connect for inventory & pricing\"]', '[\"Located in the heart of Vile Parle with excellent social infrastructure\"]', '2026-08-08 18:08:30'),
(20, 'Premium 1 & 2 BHK Residences', 'Residential Apartment', 'Location to be confirmed, Mumbai', '₹73.99 Lacs onwards', 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=800&q=80', 'Possession December 2028', NULL, NULL, '1 BHK', '365 sq.ft', '[\"Trusted developer with 1 Million+ sq.ft. delivered\",\"20+ successfully completed projects\",\"Premium rooftop amenities\",\"Smart & efficient, future-ready layouts\",\"Commitment to transparency, trust & timely delivery\"]', '[\"To be confirmed\"]', '2026-08-08 18:08:30'),
(21, 'Fully Furnished Flat, Vikhroli', 'Resale Apartment', 'Vikhroli, Mumbai', '₹1.25 Cr (Negotiable)', 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80', 'Ready to Move / For Sale', NULL, NULL, 'Fully Furnished Flat', '527 sq.ft carpet', '[\"Fully furnished \\u2014 kitchen trolley with cabinets, bed, TV unit\",\"Cabinets in all rooms & water purifier included\",\"Price negotiable, inclusive of car parking\",\"5 minutes walking distance to Vikhroli Station\",\"Schools, hospitals, market & banks within 5 minutes\"]', '[\"Vikhroli Station \\u2013 5 mins walking\",\"Schools, Hospitals, Market & Banks \\u2013 within 5 mins\"]', '2026-08-08 18:08:30'),
(22, 'SoBo Deck Residences', 'Luxury Residential Tower', 'South Mumbai (SoBo)', '₹3.47 Cr onwards', 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=800&q=80', 'Under Construction', NULL, NULL, '2 BHK Deck (RCA)', '890 sq.ft', '[\"Supersized 2 & 3 bed deck homes with panoramic views\",\"Double height entrance lobby ready & large sundecks\",\"~14,000 sq.ft. of recreational spaces incl. Jain temple\",\"Swimming pool, terrace garden, yoga room & jogging track\",\"Jodi option available\"]', '[\"Prime South Mumbai (SoBo) location\"]', '2026-08-08 18:08:30'),
(23, 'Promont, BKC–Sion Connector', 'Residential Tower', 'BKC–Sion Connector, Mumbai', 'Price on Request', 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=800&q=80', 'Under Construction, Possession Dec 2027', NULL, NULL, '2 & 3 BHK Majestic Deck Residences', 'On Request', '[\"A treasured life awaits at the BKC\\u2013Sion Connector\",\"Relaxing pool deck & Skyplex\",\"Caf\\u00e9 lounge, BBQ corner & jacuzzi\",\"Possession by December 2027\",\"Construction in full swing\"]', '[\"Located directly on the BKC\\u2013Sion Connector\"]', '2026-08-08 18:08:30'),
(24, 'Vikhroli Podium Residences', 'Residential Tower', 'Vikhroli, Mumbai', '₹1.75 Cr onwards', 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80', 'Under Construction', NULL, NULL, '1 BHK', 'On Request', '[\"Ground + 5 Podium + 29 habitable floors\",\"Fully air-conditioned homes with false ceiling & LED lights\",\"Garden, jogging track, fitness centre & zen yoga deck\",\"Open air amphitheatre, swimming pool & kid\'s pool\",\"24\\u00d77 security with video door phone in every home\"]', '[\"Eastern Express Highway \\u2013 2 mins\",\"Railway Station \\u2013 7 mins\",\"Kannamwar Bus Depot \\u2013 2 mins\",\"R City Mall \\u2013 20 mins\",\"Metro Station \\u2013 5 mins\"]', '2026-08-08 18:08:30'),
(25, 'Zero-Wastage Residences, Vikhroli', 'Residential Tower', 'Vikhroli, Mumbai', '₹79 Lacs onwards', 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=800&q=80', 'New Launch', NULL, NULL, '1 BHK', '359 / 374 sq.ft', '[\"G+22 storey tower with spacious zero-wastage layouts\",\"Premium high-end retail outlets & podium level car park\",\"10,000 sq.ft. of dedicated amenities\",\"Sample flat available with unobstructed views\",\"Yoga zone, box cricket & rooftop party lawn\"]', '[\"Kannamwar Bus Depot \\u2013 2 mins\",\"Eastern Express Highway \\u2013 5 mins\",\"Vikhroli Railway Station \\u2013 6 mins\",\"International School & College \\u2013 8 mins\"]', '2026-08-08 18:08:30'),
(26, 'Vikhroli East Gated Community', 'Luxury Residential Tower', 'Vikhroli East, Mumbai', '₹1.08 Cr onwards', 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80', 'Under Construction', NULL, NULL, '1 BHK', '426 sq.ft', '[\"31-storey tower on a 1.25 acre gated land parcel\",\"2 levels basement + 4-level podium parking\",\"5 levels of exclusive lifestyle amenities\",\"30+ world-class amenities incl. infinity pool & spa\",\"Habitable residences begin from the 6th floor\"]', '[\"Prime highway-touch connectivity in Vikhroli East\"]', '2026-08-08 18:08:30'),
(27, '1 BHK Resale, Tilak Nagar', 'Resale Apartment', 'Near Tilak Nagar Station, Mumbai', '₹24,000/sq.ft', 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80', 'New Building', NULL, NULL, '1 BHK', '596 sq.ft carpet', '[\"New building, ready to move\",\"Car parking available at \\u20b910 Lakh\",\"Located near Tilak Nagar Station\"]', '[\"Close to Tilak Nagar Station\"]', '2026-08-08 18:08:30'),
(28, '1 BHK Resale, Near Tilak Nagar', 'Resale Apartment', 'Near Tilak Nagar, Mumbai', '₹95 Lakh', 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80', 'For Sale', NULL, NULL, '1 BHK', '375 sq.ft carpet', '[\"Open view apartment\",\"Located near Tilak Nagar\"]', '[\"Close to Tilak Nagar\"]', '2026-08-08 18:08:30'),
(29, '1 BHK Resale, Badlapur East', 'Resale Apartment', 'Badlapur East, Thane District', '₹33 Lakh', 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?auto=format&fit=crop&w=800&q=80', 'Vacant / For Sale', NULL, NULL, '1 BHK', '400 sq.ft carpet', '[\"Currently vacant, ready for immediate sale\",\"Located in Badlapur East, Thane District\"]', '[\"Located in Badlapur East, Thane District\"]', '2026-08-08 18:08:30'),
(32, 'xyz', 'Luxury Villa', 'Shell Colony', 'Rs.7.5Cr', 'uploads/properties/6a79b66f16e13.jpg', 'On Demand', 'FOR SALE', 'FEATURED', '4BHK', '2,000 sq.ft', '[\"bncjh,bdjhe\",\"msbbqevjchgejy\",\"dbjhqgqu!\"]', '[\"nbcjhb\",\"ewjmbckjehu\",\"kjwbciuflei\",\"sdjcnwkej4oi\"]', '2026-08-10 11:30:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `enquiries`
--
ALTER TABLE `enquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `enquiries`
--
ALTER TABLE `enquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
