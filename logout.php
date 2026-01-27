<?php
require_once __DIR__ . '/config/security.php';

startSecureSession();
secureLogout();

header('Location: login.php');
exit;
