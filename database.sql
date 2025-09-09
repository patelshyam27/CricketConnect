CREATE DATABASE cricket_connect_box;
USE cricket_connect_box;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    role ENUM('Batsman', 'Bowler', 'All-rounder') NOT NULL,
    state VARCHAR(50) NOT NULL,
    city VARCHAR(50) NOT NULL,
    area VARCHAR(50) NOT NULL,
    availability VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    followers INT DEFAULT 0,
    following INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE follows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    follower_id INT,
    following_id INT,
    FOREIGN KEY (follower_id) REFERENCES users(id),
    FOREIGN KEY (following_id) REFERENCES users(id)
);

CREATE TABLE boxcricket_venues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    city VARCHAR(50) NOT NULL,
    area VARCHAR(50) NOT NULL,
    price_per_hour DECIMAL(10,2) NOT NULL,
    discount_percent INT DEFAULT 10,
    upi_id VARCHAR(100) NOT NULL,
    contact VARCHAR(15) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venue_id INT,
    captain_id INT,
    team_players TEXT NOT NULL,
    booking_date DATE NOT NULL,
    time_slot VARCHAR(20) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('Pending', 'Completed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (venue_id) REFERENCES boxcricket_venues(id),
    FOREIGN KEY (captain_id) REFERENCES users(id)
);

CREATE TABLE coaching_ads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coach_name VARCHAR(100) NOT NULL,
    city VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    discount_percent INT DEFAULT 15,
    coupon_code VARCHAR(20) NOT NULL,
    upi_id VARCHAR(100) NOT NULL,
    contact VARCHAR(15) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE coaching_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    coaching_id INT,
    amount_paid DECIMAL(10,2) NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (coaching_id) REFERENCES coaching_ads(id)
);

CREATE TABLE gear_store (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    affiliate_link TEXT NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data
INSERT INTO boxcricket_venues (name, city, area, price_per_hour, upi_id, contact) VALUES
('Champions Arena', 'Mumbai', 'Andheri', 1500.00, 'champions@paytm', '9876543210'),
('Cricket Zone', 'Delhi', 'CP', 1200.00, 'zone@gpay', '9876543211');

INSERT INTO coaching_ads (coach_name, city, description, price, coupon_code, upi_id, contact) VALUES
('Rahul Cricket Academy', 'Mumbai', 'Professional cricket coaching for all ages', 2000.00, 'CRICKET15', 'rahul@paytm', '9876543212'),
('Delhi Cricket Club', 'Delhi', 'Expert coaching with modern techniques', 1800.00, 'DELHI15', 'delhi@gpay', '9876543213');

INSERT INTO gear_store (product_name, price, affiliate_link) VALUES
('MRF Cricket Bat', 2500.00, 'https://amazon.in/cricket-bat'),
('SG Cricket Kit', 4500.00, 'https://flipkart.com/cricket-kit'),
('Kookaburra Ball', 800.00, 'https://amazon.in/cricket-ball');