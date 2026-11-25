<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$email = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    $_SESSION['auth_error'] = 'Please enter both your email address and password.';
    header('Location: index.php');
    exit;
}

$sql = '
    SELECT
        u.user_id,
        u.department_id,
        u.id_number,
        u.first_name,
        u.last_name,
        u.email,
        u.password_hash,
        u.role,
        u.status,
        d.department_code,
        d.department_name
    FROM users u
    LEFT JOIN departments d ON u.department_id = d.department_id
    WHERE u.email = :email
    LIMIT 1
';

$stmt = $pdo->prepare($sql);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

if (!$user || !verifyPassword($password, $user['password_hash'])) {
    $_SESSION['auth_error'] = 'Invalid email address or password.';
    header('Location: index.php');
    exit;
}

if ($user['status'] !== 'Active') {
    $_SESSION['auth_error'] = 'Your account is currently inactive. Contact the administrator.';
    header('Location: index.php');
    exit;
}

if ($user['role'] !== 'Administrator' && $user['department_id'] === null) {
    $_SESSION['auth_error'] = 'Your account is not assigned to a department. Contact the administrator.';
    header('Location: index.php');
    exit;
}

session_regenerate_id(true);

$_SESSION['user_id']         = (int) $user['user_id'];
$_SESSION['id_number']       = $user['id_number'];
$_SESSION['full_name']       = $user['first_name'] . ' ' . $user['last_name'];
$_SESSION['email']           = $user['email'];
$_SESSION['role']            = $user['role'];
$_SESSION['department_id']   = $user['department_id'] !== null ? (int) $user['department_id'] : null;
$_SESSION['department_code'] = $user['department_code'] ?? 'ALL';
$_SESSION['department_name'] = $user['department_name'] ?? 'All Departments (CABEIHM & CICS)';

header('Location: ' . getDashboardPath($user['role']));
exit;
