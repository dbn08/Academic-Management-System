<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . getDashboardPath($_SESSION['role']));
    exit;
}

$errorMessage = $_SESSION['auth_error'] ?? '';
unset($_SESSION['auth_error']);

$loggedOut = isset($_GET['logged_out']) && $_GET['logged_out'] === '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In | Academic Management &amp; Accounting Portal</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-body">
  <main class="auth-wrapper">
    <section class="auth-card" aria-labelledby="login-heading">
      <div class="auth-header">
        <div class="brand-badges">
          <span class="badge badge-cabeihm">CABEIHM</span>
          <span class="badge badge-cics">CICS</span>
        </div>
        <h1 id="login-heading">Academic &amp; Accounting Portal</h1>
        <p class="auth-subtitle">Sign in with your institutional account to continue.</p>
      </div>

      <?php if ($errorMessage !== ''): ?>
        <div class="alert alert-error" role="alert">
          <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php endif; ?>

      <?php if ($loggedOut): ?>
        <div class="alert alert-success" role="status">
          You have been signed out successfully.
        </div>
      <?php endif; ?>

      <form id="loginForm" action="login_process.php" method="POST" novalidate>
        <div class="form-group">
          <label for="email">Institutional Email</label>
          <input
            type="email"
            id="email"
            name="email"
            placeholder="name@school.edu"
            autocomplete="email"
            required
          >
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="password-field">
            <input
              type="password"
              id="password"
              name="password"
              placeholder="Enter your password"
              autocomplete="current-password"
              required
            >
            <button
              type="button"
              id="togglePassword"
              class="toggle-password"
              aria-label="Show password"
            >
              Show
            </button>
          </div>
        </div>

        <p id="clientError" class="client-error" aria-live="polite"></p>

        <button type="submit" class="btn-primary">Sign In</button>
      </form>

      <footer class="auth-footer">
        <p>Unified Prototype System &middot; CABEIHM &amp; CICS</p>
      </footer>
    </section>
  </main>

  <script src="assets/js/login.js"></script>
</body>
</html>
