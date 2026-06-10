<?php
$pageTitle = 'Register';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: /filmbase/index.php');
    exit;
}

$error  = '';
$success = '';
$fields = ['firstname' => '', 'lastname' => '', 'email' => '', 'country' => ''];

$countries = [
    'Afghanistan','Albania','Algeria','Andorra','Angola','Argentina','Armenia','Australia',
    'Austria','Azerbaijan','Bahrain','Bangladesh','Belarus','Belgium','Belize','Bolivia',
    'Bosnia and Herzegovina','Brazil','Bulgaria','Cambodia','Canada','Chile','China',
    'Colombia','Croatia','Cuba','Cyprus','Czech Republic','Denmark','Ecuador','Egypt',
    'Estonia','Ethiopia','Finland','France','Georgia','Germany','Ghana','Greece',
    'Guatemala','Honduras','Hungary','Iceland','India','Indonesia','Iran','Iraq',
    'Ireland','Israel','Italy','Jamaica','Japan','Jordan','Kazakhstan','Kenya',
    'Kosovo','Kuwait','Latvia','Lebanon','Libya','Liechtenstein','Lithuania',
    'Luxembourg','Malaysia','Malta','Mexico','Moldova','Monaco','Montenegro',
    'Morocco','Netherlands','New Zealand','Nigeria','North Macedonia','Norway',
    'Pakistan','Palestine','Panama','Paraguay','Peru','Philippines','Poland',
    'Portugal','Qatar','Romania','Russia','Saudi Arabia','Serbia','Singapore',
    'Slovakia','Slovenia','South Africa','South Korea','Spain','Sri Lanka','Sweden',
    'Switzerland','Syria','Taiwan','Thailand','Tunisia','Turkey','Ukraine',
    'United Arab Emirates','United Kingdom','United States','Uruguay','Venezuela',
    'Vietnam','Yemen','Zimbabwe'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields['firstname'] = trim($_POST['firstname'] ?? '');
    $fields['lastname']  = trim($_POST['lastname']  ?? '');
    $fields['email']     = trim($_POST['email']     ?? '');
    $fields['country']   = trim($_POST['country']   ?? '');
    $password            = $_POST['password']        ?? '';
    $passwordConfirm     = $_POST['password_confirm'] ?? '';

    if (empty($fields['firstname']) || empty($fields['lastname'])) {
        $error = 'First name and last name are required.';
    } elseif (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (empty($fields['country'])) {
        $error = 'Please select your country.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Passwords do not match.';
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $check->bind_param('s', $fields['email']);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = 'An account with this email address already exists.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO users (firstname, lastname, email, password, country, role)
                 VALUES (?, ?, ?, ?, ?, 'user')"
            );
            $stmt->bind_param(
                'sssss',
                $fields['firstname'], $fields['lastname'],
                $fields['email'], $hashed, $fields['country']
            );

            if ($stmt->execute()) {
                $success = true;
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-page">
    <div class="container">

        <?php if ($success): ?>
            <div class="auth-card" style="max-width:480px; text-align:center;">
                <div style="font-size:3rem; margin-bottom:1rem;">&#10003;</div>
                <h1 class="auth-title">Account Created!</h1>
                <p style="color:var(--text-muted); margin-bottom:2rem;">
                    Welcome to FilmBase, <?php echo e($fields['firstname']); ?>!
                    Your account has been created successfully.
                </p>
                <a href="/filmbase/login.php" class="btn btn-primary" style="width:100%;">
                    Sign In to Your Account &rarr;
                </a>
            </div>
        <?php else: ?>
            <div class="auth-card" style="max-width:520px;">
                <h1 class="auth-title">Create Account</h1>
                <p class="auth-subtitle">Join FilmBase and be part of the cinema community</p>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo e($error); ?></div>
                <?php endif; ?>

                <form method="POST" action="/filmbase/register.php" novalidate>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstname">First Name <span style="color:var(--accent)">*</span></label>
                            <input type="text" id="firstname" name="firstname"
                                   value="<?php echo e($fields['firstname']); ?>"
                                   placeholder="John" required maxlength="100" autofocus>
                        </div>
                        <div class="form-group">
                            <label for="lastname">Last Name <span style="color:var(--accent)">*</span></label>
                            <input type="text" id="lastname" name="lastname"
                                   value="<?php echo e($fields['lastname']); ?>"
                                   placeholder="Smith" required maxlength="100">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address <span style="color:var(--accent)">*</span></label>
                        <input type="email" id="email" name="email"
                               value="<?php echo e($fields['email']); ?>"
                               placeholder="john@example.com" required maxlength="255">
                    </div>

                    <div class="form-group">
                        <label for="country">Country <span style="color:var(--accent)">*</span></label>
                        <select id="country" name="country" required>
                            <option value="">— Select your country —</option>
                            <?php foreach ($countries as $c): ?>
                                <option value="<?php echo e($c); ?>"
                                    <?php echo $fields['country'] === $c ? 'selected' : ''; ?>>
                                    <?php echo e($c); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="password">Password <span style="color:var(--accent)">*</span></label>
                        <input type="password" id="password" name="password"
                               placeholder="At least 8 characters" required minlength="8">
                    </div>

                    <div class="form-group">
                        <label for="password_confirm">Confirm Password <span style="color:var(--accent)">*</span></label>
                        <input type="password" id="password_confirm" name="password_confirm"
                               placeholder="Repeat your password" required minlength="8">
                    </div>

                    <div id="pw-match-msg" style="font-size:0.82rem; margin-bottom:1rem; min-height:1.2em;"></div>

                    <button type="submit" class="btn btn-primary" style="width:100%;">
                        Create Account
                    </button>
                </form>

                <p class="auth-footer">
                    Already have an account? <a href="/filmbase/login.php">Sign in</a>
                </p>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
(function () {
    const pw1 = document.getElementById('password');
    const pw2 = document.getElementById('password_confirm');
    const msg = document.getElementById('pw-match-msg');
    if (!pw1 || !pw2 || !msg) return;

    function check() {
        if (!pw2.value) { msg.textContent = ''; return; }
        if (pw1.value === pw2.value) {
            msg.textContent = '✓ Passwords match';
            msg.style.color = 'var(--success)';
        } else {
            msg.textContent = '✗ Passwords do not match';
            msg.style.color = 'var(--danger)';
        }
    }
    pw1.addEventListener('input', check);
    pw2.addEventListener('input', check);
}());
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
