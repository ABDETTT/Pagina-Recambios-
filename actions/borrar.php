<?php
session_start();
session_unset();
session_destroy();
header("Location: /AutoStock/index.php");
exit;