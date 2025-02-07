<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amancom_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update query to properly check SIM status
$query = "SELECT s.*, 
          CASE 
              WHEN s.Device_Serial IS NOT NULL OR s.Company_Name IS NOT NULL THEN 'sold'
              ELSE 'Available'
          END as actual_status,
          CASE 
              WHEN s.Device_Serial IS NOT NULL OR s.Company_Name IS NOT NULL 
              THEN CONCAT('Sold to ', COALESCE(s.Company_Name, 'Unknown'))
              ELSE 'Available'
          END as Status,
          d.Serial_no as Device_Serial 
          FROM sim_card s 
          LEFT JOIN devices d ON s.Serial_no = d.SIM_Serial_no 
          ORDER BY s.Is_Sold DESC, s.Serial_no";

$result = $conn->query($query);

// Update the Is_Sold status for all SIMs based on Device_Serial and Company_Name
$update_status = "UPDATE sim_card 
                 SET Is_Sold = CASE 
                     WHEN Device_Serial IS NOT NULL OR Company_Name IS NOT NULL THEN 'sold'
                     ELSE 'Available'
                 END";
$conn->query($update_status);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Line Management</title>
    <link rel="stylesheet" href="../Style/Line_Management.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h1>Line Management</h1>
        <table>
            <thead>
                <tr>
                    <th>SIM Number</th>
                    <th>Serial Number</th>
                    <th>Service Provider</th>
                    <th>Status</th>
                    <th>Device Serial</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['SIM_num']; ?></td>
                        <td><?php echo $row['Serial_no']; ?></td>
                        <td><?php echo $row['Service_Provider']; ?></td>
                        <td><?php echo $row['Status']; ?></td>
                        <td><?php echo $row['Device_Serial'] ?: '-'; ?></td>
                        <td>
                            <a href="edit_line.php?id=<?php echo $row['Serial_no']; ?>" class="edit-btn">Edit</a>
                            <?php if ($row['actual_status'] === 'Available'): ?>
                                <a href="assign_line.php?id=<?php echo $row['Serial_no']; ?>" class="assign-btn">Assign</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="add_line.php" class="add-btn">Add New Line</a>
    </div>
</body>
</html>
