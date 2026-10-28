# PaciTrack - Pacific Surgical Inc.

**A Secure Surgical Equipment Tracking and Lifecycle Management System with AES-256 Cryptographic Data Protection.**

PaciTrack is a centralized, web-based platform designed for Pacific Surgical Inc. to manage surgical equipment inventory, warranties, maintenance, and service records with role-based access control (RBAC) and cryptographic data protection.

## 🚀 Key Features
- **Centralized Lifecycle Management:** Track equipment from acquisition to retirement.
- **Enterprise-Grade Security:** AES-256 encryption for sensitive data at rest, TLS/HTTPS for data in transit.
- **Role-Based Access Control (RBAC):** Tailored views and permissions for Administrators, Operations, and Technicians.
- **Immutable Audit Trails:** Logs all authentication events, data modifications, and administrative actions.
- **Premium UI/UX:** Corporate branding tailored for Pacific Surgical Inc. with responsive design.

## 🛠️ Technology Stack
- **Backend:** PHP (Custom MVC Architecture)
- **Database:** MySQL
- **Security:** Argon2id (Password Hashing), AES-256 (Data Encryption), CSRF Protection
- **Frontend:** HTML5, CSS3 (Custom Enterprise Design), JavaScript

## 💻 Local Installation Guide (XAMPP)
1. Clone this repository into your XAMPP `htdocs` folder (e.g., `C:\xampp\htdocs\pacitrack`).
2. Start **Apache** and **MySQL** via XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin/` and import the `database.sql` file.
4. Ensure the database credentials in `config.php` match your local setup (Default: `root` with empty password).
5. Open `http://localhost/pacitrack/` in your browser.

## 🔐 Default Demo Credentials
- **Administrator:** `admin@pacificsurgical.ph` | Password: `PaciTrack2025!`
- **Operations:** `operations@pacificsurgical.ph` | Password: `PaciTrack2025!`
*(Note: Change these credentials immediately in a production environment.)*

## 📂 Project Structure
- `index.php` - Front controller and routing.
- `config.php` - Database credentials and app settings.
- `actions/` - Handlers for POST/GET requests (login, save, delete).
- `pages/` - Main view files (Dashboard, Equipment, Audit, etc.).
- `partials/` - Reusable UI components (Sidebar, Topbar, Tables, Modals).
- `assets/` - CSS, JavaScript, and Image assets.

## 📋 Development Roadmap (Capstone Group)
**UI/UX Team (Pending):**
- [ ] Apply premium table design to `clients.php` and `warranties.php`.
- [ ] Revamp `audit.php` into a structured System Log Viewer.
- [ ] Polish the User Management interface (`users.php`).
- [ ] Mobile responsiveness testing.

**Backend Team (Pending):**
- [ ] Implement AES-256 field-level encryption on `queries.php` and `actions/`.
- [ ] Enforce strict RBAC middleware in `auth.php`.
- [ ] Verify comprehensive audit logging across all CRUD operations.
- [ ] Setup automated MySQL database backup script.
