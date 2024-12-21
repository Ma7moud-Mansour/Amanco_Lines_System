<?php
// الاتصال بقاعدة البيانات
$conn = new mysqli('localhost', 'root', '', 'amancom_db');

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// إنشاء استعلام SQL الأساسي
$query = "SELECT * FROM `sim_card` WHERE 1=1";

// معالجة الفلاتر
if (isset($_GET['status']) && $_GET['status'] !== 'all') {
    $status = $conn->real_escape_string($_GET['status']);
    $query .= " AND `sale_status` = '$status'";
}

if (isset($_GET['type']) && $_GET['type'] !== 'all') {
    $type = $conn->real_escape_string($_GET['type']);
    $query .= " AND `type` = '$type'";
}

if (isset($_GET['company-code']) && !empty($_GET['company-code'])) {
    $companyCode = $conn->real_escape_string($_GET['company-code']);
    $query .= " AND `company_code` LIKE '%$companyCode%'";
}

// تنفيذ الاستعلام
$result = $conn->query($query);

// التحقق من النتيجة
if (!$result) {
    die("Error in query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InventoryTrackPro</title>
    <link rel="stylesheet" href="../Style/lines_style.css">
    <script src="../JS/Add_customer.js" defer></script>
</head>
<body>
    <header>
        <div class="logo">AMANCOM</div>
        <nav>
            <ul>
                <li><a href="#">Dashboard</a></li>
                <li><a href="Line_Manegement.php">Line Management</a></li>
                <li><a href="#">Customer Management</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="filter-options">
            <h2>Filter Options</h2>
            <form method="GET" action="Line_Manegement.php">
                <div>
                    <label for="status">Filter by Status:</label>
                    <select id="status" name="status">
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
                    <label for="company-code">Search by Company Code:</label>
                    <input type="text" id="company-code" name="company-code" placeholder="Enter Company Code">
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
                        <th>Company Code</th>
                        <th>Price</th>
                        <th>Service Provider</th>
                        <th>Sale Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['SIM_num']) ?></td>
                            <td><?= htmlspecialchars($row['Serial_no']) ?></td>
                            <td><?= htmlspecialchars($row['Type']) ?></td>
                            <td><?= htmlspecialchars($row['company_code']) ?></td>
                            <td><?= htmlspecialchars($row['Price']) ?></td>
                            <td><?= htmlspecialchars($row['Service_Provider']) ?></td>
                            <td><?= htmlspecialchars($row['Is_Sold']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2023 AMANCOM. All rights reserved.</p>
        <p>Contact us: amancmo@amancom.com</p>
    </footer>

    <?php
    // إغلاق الاتصال بقاعدة البيانات
    $conn->close();
    ?>
</body>
</html>
