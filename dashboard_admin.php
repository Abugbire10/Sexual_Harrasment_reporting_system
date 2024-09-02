<?php
include 'db.php'; // Ensure this file initializes $conn

// Get the active section from URL parameter, default to 'dashboard'
$active_section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';

function getCount($table, $type = null) {
    global $conn;
    if ($table === 'reports' && $type === 'resources') {
        $sql = "SELECT COUNT(*) as count FROM reports WHERE 
                evidence1 IS NOT NULL AND evidence1 != '' OR
                evidence2 IS NOT NULL AND evidence2 != '' OR
                evidence3 IS NOT NULL AND evidence3 != '' OR
                evidence4 IS NOT NULL AND evidence4 != '' OR
                evidence5 IS NOT NULL AND evidence5 != ''";
    } else {
        $sql = "SELECT COUNT(*) as count FROM $table";
    }
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    return 0;
}

// Fetch counts from database
$totalUsers = getCount('users');
$totalReports = getCount('reports');
$totalResources = getCount('reports', 'resources');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="dashboard-styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="manage_report.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="sidebar-header">
                    <h3 class="text-center">Admin Panel</h3>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="?section=dashboard" onclick="toggleSection('dashboard')">
                            <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?section=manageReports" onclick="toggleSection('manageReports')">
                            <i class="fas fa-file-alt"></i> <span>Manage Reports</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?section=manageResources" onclick="toggleSection('manageResources')">
                            <i class="fas fa-folder-open"></i> <span>Manage Resources</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="toggleSection('forwardReport')">
                            <i class="fas fa-users"></i> <span>Forward Report</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="toggleSection('resetPassword')">
                        <i class="fas fa-cog"></i> <span>Reset Password</span></a></li>
                    <li class="nav-item mt-4">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button id="sidebar-toggle" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </div>

                <div id="dashboard" class="toggle-section" style="display: <?php echo ($active_section == 'dashboard') ? 'block' : 'none'; ?>">
                    <h1 class="mt-4">Dashboard</h1>
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card text-white bg-primary mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total Users</h5>
                                    <p class="card-text fs-2"><?php echo $totalUsers; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-success mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total Reports</h5>
                                    <p class="card-text fs-2"><?php echo $totalReports; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-warning mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total Resources</h5>
                                    <p class="card-text fs-2"><?php echo $totalResources; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manage Reports Section -->
    <div id="manageReports" class="toggle-section" style="display: <?php echo ($active_section == 'manageReports') ? 'block' : 'none'; ?>">
    <h2 class="mt-4">Manage Reports</h2>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Report ID</th>
                <th>Report Date</th>
                <th>Report Description</th>
                <th>Status</th>
                <th>Name</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>Department</th>
                <th>Admin Feedback</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Pagination logic
            $records_per_page = 3;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $records_per_page;

            $reports_sql = "SELECT * FROM reports LIMIT $offset, $records_per_page";
            $reports_result = $conn->query($reports_sql);

            while ($report = $reports_result->fetch_assoc()) {
            ?>
            <tr>
                <td><?php echo htmlspecialchars($report['report_id']); ?></td>
                <td><?php echo htmlspecialchars($report['report_date']); ?></td>
                <td><?php echo htmlspecialchars($report['description']); ?></td>
                <td id="status-<?php echo $report['report_id']; ?>">
                    <?php echo htmlspecialchars($report['status']); ?>
                </td>
                <td><?php echo $report['anonymous'] ? 'Anonymous' : htmlspecialchars($report['name']); ?></td>
                <td><?php echo $report['anonymous'] ? 'Anonymous' : htmlspecialchars($report['phone']); ?></td>
                <td><?php echo $report['anonymous'] ? 'Anonymous' : htmlspecialchars($report['email']); ?></td>
                <td><?php echo $report['anonymous'] ? 'Anonymous' : htmlspecialchars($report['department']); ?></td>
                <td>
                    <form action="manage_reports.php" method="post" enctype="multipart/form-data">
                        <textarea name="feedback" placeholder="Write feedback..." required></textarea>
                        <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($report['report_id']); ?>">
                        <select name="status" class="form-select mt-1" required>
                            <option value="under review" <?php if ($report['status'] == 'under review') echo 'selected'; ?>>Under Review</option>
                            <option value="resolved" <?php if ($report['status'] == 'resolved') echo 'selected'; ?>>Resolved</option>
                        </select>
                        <input type="hidden" name="anonymous" value="<?php echo htmlspecialchars($report['anonymous']); ?>">
                        <input type="file" name="evidence[]" multiple class="form-control mt-1">
                        <button type="submit" class="btn btn-primary mt-1">Send Feedback</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Pagination links -->
    <div class="pagination">
        <?php
        $total_records_sql = "SELECT COUNT(*) as total FROM reports";
        $total_records_result = $conn->query($total_records_sql);
        $total_records = $total_records_result->fetch_assoc()['total'];
        $total_pages = ceil($total_records / $records_per_page);

        for ($i = 1; $i <= $total_pages; $i++) {
            echo "<a href='?section=manageReports&page=$i' " . ($page == $i ? "class='active'" : "") . ">$i</a>";
        }
        ?>
    </div>
</div>

                <!-- Manage Resources Section -->
                <div id="manageResources" class="toggle-section" style="display: <?php echo ($active_section == 'manageResources') ? 'block' : 'none'; ?>">
                    <h2 class="mt-4">Manage Resources</h2>
                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>Report ID</th>
                                <th>Evidence Files</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $resources_sql = "SELECT report_id, evidence1, evidence2, evidence3, evidence4, evidence5 FROM reports";
                            $resources_result = $conn->query($resources_sql);
                            while ($resource = $resources_result->fetch_assoc()) {
                                $reportId = $resource['report_id'];
                                $evidences = [
                                    $resource['evidence1'],
                                    $resource['evidence2'],
                                    $resource['evidence3'],
                                    $resource['evidence4'],
                                    $resource['evidence5']
                                ];
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reportId); ?></td>
                                <td>
                                    <?php foreach ($evidences as $evidence): ?>
                                        <?php if (!empty($evidence)): ?>
                                            <a href="uploads/<?php echo htmlspecialchars(basename($evidence)); ?>" target="_blank">
                                                <?php echo htmlspecialchars(basename($evidence)); ?>
                                            </a><br>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
<!-- End of Manage Resources Section -->

<!-- Forward Resources Section -->
<div id="forwardReport" class="toggle-section" style="display: <?php echo ($active_section == 'forwardReport') ? 'block' : 'none'; ?>">
    <h2 class="mt-4">Forward Report</h2>
    <form id="forwardReportForm" action="forward_report.php" method="post">
        <div class="mb-3">
            <label for="report_id" class="form-label">Select Report</label>
            <select id="report_id" name="report_id" class="form-select" required onchange="fetchEvidence(this.value)">
                <option value="">Select a report</option>
                <?php
                $reports_sql = "SELECT report_id, description FROM reports";
                $reports_result = $conn->query($reports_sql);
                while ($report = $reports_result->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($report['report_id']) . '">' . htmlspecialchars($report['report_id']) . ' - ' . htmlspecialchars($report['description']) . '</option>';
                }
                ?>
            </select>
        </div>

        <div id="evidence-status" class="mb-3"></div>
        <div id="evidence-files" class="mb-3"></div>

        
        <div class="mb-3">
    <label for="email" class="form-label">Recipient Emails (comma-separated)</label>
    <textarea id="email" name="emails" class="form-control" placeholder="Enter email addresses separated by commas" required></textarea>
</div>

        <div class="mb-3">
            <label for="message" class="form-label">Additional Message (Optional)</label>
            <textarea id="message" name="message" class="form-control" placeholder="Add a message..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Forward Report</button>
    </form>
</div>                         

<!-- End of Forward Resources Section -->


<!-- reset password section -->

<div id="resetPassword" class="toggle-section" style="display: <?php echo ($active_section == 'resetPassword') ? 'block' : 'none'; ?>">
    <h2 class="mt-4">Reset Password</h2>
    <form id="resetPasswordForm" action="reset_password.php" method="post">
        <div class="mb-3">
            <label for="current_password" class="form-label">Current Password</label>
            <input type="password" id="current_password" name="current_password" class="form-control" placeholder="Enter your current password" required>
        </div>

        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Enter your new password" required>
        </div>

        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your new password" required>
        </div>

        <button type="submit" class="btn btn-primary">Reset Password</button>
    </form>
</div>




            </main>
        </div>
    </div>



    <script>
        //reset password script below
  function toggleSection(section) {
        // Hide all sections
        document.querySelectorAll('.toggle-section').forEach(function(element) {
            element.style.display = 'none';
        });

        // Show the selected section
        document.getElementById(section).style.display = 'block';
    }

        function toggleSection(sectionId) {
            const sections = document.querySelectorAll('.toggle-section');
            sections.forEach(section => {
                section.style.display = section.id === sectionId ? 'block' : 'none';
            });
        }
 function checkEvidence(reportId) {
        const evidenceAttachmentsDiv = document.getElementById('evidence-attachments');
        if (reportId) {
            fetch('check_evidence.php?report_id=' + reportId)
                .then(response => response.json())
                .then(data => {
                    const evidenceStatusDiv = document.getElementById('evidence-status');
                    if (data.hasEvidence) {
                        evidenceStatusDiv.innerHTML = '<div class="alert alert-success">This report has evidence attached.</div>';
                        evidenceAttachmentsDiv.style.display = 'block'; // Show evidence attachment inputs
                    } else {
                        evidenceStatusDiv.innerHTML = '<div class="alert alert-warning">This report does not have any evidence attached.</div>';
                        evidenceAttachmentsDiv.style.display = 'none'; // Hide evidence attachment inputs
                    }
                })
                .catch(error => {
                    console.error('Error fetching evidence status:', error);
                });
        } else {
            document.getElementById('evidence-status').innerHTML = '';
            evidenceAttachmentsDiv.style.display = 'none'; // Hide if no report is selected
        }
    }

    //function to fetch evidence from the database

    function fetchEvidence(reportId) {
        if (reportId) {
            fetch('fetch_evidence.php?report_id=' + reportId)
                .then(response => response.json())
                .then(data => {
                    const evidenceStatusDiv = document.getElementById('evidence-status');
                    const evidenceFilesDiv = document.getElementById('evidence-files');
                    
                    if (data.evidence.length > 0) {
                        evidenceStatusDiv.innerHTML = '<div class="alert alert-success">Evidence files attached:</div>';
                        evidenceFilesDiv.innerHTML = data.evidence.map(file => `<a href="uploads/${file}" target="_blank">${file}</a>`).join('<br>');
                    } else {
                        evidenceStatusDiv.innerHTML = '<div class="alert alert-warning">No evidence files attached to this report.</div>';
                        evidenceFilesDiv.innerHTML = '';
                    }
                })
                .catch(error => {
                    console.error('Error fetching evidence:', error);
                });
        } else {
            document.getElementById('evidence-status').innerHTML = '';
            document.getElementById('evidence-files').innerHTML = '';
        }
    }

    // end of the function

      function toggleSection(sectionId) {
    const sections = document.querySelectorAll('.toggle-section');
    sections.forEach(section => {
        section.style.display = section.id === sectionId ? 'block' : 'none';
    });}

document.addEventListener('DOMContentLoaded', () => {
    toggleSection('<?php echo $active_section; ?>');
});

document.getElementById('sidebar-toggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('collapsed');
    document.querySelector('.content').classList.toggle('expanded');
});
        function updateStatus(reportId, newStatus) {
            document.getElementById('status-' + reportId).textContent = newStatus;
        }

        document.addEventListener('DOMContentLoaded', () => {
            toggleSection('<?php echo $active_section; ?>');
        });

        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('expanded');
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>