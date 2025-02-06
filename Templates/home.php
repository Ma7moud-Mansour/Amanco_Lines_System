<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - AMANCOM</title>
    <link rel="stylesheet" href="../Style/home.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <main>
        <section class="intro">
            <h1>Welcome to AMANCOM</h1>
            <p>Your one-stop solution for managing lines, devices, and subscriptions.</p>
        </section>

        <section class="navigation">
            <div class="nav-item">
                <a href="Dashboard.php">
                    <img src="../photos/dashboard.png" alt="Dashboard">
                    <h2>Dashboard</h2>
                </a>
            </div>
            <div class="nav-item">
                <a href="Line_Manegement.php">
                    <img src="../photos/line_management.png" alt="Line Management">
                    <h2>Line Management</h2>
                </a>
            </div>
            <div class="nav-item">
                <a href="add_line.php">
                    <img src="../photos/add_line.png" alt="Add Line">
                    <h2>Add Line</h2>
                </a>
            </div>
            <div class="nav-item">
                <a href="add_customer.php">
                    <img src="../photos/add_customer.png" alt="Add Customer">
                    <h2>Add Customer</h2>
                </a>
            </div>
            <div class="nav-item">
                <a href="Add_Device.php">
                    <img src="../photos/add_device.png" alt="Add Device">
                    <h2>Add Device</h2>
                </a>
            </div>
            <div class="nav-item">
                <a href="server_subscription.php">
                    <img src="../photos/server_subscription.png" alt="Server Subscription">
                    <h2>Server Subscription</h2>
                </a>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2023 AMANCOM. All rights reserved.</p>
        <p>Contact us: amancom@amancom.com</p>
    </footer>
</body>
</html>
