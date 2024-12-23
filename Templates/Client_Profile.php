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
$client_query = "SELECT * FROM client_company WHERE Code = $client_id";
$client_result = $conn->query($client_query);

// التحقق من وجود العميل
if ($client_result->num_rows > 0) {
    $client = $client_result->fetch_assoc();
} else {
    die("Client not found.");
}

// استعلام لجلب الشرايح المرتبطة بالعميل
$sims_query = "SELECT * FROM sim_card WHERE Company_Name = ?";
$stmt = $conn->prepare($sims_query);
$stmt->bind_param("s", $client['Name']);
$stmt->execute();
$sims_result = $stmt->get_result();

// // استعلام لجلب الأجهزة المرتبطة بالعميل
// $devices_query = "SELECT device.*, sim_card.SIM_num FROM device 
//                   LEFT JOIN sim_card ON device.SIM_Serial_no = sim_card.Serial_no 
//                   WHERE device.Company_Name = ?";
// $stmt = $conn->prepare($devices_query);
// $stmt->bind_param("s", $client['Name']);
// $stmt->execute();
// $devices_result = $stmt->get_result();
// ?>



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
                <a href="Dashboard.php">Dashboard</a>
                <a href="Customer_Management.php" class="active">Customer Management</a>
                <a href="Line_Manegement.php">Line Management</a>
            </nav>
            <button id="darkModeToggle">Dark Mode</button>
        </header>

        <!-- Main Content -->
        <main>
            <div class="profile">
                <!-- Client Details -->
                <section class="client-details">
                    <h2>Client Profile</h2>
                    <div class="details">
                        <p><strong>Client Name:</strong> <?php echo $client['Name']; ?></p>
                        <p><strong>Type:</strong> <?php echo $client['Class']; ?></p>
                        <p><strong>SO Code:</strong> <?php echo $client['Odoo_SO']; ?></p>
                        <p><strong>Client Numbers:</strong> <?php echo $client['Client_num']; ?></p>
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
                                <th>Service Provider</th>
                                <th>Device Serial</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($sim = $sims_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $sim['SIM_num']; ?></td>
                                    <td><?php echo $sim['Serial_no']; ?></td>
                                    <td><?php echo $sim['Year']; ?></td>
                                    <td><?php echo $sim['Service_Provider']; ?></td>
                                    <td><?php echo $sim['Device_Serial'] ?: '-'; ?></td>
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
    <script>
        // Dark Mode Toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;

        // Check LocalStorage for Dark Mode
        if (localStorage.getItem('darkMode') === 'enabled') {
            body.classList.add('dark-mode');
        }

        darkModeToggle.addEventListener('click', () => {
            if (body.classList.contains('dark-mode')) {
                body.classList.remove('dark-mode');
                localStorage.setItem('darkMode', 'disabled');
            } else {
                body.classList.add('dark-mode');
                localStorage.setItem('darkMode', 'enabled');
            }
        });
    </script>

</body>
</html>
