<?php
require_once __DIR__ . '/../includes/security.php';
initSecureSession();
session_unset();
session_destroy();
header("Location: ../index.php");
exit;