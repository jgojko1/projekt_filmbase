<?php
$pageTitle = 'Contact';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$success = '';
$error   = '';
$fields  = ['firstname' => '', 'lastname' => '', 'email' => '', 'country' => '',
            'newsletter' => 0, 'subject' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields['firstname']  = trim($_POST['firstname']  ?? '');
    $fields['lastname']   = trim($_POST['lastname']   ?? '');
    $fields['email']      = trim($_POST['email']      ?? '');
    $fields['country']    = trim($_POST['country']    ?? '');
    $fields['newsletter'] = isset($_POST['newsletter']) ? 1 : 0;
    $fields['subject']    = trim($_POST['subject']    ?? '');
    $fields['message']    = trim($_POST['message']    ?? '');

    if (empty($fields['firstname']) || empty($fields['lastname'])) {
        $error = 'First name and last name are required.';
    } elseif (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (empty($fields['country'])) {
        $error = 'Please select your country.';
    } elseif (empty($fields['subject'])) {
        $error = 'Subject is required.';
    } elseif (strlen($fields['message']) < 10) {
        $error = 'Message must be at least 10 characters.';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO contacts (firstname, lastname, email, country, newsletter, subject, message)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'ssssiss',
            $fields['firstname'], $fields['lastname'], $fields['email'],
            $fields['country'], $fields['newsletter'], $fields['subject'], $fields['message']
        );

        if ($stmt->execute()) {
            $success = 'Thank you for your message! We will get back to you within 2 business days.';
            $fields  = array_map(fn($v) => is_int($v) ? 0 : '', $fields);
        } else {
            $error = 'Something went wrong. Please try again later.';
        }
    }
}

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

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
    <div class="container">
        <p class="breadcrumb"><a href="/filmbase/index.php">Home</a> &rsaquo; Contact</p>
        <h1>Contact Us</h1>
        <p>We would love to hear from you — reach out with questions, tips, or feedback</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="contact-grid">

            <div class="contact-info">
                <div class="map-wrap">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2781.5388157916!2d15.9661974!3d45.8150108!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4765d72015f8d06d%3A0xa44ef9b786f8d6f!2sZagreb%2C%20Croatia!5e0!3m2!1sen!2shr!4v1700000000000"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="FilmBase office location — Zagreb, Croatia">
                    </iframe>
                </div>

                <div class="contact-details">
                    <div class="contact-detail-item">
                        <span class="detail-icon">&#128205;</span>
                        <div>
                            <strong>Address</strong>
                            <p>Ilica 1, 10000 Zagreb, Croatia</p>
                        </div>
                    </div>
                    <div class="contact-detail-item">
                        <span class="detail-icon">&#128231;</span>
                        <div>
                            <strong>Email</strong>
                            <p><a href="mailto:info@filmbase.com">info@filmbase.com</a></p>
                        </div>
                    </div>
                    <div class="contact-detail-item">
                        <span class="detail-icon">&#128222;</span>
                        <div>
                            <strong>Phone</strong>
                            <p>+385 1 234 5678</p>
                        </div>
                    </div>
                    <div class="contact-detail-item">
                        <span class="detail-icon">&#128337;</span>
                        <div>
                            <strong>Office Hours</strong>
                            <p>Monday &ndash; Friday, 09:00 &ndash; 17:00 CET</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo e($success); ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo e($error); ?></div>
                <?php endif; ?>

                <div class="form-card form-card-wide" style="max-width:100%;">
                    <h2 style="font-size:1.3rem; margin-bottom:1.5rem;">Send a Message</h2>

                    <form method="POST" action="/filmbase/contact.php" novalidate>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="firstname">First Name <span style="color:var(--accent)">*</span></label>
                                <input type="text" id="firstname" name="firstname"
                                       value="<?php echo e($fields['firstname']); ?>"
                                       placeholder="John" required maxlength="100">
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
                            <label for="subject">Subject <span style="color:var(--accent)">*</span></label>
                            <input type="text" id="subject" name="subject"
                                   value="<?php echo e($fields['subject']); ?>"
                                   placeholder="What is your message about?" required maxlength="255">
                        </div>

                        <div class="form-group">
                            <label for="message">Message <span style="color:var(--accent)">*</span></label>
                            <textarea id="message" name="message" rows="6"
                                      placeholder="Write your message here..." required><?php echo e($fields['message']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="newsletter" name="newsletter" value="1"
                                       <?php echo $fields['newsletter'] ? 'checked' : ''; ?>>
                                <label for="newsletter">Subscribe to the FilmBase newsletter for weekly film news</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width:100%;">
                            Send Message &rarr;
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
.contact-details {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 1.5rem;
}
.contact-detail-item {
    display: flex;
    align-items: flex-start;
    gap: 0.9rem;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 0.9rem 1.1rem;
}
.detail-icon { font-size: 1.3rem; flex-shrink: 0; margin-top: 0.1rem; }
.contact-detail-item strong { display: block; font-size: 0.85rem; color: var(--accent); margin-bottom: 0.2rem; }
.contact-detail-item p { color: var(--text-muted); font-size: 0.9rem; margin: 0; }
.contact-detail-item a { color: var(--text-muted); }
.contact-detail-item a:hover { color: var(--accent); }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
