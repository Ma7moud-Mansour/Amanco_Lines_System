<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amancom_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$device_id = $_GET['id'];
$device_data = null;
$success_message = "";
$error_message = "";

// Fetch device data with current SIM card info
$device_query = "SELECT d.*, s.SIM_num, s.Is_Sold 
                FROM devices d 
                LEFT JOIN sim_card s ON d.SIM_Serial_no = s.Serial_no 
                WHERE d.id = ?";
$stmt = $conn->prepare($device_query);
$stmt->bind_param("i", $device_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $device_data = $result->fetch_assoc();
} else {
    $error_message = "Device not found.";
}

// Fetch available SIM cards plus currently assigned SIM
$sims_query = "SELECT Serial_no, SIM_num, Is_Sold 
               FROM sim_card 
               WHERE Is_Sold = 'Available' 
               OR Serial_no = ? 
               ORDER BY CASE WHEN Serial_no = ? THEN 0 ELSE 1 END";
$stmt = $conn->prepare($sims_query);
$stmt->bind_param("ss", $device_data['SIM_Serial_no'], $device_data['SIM_Serial_no']);
$stmt->execute();
$sims_result = $stmt->get_result();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serial_no = $_POST['serial_no'];
    $device_type = $_POST['device_type'];
    $new_sim_serial_no = $_POST['sim_serial_no'];
    $device_sim_no = $_POST['device_sim_no'];
    $company_name = $_POST['company_name'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Check if serial number exists (excluding current device)
        $check_serial = $conn->prepare("SELECT id FROM devices WHERE Serial_no = ? AND id != ?");
        $check_serial->bind_param("si", $serial_no, $device_id);
        $check_serial->execute();
        $serial_result = $check_serial->get_result();

        if ($serial_result->num_rows > 0) {
            throw new Exception("Error: Device serial number already exists!");
        }

        // Handle SIM card changes
        if ($device_data['SIM_Serial_no'] !== $new_sim_serial_no) {
            // Return old SIM to inventory
            if (!empty($device_data['SIM_Serial_no'])) {
                $return_sim = $conn->prepare("UPDATE sim_card 
                                           SET Device_Serial = NULL, 
                                               Is_Sold = 'Available',
                                               Company_Name = NULL 
                                           WHERE Serial_no = ?");
                $return_sim->bind_param("s", $device_data['SIM_Serial_no']);
                if (!$return_sim->execute()) {
                    throw new Exception("Failed to return old SIM to inventory.");
                }
            }

            // Assign new SIM and mark as sold
            if (!empty($new_sim_serial_no)) {
                $assign_sim = $conn->prepare("UPDATE sim_card 
                                           SET Device_Serial = ?,
                                               Is_Sold = 'sold',
                                               Company_Name = ? 
                                           WHERE Serial_no = ?");
                $assign_sim->bind_param("sss", $serial_no, $company_name, $new_sim_serial_no);
                if (!$assign_sim->execute()) {
                    throw new Exception("Failed to assign new SIM.");
                }
            }
        }

        // Update device data
        $update_query = "UPDATE devices 
                        SET Serial_no = ?, Device_type = ?, 
                            SIM_Serial_no = ?, Device_SIM_no = ?, 
                            Company_name = ? 
                        WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sssssi", $serial_no, $device_type, $new_sim_serial_no, 
                         $device_sim_no, $company_name, $device_id);

        if (!$stmt->execute()) {
            throw new Exception("Failed to update device data.");
        }

        $conn->commit();
        $success_message = "Device data updated successfully!";

        // Refresh device data
        $stmt = $conn->prepare($device_query);
        $stmt->bind_param("i", $device_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $device_data = $result->fetch_assoc();

    } catch (Exception $e) {
        $conn->rollback();
        $error_message = $e->getMessage();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Device</title>
    <link rel="stylesheet" href="../Style/device_profile.css">
    <link rel="stylesheet" href="../Style/navbar.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Edit Device</h1>

        <?php if (!empty($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <p style="color: green;"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <?php if ($device_data): ?>
            <form method="POST">
                <div class="form-group">
                    <label for="serial_no">Serial Number</label>
                    <input type="text" id="serial_no" name="serial_no" value="<?php echo $device_data['Serial_no']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="device_type">Device Type</label>
                    <select id="device_type" name="device_type" required>
                        <option value="GT06N" <?php echo ($device_data['Device_type'] === 'GT06N') ? 'selected' : ''; ?>>GT06N</option>
                        <option value="AT4" <?php echo ($device_data['Device_type'] === 'AT4') ? 'selected' : ''; ?>>AT4</option>
                        <option value="TR" <?php echo ($device_data['Device_type'] === 'TR') ? 'selected' : ''; ?>>TR</option>
                        <option value="OBD" <?php echo ($device_data['Device_type'] === 'OBD') ? 'selected' : ''; ?>>OBD</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sim_serial_no">SIM Card</label>
                    <select id="sim_serial_no" name="sim_serial_no">
                        <option value="">Select SIM Card</option>
                        <?php while ($sim = $sims_result->fetch_assoc()): ?>
                            <option value="<?php echo $sim['Serial_no']; ?>" 
                                    <?php echo ($device_data['SIM_Serial_no'] === $sim['Serial_no']) ? 'selected' : ''; ?>>
                                <?php echo $sim['SIM_num'] . ' (' . $sim['Serial_no'] . ')'; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="device_sim_no">Device SIM Number</label>
                    <input type="text" id="device_sim_no" name="device_sim_no" value="<?php echo $device_data['Device_SIM_no']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <input type="text" id="company_name" name="company_name" value="<?php echo $device_data['Company_name']; ?>" required>
                </div>
                <button type="submit">Update</button>
            </form>
        <?php else: ?>
            <p>Device data not available.</p>
        <?php endif; ?>
    </div>
</body>
</html>
