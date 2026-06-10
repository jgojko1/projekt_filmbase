<?php
require_once __DIR__ . '/includes/auth.php';

logoutUser();

header('Location: /filmbase/login.php?logged_out=1');
exit;
