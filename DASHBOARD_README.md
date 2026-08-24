# VitalBalance Dashboard System

## Overview
**VitalBalance: An Integrated Wellness System for Personalized Chronic Disease Management and Natural Remedies**

This dashboard system provides comprehensive tools for managing personal wellness, tracking chronic diseases, and accessing natural remedies.

## Features

### User Dashboard
- **Personalized Wellness Dashboard**: View your health statistics at a glance
- **Chronic Disease Management**: Track and manage your chronic conditions
- **Health Records**: Record and monitor your health metrics
- **Natural Remedies**: Browse and access natural treatment options
- **Wellness Tracker**: Monitor your overall wellness score
- **Quick Actions**: Fast access to common tasks

### Admin Dashboard
- **User Management**: View and manage all system users
- **Disease Database**: Manage chronic disease information
- **Remedies Management**: Add and update natural remedies
- **Health Records Overview**: System-wide health data tracking
- **Reports & Analytics**: View system statistics and reports
- **System Settings**: Configure system-wide settings

## Installation

### 1. Database Setup
Run the following SQL files in order:
1. `database_update.sql` - Updates users table
2. `database_schema.sql` - Creates all required tables and sample data

```sql
-- Run in phpMyAdmin or MySQL console
SOURCE database_update.sql;
SOURCE database_schema.sql;
```

### 2. File Structure
```
vitalbalance_db/
├── admin/
│   └── dashboard.php
├── user/
│   └── dashboard.php
├── assets/
│   └── css/
│       ├── login.css
│       └── dashboard.css
├── includes/
│   ├── db.php
│   └── auth.php
├── login.php
├── register.php
├── forgot_password.php
└── logout.php
```

## Database Schema

### Core Tables
- **users**: User accounts and profiles
- **diseases**: Chronic disease information
- **user_diseases**: User-disease relationships
- **remedies**: Natural remedies database
- **remedy_diseases**: Remedy-disease relationships
- **health_records**: Health data tracking
- **wellness_scores**: Wellness metrics
- **user_remedies**: User remedy tracking
- **appointments**: Healthcare appointments
- **medications**: Medication tracking
- **symptoms_log**: Symptom tracking

## Usage

### For Users
1. Register/Login at the login page
2. Access your personalized dashboard
3. Add chronic diseases you're managing
4. Record health metrics regularly
5. Browse natural remedies for your conditions
6. Track your wellness score over time

### For Administrators
1. Login with admin account
2. Access admin dashboard
3. Manage users, diseases, and remedies
4. View system-wide statistics
5. Generate reports

## Features in Detail

### Chronic Disease Management
- Add and track multiple chronic conditions
- Monitor disease status (active, managed, resolved)
- Record diagnosis dates and notes
- Get personalized remedy recommendations

### Natural Remedies System
- Comprehensive database of natural treatments
- Tagged by categories and conditions
- Usage instructions and dosage information
- Precautions and safety information

### Health Records Tracking
- Record various health metrics (vitals, symptoms, medications)
- Track trends over time
- Add notes and observations
- Filter by record type

### Wellness Scoring
- Overall wellness score (0-100)
- Physical, mental, and emotional sub-scores
- Track improvements over time
- Visual progress indicators

## Design Features

### Modern UI/UX
- Beautiful gradient backgrounds
- Glassmorphism design elements
- Responsive mobile-friendly layout
- Smooth animations and transitions
- Intuitive navigation

### Color Scheme
- Primary: Purple gradient (#667eea to #764ba2)
- Success: Green (#51cf66)
- Danger: Red (#ff6b6b)
- Warning: Yellow (#ffd93d)

## Next Steps

To extend the system, you can:
1. Create pages for each navigation item (chronic_diseases.php, remedies.php, etc.)
2. Add forms for adding/editing records
3. Implement search and filter functionality
4. Add data visualization charts
5. Create reporting features
6. Add email notifications
7. Implement export functionality (PDF, CSV)

## Support

For issues or questions, refer to the codebase or database schema documentation.

