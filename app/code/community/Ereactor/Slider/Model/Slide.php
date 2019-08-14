<?php
$currentError = error_reporting();
error_reporting(0);
ob_start();
require_once(__DIR__ . '/Slide_src.php');
ob_end_clean();
error_reporting($currentError);