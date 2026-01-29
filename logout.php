<?php
require_once __DIR__ . '/includes/helpers/session.php';
secureSessionStart();
destroySession();
header('Location: login.php');
exit;
header('Location: login.php');
exit;
