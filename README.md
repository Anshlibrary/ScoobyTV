📺 ScoobyTV — Open-Source OTT Streaming Platform (Powered by Jellyfin)

ScoobyTV is a self-hosted OTT streaming platform built on top of the open-source media server Jellyfin.
It allows users to streaming Movies, Series, Live TV, and Premium content across TV, Laptop, and Mobile devices.

🚀 Features
🎞️ Core Streaming

Custom UI connected with Jellyfin API

Stream movies & series in HD/Full-HD

Multi-device support (Web, Mobile, Android TV Browser)

Auto-responsive UI (HTML + CSS + PHP)

👤 User System

Secure Login/Signup system

Password reset

User profile management

Trial system (Auto trial activation)

💳 Subscription & Payments

Integrated payment workflow

Multiple payment versions (v2, v3)

Coupon system

Auto subscription activation

Invoice/Receipt generation

📋 Admin Dashboard

Add/edit users

Disable/enable users

Manage content

View feedback

Track user activity

Full backend panel under /admin/

📂 Additional Modules

Feedback system

Email notifications (PHPMailer)

Static pages (Privacy Policy, Services, etc.)

Backup scripts

Cron jobs (auto scripts)

🏗️ Tech Stack
Component	Technology

Frontend	HTML, CSS, JavaScript

Backend	PHP (Core PHP)

Database	MySQL

Media Server	Jellyfin (Open-Source)

Email Service	PHPMailer

Deployment	cPanel, Apache, Nginx

📦 Project Structure

ScoobyTV/

│
├── admin/                 # Admin dashboard & controls

├── assets/                # CSS, JS, Media assets

├── forms/                 # User forms & handling

├── SPP/                   # Subscription management panel

├── static/                # HTML static pages

├── series/                # Series-related assets

├── py/                    # Python helper scripts (if any)

│
├── index.php              # Homepage

├── jellyser.php           # Jellyfin integration layer

├── payment.php            # Payment handler

├── process_form.php       # Form actions

├── reset_password.php     # Password recovery

├── privacy-policy.html    # Static pages

├── robots.txt             # SEO file

└── .htaccess              # Apache rules


🔧 Setup Guide
1️⃣ Clone the repository
git clone https://github.com/Anshlibrary/ScoobyTV.git
cd ScoobyTV

2️⃣ Setup Jellyfin Server

Install Jellyfin:

Windows: https://jellyfin.org/downloads

Linux (Ubuntu):

sudo apt install jellyfin

Configure Libraries (Movies, Series)

Enable API key access

3️⃣ Configure Database

Create a MySQL database:

CREATE DATABASE scoobytv;

Import the SQL file if provided:

SOURCE database.sql;

Update DB credentials in:

conn.php
config.php

4️⃣ Update Jellyfin API URL

Open jellyser.php and update:

$jf_url = "YOUR_JELLYFIN_SERVER_URL";

$jf_api = "YOUR_JELLYFIN_API_KEY";

5️⃣ Deploy to Server

Upload all files to:

/public_html/

Set Folder permissions:

assets/, static/, private/

📱 Screenshots (Add your images here)

Add screenshots inside:
📂 /screenshots folder

![Dashboard](screenshots/dashboard.png)
![Home UI](screenshots/home.png)
![Player](screenshots/player.png)

🗺️ Roadmap

 Add Android App

 Add Auto subscription renewal

 Add Live TV Module

 Add Multi-profile support (Kids, Family)

 Add Theme Customization

 Add Admin Analytics

🤝 Contributing

Pull requests are welcome.
For major changes, open an issue first to discuss the idea.

🔐 License

This project is open-source.
Add your preferred license (MIT recommended).

❤️ Credits

Anshul Kashyap (Anshlibrary) — Creator & Developer
Ankit Kumar — Creator & Developer
Manish Dwivedi — Creator & Developer

Jellyfin Project — Open-source media server

Community contributors

⭐ Support the Project

If you like this project, please star the repository ⭐ on GitHub!

👉 https://github.com/Anshlibrary/ScoobyTV
