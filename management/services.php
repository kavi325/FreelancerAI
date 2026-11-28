<?php
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/layout.php';

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrfToken)) {
        $errorMessage = 'Invalid security token. Please refresh the page and try again.';
    } elseif ($action === 'create_service') {
        $serviceName = trim((string)($_POST['service_name'] ?? ''));
        $serviceDescription = trim((string)($_POST['service_description'] ?? ''));

        if ($serviceName === '') {
            $errorMessage = 'Service name is required.';
        } elseif (mb_strlen($serviceName) > 150) {
            $errorMessage = 'Service name must not exceed 150 characters.';
        } elseif (mb_strlen($serviceDescription) > 2000) {
            $errorMessage = 'Service description must not exceed 2000 characters.';
        } else {
            try {
                $dupStmt = $pdo->prepare("
                    SELECT service_id
                    FROM AI_SERVICE
                    WHERE LOWER(service_name) = LOWER(?)
                    LIMIT 1
                ");
                $dupStmt->execute([$serviceName]);

                if ($dupStmt->fetch()) {
                    $errorMessage = 'An AI service with that name already exists.';
                } else {
                    $insertStmt = $pdo->prepare("
                        INSERT INTO AI_SERVICE (service_name, service_description)
                        VALUES (?, ?)
                    ");
                    $insertStmt->execute([
                        $serviceName,
                        $serviceDescription !== '' ? $serviceDescription : null
                    ]);
                    $successMessage = 'AI service "' . $serviceName . '" has been added.';
                }
            } catch (PDOException $ex) {
                $errorMessage = 'Could not add the AI service. Please try again.';
            }
        }
    } elseif ($action === 'update_service') {
        $serviceId = filter_input(INPUT_POST, 'service_id', FILTER_VALIDATE_INT);
        $serviceName = trim((string)($_POST['service_name'] ?? ''));
        $serviceDescription = trim((string)($_POST['service_description'] ?? ''));

        if (!$serviceId || $serviceId <= 0) {
            $errorMessage = 'Invalid AI service selected.';
        } elseif ($serviceName === '') {
            $errorMessage = 'Service name is required.';
        } elseif (mb_strlen($serviceName) > 150) {
            $errorMessage = 'Service name must not exceed 150 characters.';
        } elseif (mb_strlen($serviceDescription) > 2000) {
            $errorMessage = 'Service description must not exceed 2000 characters.';
        } else {
            try {
                $dupStmt = $pdo->prepare("
                    SELECT service_id
                    FROM AI_SERVICE
                    WHERE LOWER(service_name) = LOWER(?) AND service_id <> ?
                    LIMIT 1
                ");
                $dupStmt->execute([$serviceName, $serviceId]);

                if ($dupStmt->fetch()) {
                    $errorMessage = 'Another AI service already uses that name.';
                } else {
                    $updateStmt = $pdo->prepare("
                        UPDATE AI_SERVICE
                        SET service_name = ?, service_description = ?
                        WHERE service_id = ?
                    ");
                    $updateStmt->execute([
                        $serviceName,
                        $serviceDescription !== '' ? $serviceDescription : null,
                        $serviceId
                    ]);
                    $successMessage = 'AI service "' . $serviceName . '" has been updated.';
                }
            } catch (PDOException $ex) {
                $errorMessage = 'Could not update the AI service. Please try again.';
            }
        }
    } elseif ($action === 'delete_service') {
        if (!$canDeleteCatalogItems) {
            http_response_code(403);
            $errorMessage = 'Deleting AI services requires Administration privileges.';
        } else {
            $serviceId = filter_input(INPUT_POST, 'service_id', FILTER_VALIDATE_INT);

            if (!$serviceId || $serviceId <= 0) {
                $errorMessage = 'Invalid AI service selected.';
            } else {
                try {
                    $deleteStmt = $pdo->prepare("
                        DELETE FROM AI_SERVICE
                        WHERE service_id = ?
                    ");
                    $deleteStmt->execute([$serviceId]);

                    if ($deleteStmt->rowCount() > 0) {
                        $successMessage = 'AI service has been removed.';
                    } else {
                        $errorMessage = 'The selected AI service was not found.';
                    }
                } catch (PDOException $ex) {
                    $errorMessage = 'Could not remove the AI service because it is currently in use.';
                }
            }
        }
    } else {
        $errorMessage = 'Unsupported action requested.';
    }
}

$searchTerm = trim((string)($_GET['search'] ?? ''));
$editServiceId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
$editingService = null;

if ($editServiceId && $editServiceId > 0) {
    try {
        $editStmt = $pdo->prepare("
            SELECT service_id, service_name, service_description
            FROM AI_SERVICE
            WHERE service_id = ?
            LIMIT 1
        ");
        $editStmt->execute([$editServiceId]);
        $editingService = $editStmt->fetch() ?: null;
    } catch (PDOException $ex) {
        $errorMessage = 'Unable to load the selected AI service for editing.';
    }
}

$services = [];
try {
    if ($searchTerm !== '') {
        $likeValue = '%' . $searchTerm . '%';
        $listStmt = $pdo->prepare("
            SELECT service_id, service_name, service_description
            FROM AI_SERVICE
            WHERE service_name LIKE ? OR service_description LIKE ?
            ORDER BY service_name ASC
        ");
        $listStmt->execute([$likeValue, $likeValue]);
        $services = $listStmt->fetchAll();
    } else {
        $services = $pdo->query("
            SELECT service_id, service_name, service_description
            FROM AI_SERVICE
            ORDER BY service_name ASC
        ")->fetchAll();
    }
} catch (PDOException $ex) {
    $errorMessage = 'Unable to load AI services right now.';
}

$csrfToken = get_csrf_token();

render_management_header(
    'AI Services',
    'Maintain official AI service categories across FreelancerAI',
    'services',
    $currentStaff,
    $staffRoleLabel
);
?>

<section class="mgmt-banner">
    <div>
        <span class="eyebrow">CATALOG MANAGEMENT</span>
        <h2>AI <span>Services</span></h2>
        <p>Add, search, and update AI service definitions stored in the platform catalog. Duplicate service names are automatically rejected.</p>
    </div>
    <span class="mgmt-pill"><?= count($services) ?> Services Listed</span>
</section>

<?php if ($successMessage !== ''): ?>
    <div class="alert-banner alert-success"><?= e($successMessage) ?></div>
<?php endif; ?>

<?php if ($errorMessage !== ''): ?>
    <div class="alert-banner alert-error"><?= e($errorMessage) ?></div>
<?php endif; ?>

<section class="mgmt-grid-main">
    <div class="mgmt-panel">
        <div class="panel-header">
            <h3>Registered AI Services</h3>
        </div>

        <form method="GET" action="services.php" class="filter-bar">
            <div class="filter-field">
                <label for="search">Search Services</label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    placeholder="Search by service name or description..."
                    value="<?= e($searchTerm) ?>"
                >
            </div>
            <div class="action-group">
                <button type="submit" class="btn-action">Search</button>
                <a href="services.php" class="btn-outline">Reset</a>
            </div>
        </form>

        <?php if ($services): ?>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Service Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                            <tr>
                                <td>#<?= (int)$service['service_id'] ?></td>
                                <td><strong><?= e($service['service_name']) ?></strong></td>
                                <td><?= e($service['service_description'] ?: 'No description provided') ?></td>
                                <td>
                                    <div class="action-group">
                                        <a href="services.php?edit=<?= (int)$service['service_id'] ?>" class="btn-outline btn-small">Edit</a>
                                        <?php if ($canDeleteCatalogItems): ?>
                                            <form method="POST" action="services.php" class="inline-form">
                                                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                                <input type="hidden" name="action" value="delete_service">
                                                <input type="hidden" name="service_id" value="<?= (int)$service['service_id'] ?>">
                                                <button type="submit" class="btn-danger-outline">Delete</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="empty-message">No AI services found.</p>
        <?php endif; ?>
    </div>

    <div class="mgmt-panel">
        <div class="panel-header">
            <h3><?= $editingService ? 'Edit AI Service' : 'Add New AI Service' ?></h3>
            <?php if ($editingService): ?>
                <a href="services.php" class="panel-link">Cancel Edit</a>
            <?php endif; ?>
        </div>

        <form method="POST" action="services.php" class="mgmt-form">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <input type="hidden" name="action" value="<?= $editingService ? 'update_service' : 'create_service' ?>">
            <?php if ($editingService): ?>
                <input type="hidden" name="service_id" value="<?= (int)$editingService['service_id'] ?>">
            <?php endif; ?>

            <div class="form-row">
                <label for="service_name">Service Name</label>
                <input
                    type="text"
                    id="service_name"
                    name="service_name"
                    maxlength="150"
                    required
                    placeholder="e.g., Custom LLM Integration"
                    value="<?= e($editingService ? $editingService['service_name'] : '') ?>"
                >
            </div>

            <div class="form-row">
                <label for="service_description">Description</label>
                <textarea
                    id="service_description"
                    name="service_description"
                    maxlength="2000"
                    placeholder="Describe the scope of this AI service..."
                ><?= e($editingService ? ($editingService['service_description'] ?? '') : '') ?></textarea>
            </div>

            <div class="action-group">
                <button type="submit" class="btn-action">
                    <?= $editingService ? 'Save Changes' : 'Add AI Service' ?>
                </button>
                <?php if ($editingService): ?>
                    <a href="services.php" class="btn-outline">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</section>

<?php
render_management_footer();
