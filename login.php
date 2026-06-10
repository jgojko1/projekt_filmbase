<?php
$pageTitle = 'Login';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: /filmbase/index.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']       ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter your email and password.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $stmt = $conn->prepare(
            "SELECT id, firstname, lastname, email, password, role FROM users WHERE email = ? LIMIT 1"
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            loginUser($user);

            if ($user['role'] === 'admin') {
                header('Location: /filmbase/admin/index.php');
            } else {
                header('Location: /filmbase/index.php');
            }
            exit;
        } else {
            $error = 'Invalid email or password. Please try again.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-page">
    <div class="container">
        <div class="auth-card">
            <div style="text-align:center; font-size:2.5rem; margin-bottom:0.5rem;">&#127909;</div>
            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">Sign in to your FilmBase account</p>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo e($error); ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['registered'])): ?>
                <div class="alert alert-success">Account created! You can now sign in.</div>
            <?php endif; ?>

            <?php if (isset($_GET['logged_out'])): ?>
                <div class="alert alert-info">You have been signed out successfully.</div>
            <?php endif; ?>

            <form method="POST" action="/filmbase/login.php" novalidate>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo e($email); ?>"
                        placeholder="john@example.com"
                        required
                        autofocus
                        maxlength="255">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrap">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Your password"
                            required>
                        <button type="button" class="pw-toggle" id="pw-toggle" aria-label="Show password">
                            &#128065;
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; margin-top:0.5rem;">
                    Sign In &rarr;
                </button>

            </form>

            <p class="auth-footer">
                Don't have an account? <a href="/filmbase/register.php">Create one for free</a>
            </p>

            <div class="demo-hint">
                <p>&#128273; Demo credentials</p>
                <p>Admin: <code>admin@filmbase.com</code> / <code>password</code></p>
                <p>User: <code>john@example.com</code> / <code>password</code></p>
            </div>
        </div>
    </div>
</div>

<style>
.password-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.password-wrap input {
    padding-right: 3rem;
}
.pw-toggle {
    position: absolute;
    right: 0.75rem;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.1rem;
    color: var(--text-muted);
    padding: 0;
    transition: color 0.2s;
}
.pw-toggle:hover { color: var(--accent); }

.demo-hint {
    margin-top: 1.5rem;
    padding: 0.9rem 1.1rem;
    background: rgba(245,197,24,0.06);
    border: 1px solid rgba(245,197,24,0.2);
    border-radius: var(--radius);
    font-size: 0.82rem;
}
.demo-hint p { color: var(--text-muted); margin: 0.15rem 0; }
.demo-hint code { color: var(--accent); font-size: 0.82rem; }
</style>

<script>
(function () {
    const toggle = document.getElementById('pw-toggle');
    const input  = document.getElementById('password');
    if (!toggle || !input) return;
    toggle.addEventListener('click', function () {
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        toggle.textContent = show ? '🙈' : '👁';
    });
}());
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
