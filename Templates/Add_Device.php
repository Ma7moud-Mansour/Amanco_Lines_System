<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "amancom_db";

$conn = new mysqli($host, $username, $password, $dbname);

$message = "";

// Check if the table 'devices' exists
$table_check_query = "SHOW TABLES LIKE 'devices'";
$table_check_result = $conn->query($table_check_query);

if ($table_check_result->num_rows == 0) {
    // Create the table if it doesn't exist
    $create_table_query = "CREATE TABLE devices (
        id INT AUTO_INCREMENT PRIMARY KEY,
        Serial_no VARCHAR(255) NOT NULL,
        Device_type VARCHAR(255) NOT NULL,
        SIM_Serial_no VARCHAR(255),
        Device_SIM_no VARCHAR(255) NOT NULL,
        Company_name VARCHAR(255) NOT NULL
    )";
    
    if ($conn->query($create_table_query) === FALSE) {
        die("Error creating table: " . $conn->error);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $serial_no = $_POST['Serial_no'];
    $device_type = $_POST['Device_type'];
    $sim_serial_no = $_POST['SIM_Serial_no'];
    $device_sim_no = $_POST['Device_SIM_no'];
    $company_name = $_POST['company_name'];

    $insert_query = "INSERT INTO devices (Serial_no, Device_type, SIM_Serial_no, Device_SIM_no, Company_name) VALUES ('$serial_no', '$device_type', '$sim_serial_no', '$device_sim_no', '$company_name')";

    if ($conn->query($insert_query) === TRUE) {
        $message = "Device added successfully!";
    } else {
        $message = "Error: " . $conn->error;
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
            <?php if ($message): ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>
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

            $companies_query = "SELECT Code, Name FROM client_company";
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
                    <label for="Device_SIM_no">Device SIM Number</label>
                    <input type="text" id="Device_SIM_no" name="Device_SIM_no" required>
                </div>
                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <select id="company_name" name="company_name" required>
                        <option value="">Select a Company</option>
                        <?php while ($company = $companies_result->fetch_assoc()): ?>
                            <option value="<?php echo $company['Name']; ?>">
                                <?php echo $company['Name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit">Submit and Next</button>
            </form>
            <?php $conn->close(); // Close the connection after fetching companies ?>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 AMANCOM. All Rights Reserved.</p>
    </footer>
</div>
</body>
</html>
