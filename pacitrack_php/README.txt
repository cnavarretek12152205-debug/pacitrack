PaciTrack PHP + HTML + CSS + MySQL

INSTALLATION (XAMPP / Windows)
1. Extract the pacitrack_php folder into C:\\xampp\\htdocs\\
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Open http://localhost/phpmyadmin
4. Choose Import, select database.sql, then click Import/Go. The SQL creates the pacitrack database and sample data.
5. Open http://localhost/pacitrack_php/
6. Sign in using either account:
   Administrator: admin@pacificsurgical.ph
   Operations:    operations@pacificsurgical.ph
   Password for both: PaciTrack2025!

FILES
- index.php: PHP page, login/session, dashboard, search, equipment registration, audit log and role restrictions.
- style.css: styling adapted from the supplied CSS.
- database.sql: database tables and sample records.

IMPORTANT
- This is a working starter/prototype. The seeded credentials are for local testing only. Change them before any public deployment.
- Configure the database connection variables near the top of index.php if your MySQL settings differ.
- The UI labels mention AES-256 as a project objective, but this starter does not yet implement application-level AES-256 encryption for selected fields or TLS. Use HTTPS in deployment and implement key management/encryption separately before claiming those protections are active.
- The "Keep me signed in" checkbox is visual only; persistent login is not implemented.
