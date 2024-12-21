<?php
// اتصال بقاعدة البيانات
$conn = new mysqli('localhost', 'root', '', 'amancom_db');

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// جلب معرف العميل من الرابط
$client_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// استعلام لجلب بيانات العميل
$client_query = "SELECT * FROM clients WHERE id = $client_id";
$client_result = $conn->query($client_query);

// التحقق من وجود العميل
if ($client_result->num_rows > 0) {
    $client = $client_result->fetch_assoc();
} else {
    die("Client not found.");
}

// استعلام لجلب الشرايح المرتبطة بالعميل
$sims_query = "SELECT * FROM sim_cards WHERE client_id = $client_id";
$sims_result = $conn->query($sims_query);

// استعلام لجلب الأجهزة المرتبطة بالعميل
$devices_query = "SELECT devices.*, sim_cards.sim_number FROM devices 
                  LEFT JOIN sim_cards ON devices.sim_id = sim_cards.id 
                  WHERE devices.client_id = $client_id";
$devices_result = $conn->query($devices_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Profile</title>
    <link rel="stylesheet" href="../Style/Client_Profile.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header>
            <h1>AMANCOM</h1>
            <nav>
                <a href="Dashboard.html">Dashboard</a>
                <a href="Customer_Management.html" class="active">Customer Management</a>
                <a href="Line_Management.html">Line Management</a>
            </nav>
        </header>

        <!-- Main Content -->
        <main>
            <div class="profile">
                <!-- Client Details -->
                <section class="client-details">
                    <h2>Client Profile</h2>
                    <div class="details">
                        <p><strong>Client Name:</strong> <?php echo $client['name']; ?></p>
                        <p><strong>Type:</strong> <?php echo $client['type']; ?></p>
                        <p><strong>SO Code:</strong> <?php echo $client['so_code']; ?></p>
                        <p><strong>Client Numbers:</strong> <?php echo $client['phone_numbers']; ?></p>
                    </div>
                </section>

                <!-- SIM Cards List -->
                <section class="sim-list">
                    <h2>SIM Cards</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>SIM Number</th>
                                <th>Serial Number</th>
                                <th>Sale Date</th>
                                <th>Device Serial</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($sim = $sims_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $sim['sim_number']; ?></td>
                                    <td><?php echo $sim['serial_number']; ?></td>
                                    <td><?php echo $sim['sale_date']; ?></td>
                                    <td><?php echo $sim['device_serial'] ?: '-'; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </section>

                <!-- Devices List -->
                <section class="device-list">
                    <h2>Devices</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Device Serial</th>
                                <th>Device Type</th>
                                <th>Server Name</th>
                                <th>Subscription Date</th>
                                <th>Remaining Days</th>
                                <th>SIM Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($device = $devices_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $device['serial']; ?></td>
                                    <td><?php echo $device['type']; ?></td>
                                    <td><?php echo $device['server_name']; ?></td>
                                    <td><?php echo $device['subscription_date']; ?></td>
                                    <td><?php echo $device['remaining_days']; ?></td>
                                    <td><?php echo $device['sim_number'] ?: '-'; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </section>
            </div>
        </main>

        <footer>
            <p>&copy; 2023 Amancom. All rights reserved.</p>
            <p>Contact us: amancom@amancom.com</p>
        </footer>
    </div>
</body>
</html>