<?php
if (!isset($active)) { $active = ''; }
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-building"></i> Reception
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo $active === 'dashboard' ? 'active' : ''; ?>"
               href="<?php echo $base_path; ?>dashboard.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $active === 'visitors' ? 'active' : ''; ?>"
               href="<?php echo $base_path; ?>visitors/list.php">
                <i class="bi bi-people"></i> Visitors List
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $active === 'add' ? 'active' : ''; ?>"
               href="<?php echo $base_path; ?>visitors/add.php">
                <i class="bi bi-person-plus"></i> Add Visitor
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $active === 'reports' ? 'active' : ''; ?>"
               href="<?php echo $base_path; ?>reports.php">
                <i class="bi bi-file-earmark-bar-graph"></i> Reports
            </a>
        </li>
        <li class="nav-item mt-3">
            <a class="nav-link <?php echo $active === 'profile' ? 'active' : ''; ?>"
               href="<?php echo $base_path; ?>profile.php">
                <i class="bi bi-gear"></i> Profile
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-danger" href="<?php echo $base_path; ?>logout.php">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
    </ul>
</aside>
