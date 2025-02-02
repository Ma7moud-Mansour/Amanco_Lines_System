<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amancom_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//  Statistics
$sim_count_query = "SELECT COUNT(*) FROM sim_card WHERE Is_Sold = 'sold'";
$stored_sims_query = "SELECT COUNT(*) FROM sim_card WHERE Is_Sold = 'Available'";
$company_count_query = "SELECT COUNT(*) FROM client_company";

// do statitistics
$sim_count_result = $conn->query($sim_count_query);
$stored_sims_result = $conn->query($stored_sims_query);
$company_count_result = $conn->query($company_count_query);

// Get values
$sim_count = $sim_count_result->fetch_row()[0];
$stored_sims = $stored_sims_result->fetch_row()[0];
$company_count = $company_count_result->fetch_row()[0];

// Search company name
$search_query = "";
$search_results = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_company'])) {
    $search_name = $_POST['company_name'];
    $search_query = "SELECT * FROM client_company WHERE Name LIKE '%$search_name%'";
    $search_result = $conn->query($search_query);
    if ($search_result->num_rows > 0) {
        while ($row = $search_result->fetch_assoc()) {
            $search_results[] = $row;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Dashboard</title>
<link rel="stylesheet" href="../Style/Dashboard.css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
/* Statistics Section */
        /* التصميم العام */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #fff;
            overflow: hidden;
        }

        /* العنوان h1 */
        h1 {
            margin: 20px 0;
            font-size: 32px;
            font-weight: bold;
            color: #fff;
            text-align: center;
        }

        /* تأثيرات الخلفية */
        .background {
            position: absolute;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 10%, transparent 10%);
            background-size: 20px 20px;
            animation: moveBackground 10s linear infinite;
            z-index: -1;
        }

        @keyframes moveBackground {
            from { transform: translateY(0); }
            to { transform: translateY(-200px); }
        }

        /* صندوق المحتوى */
        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 20px;
            width: 90%;
            max-width: 1200px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: fadeIn 1s ease-in-out;
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            margin-bottom: 20px;
        }

        header h1 {
            font-size: 1.5em;
            margin: 0;
        }

        header nav a {
            text-decoration: none;
            color: #fff;
            margin: 0 10px;
            font-weight: bold;
        }

        header nav a.active {
            color: #007bff;
        }

        /* Search Bar */
        .search-bar {
            margin-bottom: 20px;
        }

        .search-bar form {
            display: flex;
            gap: 10px;
        }

        .search-bar input {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 14px;
        }

        .search-bar input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-bar button {
            padding: 10px 20px;
            border: none;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: #fff;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .search-bar button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab-btn {
            padding: 10px 20px;
            border: none;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .tab-btn.active {
            background: linear-gradient(135deg, #007bff, #0056b3);
        }

        /* Tab Content */
        .tab-content {
            display: none;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
        }

        .tab-content.active {
            display: block;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            background: rgba(255, 255, 255, 0.1);
            font-weight: bold;
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        /* Statistics */
        .statistics {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 20px;
        }

        .stat {
            background: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            flex: 1;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .stat h2 {
            margin: 10px 0;
            font-size: 2.5em;
            color: #4CAF50;
        }

        .stat p {
            margin: 0;
            font-size: 1.2em;
            color: #777;
        }

        .icon {
            margin-bottom: 15px;
        }

        .icon img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .sName {
            color: black;
        }

        /* Charts */
        .charts {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .chart-container {
            flex: 1;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .statistics, .charts {
                flex-direction: column;
            }

            .container {
                width: 95%;
                padding: 10px;
            }

            h1 {
                font-size: 24px;
            }
        }
</style>
</head>
<body>
<header>
    <h1>Dashboard</h1>
    <nav>
        <a href="#" class="active">Dashboard</a>
        <a href="add_line.php">Add Line</a>
        <a href="add_customer.php">Add Customer</a>
        <a href="Add_Device.php">Add Device</a>
        <a href="Line_Manegement.php">Line Inventory</a>
        <button class="logout"><a href="Logout.php">Logout</a></button>
    </nav>
</header>

<div class="container">

    <!-- Search Bar -->
    <div class="search-bar">
        <form method="POST">
            <label for="company_name">Search Company Name:</label>
            <input type="text" id="company_name" name="company_name" required />
            <button type="submit" name="search_company">Search</button>
        </form>
    </div>

    <!-- Search Results Section -->
    <div class="search-results">
        <?php if (!empty($search_results)): ?>
            <div class="tab-content active" id="companies">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($search_results as $result): ?>
                            <tr>
                                <td><?php echo $result['Code']; ?></td>
                                <td><a class="sName" href="Client_Profile.php?id=<?php echo $result['Code']; ?>"><?php echo $result['Name']; ?></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_company'])): ?>
            <p>No matching companies found.</p>
        <?php endif; ?>
    </div>

    <!-- Statistics Section -->
    <div class="statistics">
        <div class="stat">
            <div class="icon">
                <img src="../photos/sim.jpeg" alt="Sold SIMs" />
            </div>
            <h2><?php echo $sim_count; ?></h2>
            <p>Total SIMs Sold</p>
        </div>
        <div class="stat">
            <div class="icon">
                <img src="../photos/sim2.jpeg" alt="Stored SIMs" />
            </div>
            <h2><?php echo $stored_sims; ?></h2>
            <p>Total SIMs Stored</p>
        </div>
        <div class="stat">
            <div class="icon">
                <img src="../photos/company.png" alt="Companies" />
            </div>
            <h2><?php echo $company_count; ?></h2>
            <p>Total Companies</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts">
        <div class="chart-container">
            <canvas id="statusPieChart"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="companyPieChart"></canvas>
        </div>
    </div>

</div>

<script>
    // Chart for Status (Sold vs Stored)
    var ctx1 = document.getElementById('statusPieChart').getContext('2d');
    var statusChart = new Chart(ctx1, {
        type: 'pie',
        data: {
            labels: ['Stored', 'Sold'],
            datasets: [ {
                label: 'SIMs Status',
                data: [<?php echo $stored_sims; ?>, <?php echo $sim_count; ?>],
                backgroundColor: ['#FFC107', '#28A745']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: { enabled: true }
            }
        }
    });

    // Chart for Companies
    var ctx2 = document.getElementById('companyPieChart').getContext('2d');
    var companyChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: ['Companies'],
            datasets: [ {
                label: 'Number of Companies',
                data: [<?php echo $company_count; ?>],
                backgroundColor: ['#007BFF']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: { enabled: true }
            }
        }
    });

    
</script>

</body>
</html>
