CREATE DATABASE IF NOT EXISTS apnaaghar_db;
USE apnaaghar_db;

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Admin User
-- Password is 'admin123'
INSERT IGNORE INTO admin_users (username, password_hash) VALUES 
('admin@apnaghar', '$2y$10$tZ9y1vT/x5v.0Qy6H7kMJu.x6w.N8Q.2m9T8yY4.yPzN/0xT7W7'); 

-- Properties Table
CREATE TABLE IF NOT EXISTS properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    type VARCHAR(100) NOT NULL,
    location VARCHAR(255) NOT NULL,
    price VARCHAR(50) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    status VARCHAR(100) NOT NULL,
    badge_status VARCHAR(50) DEFAULT NULL, -- 'FOR SALE' or 'FOR RENT' or empty
    badge_featured VARCHAR(50) DEFAULT NULL, -- 'FEATURED' or empty
    bhk VARCHAR(50) NOT NULL,
    size VARCHAR(100) NOT NULL,
    highlights_json TEXT NOT NULL,
    connectivity_json TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Gallery Table
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Dummy Properties Data to match previous static layout
INSERT INTO properties (title, type, location, price, image_url, status, badge_status, badge_featured, bhk, size, highlights_json, connectivity_json) VALUES 
('The Grand Horizon Residency', 'Luxury Tower', 'Shell Colony, Chembur, Mumbai', '₹3.45 Cr', 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80', 'OC Received', 'FOR SALE', 'FEATURED', '3 BHK', '1,450 sq.ft', '["Double Height Grand Entrance Lobby (Air-Conditioned)", "Fully Equipped Modern Gymnasium", "Beautiful Rooftop Garden & Lounge Area", "High-speed Passenger Elevators", "24/7 Security Surveillance & Intercom System"]', '["5 mins from Chembur Railway Station", "2 mins drive from Eastern Express Highway", "10 mins to Bandra Kurla Complex (BKC) via connector", "Conveniently close to upcoming Metro Line 4"]'),
('Symphony Sky Villa', 'Luxury Penthouse', 'Tilak Nagar, Chembur, Mumbai', '₹5.20 Cr', 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=800&q=80', 'OC Received', 'FOR SALE', '', '4 BHK', '2,200 sq.ft', '["Exclusive Private High-Speed Elevator Access", "Infinity Edge Rooftop Swimming Pool", "3 Reserved Private Covered Car Parks", "Advanced Smart Home Automation System", "360-degree Panoramic Mumbai Skyline View"]', '["2 mins walking distance to Tilak Nagar Railway Station", "5 mins drive to SCLR & Kurla area", "12 mins drive to BKC via Connector", "Easy connection to the Eastern Freeway"]'),
('Elegance Court Duplex', 'Builder Floor', 'Union Park, Chembur, Mumbai', '₹1.25 L/Mo', 'https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=800&q=80', 'Available immediately', 'FOR RENT', '', '4 BHK', '1,950 sq.ft', '["Massive Private Terrace Area", "Fully Furnished with Premium Imported Fittings", "Pet-Friendly Building", "Designated Servant Quarters", "24-hour uninterrupted Power Backup"]', '["In the heart of Chembur\'s most premium residential zone", "Walking distance to Gymkhana and fine-dining", "Quick access to Eastern Freeway", "15 mins to Navi Mumbai"]');

-- Insert Dummy Gallery Data
INSERT INTO gallery (title, category, image_url) VALUES 
('Luxury Master Bedroom', 'Interiors', 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80'),
('Modern Kitchen', 'Interiors', 'https://images.unsplash.com/photo-1556912172-45b7abe8b7e1?auto=format&fit=crop&w=800&q=80'),
('Grand Lobby', 'Amenities', 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=800&q=80'),
('Gymnasium', 'Amenities', 'https://images.unsplash.com/photo-1540497077202-7c8a3999166f?auto=format&fit=crop&w=800&q=80'),
('Elevation View', 'Exteriors', 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80'),
('Living Room', 'Interiors', 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=800&q=80'),
('Swimming Pool', 'Amenities', 'https://images.unsplash.com/photo-1576013551627-0cc20b96c2a7?auto=format&fit=crop&w=800&q=80');
