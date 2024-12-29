<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amancom_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = "";
$success_message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $device_no_serial = $_POST['device_no_serial'];
    $odoo_so = $_POST['odoo_so'];
    $programme_name = $_POST['programme_name'];
    $subscription_duration = $_POST['subscription_duration'];
    $subscription_price = $_POST['subscription_price'];
    $subscription_date = $_POST['subscription_date'];

    // Check if device_no_serial exists in device table
    $check_device_query = "SELECT * FROM device WHERE device_no_serial = ?";
    $stmt = $conn->prepare($check_device_query);
    $stmt->bind_param("s", $device_no_serial);
    $stmt->execute();
    $device_result = $stmt->get_result();

    if ($device_result->num_rows == 0) {
        $error_message = "Device number serial does not exist.";
    } else {
        // Insert data into server_subscription
        $insert_query = "INSERT INTO server_subscription 
            (device_no_serial, odoo_so, programme_name, subscription_duration, subscription_price, subscription_date)
            VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sssids", $device_no_serial, $odoo_so, $programme_name, $subscription_duration, $subscription_price, $subscription_date);
        if ($stmt->execute()) {
            $success_message = "Subscription added successfully!";
        } else {
            $error_message = "Failed to add subscription. Please try again.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Subscription</title>
    <link rel="stylesheet" href="../Style/server_subscription.css" />
</head>
<body>
    <div class="container">
        <h1>Server Subscription</h1>

        <?php if (!empty($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <p style="color: green;"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="device_no_serial">Device No Serial</label>
                <input type="text" name="device_no_serial" id="device_no_serial" required>
            </div>
            <div class="form-group">
                <label for="odoo_so">Odoo SO</label>
                <input type="text" name="odoo_so" id="odoo_so" required>
            </div>
            <div class="form-group">
                <label for="programme_name">Programme Name</label>
                <input type="text" name="programme_name" id="programme_name" required>
            </div>
            <div class="form-group">
                <label for="subscription_duration">Subscription Duration (Months)</label>
                <input type="number" name="subscription_duration" id="subscription_duration" required>
            </div>
            <div class="form-group">
                <label for="subscription_price">Subscription Price</label>
                <input type="number" step="0.01" name="subscription_price" id="subscription_price" required>
            </div>
            <div class="form-group">
                <label for="subscription_date">Subscription Date</label>
                <input type="date" name="subscription_date" id="subscription_date" required>
            </div>
            <button type="submit">Add Subscription</button>
        </form>
    </div>
</body>
</html>
