<?php

require_once '../helpers/flash.php';

session_start();
session_destroy();

// ✅ Flash message and redirect
set_flash("You have been logged out successfully.", "info");

header('Location: ../index.php');
exit;
