<?php
$pageTitle = 'Manage Users';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$success = '';
$error   = '';
$editUser = null;

if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $me = currentUser();

    if ($deleteId === (int)$me['id']) {
        $error = 'You cannot delete your own account.';
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param('i', $deleteId);
        $stmt->execute();
        $success = 'User deleted successfully.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user_id'])) {
    $editId    = (int)$_POST['edit_user_id'];
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $country   = trim($_POST['country']   ?? '');
    $role      = in_array($_POST['role'] ?? '', ['admin', 'user']) ? $_POST['role'] : 'user';
    $newPass   = $_POST['new_password'] ?? '';

    if (empty($firstname) || empty($lastname)) {
        $error = 'First and last name are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $chk = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
        $chk->bind_param('si', $email, $editId);
        $chk->execute();
        $chk->store_result();

        if ($chk->num_rows > 0) {
            $error = 'That email address is already in use by another account.';
        } else {
            if (!empty($newPass)) {
                if (strlen($newPass) < 8) {
                    $error = 'New password must be at least 8 characters.';
                } else {
                    $hashed = password_hash($newPass, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare(
                        "UPDATE users SET firstname=?, lastname=?, email=?, country=?, role=?, password=? WHERE id=?"
                    );
                    $stmt->bind_param('ssssssi', $firstname, $lastname, $email, $country, $role, $hashed, $editId);
                }
            } else {
                $stmt = $conn->prepare(
                    "UPDATE users SET firstname=?, lastname=?, email=?, country=?, role=? WHERE id=?"
                );
                $stmt->bind_param('sssssi', $firstname, $lastname, $email, $country, $role, $editId);
            }

            if (empty($error)) {
                if ($stmt->execute()) {
                    $success = 'User updated successfully.';
                } else {
                    $error = 'Update failed. Please try again.';
                }
            }
        }
    }
}

if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt   = $conn->prepare("SELECT id, firstname, lastname, email, country, role FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $editId);
    $stmt->execute();
    $editUser = $stmt->get_result()->fetch_assoc();
}

$users = $conn->query(
    "SELECT id, firstname, lastname, email, country, role, created_at FROM users ORDER BY created_at DESC"
)->fetch_all(MYSQLI_ASSOC);

$countries = [
    'Afghanistan','Albania','Algeria','Andorra','Angola','Argentina','Armenia','Australia',
    'Austria','Azerbaijan','Bahrain','Bangladesh','Belarus','Belgium','Bosnia and Herzegovina',
    'Brazil','Bulgaria','Canada','Chile','China','Colombia','Croatia','Cuba','Cyprus',
    'Czech Republic','Denmark','Egypt','Estonia','Finland','France','Germany','Ghana',
    'Greece','Hungary','Iceland','India','Indonesia','Iran','Iraq','Ireland','Israel',
    'Italy','Japan','Jordan','Kazakhstan','Kenya','Latvia','Lebanon','Lithuania',
    'Luxembourg','Malaysia','Malta','Mexico','Moldova','Montenegro','Morocco',
    'Netherlands','New Zealand','Nigeria','North Macedonia','Norway','Pakistan',
    'Palestine','Peru','Philippines','Poland','Portugal','Qatar','Romania','Russia',
    'Saudi Arabia','Serbia','Singapore','Slovakia','Slovenia','South Africa',
    'South Korea','Spain','Sri Lanka','Sweden','Switzerland','Syria','Taiwan',
    'Thailand','Tunisia','Turkey','Ukraine','United Arab Emirates','United Kingdom',
    'United States','Uruguay','Venezuela','Vietnam','Zimbabwe'
];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-layout">

    <aside class="admin-sidebar">
        <div class="admin-sidebar-header"><h3>Admin Panel</h3></div>
        <nav class="admin-nav">
            <a href="/filmbase/admin/index.php">       <span class="nav-icon">&#128202;</span> Dashboard</a>
            <a href="/filmbase/admin/users.php" class="active"><span class="nav-icon">&#128101;</span> Users</a>
            <a href="/filmbase/admin/news-add.php">    <span class="nav-icon">&#128221;</span> Add News</a>
            <a href="/filmbase/admin/gallery-add.php"> <span class="nav-icon">&#128247;</span> Add to Gallery</a>
            <a href="/filmbase/index.php" style="margin-top:1rem; border-top:1px solid var(--border); padding-top:1rem;">
                <span class="nav-icon">&#8592;</span> Back to Site
            </a>
        </nav>
    </aside>

    <div class="admin-content">

        <div class="admin-header">
            <h1 class="admin-title">Manage Users</h1>
            <span style="color:var(--text-muted); font-size:0.9rem;"><?php echo count($users); ?> total users</span>
        </div>

        <?php if ($success): ?><div class="alert alert-success"><?php echo e($success); ?></div><?php endif; ?>
        <?php if ($error):   ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>

        <?php if ($editUser): ?>
        <div class="form-card form-card-wide" style="margin-bottom:2rem; max-width:100%;">
            <h2 style="font-size:1.1rem; margin-bottom:1.5rem;">
                Edit User: <span class="text-accent"><?php echo e($editUser['firstname'] . ' ' . $editUser['lastname']); ?></span>
            </h2>
            <form method="POST" action="/filmbase/admin/users.php">
                <input type="hidden" name="edit_user_id" value="<?php echo (int)$editUser['id']; ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="firstname">First Name</label>
                        <input type="text" id="firstname" name="firstname"
                               value="<?php echo e($editUser['firstname']); ?>" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="lastname">Last Name</label>
                        <input type="text" id="lastname" name="lastname"
                               value="<?php echo e($editUser['lastname']); ?>" required maxlength="100">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email"
                               value="<?php echo e($editUser['email']); ?>" required maxlength="255">
                    </div>
                    <div class="form-group">
                        <label for="country">Country</label>
                        <select id="country" name="country">
                            <option value="">— Select country —</option>
                            <?php foreach ($countries as $c): ?>
                                <option value="<?php echo e($c); ?>" <?php echo $editUser['country'] === $c ? 'selected' : ''; ?>>
                                    <?php echo e($c); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role">
                            <option value="user"  <?php echo $editUser['role'] === 'user'  ? 'selected' : ''; ?>>User</option>
                            <option value="admin" <?php echo $editUser['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password <span style="color:var(--text-muted); font-weight:400;">(leave blank to keep current)</span></label>
                        <input type="password" id="new_password" name="new_password"
                               placeholder="Min. 8 characters" minlength="8">
                    </div>
                </div>

                <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="/filmbase/admin/users.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <div class="admin-table-card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Country</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="7" style="text-align:center; color:var(--text-muted); padding:2rem;">No users found.</td></tr>
                    <?php else: ?>
                        <?php
                        $me = currentUser();
                        foreach ($users as $u):
                        ?>
                            <tr>
                                <td><?php echo (int)$u['id']; ?></td>
                                <td>
                                    <?php echo e($u['firstname'] . ' ' . $u['lastname']); ?>
                                    <?php if ((int)$u['id'] === (int)$me['id']): ?>
                                        <span style="font-size:0.75rem; color:var(--accent); margin-left:0.4rem;">(you)</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($u['email']); ?></td>
                                <td><?php echo e($u['country'] ?: '—'); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo e($u['role']); ?>">
                                        <?php echo e($u['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo e(date('d M Y', strtotime($u['created_at']))); ?></td>
                                <td style="display:flex; gap:0.4rem; flex-wrap:wrap;">
                                    <a href="/filmbase/admin/users.php?edit=<?php echo (int)$u['id']; ?>"
                                       class="btn btn-outline btn-sm">Edit</a>

                                    <?php if ((int)$u['id'] !== (int)$me['id']): ?>
                                        <a href="/filmbase/admin/users.php?delete=<?php echo (int)$u['id']; ?>"
                                           class="btn btn-danger btn-sm"
                                           data-confirm="Delete user <?php echo e($u['firstname'] . ' ' . $u['lastname']); ?>? This cannot be undone.">
                                            Delete
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
