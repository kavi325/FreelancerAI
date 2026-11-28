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
    } elseif ($action === 'create_skill') {
        $skillName = trim((string)($_POST['skill_name'] ?? ''));
        $skillDescription = trim((string)($_POST['skill_description'] ?? ''));

        if ($skillName === '') {
            $errorMessage = 'Skill name is required.';
        } elseif (mb_strlen($skillName) > 100) {
            $errorMessage = 'Skill name must not exceed 100 characters.';
        } elseif (mb_strlen($skillDescription) > 2000) {
            $errorMessage = 'Skill description must not exceed 2000 characters.';
        } else {
            try {
                $dupStmt = $pdo->prepare("
                    SELECT skill_id
                    FROM AI_SKILL
                    WHERE LOWER(skill_name) = LOWER(?)
                    LIMIT 1
                ");
                $dupStmt->execute([$skillName]);

                if ($dupStmt->fetch()) {
                    $errorMessage = 'An AI skill with that name already exists.';
                } else {
                    $insertStmt = $pdo->prepare("
                        INSERT INTO AI_SKILL (skill_name, skill_description)
                        VALUES (?, ?)
                    ");
                    $insertStmt->execute([
                        $skillName,
                        $skillDescription !== '' ? $skillDescription : null
                    ]);
                    $successMessage = 'AI skill "' . $skillName . '" has been added.';
                }
            } catch (PDOException $ex) {
                $errorMessage = 'Could not add the AI skill. Please try again.';
            }
        }
    } elseif ($action === 'update_skill') {
        $skillId = filter_input(INPUT_POST, 'skill_id', FILTER_VALIDATE_INT);
        $skillName = trim((string)($_POST['skill_name'] ?? ''));
        $skillDescription = trim((string)($_POST['skill_description'] ?? ''));

        if (!$skillId || $skillId <= 0) {
            $errorMessage = 'Invalid AI skill selected.';
        } elseif ($skillName === '') {
            $errorMessage = 'Skill name is required.';
        } elseif (mb_strlen($skillName) > 100) {
            $errorMessage = 'Skill name must not exceed 100 characters.';
        } elseif (mb_strlen($skillDescription) > 2000) {
            $errorMessage = 'Skill description must not exceed 2000 characters.';
        } else {
            try {
                $dupStmt = $pdo->prepare("
                    SELECT skill_id
                    FROM AI_SKILL
                    WHERE LOWER(skill_name) = LOWER(?) AND skill_id <> ?
                    LIMIT 1
                ");
                $dupStmt->execute([$skillName, $skillId]);

                if ($dupStmt->fetch()) {
                    $errorMessage = 'Another AI skill already uses that name.';
                } else {
                    $updateStmt = $pdo->prepare("
                        UPDATE AI_SKILL
                        SET skill_name = ?, skill_description = ?
                        WHERE skill_id = ?
                    ");
                    $updateStmt->execute([
                        $skillName,
                        $skillDescription !== '' ? $skillDescription : null,
                        $skillId
                    ]);
                    $successMessage = 'AI skill "' . $skillName . '" has been updated.';
                }
            } catch (PDOException $ex) {
                $errorMessage = 'Could not update the AI skill. Please try again.';
            }
        }
    } elseif ($action === 'delete_skill') {
        if (!$canDeleteCatalogItems) {
            http_response_code(403);
            $errorMessage = 'Deleting AI skills requires Administration privileges.';
        } else {
            $skillId = filter_input(INPUT_POST, 'skill_id', FILTER_VALIDATE_INT);

            if (!$skillId || $skillId <= 0) {
                $errorMessage = 'Invalid AI skill selected.';
            } else {
                try {
                    $pdo->beginTransaction();

                    $freelancerRefStmt = $pdo->prepare("
                        SELECT COUNT(*)
                        FROM FREELANCER_SKILL
                        WHERE skill_id = ?
                    ");
                    $freelancerRefStmt->execute([$skillId]);
                    $freelancerRefCount = (int)$freelancerRefStmt->fetchColumn();

                    $projectRefStmt = $pdo->prepare("
                        SELECT COUNT(*)
                        FROM PROJECT_REQUIREMENT
                        WHERE skill_id = ?
                    ");
                    $projectRefStmt->execute([$skillId]);
                    $projectRefCount = (int)$projectRefStmt->fetchColumn();

                    if ($freelancerRefCount > 0 || $projectRefCount > 0) {
                        $pdo->rollBack();
                        $errorMessage = 'This AI skill cannot be deleted because it is referenced by '
                            . $freelancerRefCount . ' freelancer profile(s) and '
                            . $projectRefCount . ' project requirement(s).';
                    } else {
                        $deleteStmt = $pdo->prepare("
                            DELETE FROM AI_SKILL
                            WHERE skill_id = ?
                        ");
                        $deleteStmt->execute([$skillId]);
                        $pdo->commit();

                        if ($deleteStmt->rowCount() > 0) {
                            $successMessage = 'Unused AI skill has been removed.';
                        } else {
                            $errorMessage = 'The selected AI skill was not found.';
                        }
                    }
                } catch (PDOException $ex) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    $errorMessage = 'Could not remove the AI skill because it is referenced by existing records.';
                }
            }
        }
    } else {
        $errorMessage = 'Unsupported action requested.';
    }
}

$searchTerm = trim((string)($_GET['search'] ?? ''));
$editSkillId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
$editingSkill = null;

if ($editSkillId && $editSkillId > 0) {
    try {
        $editStmt = $pdo->prepare("
            SELECT skill_id, skill_name, skill_description
            FROM AI_SKILL
            WHERE skill_id = ?
            LIMIT 1
        ");
        $editStmt->execute([$editSkillId]);
        $editingSkill = $editStmt->fetch() ?: null;
    } catch (PDOException $ex) {
        $errorMessage = 'Unable to load the selected AI skill for editing.';
    }
}

$skills = [];
try {
    $baseSql = "
        SELECT
            s.skill_id,
            s.skill_name,
            s.skill_description,
            (SELECT COUNT(*) FROM FREELANCER_SKILL fs WHERE fs.skill_id = s.skill_id) AS freelancer_count,
            (SELECT COUNT(*) FROM PROJECT_REQUIREMENT pr WHERE pr.skill_id = s.skill_id) AS project_count
        FROM AI_SKILL s
    ";

    if ($searchTerm !== '') {
        $likeValue = '%' . $searchTerm . '%';
        $listStmt = $pdo->prepare($baseSql . " WHERE s.skill_name LIKE ? OR s.skill_description LIKE ? ORDER BY s.skill_name ASC");
        $listStmt->execute([$likeValue, $likeValue]);
        $skills = $listStmt->fetchAll();
    } else {
        $skills = $pdo->query($baseSql . " ORDER BY s.skill_name ASC")->fetchAll();
    }
} catch (PDOException $ex) {
    $errorMessage = 'Unable to load AI skills right now.';
}

$csrfToken = get_csrf_token();

render_management_header(
    'AI Skills',
    'Maintain standardized AI skills and monitor freelancer & project usage',
    'skills',
    $currentStaff,
    $staffRoleLabel
);
?>

<section class="mgmt-banner">
    <div>
        <span class="eyebrow">CATALOG MANAGEMENT</span>
        <h2>AI <span>Skills</span></h2>
        <p>Manage standardized AI skills linked to freelancer profiles and project requirements. Referenced skills are protected from deletion.</p>
    </div>
    <span class="mgmt-pill"><?= count($skills) ?> Skills Listed</span>
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
            <h3>Registered AI Skills</h3>
        </div>

        <form method="GET" action="skills.php" class="filter-bar">
            <div class="filter-field">
                <label for="search">Search Skills</label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    placeholder="Search by skill name or description..."
                    value="<?= e($searchTerm) ?>"
                >
            </div>
            <div class="action-group">
                <button type="submit" class="btn-action">Search</button>
                <a href="skills.php" class="btn-outline">Reset</a>
            </div>
        </form>

        <?php if ($skills): ?>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Skill Name</th>
                            <th>Description</th>
                            <th>Usage</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($skills as $skill): ?>
                            <?php
                            $freelancerCount = (int)$skill['freelancer_count'];
                            $projectCount = (int)$skill['project_count'];
                            $isReferenced = ($freelancerCount + $projectCount) > 0;
                            ?>
                            <tr>
                                <td>#<?= (int)$skill['skill_id'] ?></td>
                                <td><strong><?= e($skill['skill_name']) ?></strong></td>
                                <td><?= e($skill['skill_description'] ?: 'No description provided') ?></td>
                                <td>
                                    <span class="badge badge-role"><?= $freelancerCount ?> freelancers</span>
                                    <span class="badge badge-role"><?= $projectCount ?> projects</span>
                                </td>
                                <td>
                                    <div class="action-group">
                                        <a href="skills.php?edit=<?= (int)$skill['skill_id'] ?>" class="btn-outline btn-small">Edit</a>
                                        <?php if ($canDeleteCatalogItems && !$isReferenced): ?>
                                            <form method="POST" action="skills.php" class="inline-form">
                                                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                                <input type="hidden" name="action" value="delete_skill">
                                                <input type="hidden" name="skill_id" value="<?= (int)$skill['skill_id'] ?>">
                                                <button type="submit" class="btn-danger-outline">Delete</button>
                                            </form>
                                        <?php elseif ($canDeleteCatalogItems && $isReferenced): ?>
                                            <span class="badge">Protected</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="empty-message">No AI skills found.</p>
        <?php endif; ?>
    </div>

    <div class="mgmt-panel">
        <div class="panel-header">
            <h3><?= $editingSkill ? 'Edit AI Skill' : 'Add New AI Skill' ?></h3>
            <?php if ($editingSkill): ?>
                <a href="skills.php" class="panel-link">Cancel Edit</a>
            <?php endif; ?>
        </div>

        <form method="POST" action="skills.php" class="mgmt-form">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <input type="hidden" name="action" value="<?= $editingSkill ? 'update_skill' : 'create_skill' ?>">
            <?php if ($editingSkill): ?>
                <input type="hidden" name="skill_id" value="<?= (int)$editingSkill['skill_id'] ?>">
            <?php endif; ?>

            <div class="form-row">
                <label for="skill_name">Skill Name</label>
                <input
                    type="text"
                    id="skill_name"
                    name="skill_name"
                    maxlength="100"
                    required
                    placeholder="e.g., PyTorch"
                    value="<?= e($editingSkill ? $editingSkill['skill_name'] : '') ?>"
                >
            </div>

            <div class="form-row">
                <label for="skill_description">Description</label>
                <textarea
                    id="skill_description"
                    name="skill_description"
                    maxlength="2000"
                    placeholder="Describe this AI skill..."
                ><?= e($editingSkill ? ($editingSkill['skill_description'] ?? '') : '') ?></textarea>
            </div>

            <div class="action-group">
                <button type="submit" class="btn-action">
                    <?= $editingSkill ? 'Save Changes' : 'Add AI Skill' ?>
                </button>
                <?php if ($editingSkill): ?>
                    <a href="skills.php" class="btn-outline">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</section>

<?php
render_management_footer();
