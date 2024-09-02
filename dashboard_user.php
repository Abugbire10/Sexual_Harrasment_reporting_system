<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard | Reporting System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #1a202c;
            overflow-x: hidden;
            padding-top: 20px;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: #818181;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover {
            color: #f1f1f1;
            background-color: #2d3748;
        }
        .active {
            background-color: #2d3748;
            color: #f1f1f1 !important;
        }
        .file-list {
            margin-top: 10px;
        }
        .file-item {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }
        .file-item button {
            margin-left: 10px;
            background-color: red;
            color: white;
            border: none;
            padding: 5px;
            border-radius: 3px;
            cursor: pointer;
        }

        
    </style>
</head>
<body class="bg-gray-100">
    <div class="sidebar">
        <h2 class="text-white text-xl font-bold px-4 py-2">Dashboard</h2>
        <a href="#" class="active" id="dashboard-link">Dashboard</a>
        <a href="#" id="report-link">Report a case</a>
        <a href="#" id="track-link">Track Report</a>
        <!--<a href="follow_up.php">Follow Up</a>-->
        <a href="logout.php">Log Out</a>
    </div>

    <div class="main-content">
        <h1 class="text-3xl font-bold mb-6">Dashboard</h1>

        <div id="dashboard-content" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h2 class="text-2xl font-bold mb-4">Welcome to Your Dashboard</h2>
            <p>Select an option from the sidebar to get started.</p>
        </div>

        <div id="report-form" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" style="display: none;">
            <h2 class="text-2xl font-bold mb-4">Report a Complaint</h2>
            <form id="complaint-form" class="space-y-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="type">
                        Type of Incident
                    </label>
                    <select class="shadow border rounded w-full py-2 px-3 text-gray-700" id="type" name="type" required>
                        <option value="">Select Type</option>
                        <option value="harassment">Harassment</option>
                        <option value="violence">Violence</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                        Description
                    </label>
                    <textarea class="shadow border rounded w-full py-2 px-3 text-gray-700" id="description" name="description" rows="5" required></textarea>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="anonymous">
                        Submit Anonymously?
                    </label>
                    <select class="shadow border rounded w-full py-2 px-3 text-gray-700" id="anonymous" name="anonymous" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div id="non-anonymous-fields" style="display: none;">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                            Name
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" name="name">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                            Email
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" name="email">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="department">
                            Department
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="department" type="text" name="department">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                            Phone Number
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="phone" type="text" name="phone">
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Upload Evidence (up to 5 files)
                    </label>
                    <input type="file" multiple class="shadow border rounded w-full py-2 px-3 text-gray-700" id="evidence" name="evidence[]" accept="image/*,.pdf,.doc,.docx">
                </div>
                <div id="file-list" class="file-list">
                    <!-- List of uploaded files will appear here -->
                </div>
                <div>
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        Submit Report
                    </button>
                </div>
            </form>
        </div>

<!-- Track report section -->
<style>

    .status-badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
        text-align: center;
        min-width: 100px;
    }

    .status-submitted {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-under-review {
        background-color: #e0f2fe;
        color: #075985;
    }

    .status-resolved {
        background-color: #d1fae5;
        color: #065f46;
    }
    /* Table Styles */
    table {
        border-collapse: collapse;
        width: 100%;
    }

    thead {
        background-color: #2d3748; /* Darker gray for the header */
    }

    th {
        border-bottom: 2px solid #e2e8f0; /* Light gray line below header */
    }

    th, td {
        padding: 12px 15px;
        text-align: left;
    }

    tbody tr {
        border-bottom: 1px solid #e2e8f0; /* Light gray line between rows */
        transition: background-color 0.3s; /* Smooth transition for hover effect */
    }

    tbody tr:hover {
        background-color: #f7fafc; /* Light gray background on hover */
    }

    td {
        color: #4a5568; /* Dark gray text for body */
    }

    /* Responsive Styling */
    @media (max-width: 768px) {
        th, td {
            padding: 8px 10px; /* Adjust padding for smaller screens */
            font-size: 14px; /* Smaller font size */
        }
    }

    /* Additional styles for enhanced design and interactivity */
    #track-reports {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    #track-reports table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
    }

    #track-reports thead {
        background-color: #2c5282;
    }

    #track-reports th {
        color: #ffffff;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 16px;
        border-bottom: none;
    }

    #track-reports tbody tr {
        transition: all 0.3s ease;
    }

    #track-reports tbody tr:hover {
        background-color: #ebf4ff;
        transform: translateY(-2px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    #track-reports td {
        padding: 16px;
        border-bottom: 1px solid #e2e8f0;
    }

    #track-reports tbody tr:last-child td {
        border-bottom: none;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-in-progress {
        background-color: #e0f2fe;
        color: #075985;
    }

    .status-completed {
        background-color: #d1fae5;
        color: #065f46;
    }

    .feedback-button {
        background-color: #3b82f6;
        color: #ffffff;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .feedback-button:hover {
        background-color: #2563eb;
    }

    @media (max-width: 768px) {
        #track-reports th, 
        #track-reports td {
            padding: 12px;
        }

        .status-badge {
            font-size: 0.75rem;
        }
    }
</style>

<div id="track-reports" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" style="display: none;">
    <h2 class="text-2xl font-bold mb-4">Track Your Reports</h2>
    <table class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">Report ID</th>
                <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">Type</th>
                <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">Description</th>
                <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">Date</th>
                <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">Status</th>
                <th class="py-3 px-4 text-left text-sm font-medium uppercase tracking-wider">Feedback</th>
            </tr>
        </thead>
        <tbody id="reports-table-body">
            <!-- Report rows will be dynamically inserted here -->
        </tbody>
    </table>
</div>

<script>
    // Handle navigation
    document.getElementById('dashboard-link').addEventListener('click', function(e) {
        e.preventDefault();
        showSection('dashboard-content');
    });

    document.getElementById('report-link').addEventListener('click', function(e) {
        e.preventDefault();
        showSection('report-form');
    });

    document.getElementById('track-link').addEventListener('click', function(e) {
        e.preventDefault();
        showSection('track-reports');
        loadReports();
    });

    function showSection(sectionId) {
        // Hide all sections
        document.getElementById('dashboard-content').style.display = 'none';
        document.getElementById('report-form').style.display = 'none';
        document.getElementById('track-reports').style.display = 'none';

        // Show the selected section
        document.getElementById(sectionId).style.display = 'block';

        // Update active link
        document.querySelectorAll('.sidebar a').forEach(link => link.classList.remove('active'));
        event.target.classList.add('active');
    }

    // Load reports from server
    function loadReports() {
        fetch('track_report.php')
        .then(response => response.json())
        .then(reports => {
            const tableBody = document.getElementById('reports-table-body');
            tableBody.innerHTML = ''; // Clear existing rows

            reports.forEach(report => {
                const row = `
                    <tr>
                        <td class="py-2 px-4">${report.report_id}</td>
                        <td class="py-2 px-4">${report.type}</td>
                        <td class="py-2 px-4">${report.description}</td>
                        <td class="py-2 px-4">${report.report_date}</td>
                        <td class="py-2 px-4">${report.admin_evidence}</td>
                        <td class="py-2 px-4">
                            <span class="status-badge status-${report.status.toLowerCase().replace(' ', '-')}">
                                ${report.status}
                            </span>
                        </td>
                        <td class="py-2 px-4">
                            ${report.Admin_Feedback ? 
                                report.Admin_Feedback : 
                                '<button class="feedback-button" onclick="requestFeedback(' + report.report_id + ')">Request Feedback</button>'
                            }
                        </td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading reports.');
        });
    }

    function requestFeedback(reportId) {
        // Implement the feedback request logic here
        alert(`Requesting feedback for report ${reportId}`);
    }
</script>

    <script>
        let fileList = new DataTransfer();
        const maxFiles = 5;

        // Handle file selection
        document.getElementById('evidence').addEventListener('change', function(event) {
            const files = event.target.files;
            const fileListElement = document.getElementById('file-list');

            for (const file of files) {
                if (fileList.files.length < maxFiles) {
                    fileList.items.add(file);
                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-item';
                    fileItem.innerHTML = `
                        <span>${file.name}</span>
                        <button type="button" onclick="removeFile('${file.name}')">Remove</button>
                    `;
                    fileListElement.appendChild(fileItem);
                } else {
                    alert('You can only upload up to 5 files.');
                    break;
                }
            }

            // Update the file input with the new file list
            this.files = fileList.files;
        });

        function removeFile(fileName) {
            const fileListElement = document.getElementById('file-list');
            const fileItems = fileListElement.querySelectorAll('.file-item');
            
            for (let i = 0; i < fileList.files.length; i++) {
                if (fileList.files[i].name === fileName) {
                    fileList.items.remove(i);
                    break;
                }
            }

            for (const item of fileItems) {
                if (item.textContent.includes(fileName)) {
                    fileListElement.removeChild(item);
                    break;
                }
            }

            // Update the file input with the new file list
            document.getElementById('evidence').files = fileList.files;
        }

        // Handle form submission
        document.getElementById('complaint-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            // Replace the file input data with our maintained file list
            formData.delete('evidence[]');
            for (let file of fileList.files) {
                formData.append('evidence[]', file);
            }

            fetch('submit_report.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    this.reset();
                    document.getElementById('file-list').innerHTML = '';
                    fileList = new DataTransfer(); // Reset the file list
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting the report.');
            });
        });

        // Toggle visibility of non-anonymous fields
        document.getElementById('anonymous').addEventListener('change', function() {
            const nonAnonymousFields = document.getElementById('non-anonymous-fields');
            nonAnonymousFields.style.display = this.value === '0' ? 'block' : 'none';
        });

        // Handle navigation
        document.getElementById('dashboard-link').addEventListener('click', function(e) {
            e.preventDefault();
            showSection('dashboard-content');
        });

        document.getElementById('report-link').addEventListener('click', function(e) {
            e.preventDefault();
            showSection('report-form');
        });

        document.getElementById('track-link').addEventListener('click', function(e) {
            e.preventDefault();
            showSection('track-reports');
            loadReports();
        });

        function showSection(sectionId) {
            // Hide all sections
            document.getElementById('dashboard-content').style.display = 'none';
            document.getElementById('report-form').style.display = 'none';
            document.getElementById('track-reports').style.display = 'none';

            // Show the selected section
            document.getElementById(sectionId).style.display = 'block';

            // Update active link
            document.querySelectorAll('.sidebar a').forEach(link => link.classList.remove('active'));
            event.target.classList.add('active');
        }

        // Load reports from server
     function loadReports() {
        fetch('track_report.php')
        .then(response => response.json())
        .then(reports => {
            const tableBody = document.getElementById('reports-table-body');
            tableBody.innerHTML = ''; // Clear existing rows

            reports.forEach(report => {
                let statusClass = '';
                switch(report.status.toLowerCase()) {
                    case 'submitted':
                        statusClass = 'status-submitted';
                        break;
                    case 'under review':
                        statusClass = 'status-under-review';
                        break;
                    case 'resolved':
                        statusClass = 'status-resolved';
                        break;
                    default:
                        statusClass = '';
                }

                const row = `
                    <tr>
                        <td class="py-2 px-4 border-b border-gray-200">${report.report_id}</td>
                        <td class="py-2 px-4 border-b border-gray-200">${report.type}</td>
                        <td class="py-2 px-4 border-b border-gray-200">${report.description}</td>
                        <td class="py-2 px-4 border-b border-gray-200">${report.report_date}</td>
                        <td class="py-2 px-4 border-b border-gray-200">
                            <span class="status-badge ${statusClass}">
                                ${report.status}
                            </span>
                        </td>
                        <td class="py-2 px-4 border-b border-gray-200">${report.Admin_Feedback}</td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading reports.');
        });
    }



        
    </script>
</body>
</html>