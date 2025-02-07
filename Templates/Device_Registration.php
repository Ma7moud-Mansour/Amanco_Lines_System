<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "amancom_db";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Check if the table 'devices' exists
$table_check_query = "SHOW TABLES LIKE 'devices'";
$table_check_result = $conn->query($table_check_query);

if ($table_check_result->num_rows == 0) {
    // Create the table if it doesn't exist with all required fields
    $create_table_query = "CREATE TABLE devices (
        id INT AUTO_INCREMENT PRIMARY KEY,
        Serial_no VARCHAR(255) NOT NULL UNIQUE,
        Device_type ENUM('GT06N', 'AT4', 'TR', 'OBD') NOT NULL,
        SIM_Serial_no VARCHAR(255),
        Company_name VARCHAR(255) NOT NULL,
        Odoo_SO VARCHAR(255),
        Programme_Name VARCHAR(255),
        Subscription_Duration INT,
        Subscription_Price DECIMAL(10,2),
        Subscription_Date DATE,
        Remaining_Days INT,
        FOREIGN KEY (SIM_Serial_no) REFERENCES sim_card(Serial_no),
        FOREIGN KEY (Company_name) REFERENCES client_company(Name)
    )";
    
    if ($conn->query($create_table_query) === FALSE) {
        die("Error creating table: " . $conn->error);
    }
}

// Add column if it doesn't exist
$columns_to_add = [
    "Odoo_SO" => "VARCHAR(255)",
    "Programme_Name" => "VARCHAR(255)",
    "Subscription_Duration" => "INT",
    "Subscription_Price" => "DECIMAL(10,2)",
    "Subscription_Date" => "DATE",
    "Remaining_Days" => "INT"
];

foreach ($columns_to_add as $column => $type) {
    $check_column = "SHOW COLUMNS FROM `devices` LIKE '$column'";
    $column_exists = $conn->query($check_column);
    if ($column_exists->num_rows == 0) {
        $add_column = "ALTER TABLE `devices` ADD `$column` $type";
        $conn->query($add_column);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $serial_no = $_POST['Serial_no'];
    $device_type = $_POST['Device_type'];
    $sim_serial_no = $_POST['SIM_Serial_no'];
    $company_name = $_POST['company_name'];
    $odoo_so = $_POST['Odoo_SO'];
    $programme_name = $_POST['Programme_Name'];
    $subscription_duration = $_POST['Subscription_Duration'];
    $subscription_price = $_POST['Subscription_Price'];
    $subscription_date = date('Y-m-d', strtotime($_POST['Subscription_Date']));

    // Validate required fields
    if (empty($serial_no)) {
        $message = "Error: Serial Number is required.";
    } elseif (empty($device_type)) {
        $message = "Error: Device Type is required.";
    } elseif (empty($company_name)) {
        $message = "Error: Company Name is required.";
    } elseif (empty($odoo_so)) {
        $message = "Error: Odoo SO is required.";
    } elseif (empty($programme_name)) {
        $message = "Error: Programme Name is required.";
    } elseif (empty($subscription_duration)) {
        $message = "Error: Subscription Duration is required.";
    } elseif (empty($subscription_price)) {
        $message = "Error: Subscription Price is required.";
    } elseif (empty($subscription_date)) {
        $message = "Error: Subscription Date is required.";
    } else {
        // Validate serial number uniqueness
        $check_serial = $conn->prepare("SELECT Serial_no FROM devices WHERE Serial_no = ?");
        $check_serial->bind_param("s", $serial_no);
        $check_serial->execute();
        if ($check_serial->get_result()->num_rows > 0) {
            $message = "Error: Device serial number already exists!";
        } else {
            // Insert new device with subscription
            $stmt = $conn->prepare("INSERT INTO devices (
                Serial_no, Device_type, SIM_Serial_no, 
                Company_name, Odoo_SO, Programme_Name, 
                Subscription_Duration, Subscription_Price, Subscription_Date
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->bind_param("ssssssisd",
                $serial_no,
                $device_type,
                $sim_serial_no,
                $company_name,
                $odoo_so,
                $programme_name,
                $subscription_duration,
                $subscription_price,
                $subscription_date
            );

            if ($stmt->execute()) {
                // Update SIM card status
                $update_sim = $conn->prepare("UPDATE sim_card SET Device_Serial = ? WHERE Serial_no = ?");
                $update_sim->bind_param("ss", $serial_no, $sim_serial_no);
                $update_sim->execute();
                
                $message = "Device registered successfully with subscription!";
            } else {
                $message = "Error: " . $conn->error;
            }
        }
    }
}

// Fetch available SIM cards
$sims_query = "SELECT Serial_no, SIM_num FROM sim_card WHERE Device_Serial IS NULL AND Is_Sold = 'Available'";
$sims_result = $conn->query($sims_query);

// Fetch companies
$companies_query = "SELECT Name, Odoo_SO FROM client_company";
$companies_result = $conn->query($companies_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Registration</title>
    <link rel="stylesheet" href="../Style/device_registration.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <h1>Device Registration</h1>
        
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-section">
                <h2>Device Information</h2>
                <div class="form-group">
                    <label for="Serial_no">Device Serial Number*</label>
                    <input type="text" id="Serial_no" name="Serial_no" required>
                </div>

                <div class="form-group">
                    <label for="Device_type">Device Model*</label>
                    <select id="Device_type" name="Device_type" required>
                        <option value="">Select Device Type</option>
                        <option value="GT06N">GT06N</option>
                        <option value="AT4">AT4</option>
                        <option value="TR">TR</option>
                        <option value="OBD">OBD</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="SIM_Serial_no">SIM Card*</label>
                    <select id="SIM_Serial_no" name="SIM_Serial_no" required>
                        <option value="">Select SIM Card</option>
                        <?php while ($sim = $sims_result->fetch_assoc()): ?>
                            <option value="<?php echo $sim['Serial_no']; ?>">
                                <?php echo $sim['SIM_num'] . ' (' . $sim['Serial_no'] . ')'; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="company_name">Customer*</label>
                    <select id="company_name" name="company_name" required onchange="updateOdooSO(this)">
                        <option value="">Select Customer</option>
                        <?php while ($company = $companies_result->fetch_assoc()): ?>
                            <option value="<?php echo $company['Name']; ?>" data-odoo="<?php echo $company['Odoo_SO']; ?>">
                                <?php echo $company['Name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <input type="hidden" id="Odoo_SO" name="Odoo_SO">
            </div>

            <div class="form-section">
                <h2>Subscription Details</h2>
                <div class="form-group">
                    <label for="Programme_Name">Server Name*</label>
                    <select id="Programme_Name" name="Programme_Name" required>
                        <option value="">Select Server</option>
                        <option value="itrack">itrack</option>
                        <option value="fms">fms</option>
                        <option value="track solid">track solid</option>
                        <option value="pro track">pro track</option>
                        <option value="whats gps">whats gps</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="Subscription_Duration">Subscription Duration (Months)*</label>
                    <input type="number" id="Subscription_Duration" name="Subscription_Duration" min="1" required>
                </div>

                <div class="form-group">
                    <label for="Subscription_Price">Subscription Price*</label>
                    <input type="number" id="Subscription_Price" name="Subscription_Price" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="Subscription_Date">Subscription Date*</label>
                    <input type="date" id="Subscription_Date" name="Subscription_Date" 
                           value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>

            <button type="submit">Register Device</button>
        </form>
    </div>

    <script>
        function updateOdooSO(select) {
            const odooSO = select.options[select.selectedIndex].getAttribute('data-odoo');
            document.getElementById('Odoo_SO').value = odooSO;
        }
    </script>
</body>
</html>
