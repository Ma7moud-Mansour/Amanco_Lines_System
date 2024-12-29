<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "amancom_db";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Serial_no = $_POST['Serial_no'];
    $Device_type = $_POST['Device_type'];
    $SIM_Serial_no = empty($_POST['SIM_Serial_no']) ? null : $_POST['SIM_Serial_no'];
    $company_name = $_POST['company_name'];

    // Check if the serial is unieq
    $check_serial_query = "SELECT * FROM device WHERE Serial_no = ?";
    $stmt = $conn->prepare($check_serial_query);
    $stmt->bind_param("s", $Serial_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Device serial number already exists!');</script>";
    } else {
        // Cpmany code
        $check_company_query = "SELECT Code FROM client_company WHERE name = ?";
        $stmt = $conn->prepare($check_company_query);
        $stmt->bind_param("s", $company_name);
        $stmt->execute();
        $company_result = $stmt->get_result();

        if ($company_result->num_rows === 0) {
            echo "<script>alert('Company name does not exist!');</script>";
        } else {
            $company_row = $company_result->fetch_assoc();
            $client_id = $company_row['Code'];

            //SIM Serial check
            if (!empty($SIM_Serial_no)) {
                $check_sim_query = "SELECT * FROM sim_card WHERE Serial_no = ?";
                $stmt = $conn->prepare($check_sim_query);
                $stmt->bind_param("s", $SIM_Serial_no);
                $stmt->execute();
                $sim_result = $stmt->get_result();

                if ($sim_result->num_rows === 0) {
                    echo "<script>alert('SIM Serial Number does not exist in the database!');</script>";
                    exit;
                }
            }

            // Insert the device
            $insert_device_query = "INSERT INTO device (Serial_no, Device_type, SIM_Serial_no, client_id) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_device_query);
            $stmt->bind_param("sssi", $Serial_no, $Device_type, $SIM_Serial_no, $client_id);

            if ($stmt->execute()) {
                // Go to Subscription page
                header("Location: add_subscription.php?Serial_no=" . urlencode($Serial_no));
                exit;
            } else {
                echo "<script>alert('Failed to add device.');</script>";
            }
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Device</title>
    <link rel="stylesheet" href="../Style/add_device.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Add New Device</h1>
    </header>
    <main>
        <section>
            <h2>Device Information</h2>
            <?php
            // Companies
            $host = "localhost";
            $username = "root";
            $password = "";
            $dbname = "amancom_db";
            $conn = new mysqli($host, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $companies_query = "SELECT Code, name FROM client_company";
            $companies_result = $conn->query($companies_query);
            ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="Serial_no">Serial Number</label>
                    <input type="text" id="Serial_no" name="Serial_no" required>
                </div>
                <div class="form-group">
                    <label for="Device_type">Device Type</label>
                    <input type="text" id="Device_type" name="Device_type" required>
                </div>
                <div class="form-group">
                    <label for="SIM_Serial_no">SIM Serial Number (Optional)</label>
                    <input type="text" id="SIM_Serial_no" name="SIM_Serial_no">
                </div>
                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <select id="company_name" name="company_name" required>
                        <option value="">Select a Company</option>
                        <?php while ($company = $companies_result->fetch_assoc()): ?>
                            <option value="<?php echo $company['name']; ?>">
                                <?php echo $company['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit">Submit and Next</button>
            </form>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 AMANCOM. All Rights Reserved.</p>
    </footer>
</div>
</body>
</html>
