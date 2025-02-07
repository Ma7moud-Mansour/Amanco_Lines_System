<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amancom_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get inventory statistics
$stats_query = "SELECT 
    Service_Provider,
    COUNT(*) as total,
    SUM(CASE WHEN Is_Sold = 'Available' THEN 1 ELSE 0 END) as available,
    SUM(CASE WHEN Is_Sold = 'sold' THEN 1 ELSE 0 END) as sold
    FROM sim_card
    GROUP BY Service_Provider";

$stats_result = $conn->query($stats_query);

// Get detailed inventory
$inventory_query = "SELECT s.*, 
                   CASE 
                       WHEN s.Is_Sold = 'sold' THEN CONCAT('Sold to ', s.Company_Name)
                       ELSE 'Available'
                   END as Status,
                   d.Serial_no as Device_Serial,
                   c.Name as Company_Name
                   FROM sim_card s 
                   LEFT JOIN devices d ON s.Serial_no = d.SIM_Serial_no
                   LEFT JOIN client_company c ON s.Company_Name = c.Name
                   ORDER BY s.Service_Provider, s.Is_Sold";

$inventory_result = $conn->query($inventory_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Line Inventory</title>
    <link rel="stylesheet" href="../Style/Line_Inventory.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h1>Line Inventory Status</h1>
        
        <!-- Statistics Summary -->
        <section class="inventory-stats">
            <h2>Inventory Summary</h2>
            <table class="stats-table">
                <thead>
                    <tr>
                        <th>Service Provider</th>
                        <th>Total SIMs</th>
                        <th>Available</th>
                        <th>Sold</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($stats = $stats_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $stats['Service_Provider']; ?></td>
                            <td><?php echo $stats['total']; ?></td>
                            <td><?php echo $stats['available']; ?></td>
                            <td><?php echo $stats['sold']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <!-- Detailed Inventory -->
        <section class="inventory-details">
            <h2>Detailed Inventory</h2>
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>SIM Number</th>
                        <th>Serial Number</th>
                        <th>Service Provider</th>
                        <th>Status</th>
                        <th>Device Serial</th>
                        <th>Company</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $inventory_result->fetch_assoc()): ?>
                        <tr class="<?php echo $item['Is_Sold'] === 'Available' ? 'available' : 'sold'; ?>">
                            <td><?php echo $item['SIM_num']; ?></td>
                            <td><?php echo $item['Serial_no']; ?></td>
                            <td><?php echo $item['Service_Provider']; ?></td>
                            <td><?php echo $item['Status']; ?></td>
                            <td><?php echo $item['Device_Serial'] ?: '-'; ?></td>
                            <td><?php echo $item['Company_Name'] ?: '-'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
