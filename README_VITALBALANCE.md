# VitalBalance - Modern Wellness Platform

A comprehensive wellness platform for chronic disease management built with HTML, CSS, JavaScript, and PHP.

## Features

### 🏠 Landing/Login Page (`index.php`)
- **Animated Marquee Header** - Rotating wellness tips and motivational quotes
- **Split Screen Design** - Beautiful branding section + authentication forms
- **Dual Forms** - Seamless toggle between login and registration
- **Form Validation** - Real-time JavaScript validation
- **Animated Background** - Floating particles effect
- **Glassmorphism Design** - Modern frosted glass effects
- **Responsive Layout** - Mobile-first design

### 📊 Dashboard (`dashboard.php`)
- **Welcome Section** - Personalized greeting with time-based messages
- **Stats Cards** - 4 animated cards showing:
  - Active Conditions
  - Health Records
  - Wellness Score (with animated progress ring)
  - Food Recipes
- **Trending Diseases** - Real-time disease trends with charts
- **Health Records** - Timeline view of health metrics
- **Recipe Section** - Filterable healthy recipes
- **Quick Actions** - Floating action buttons

## Tech Stack

- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Libraries**:
  - AOS (Animate On Scroll) - Scroll animations
  - Chart.js - Data visualizations
  - Font Awesome 6.4.0 - Icons
  - Google Fonts (Poppins, Inter) - Typography

## Installation

### 1. Database Setup

Run the SQL file to create the database structure:

```bash
mysql -u root -p vitalbalance_db < database_schema_v2.sql
```

Or import `database_schema_v2.sql` through phpMyAdmin.

### 2. Configuration

Update database credentials in `includes/db.php`:

```php
$conn = mysqli_connect("localhost", "your_username", "your_password", "vitalbalance_db");
```

### 3. File Structure

```
vitalbalance_db/
├── api/
│   ├── add_record.php
│   ├── delete_record.php
│   └── toggle_favorite.php
├── assets/
│   ├── css/
│   │   ├── style.css (Landing page styles)
│   │   └── dashboard.css (Dashboard styles)
│   └── js/
│       ├── landing.js (Landing page scripts)
│       └── dashboard.js (Dashboard scripts)
├── includes/
│   ├── db.php (Database connection)
│   └── auth.php (Authentication check)
├── index.php (Landing/Login page)
├── dashboard.php (Main dashboard)
├── login.php (Legacy login - can be removed)
├── register.php (Legacy register - can be removed)
└── database_schema_v2.sql (Database schema)
```

### 4. Access the Application

1. Start your web server (XAMPP, WAMP, etc.)
2. Navigate to: `http://localhost/vitalbalance_db/`
3. Create an account or login with existing credentials

## Features in Detail

### Landing Page Features
- ✅ Animated marquee with wellness tips
- ✅ Particle background animation
- ✅ Glassmorphism card design
- ✅ Form validation (client-side)
- ✅ Password visibility toggle
- ✅ Smooth form transitions
- ✅ Responsive design

### Dashboard Features
- ✅ Sticky navigation bar
- ✅ Profile dropdown menu
- ✅ Animated stat counters
- ✅ Progress ring for wellness score
- ✅ Disease trend charts
- ✅ Health records timeline
- ✅ Recipe filtering
- ✅ Modal for adding records
- ✅ AJAX form submissions
- ✅ Toast notifications

## API Endpoints

### Add Health Record
- **URL**: `api/add_record.php`
- **Method**: POST
- **Parameters**: 
  - `record_type` (required)
  - `value` (required)
  - `unit` (optional)
  - `recorded_at` (optional, defaults to now)
  - `notes` (optional)

### Delete Health Record
- **URL**: `api/delete_record.php`
- **Method**: POST
- **Body**: JSON `{ "record_id": 123 }`

### Toggle Recipe Favorite
- **URL**: `api/toggle_favorite.php`
- **Method**: POST
- **Body**: JSON `{ "recipe_id": 123 }`

## Database Tables

- `users` - User accounts
- `diseases` - Common/trending diseases
- `user_diseases` - User's personal chronic diseases
- `health_records` - Health metric records
- `recipes` - Healthy food recipes
- `user_favorites` - User's favorite recipes

## Customization

### Colors
Edit CSS variables in `assets/css/style.css` and `assets/css/dashboard.css`:

```css
:root {
    --primary-purple: #8b5cf6;
    --primary-blue: #3b82f6;
    --primary-teal: #14b8a6;
}
```

### Animations
- AOS animations: Modify `data-aos` attributes in HTML
- CSS animations: Edit `@keyframes` in CSS files
- JavaScript animations: Modify timing in `dashboard.js`

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Security Notes

- ✅ Password hashing with `password_hash()`
- ✅ SQL injection prevention with `mysqli_real_escape_string()`
- ✅ Session-based authentication
- ✅ CSRF protection (can be added)
- ⚠️ Input validation (add server-side validation)

## Future Enhancements

- [ ] Email verification
- [ ] Password reset functionality
- [ ] Export health records as PDF
- [ ] Advanced analytics and charts
- [ ] Mobile app (React Native)
- [ ] Push notifications
- [ ] Social sharing features
- [ ] Recipe ratings and reviews

## Support

For issues or questions, please check:
- Database connection issues → Check `includes/db.php`
- Authentication problems → Check `includes/auth.php`
- JavaScript errors → Check browser console
- PHP errors → Check server error logs

## License

This project is part of the VitalBalance wellness platform.

---

**Built with ❤️ for better health management**

