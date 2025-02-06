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
    $Serial_no = $_GET['Serial_no'] ?? ''; // استلام السيريال من الصفحة السابقة
    $Odoo_SO = $_POST['Odoo_SO'] ?? '';
    $Programm_Name = $_POST['Programm_Name'] ?? '';
    $Subscription_Period = $_POST['Subscription_Period'] ?? '';
    $Subscription_Price = $_POST['Subscription_Price'] ?? '';
    $sub_Day = $_POST['sub_Day'] ?? '';
    $sub_Month = $_POST['sub_Month'] ?? '';
    $sub_Year = $_POST['sub_Year'] ?? '';

    // Check serial no
    if (!empty($Serial_no)) {
        $check_device_query = "SELECT * FROM device WHERE Serial_no = ?";
        $stmt = $conn->prepare($check_device_query);
        $stmt->bind_param("s", $Serial_no);
        $stmt->execute();
        $device_result = $stmt->get_result();

        if ($device_result->num_rows === 0) {
            echo "<script>alert('Device Serial Number does not exist in the database!');</script>";
            exit;
        }
    }

    //Insert data
    $insert_subscription_query = "INSERT INTO server_subscription 
                                    (Serial_no, Odoo_SO, Programm_Name, Subscription_Period, Subscription_Price, sub_Day, sub_Month, sub_Year) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_subscription_query);
    $stmt->bind_param("ssssssii", $Serial_no, $Odoo_SO, $Programm_Name, $Subscription_Period, $Subscription_Price, $sub_Day, $sub_Month, $sub_Year);

    if ($stmt->execute()) {
        echo "<script>alert('Subscription added successfully!'); window.location.href = 'next_page.php';</script>";
    } else {
        echo "<script>alert('Failed to add subscription.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Subscription</title>
  <link rel="stylesheet" href="../Style/add_subscription.css">
  <link rel="stylesheet" href="../Style/navbar.css">
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="container">
    <header>
      <h1>Add Subscription</h1>
    </header>
    <main>
      <section>
        <form action="add_subscription.php?Serial_no=<?php echo urlencode($Serial_no); ?>" method="POST">
          <div class="form-group">
            <label for="Odoo_SO">Odoo SO</label>
            <input type="text" id="Odoo_SO" name="Odoo_SO" required>
          </div>
          <div class="form-group">
            <label for="Programm_Name">Program Name</label>
            <input type="text" id="Programm_Name" name="Programm_Name" required>
          </div>
          <div class="form-group">
            <label for="Subscription_Period">Subscription Period</label>
            <input type="text" id="Subscription_Period" name="Subscription_Period" required>
          </div>
          <div class="form-group">
            <label for="Subscription_Price">Subscription Price</label>
            <input type="text" id="Subscription_Price" name="Subscription_Price" required>
          </div>
          <div class="form-group">
            <label for="sub_Day">Subscription Day</label>
            <input type="number" id="sub_Day" name="sub_Day" required>
          </div>
          <div class="form-group">
            <label for="sub_Month">Subscription Month</label>
            <input type="number" id="sub_Month" name="sub_Month" required>
          </div>
          <div class="form-group">
            <label for="sub_Year">Subscription Year</label>
            <input type="number" id="sub_Year" name="sub_Year" required>
          </div>
          <button type="submit">Submit</button>
        </form>
      </section>
    </main>
  </div>
</body>
</html>
