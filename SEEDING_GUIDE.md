# 📊 RestIQ Dashboard - Realistic Data Population Guide

## Quick Start

### Step 1: Run the Data Seeder
1. Open your browser and navigate to:
   ```
   http://localhost/sleepy_project/seed_data.php
   ```

2. The script will:
   - ✅ Create 1 admin user (admin / admin123)
   - ✅ Generate 100 realistic users with:
     - Real-looking names (combined from 50 first names, 30 last names)
     - Varied genders (M, F, Other)
     - Realistic email addresses
     - Random registration dates (last 30 days)
   
   - ✅ Generate 500+ realistic predictions with:
     - 5-8 predictions per user
     - 6 sleep categories (Normal, Light, Deep, Insomnia, REM, Restless)
     - Sleep hours ranging 4-10 hours
     - Distributed timestamps over last 30 days

3. The seeder will display completion stats:
   - Total Users created
   - Total Predictions created
   - Average sleep hours
   - Most common sleep category

### Step 2: Login to Admin Dashboard
- **URL**: `http://localhost/sleepy_project/login.php`
- **Username**: `admin`
- **Password**: `admin123`

### Step 3: View Realistic Dashboard
- Navigate to: `http://localhost/sleepy_project/myadmin.php`
- You should see:
  - ✅ 100 total users
  - ✅ 500+ total predictions
  - ✅ Average sleep ~7h (realistic average)
  - ✅ User growth chart with 7-day trend
  - ✅ Sleep distribution categories

---

## 📊 Data Generated

### User Distribution
- **Total**: 100 users
- **Registration Period**: Last 30 days (random dates/times)
- **Gender Split**: Mixed (Male, Female, Other)
- **Unique Emails**: Realistic format (firstname.lastname+number@email.com)

### Prediction Data
- **Total Predictions**: 500-800 entries (5-8 per user)
- **Sleep Hours**: 4h to 10h (realistic range)
- **Categories**: 
  - Normal (most common)
  - Light
  - Deep
  - Insomnia
  - REM
  - Restless
- **Timeline**: Distributed over last 30 days

### Dashboard Metrics
- **Total Users**: 100
- **Daily New Users**: 2-5 (varying)
- **Average Sleep**: ~7 hours (healthy benchmark)
- **Most Common**: Usually "Normal" sleep
- **Admin Count**: 1

---

## 🔐 Test Accounts

### Admin Account
```
Username: admin
Password: admin123
Role: Administrator
```

### Sample User Accounts
Use any of the 100 generated users:
- Generated emails follow format: firstname.lastname@email.com
- All user passwords: `password123`
- Access user dashboard via: `/dashboard.php`

---

## 📈 Dashboard Visualization Features

### Real-Time Elements
- ⏱️ Live clock (updates every second)
- 🟢 Live status indicator
- 📊 Real metrics from database

### Charts Included
1. **User Growth Line Chart**
   - 7-day trend
   - New registrations over time
   - Smooth animations

2. **Sleep Distribution Doughnut Chart**
   - Category breakdown
   - Color-coded segments
   - Interactive hover effects

### Key Metrics Displayed
- 👥 Total Users (100)
- 🧠 Total Predictions (500+)
- 😴 Average Sleep (7h)
- 🛡️ Admins Online (1)
- 📈 Today's New Users (dynamic)

---

## 🔄 Re-Running the Seeder

⚠️ **Important**: The seeder will NOT run if data already exists. To reseed:

1. Clear the database:
   ```sql
   DELETE FROM predictions;
   DELETE FROM users WHERE role = 'user';
   ```

2. Or delete the `seed_data.php` file's check at the top:
   ```php
   // Comment out these lines if you want to reseed
   // $check_seed = mysqli_query(...);
   // if ($user_count > 0) { die(...); }
   ```

3. Re-run `http://localhost/sleepy_project/seed_data.php`

---

## 🎯 What You'll See

### Dashboard Stats Section
```
👥 Total Users: 100
🧠 Total Predictions: 650 (example)
😴 Avg Sleep Time: 7.2h
🛡️ Admins Online: 1
```

### Growth Chart
- Shows steady user registrations over 7 days
- Starts from ~2 users per day, trending upward
- Smooth line chart with point markers

### Category Distribution
- Normal: ~35% (largest segment)
- Light: ~20%
- Deep: ~15%
- Insomnia: ~15%
- REM: ~10%
- Restless: ~5%

### Quick Actions
- Manage Users
- Add User
- Settings
- Logout

---

## ✨ Features Demonstrated

✅ Realistic user data with varied demographics
✅ Time-series prediction data
✅ Live clock and status updates
✅ Interactive charts with Chart.js
✅ Responsive design
✅ Professional dashboard layout
✅ Color-coded metrics
✅ Hover animations and effects

---

**Generated**: May 7, 2026  
**Database**: RestIQ (restiq_db)  
**Status**: Ready for demonstration ✓
