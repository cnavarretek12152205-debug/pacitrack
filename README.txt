PaciTrack PHP + MySQL — restructured

INSTALLATION (XAMPP / Windows)
1. Extract into C:\xampp\htdocs\pacitrack\
2. Start Apache and MySQL.
3. Open http://localhost/phpmyadmin → Import → database.sql
4. Open http://localhost/pacitrack/
5. Sign in:
     Administrator: admin@pacificsurgical.ph
     Operations:    operations@pacificsurgical.ph
     Password:      PaciTrack2025!

PROJECT STRUCTURE
  index.php         Front controller — routes and enforces auth
  config.php        DB credentials and app settings
  bootstrap.php     Session, DB connection, loads functions
  functions.php     h(), icon(), csrf_token(), flash(), audit()...
  auth.php          is_logged_in(), is_admin(), login_user(), logout_user()
  queries.php       All read-only data access (get_all_equipment, get_stats...)
  actions/          POST/GET handlers — redirect on success, set $notice on error
  pages/            One view per route (login, overview, equipment, ...)
  partials/         Reusable UI fragments (head, sidebar, topbar, stats, table, footer)

HOW ROUTING WORKS
  All requests hit index.php. It:
    1. Runs the appropriate action (from actions/) if the request matches
    2. Redirects to pages/login.php if not signed in
    3. Resolves ?page= to a slug in pages/
    4. Loads shared data ($stats, $equipment, $q)
    5. Renders partials/head.php → sidebar.php → topbar.php →
       pages/<slug>.php → partials/footer.php

ADDING A NEW PAGE
  1. Add a slug to $navItems in index.php
  2. Create pages/<slug>.php — it has $pdo, $currentUser, $stats, $equipment, $q
  3. If it needs new data, add a function to queries.php

ADDING A NEW ACTION
  1. Add a branch in index.php under "Actions"
  2. Create actions/<name>.php — start with verify_csrf() if it's a POST,
     call redirect() on success, set $notice on error, and return
  3. Point your form's action= at index.php (with any page query param it needs)

IMPORTANT
- Demo credentials are for local testing only — change before deployment.
- CSRF tokens are enforced on every POST.
- "Keep me signed in" extends the session cookie to 30 days. If your PHP
  session.gc_maxlifetime is lower (default 1440s), set it in php.ini to
  2592000 to make that really work.
- For production: enforce HTTPS, move config.php outside the webroot or
  chmod it 0600, and add actions/.htaccess + pages/.htaccess + partials/.htaccess
  containing "Require all denied" to prevent direct URL access.