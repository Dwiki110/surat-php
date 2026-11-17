<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

// Router tipis: arahkan ke dashboard sesuai peran masing-masing
if (current_role() === 'sekretaris') {
    header('Location: sekretaris/dashboard.php');
} else {
    header('Location: resepsionis/dashboard.php');
}
exit;
