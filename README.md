# 🎟️ EventHub — Event Management System

<div align="center">

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-MariaDB_10.4-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

**A full-featured web-based event management and ticketing platform built with PHP & MySQL.**  
Supports multi-role access (Admin, Client, User), event approval workflows, UPI-based payments, and digital ticket generation.

</div>

---

## 📋 Table of Contents

- [What's New](#-whats-new)
- [Overview](#-overview)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Database Schema](#-database-schema)
- [Project Structure](#-project-structure)
- [Getting Started](#-getting-started)
- [User Roles & Workflows](#-user-roles--workflows)
- [Screenshots & Pages](#-screenshots--pages)
- [Configuration](#-configuration)
- [Contributing](#-contributing)

---

## 🆕 What's New

The following features were added in the latest update:

| # | Feature | File(s) Changed |
|---|---------|----------------|
| 1 | **Live Search & Category Filter** — search events by name/venue; filter by category with one click | `main.php` |
| 2 | **My Bookings Page** — logged-in users can view their full booking history with stats and ticket links | `my_bookings.php` *(new)* |
| 3 | **Admin Stats Dashboard** — 7 at-a-glance stat cards (events, users, organizers, bookings, revenue) | `admin_dashboard.php` |
| 4 | **Event Countdown Timer** — live days/hours/mins/secs countdown on the booking page | `booking_page.php` |
| 5 | **Print / Save Ticket** — print-clean ticket output with a dedicated print button | `ticket.php` |

---

## 🌟 Overview

**EventHub** is a comprehensive event management system that enables organizations and individuals to discover, create, and book events online. It features a three-tier role system — **Admins**, **Clients (Organizers)**, and **general Users** — each with dedicated portals and workflows.

Key capabilities include:
- **Event discovery** with live search, category filtering, and a hero landing page
- **My Bookings** dashboard so users can track all their past and upcoming tickets
- **Client portal** for submitting and managing event listings
- **Admin dashboard** with statistics overview and event moderation (approve / reject)
- **Multi-step booking** flow with UPI payment proof upload
- **Digital ticket generation** with a unique booking ID, countdown timer, and print support

---

## ✨ Features

### 👤 User Features
- 🔐 Sign up & log in to a personal account
- 🔍 **Live search** — filter events by name or venue as you type
- 🏷️ **Category filter buttons** — browse events by Music, Education, etc. with one click
- 📅 **My Bookings page** — view all past bookings, total tickets purchased, and total amount spent
- 🎫 **Multi-step ticket booking**: event summary → personal details → ticket count & payment
- ⏱️ **Event countdown timer** — see exactly how many days/hours/mins/secs until the event starts
- 💳 UPI payment integration with QR code display and one-click UPI ID copy
- 📄 Upload payment screenshot as proof of transaction
- 🎟️ View a stylish digital ticket with full booking details after purchase
- 🖨️ **Print / Save ticket** — print-ready layout, or save as PDF via the browser

### 🏢 Client (Organizer) Features
- 📝 Register as a business client with KYC document uploads (Aadhaar, PAN, photo)
- 🗓️ Submit event creation forms with details: name, venue, date/time, speaker, category, poster, pricing, seat count
- ⏳ View event approval status (pending / approved / rejected)

### 🛡️ Admin Features
- 🔐 Secure admin login with hashed password authentication
- 📊 **Statistics dashboard** — 7 live stat cards: Total Events, Pending, Approved, Users, Organizers, Bookings, Revenue
- 📋 Pending events list with filter options (All / Upcoming / Past)
- ✅ One-click **Approve** or **Reject** events with confirmation dialogs
- 🚪 Admin session management with logout

---

## 🛠️ Tech Stack

| Layer        | Technology                                |
|--------------|-------------------------------------------|
| **Frontend** | HTML5, CSS3 (custom + Bootstrap 5.3)      |
| **Backend**  | PHP 8.2                                   |
| **Database** | MySQL / MariaDB 10.4                      |
| **Fonts**    | Google Fonts (Poppins, Inter, Montserrat) |
| **Payment**  | UPI (QR Code + manual transaction ID)     |
| **Server**   | Apache via XAMPP / WAMP / LAMP            |

---

## 🗄️ Database Schema

The system uses a single database named `eventmanagement` with the following tables:

```
eventmanagement
├── admin           — Admin login credentials
├── signup          — General user accounts (full name, email, mobile)
├── login           — Login audit log (tracks login timestamps)
├── clients         — Client/organizer profiles with KYC documents
├── clientlogin     — Client login credentials with approval status
├── events          — Event listings (name, venue, date, price, status)
└── event_bookings  — Booking records linked to events
```

### Key Relationships
- `event_bookings.event_id` → `events.id` *(ON DELETE CASCADE)*
- `events.event_status` → `enum('pending', 'approved', 'rejected')`
- `clientlogin.status` → `enum('pending', 'approved', 'rejected')`

> No schema changes are required for the new features — all additions use the existing tables.

---

## 📁 Project Structure

```
EventManagementSystem/
│
├── index.html              # Landing page — User Login & Signup forms
├── main.php                # EventHub homepage — event listing with search & category filter
├── my_bookings.php         # User booking history with stats and ticket links  ← NEW
│
├── signup.php              # Handle user registration (POST)
├── login.php               # Handle user login (POST)
├── logout.php              # Destroy user session
│
├── admin.php               # Admin login page
├── admin_dashboard.php     # Admin dashboard — stats cards + approve/reject events
├── admin_logout.php        # Admin session logout
├── CreateAdmin.php         # Utility to create an initial admin account
│
├── client_login.html       # Client login page
├── client_login.php        # Handle client login (POST)
├── client_register.html    # Client registration form (with KYC upload)
├── client_register.php     # Handle client registration (POST)
├── client_event_form.html  # Form for clients to submit a new event
├── submit_event.php        # Handle event submission (POST)
├── ClientUploads.php       # Handle file uploads for client KYC documents
│
├── booking_page.php        # Multi-step booking flow — includes countdown timer
├── process_booking.php     # Handle booking form submission (POST)
├── ticket.php              # Display digital ticket with print/save support
│
├── fetch.php               # Fetches approved events from DB for main.php
├── databaseConnection.php  # Database credentials & MySQLi connection
│
├── Terms.html              # Terms and Conditions page
├── style.css               # Global stylesheet
├── QR_Code.jpg             # UPI payment QR code image
└── eventmanagement.sql     # Full database dump (schema + sample data)
```

---

## 🚀 Getting Started

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (or WAMP / LAMP / any PHP+MySQL stack)
- PHP **8.0+**
- MySQL / MariaDB
- A modern web browser

---

### Installation Steps

**1. Clone or Download the Repository**
```bash
git clone https://github.com/your-username/EventManagementSystem.git
```
Or download and extract the ZIP into your server's web root folder.

**2. Copy Files to Web Root**

Place the project folder inside your server's web root:
- **XAMPP (Windows):** `C:/xampp/htdocs/EventHub/`
- **WAMP (Windows):** `C:/wamp64/www/EventHub/`
- **Linux/Mac:** `/var/www/html/EventHub/`

**3. Import the Database**

1. Start **Apache** and **MySQL** from the XAMPP Control Panel.
2. Open your browser and go to: `http://localhost/phpmyadmin`
3. Click **New** → Create a database named `eventmanagement`.
4. Select the `eventmanagement` database → click **Import**.
5. Choose `eventmanagement.sql` from the project folder → click **Go**.

**4. Configure Database Connection**

Open `databaseConnection.php` and update the credentials if needed:

```php
<?php
$host     = "localhost";
$user     = "root";
$password = "";          // Your MySQL password (blank for XAMPP default)
$dbname   = "eventmanagement";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

**5. Create an Uploads Directory**

Ensure an `uploads/` directory exists in the project root with write permissions:
```bash
mkdir uploads
chmod 777 uploads    # Linux/Mac only
```

**6. Launch the Application**

Open your browser and navigate to:
```
http://localhost/EventHub/main.php
```

---

### Default Credentials

| Role | Username | Notes |
|------|----------|-------|
| Admin | `aru226` | From the SQL dump |
| User | `mani226` | From the SQL dump |
| Client | `johnsmith` | From the SQL dump |

> ⚠️ **Delete or restrict `CreateAdmin.php` after use** — it creates admin accounts and should not be publicly accessible in production.

---

## 👥 User Roles & Workflows

### 🧑‍💼 Admin Flow
```
admin.php (login)
  → admin_dashboard.php  (stats cards + pending event list)
  → Approve / Reject events
  → admin_logout.php
```

### 🏢 Client (Organizer) Flow
```
client_register.html
  → (admin approves client account)
  → client_login.html
  → client_event_form.html
  → submit_event.php
  → (wait for admin approval)
```

### 🙋 User (Attendee) Flow
```
index.html  (signup / login)
  → main.php  (search events, filter by category)
  → booking_page.php  (step 1: event summary + countdown timer)
  → booking_page.php  (step 2: personal details)
  → booking_page.php  (step 3: tickets + UPI payment)
  → process_booking.php
  → ticket.php  (digital ticket — print or save as PDF)
  → my_bookings.php  (view all past bookings)
```

---

## 📸 Screenshots & Pages

| Page | URL | Description |
|------|-----|-------------|
| Home / Login | `index.html` | Animated login & signup entry point |
| Event Listing | `main.php` | EventHub homepage — search bar, category filters, event grid |
| My Bookings | `my_bookings.php` | User's personal booking history with stats ← NEW |
| Admin Dashboard | `admin_dashboard.php` | Stats cards + pending event moderation |
| Client Registration | `client_register.html` | Business onboarding with KYC upload |
| Event Submission | `client_event_form.html` | Submit a new event for approval |
| Booking | `booking_page.php?id={id}` | 3-step booking with live countdown timer |
| Digital Ticket | `ticket.php?booking_id={id}` | Neon ticket with print/save button |

---

## ⚙️ Configuration

### File Upload Paths
Uploaded files (event posters, KYC documents, payment screenshots) are stored in the `uploads/` directory. Adjust paths in `ClientUploads.php` and `submit_event.php` if deploying to a subdirectory.

### UPI Payment
The UPI QR code (`QR_Code.jpg`) and UPI ID (`9951489478@axl`) displayed in `booking_page.php` are configured directly in the HTML. Update these to your own UPI details before going live.

### Session Security
Sessions are used across all PHP files. Ensure `session_start()` is called before any output. For production, consider setting `session.cookie_secure` and `session.cookie_httponly` in `php.ini`.

---

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!

1. Fork the repository
2. Create a new branch: `git checkout -b feature/your-feature-name`
3. Commit your changes: `git commit -m "Add: your feature description"`
4. Push to the branch: `git push origin feature/your-feature-name`
5. Open a Pull Request

---

## 📄 License

This project is licensed under the **MIT License** — you are free to use, modify, and distribute it with attribution.

---

<div align="center">

Made with ❤️ by **Shaik Shafi** · Built with PHP, MySQL & Bootstrap

</div>
