-- Database: car_rental
CREATE DATABASE IF NOT EXISTS car_rental;
USE car_rental;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS rentals;
DROP TABLE IF EXISTS vehicles;
DROP TABLE IF EXISTS promotions;
DROP TABLE IF EXISTS customers;
SET FOREIGN_KEY_CHECKS = 1;

-- Table for customers
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    favorites JSON,
    loyalty_points INT DEFAULT 0,
    is_verified BOOLEAN DEFAULT FALSE,
    driver_license_url VARCHAR(255),
    role ENUM('customer', 'admin', 'maintenance', 'delivery') DEFAULT 'customer',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for vehicles
CREATE TABLE IF NOT EXISTS vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    brand VARCHAR(255),
    type VARCHAR(100),
    rent_per_day DECIMAL(10, 2) NOT NULL,
    status ENUM('Available', 'Booked', 'Maintenance') DEFAULT 'Available',
    image VARCHAR(255),
    description TEXT,
    transmission VARCHAR(50) DEFAULT 'Automatic',
    fuel_type VARCHAR(50) DEFAULT 'Petrol',
    seating_capacity INT DEFAULT 5,
    model_year INT,
    rating_average DECIMAL(3, 1) DEFAULT 0.0,
    odometer INT DEFAULT 0,
    fuel_level INT DEFAULT 100,
    license_plate VARCHAR(20) DEFAULT 'TN 01 AB 1234',
    color VARCHAR(50) DEFAULT 'White',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for system activity logs
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100),
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES customers(id)
);

-- Seed Vehicles (10 High-Quality Examples)
INSERT IGNORE INTO vehicles (name, brand, type, rent_per_day, status, image, description, transmission, fuel_type, seating_capacity, model_year, rating_average) VALUES
('Model S', 'Tesla', 'Luxury', 150.00, 'Available', 'https://images.unsplash.com/photo-1560958089-b8a1929cea89?auto=format&fit=crop&w=800&q=80', 'Experience the pinnacle of electric performance with the Tesla Model S. Unmatched range and futuristic tech.', 'Automatic', 'Electric', 5, 2024, 4.9),
('Cayenne', 'Porsche', 'SUV', 200.00, 'Available', 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=800&q=80', 'The Porsche Cayenne combines performance with versatility. A luxury SUV for those who refuse to compromise.', 'Automatic', 'Petrol', 5, 2023, 4.8),
('Civic Type R', 'Honda', 'Hatchback', 85.00, 'Available', 'https://images.unsplash.com/photo-1594502184342-2e12f877aa73?auto=format&fit=crop&w=800&q=80', 'The ultimate hot hatch. Sharp handling and aggressive styling make every drive an event.', 'Manual', 'Petrol', 5, 2023, 4.7),
('S-Class', 'Mercedes-Benz', 'Luxury', 250.00, 'Available', 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?auto=format&fit=crop&w=800&q=80', 'The benchmark of luxury sedans. The S-Class offers unparalleled comfort and cutting-edge safety features.', 'Automatic', 'Petrol', 5, 2024, 5.0),
('Land Cruiser', 'Toyota', 'SUV', 180.00, 'Available', 'https://images.unsplash.com/photo-1594535182308-8ffefbb661e1?auto=format&fit=crop&w=800&q=80', 'A legendary off-roader. The Land Cruiser is built to handle the toughest terrains in absolute luxury.', 'Automatic', 'Diesel', 7, 2022, 4.9),
('Mustang GT', 'Ford', 'Luxury', 120.00, 'Available', 'https://images.unsplash.com/photo-1584345604476-8ec5e12e42dd?auto=format&fit=crop&w=800&q=80', 'Pure American muscle. The Mustang GT delivers high horsepower and an iconic exhaust note.', 'Manual', 'Petrol', 4, 2023, 4.6),
('A8', 'Audi', 'Luxury', 230.00, 'Available', 'https://images.unsplash.com/photo-1606152421802-db97b9c7a11b?auto=format&fit=crop&w=800&q=80', 'Precision engineering meets modern luxury. The A8 is the flagship sedan for the tech-savvy executive.', 'Automatic', 'Hybrid', 5, 2024, 4.8),
('Range Rover', 'Land Rover', 'SUV', 220.00, 'Available', 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?auto=format&fit=crop&w=800&q=80', 'The epitome of British luxury and off-road capability. The Range Rover is as at home in the city as it is in the wild.', 'Automatic', 'Diesel', 5, 2023, 4.7),
('911 Carrera', 'Porsche', 'Luxury', 300.00, 'Available', 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=800&q=80', 'The worlds most versatile sports car. Incredible performance with daily usability.', 'Automatic', 'Petrol', 4, 2024, 5.0),
('Ionic 5', 'Hyundai', 'SUV', 95.00, 'Available', 'https://images.unsplash.com/photo-1630136595427-bd085b9e0967?auto=format&fit=crop&w=800&q=80', 'Award-winning electric SUV. Retro-futuristic design with super-fast charging capabilities.', 'Automatic', 'Electric', 5, 2023, 4.8),
('M5 CS', 'BMW', 'Luxury', 180.00, 'Available', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=800&q=80', 'The benchmark of performance sedans. The M5 CS is lighter, more powerful, and faster than ever.', 'Automatic', 'Petrol', 5, 2023, 4.9),
('Q7', 'Audi', 'SUV', 110.00, 'Available', 'https://images.unsplash.com/photo-1542281286-9e0a16bb7366?auto=format&fit=crop&w=800&q=80', 'Combining safety, luxury, and technology. The Audi Q7 is the versatile family SUV that handles like a dream.', 'Automatic', 'Diesel', 7, 2023, 4.7),
('G-Class', 'Mercedes-Benz', 'SUV', 350.00, 'Available', 'https://images.unsplash.com/photo-1520031444948-4ce50c26bcc1?auto=format&fit=crop&w=800&q=80', 'The legendary Geländewagen. Unmatched off-road prowess with an interior that rivals a private jet.', 'Automatic', 'Petrol', 5, 2024, 5.0),
('F8 Tributo', 'Ferrari', 'Luxury', 450.00, 'Available', 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?auto=format&fit=crop&w=800&q=80', 'An homage to the most powerful V8 in Ferrari history. Breathtaking speed and Italian artistry.', 'Automatic', 'Petrol', 2, 2023, 5.0),
('Huracán STO', 'Lamborghini', 'Luxury', 480.00, 'Available', 'https://images.unsplash.com/photo-1544636331-e268798d7dfb?auto=format&fit=crop&w=800&q=80', 'A track-focused road-legal super sports car. Aggressive aerodynamics and soul-stirring sound.', 'Automatic', 'Petrol', 2, 2024, 4.9),
('Wrangler Rubicon', 'Jeep', 'SUV', 90.00, 'Available', 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=800&q=80', 'The ultimate off-road icon. Removable doors and roof make for an unforgettable adventure.', 'Manual', 'Petrol', 4, 2022, 4.6),
('Camry', 'Toyota', 'Sedan', 55.00, 'Available', 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?auto=format&fit=crop&w=800&q=80', 'Efficiency meets reliability. The Camry offers a smooth ride and class-leading fuel economy.', 'Automatic', 'Hybrid', 5, 2024, 4.8),
('F-150 Raptor', 'Ford', 'SUV', 130.00, 'Available', 'https://images.unsplash.com/photo-1533106418989-88406c7cc8ca?auto=format&fit=crop&w=800&q=80', 'Built for high-speed desert running. The Raptor is as tough as it looks.', 'Automatic', 'Petrol', 5, 2023, 4.7),
('GT-R Nismo', 'Nissan', 'Luxury', 280.00, 'Available', 'https://images.unsplash.com/photo-1502161828065-d4aed903062d?auto=format&fit=crop&w=800&q=80', 'Godzilla refined. Precision handling and explosive acceleration in a tech-driven package.', 'Automatic', 'Petrol', 4, 2024, 4.8),
('Corvette Z06', 'Chevrolet', 'Luxury', 190.00, 'Available', 'https://images.unsplash.com/photo-1525609004556-c46c7d6cf048?auto=format&fit=crop&w=800&q=80', 'A mid-engine masterpiece. supercar performance at a fraction of the cost.', 'Automatic', 'Petrol', 2, 2024, 4.9),
('Thar', 'Mahindra', 'SUV', 45.00, 'Available', 'https://images.unsplash.com/photo-1662924151272-98444e735234?auto=format&fit=crop&w=800&q=80', 'The ultimate Indian off-roader. Conquer any terrain with the legendary Mahindra Thar.', 'Manual', 'Diesel', 4, 2023, 4.6),
('Safari', 'Tata', 'SUV', 85.00, 'Available', 'https://images.unsplash.com/photo-1619767886558-efdc259cde1a?auto=format&fit=crop&w=800&q=80', 'Reclaim your life. The Tata Safari offers a perfect blend of power, prestige, and comfort.', 'Automatic', 'Diesel', 7, 2024, 4.7),
('Swift', 'Maruti Suzuki', 'Hatchback', 35.00, 'Available', 'https://images.unsplash.com/photo-1590362891991-f776e747a588?auto=format&fit=crop&w=800&q=80', 'Indias favorite hatchback. Sporty design meets incredible fuel efficiency.', 'Manual', 'Petrol', 5, 2022, 4.5),
('Creta', 'Hyundai', 'SUV', 70.00, 'Available', 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?auto=format&fit=crop&w=800&q=80', 'The ultimate SUV. Premium features combined with advanced safety and smart tech.', 'Automatic', 'Petrol', 5, 2024, 4.8),
('Seltos', 'Kia', 'SUV', 75.00, 'Available', 'https://images.unsplash.com/photo-1610647780823-d29994c6d3df?auto=format&fit=crop&w=800&q=80', 'Badass by design. The Kia Seltos stands out with its bold styling and performance.', 'Automatic', 'Diesel', 5, 2023, 4.7),
('Innova Hycross', 'Toyota', 'SUV', 110.00, 'Available', 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=800&q=80', 'Legendary comfort, hybrid efficiency. The Innova Hycross is the king of family travel.', 'Automatic', 'Hybrid', 7, 2024, 4.9),
('XUV700', 'Mahindra', 'SUV', 95.00, 'Available', 'https://images.unsplash.com/photo-1619682817481-e994891cd1f5?auto=format&fit=crop&w=800&q=80', 'Global safety standards with sci-fi technology. The XUV700 is built for the future.', 'Automatic', 'Petrol', 7, 2023, 4.9);

-- Table for rentals (bookings)
CREATE TABLE IF NOT EXISTS rentals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_email VARCHAR(255),
    vehicle_id INT,
    start_date DATE,
    end_date DATE,
    days INT,
    total_amount DECIMAL(10, 2),
    status ENUM('Pending', 'Confirmed', 'Ongoing', 'Completed', 'Cancelled') DEFAULT 'Pending',
    is_rated BOOLEAN DEFAULT FALSE,
    delivery_employee_id INT,
    delivery_address TEXT,
    delivery_status ENUM('Pending', 'Assigned', 'Out for Delivery', 'Delivered') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_email) REFERENCES customers(email),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (delivery_employee_id) REFERENCES customers(id)
);

-- Table for maintenance logs
CREATE TABLE IF NOT EXISTS maintenance_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT,
    staff_id INT,
    category ENUM('Oil Change', 'Tire Rotation', 'Brake Service', 'Engine Check', 'General Cleaning', 'Other'),
    description TEXT,
    cost DECIMAL(10, 2) DEFAULT 0.00,
    odometer_at_service INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (staff_id) REFERENCES customers(id)
);

-- Table for payments
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rental_id INT,
    amount DECIMAL(10, 2),
    method VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rental_id) REFERENCES rentals(id)
);

-- Table for promotions
CREATE TABLE IF NOT EXISTS promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    promo_code VARCHAR(50),
    discount_percentage INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for reviews
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT,
    customer_id INT,
    rating INT,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);

-- Initial admin user
INSERT IGNORE INTO customers (name, email, password, phone, role) VALUES
('Admin User', 'admin@fastride.com', 'admin123', '1234567890', 'admin'),
('Vasudevan', 'vasudevan12506@gmail.com', 'pass12345', '1234567890', 'admin');
