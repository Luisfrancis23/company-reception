<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . " - Company Reception" : "Company Reception"; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="<?php echo $base_path; ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg top-navbar">
    <div class="container-fluid">
        <button class="btn btn-sm btn-outline-light d-lg-none" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <span class="navbar-brand text-white ms-2">
            <i class="bi bi-building"></i> Company Reception System
        </span>
        <div class="ms-auto d-flex align-items-center">
            <span class="text-white me-3 d-none d-sm-inline">
                <i class="bi bi-person-circle"></i>
                <?php echo isset($_SESSION['fullname']) ? htmlspecialchars($_SESSION['fullname']) : ''; ?>
            </span>
            <a href="<?php echo $base_path; ?>profile.php" class="btn btn-sm btn-outline-light me-2" title="Profile">
                <i class="bi bi-gear"></i>
            </a>
            <a href="<?php echo $base_path; ?>logout.php" class="btn btn-sm btn-danger">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="app-wrapper">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main class="main-content">
