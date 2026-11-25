
<?php
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$search = trim($_GET['search'] ?? '');
$skill = trim($_GET['skill'] ?? '');
$sort = $_GET['sort'] ?? 'newest';

$allowedSort = [
    'newest' => 'p.created_at DESC',
    'deadline' => 'p.deadline ASC',
    'budget_low' => 'p.budget_min ASC',
    'budget_high' => 'p.budget_max DESC'
];

$orderBy = $allowedSort[$sort] ?? $allowedSort['newest'];

$sql = "SELECT 
            p.project_id,
            p.title,
            p.description,
            p.budget_min,
            p.budget_max,
            p.deadline,
            p.status,
            p.created_at
        FROM PROJECT p
        WHERE p.status = :status";

$params = [':status' => 'Open'];

if ($search !== '') {
    $sql .= " AND (p.title LIKE :search OR p.description LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($skill !== '') {
    $sql .= " AND EXISTS (
        SELECT 1
        FROM PROJECT_REQUIREMENT pr
        INNER JOIN AI_SKILL s
        ON pr.skill_id = s.skill_id
        WHERE pr.project_id = p.project_id
        AND s.skill_name = :skill
    )";
    $params[':skill'] = $skill;
}

$sql .= " ORDER BY $orderBy";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

$skillQuery = $pdo->query(
    "SELECT DISTINCT skill_name FROM AI_SKILL ORDER BY skill_name"
);
$skills = $skillQuery->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Projects | FreelancerAI</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .browse-container {
            max-width: 1250px;
            margin: 40px auto;
            padding: 20px;
        }

        .browse-header {
            margin-bottom: 30px;
        }

        .browse-header h1 {
            color: #03045E;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .browse-header p {
            color: #64748b;
        }

        .filter-section {
            background: white;
            padding: 22px;
            border: 1px solid #e2e8f0;
            margin-bottom: 30px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-section input,
        .filter-section select {
            flex: 1;
            min-width: 170px;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            font-size: 14px;
        }

        .search-btn {
            background: #0077B6;
            color: white;
            border: none;
            padding: 12px 25px;
            cursor: pointer;
            border-radius: 5px;
        }

        .search-btn:hover {
            background: #023E8A;
        }

        .project-count {
            color: #023E8A;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .project-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 22px;
        }

        .project-card {
            background: white;
            border: 1px solid #e2e8f0;
            padding: 25px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .project-card:hover {
            box-shadow: 0 5px 18px rgba(0,0,0,0.08);
            transform: translateY(-3px);
        }

        .project-card h2 {
            color: #03045E;
            font-size: 21px;
            margin-bottom: 12px;
        }

        .project-description {
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .project-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            padding: 15px 0;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-label {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .info-value {
            color: #023E8A;
            font-weight: bold;
        }

        .project-status {
            display: inline-block;
            background: #CAF0F8;
            color: #023E8A;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-bottom: 15px;
        }

        .view-btn {
            display: inline-block;
            background: #0077B6;
            color: white;
            padding: 11px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .view-btn:hover {
            background: #023E8A;
        }

        .empty-state {
            background: white;
            text-align: center;
            padding: 50px 20px;
            border: 1px solid #e2e8f0;
            color: #64748b;
            grid-column: 1 / -1;
        }

        @media(max-width: 768px) {
            .project-grid {
                grid-template-columns: 1fr;
            }

            .filter-section {
                flex-direction: column;
            }

            .filter-section input,
            .filter-section select {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<?php include '../includes/navbar.php'; ?>

<main class="browse-container">

    <div class="browse-header">
        <h1>Browse Projects</h1>
        <p>Discover exciting AI projects and find opportunities
           to showcase your skills.</p>
    </div>

    <form method="GET" class="filter-section">

        <input
            type="text"
            name="search"
            placeholder="Search projects..."
            value="<?= htmlspecialchars($search) ?>"
        >

        <select name="skill">
            <option value="">All Skills</option>

            <?php foreach ($skills as $item): ?>
                <option
                    value="<?= htmlspecialchars($item) ?>"
                    <?= $skill === $item ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($item) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="sort">
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>
                Newest First
            </option>

            <option value="deadline" <?= $sort === 'deadline' ? 'selected' : '' ?>>
                Deadline
            </option>

            <option value="budget_low" <?= $sort === 'budget_low' ? 'selected' : '' ?>>
                Lowest Budget
            </option>

            <option value="budget_high" <?= $sort === 'budget_high' ? 'selected' : '' ?>>
                Highest Budget
            </option>
        </select>

        <button type="submit" class="search-btn">
            Search
        </button>

    </form>

    <div class="project-count">
        <?= count($projects) ?> Projects Found
    </div>

    <div class="project-grid">

        <?php if (count($projects) > 0): ?>

            <?php foreach ($projects as $project): ?>

                <div class="project-card">

                    <span class="project-status">
                        <?= htmlspecialchars($project['status']) ?>
                    </span>

                    <h2>
                        <?= htmlspecialchars($project['title']) ?>
                    </h2>

                    <p class="project-description">
                        <?= htmlspecialchars(
                            substr($project['description'], 0, 150)
                        ) ?>
                        <?= strlen($project['description']) > 150 ? '...' : '' ?>
                    </p>

                    <div class="project-info">

                        <div>
                            <p class="info-label">Budget</p>
                            <p class="info-value">
                                <?= number_format((float)$project['budget_min'], 2) ?>
                                -
                                <?= number_format((float)$project['budget_max'], 2) ?>
                            </p>
                        </div>

                        <div>
                            <p class="info-label">Deadline</p>
                            <p class="info-value">
                                <?= htmlspecialchars($project['deadline']) ?>
                            </p>
                        </div>

                    </div>

                    <a
                        href="details.php?id=<?= (int)$project['project_id'] ?>"
                        class="view-btn"
                    >
                        View Details
                    </a>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="empty-state">
                <h2>No Projects Found</h2>
                <p>There are no projects matching your search.</p>
            </div>

        <?php endif; ?>

    </div>

</main>

<?php include '../includes/footer.php'; ?>

</body>
</html>
