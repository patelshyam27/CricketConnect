# Cricket Connect Box WebApp

A comprehensive web application for cricket enthusiasts to find players, book BoxCricket venues, join coaching classes, and purchase cricket gear.

## Features

### 🏏 Core Functionality
- **Player Search**: Find cricket players by location (State → City → Area)
- **BoxCricket Booking**: Book venues with verified player discounts
- **Coaching Enrollment**: Join coaching classes with exclusive discounts
- **Cricket Gear Store**: Purchase equipment through affiliate links
- **Social Features**: Follow/unfollow players, WhatsApp integration

### 👥 User Management
- User registration and authentication
- Profile management with editable details
- Follow system with follower/following counts
- Session-based authentication

### 🏟️ Venue & Coaching System
- Venue booking with team verification
- Discount validation for registered players only
- Direct UPI payment integration
- City-based coaching filtering

### 🛡️ Admin Panel
- User management (view/delete)
- Add/manage BoxCricket venues
- Add/manage coaching advertisements
- Manage cricket gear products
- Complete CRUD operations

## Technology Stack

- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend**: PHP 8+
- **Database**: MySQL
- **Server**: XAMPP
- **Icons**: Font Awesome 6
- **Styling**: Custom gradients and modern UI

## Installation

1. **Setup XAMPP**
   ```
   - Install XAMPP
   - Start Apache and MySQL services
   ```

2. **Database Setup**
   ```
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Import database.sql file
   - Database will be created with sample data
   ```

3. **File Placement**
   ```
   - Copy all files to: C:\xampp\htdocs\Cricket_Connect_Box\
   ```

   ```

## Database Schema

### Tables
- `users` - User profiles and authentication
- `follows` - Follow relationships between users
- `boxcricket_venues` - Venue information and pricing
- `bookings` - Venue booking records
- `coaching_ads` - Coaching advertisements
- `coaching_enrollments` - User coaching enrollments
- `gear_store` - Cricket equipment products

## Key Features Implementation

### 🔐 Authentication System
- Secure password hashing
- Session management
- Admin role separation
- Login/logout functionality

### 🎯 Discount Verification
- Team player registration check
- Automatic discount calculation
- UPI payment integration
- Booking validation system

### 📱 Modern UI/UX
- Responsive Bootstrap design
- Gradient backgrounds and cards
- Hover effects and animations
- Mobile-friendly interface
- Font Awesome icons

### 🔍 Search & Filter
- Location-based player search
- City-based coaching filter
- Real-time follow/unfollow
- Dynamic content loading


## Contact Information
- **Support Email**: shyamnp27@gmail.com

## File Structure
```
Cricket_Connect_Box/
├── config.php          # Database configuration
├── database.sql        # Database schema and sample data
├── index.php          # Home page
├── register.php       # User registration
├── login.php          # User login
├── profile.php        # User profile management
├── search.php         # Player search functionality
├── boxcricket.php     # Venue booking system
├── coaching.php       # Coaching enrollment
├── gear.php           # Cricket gear store
├── admin.php          # Admin panel
├── logout.php         # Logout functionality
└── README.md          # Documentation
```

## Usage Instructions

1. **User Registration**: Create account with cricket profile details
2. **Player Search**: Find players in your area using location filters
3. **Follow System**: Connect with other players through follow feature
4. **Venue Booking**: Book BoxCricket venues with team verification
5. **Coaching**: Enroll in coaching classes with verified discounts
6. **Gear Shopping**: Purchase cricket equipment through affiliate links
7. **Admin Management**: Manage all aspects through admin panel

## Security Features
- Password hashing with PHP password_hash()
- SQL injection prevention with prepared statements
- XSS protection with htmlspecialchars()
- Session-based authentication
- Admin role verification

## Future Enhancements
- Payment gateway integration
- Mobile app development
- Advanced search filters
- Tournament management
- Chat system integration
- Email notifications

---
**Developed for cricket enthusiasts to connect, play, and grow together! 🏏**
