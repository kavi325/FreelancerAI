
<?php
$pageTitle = "About Us | FreelancerAI";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<main class="container about-page">

    <section class="about-header">
        <span class="eyebrow">ABOUT OUR PROJECT</span>
        <h1>AI Projects & Services <span>Freelancing Platform</span></h1>
        <p>A university group project developed as part of the PUSL2019 module.</p>
    </section>

    <section class="about-info">
        <div class="about-info-card">
            <h3>Module Information</h3>
            <p><strong>Module Code:</strong> PUSL2019</p>
            <p><strong>Group:</strong> 12</p>
            <p><strong>Academic Batch:</strong> 26.1</p>
            <p><strong>Academic Year:</strong> 2nd Year</p>
            <p><strong>Semester:</strong> 1st Semester</p>
        </div>

        <div class="about-info-card">
            <h3>Module Leaders</h3>
            <p>Mr. S. Nasiketha (Naji)</p>
            <p>Ms. Thisarani Wickramasinghe</p>
        </div>
    </section>

    <section class="institution-section">
        <h2>Academic Institutions</h2>

        <div class="institution-logos">
            <div class="institution-card">
                <img src="assets/images/nsbm-logo.png" alt="NSBM Green University Logo">
                <h3>NSBM Green University</h3>
            </div>

            <div class="institution-card">
                <img src="assets/images/plymouth-logo.png" alt="University of Plymouth Logo">
                <h3>University of Plymouth</h3>
            </div>
        </div>
    </section>

    <section class="team-section">
        <div class="section-heading">
            <div>
                <h2>Meet Our Team</h2>
                <p>Group 12 — Project Development Team</p>
            </div>
            <span class="team-count">6 Members</span>
        </div>

        <div class="team-table-wrapper">
            <table class="team-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>NSBM ID</th>
                        <th>Plymouth ID</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>K.G.K.N. Kaleekotuwa</td>
                        <td>40383</td>
                        <td>27106086</td>
                    </tr>
                    <tr>
                        <td>W.S. De Silva</td>
                        <td>40178</td>
                        <td>27106084</td>
                    </tr>
                    <tr>
                        <td>Jonnah Stephen</td>
                        <td>40626</td>
                        <td>27109121</td>
                    </tr>
                    <tr>
                        <td>Chathuni Vimendika</td>
                        <td>40464</td>
                        <td>27109116</td>
                    </tr>
                    <tr>
                        <td>Shashiru Kavindi Jayawardena</td>
                        <td>40362</td>
                        <td>27109013</td>
                    </tr>
                    <tr>
                        <td>J.P.S.V. Thilakarathne</td>
                        <td>40490</td>
                        <td>27109017</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
