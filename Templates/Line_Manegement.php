<?php
$conn = new mysqli('localhost', 'root', '', 'amancom_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$query = "
    SELECT 
        SIM_num,
        Serial_no,
        Type,
        company_name,
        Price,
        Service_Provider,
        Is_Sold,
        Year,
        Month,
        Day
    FROM 
        sim_card
    WHERE 1=1
";

if (isset($_GET['Is_Sold']) && $_GET['Is_Sold'] !== 'all') {
    $Is_Sold = strtolower($conn->real_escape_string($_GET['Is_Sold'])); 
    if ($Is_Sold === 'sold' || $Is_Sold === 'available') {
        $query .= " AND `Is_Sold` = '$Is_Sold'";
    }
}

if (isset($_GET['type']) && $_GET['type'] !== 'all') {
    $type = $conn->real_escape_string($_GET['type']);
    $query .= " AND `Type` = '$type'";
}

if (isset($_GET['company-name']) && !empty($_GET['company-name'])) {
    $companyName = $conn->real_escape_string($_GET['company-name']);
    $query .= " AND `company_name` LIKE '%$companyName%'";
}

$result = $conn->query($query);

if (!$result) {
    die("Error in query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AMANCO</title>
    <link rel="stylesheet" href="../Style/lines_style.css">
    <script src="../JS/Add_customer.js" defer></script>
</head>
<body>
    <header>
        <div class="logo">AMANCOM</div>
        <nav>
            <ul>
                <li><a href="Dashboard.php">Dashboard</a></li>
                <li><a href="Line_Manegement.php" class="active">Line Inventory</a></li>
                <li><a href="add_line.php">Add Line</a></li>
                <li><a href="add_customer.php">Add Customer</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="filter-options">
            <h2>Filter Options</h2>
            <form method="GET" action="Line_Manegement.php">
                <div>
                    <label for="Is_Sold">Filter by Sale Status:</label>
                    <select id="Is_Sold" name="Is_Sold">
                        <option value="all">ALL</option>
                        <option value="available">Available</option>
                        <option value="sold">Sold</option>
                    </select>
                </div>

                <div>
                    <label for="type">Filter by Type:</label>
                    <select id="type" name="type">
                        <option value="all">ALL</option>
                        <option value="voice">Voice</option>
                        <option value="data">Data</option>
                    </select>
                </div>

                <div>
                    <label for="company-name">Search by Company Name:</label>
                    <input type="text" id="company-name" name="company-name" placeholder="Enter Company Name">
                </div>

                <button type="submit">Apply Filters</button>
            </form>
        </section>

        <section class="line-list">
            <h2>View Line List</h2>
            <table>
                <thead>
                    <tr>
                        <th>SIM num</th>
                        <th>Serial Number</th>
                        <th>Type</th>
                        <th>Company Name</th>
                        <th>Price</th>
                        <th>Service Provider</th>
                        <th>Sale Status</th>
                        <th>Sale Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['SIM_num']) ?></td>
                            <td><?= htmlspecialchars($row['Serial_no']) ?></td>
                            <td><?= htmlspecialchars($row['Type']) ?></td>
                            <td><?= htmlspecialchars($row['company_name']) ?></td>
                            <td><?= htmlspecialchars($row['Price']) ?></td>
                            <td><?= htmlspecialchars($row['Service_Provider']) ?></td>
                            <td><?= htmlspecialchars($row['Is_Sold']) ?></td>
                            <td>
                                <?php if (strtolower($row['Is_Sold']) === 'sold'): ?>
                                    <?= htmlspecialchars($row['Day']) ?>/<?= htmlspecialchars($row['Month']) ?>/<?= htmlspecialchars($row['Year']) ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2023 AMANCOM. All rights reserved.</p>
        <p>Contact us: amancom@amancom.com</p>
    </footer>

    <?php
    $conn->close();
    ?>
</body>
</html>
