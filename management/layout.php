<?php
function get_staff_initials(string $fullName): string
{
    $parts = preg_split('/\s+/', trim($fullName));
    $initials = '';
    foreach ($parts as $part) {
        if ($part !== '') {
            $initials .= strtoupper(substr($part, 0, 1));
        }
        if (strlen($initials) === 2) {
            break;
        }
    }
    return $initials !== '' ? $initials : 'ST';
}

function render_management_header(string $pageTitle, string $pageSubtitle, string $activeSection, array $staff, string $roleLabel): void
{
    $navItems = [
        'dashboard' => [
            'href' => 'dashboard.php',
            'label' => 'Overview',
            'icon' => '<svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>'
        ],
        'users' => [
            'href' => 'users.php',
            'label' => 'User Management',
            'icon' => '<svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>'
        ],
        'services' => [
            'href' => 'services.php',
            'label' => 'AI Services',
            'icon' => '<svg viewBox="0 0 24 24"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>'
        ],
        'skills' => [
            'href' => 'skills.php',
            'label' => 'AI Skills',
            'icon' => '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.01a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.01a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.01a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>'
        ],
        'projects' => [
            'href' => 'projects.php',
            'label' => 'Project Oversight',
            'icon' => '<svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>'
        ]
    ];
    $initials = get_staff_initials((string)$staff['full_name']);
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | FreelancerAI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/management.css">
</head>
<body>

<div class="mgmt-shell">
    <aside class="mgmt-sidebar" id="mgmtSidebar" aria-label="Management sidebar">
        <div class="mgmt-brand">
            <a href="dashboard.php">Freelancer<span>AI</span></a>
            <small>Management Console</small>
        </div>

        <nav class="mgmt-nav-group">
            <span class="mgmt-nav-label">Operations</span>
            <?php foreach ($navItems as $key => $item): ?>
                <a href="<?= e($item['href']) ?>" class="mgmt-nav-link <?= $activeSection === $key ? 'active' : '' ?>">
                    <span class="mgmt-nav-icon"><?= $item['icon'] ?></span>
                    <span><?= e($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="mgmt-sidebar-footer">
            <a href="../index.php" class="mgmt-nav-link">
                <span class="mgmt-nav-icon">
                    <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                </span>
                <span>Public Site</span>
            </a>
            <a href="logout.php" class="mgmt-nav-link">
                <span class="mgmt-nav-icon">
                    <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                </span>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <div class="mgmt-overlay" id="mgmtOverlay"></div>

    <div class="mgmt-workspace">
        <header class="mgmt-topbar">
            <div class="mgmt-topbar-left">
                <button type="button" class="mgmt-menu-toggle" id="mgmtMenuToggle" aria-label="Toggle navigation">&#9776;</button>
                <div>
                    <h1 class="mgmt-page-title"><?= e($pageTitle) ?></h1>
                    <p class="mgmt-page-subtitle"><?= e($pageSubtitle) ?></p>
                </div>
            </div>

            <div class="mgmt-topbar-right">
                <div class="mgmt-staff-chip">
                    <span class="mgmt-avatar"><?= e($initials) ?></span>
                    <div class="mgmt-staff-text">
                        <span class="mgmt-staff-name"><?= e($staff['full_name']) ?></span>
                        <span class="mgmt-staff-role"><?= e($roleLabel) ?></span>
                    </div>
                </div>
            </div>
        </header>

        <main class="mgmt-content">
    <?php
}

function render_management_footer(): void
{
    ?>
        </main>

        <footer class="mgmt-footer">
            <span>&copy; <?= date('Y') ?> FreelancerAI · Management Dashboard</span>
            <span>Your Skills. Your Intelligence. Your Opportunities.</span>
        </footer>
    </div>
</div>

<script>
(function () {
    var toggleBtn = document.getElementById('mgmtMenuToggle');
    var sidebar = document.getElementById('mgmtSidebar');
    var overlay = document.getElementById('mgmtOverlay');

    if (!toggleBtn || !sidebar || !overlay) {
        return;
    }

    function closeMenu() {
        sidebar.classList.remove('open');
        overlay.classList.remove('visible');
    }

    toggleBtn.addEventListener('click', function () {
        var isOpen = sidebar.classList.toggle('open');
        overlay.classList.toggle('visible', isOpen);
    });

    overlay.addEventListener('click', closeMenu);
})();
</script>

</body>
</html>
    <?php
}
