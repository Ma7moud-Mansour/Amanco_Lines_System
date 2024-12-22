<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amancom_db";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// معالجة إضافة الشريحة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_line'])) {
    $sim_number = $_POST['sim_number'];
    $serial_no = $_POST['serial_no'];
    $service_provider = $_POST['service_provider'];
    $storage_or_sell = $_POST['storage_or_sell'];
    $company_name = $_POST['company_name'] ?? null;

    // التحقق من أن الرقم فريد
    $check_sim_query = "SELECT * FROM sim_card WHERE SIM_num = ? UNION SELECT * FROM sim_card WHERE SIM_num = ?";
    $stmt = $conn->prepare($check_sim_query);
    $stmt->bind_param("ss", $sim_number, $sim_number);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $error_message = "This SIM Number already exists. Please use a unique SIM number.";
    }
    $stmt->close();

    // التحقق من أن السيريال فريد
    if (empty($error_message)) {
        $check_serial_query = "SELECT * FROM sim_card WHERE Serial_no = ? UNION SELECT * FROM sim_card WHERE Serial_no = ?";
        $stmt = $conn->prepare($check_serial_query);
        $stmt->bind_param("ss", $serial_no, $serial_no);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error_message = "This SIM Serial Number already exists. Please use a unique serial number.";
        }
        $stmt->close();
    }

    // إذا كان "Sell"، تحقق من وجود اسم الشركة في قاعدة البيانات
    if ($storage_or_sell === 'sell' && empty($error_message)) {
        $check_company_query = "SELECT * FROM client_company WHERE Name = ?";
        $stmt = $conn->prepare($check_company_query);
        $stmt->bind_param("s", $company_name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $error_message = "The company name does not exist in the database. <br> Please Add this Client at first.";
        }
        $stmt->close();
    }

    // إذا لم يكن هناك أي خطأ، قم بالإدخال
    if (empty($error_message)) {
        if ($storage_or_sell === 'sell') {
            $stmt = $conn->prepare("INSERT INTO sim_card (SIM_num, Serial_no, Service_Provider, Is_Sold, Company_Name) VALUES (?, ?, ?, 'sold', ?)");
            $stmt->bind_param("ssss", $sim_number, $serial_no, $service_provider, $company_name);
        } else {
            $stmt = $conn->prepare("INSERT INTO sim_card (SIM_num, Serial_no, Service_Provider, Is_Sold) VALUES (?, ?, ?, 'Available')");
            $stmt->bind_param("sss", $sim_number, $serial_no, $service_provider);
        }

        if ($stmt->execute()) {
            $success_message = "Line added successfully!";
        } else {
            $error_message = "Error adding line: " . $conn->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Add Line</title>
<link rel="stylesheet" href="../Style/add_line.css" />
</head>
<body>
<header>
    <h1>Add New Line</h1>
    <nav>
        <a href="#">Home</a>
        <a href="#" class="active">Add Line</a>
        <a href="#">View Inventory</a>
    </nav>
</header>
<div class="container">
    <?php if (isset($success_message)): ?>
        <p style="color: green;"><?php echo $success_message; ?></p>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <form method="POST">
        <label for="sim_number">SIM Number</label>
        <input type="text" id="sim_number" name="sim_number" required />

        <label for="serial_no">Serial Number</label>
        <input type="text" id="serial_no" name="serial_no" required />

        <label for="service_provider">Service Provider</label>
        <select id="service_provider" name="service_provider" required>
            <option value="Vodafone">Vodafone</option>
            <option value="Etisalat">Etisalat</option>
            <option value="Orange">Orange</option>
            <option value="WE">WE</option>
        </select>
        
        <div class="form-group">
            <label for="Is_Sold">Storage or Sell</label>
            <select id="Is_Sold" name="storage_or_sell" class="choose-server" required>
                <option value="store">Stored</option>
                <option value="sell">Sell</option>
            </select>
        </div>

        <div class="form-group" id="company-name-container" style="display: none;">
            <label for="company_name">Company Name</label>
            <input
                type="text"
                id="company_name"
                name="company_name"
                placeholder="Enter Company Name"
                class="choose-server"
            />
        </div>

        <button type="submit" name="add_line">Add Line</button>
    </form>
</div>

<script>
    const Is_SoldSelect = document.getElementById("Is_Sold");
    const companyNameContainer = document.getElementById("company-name-container");
    const companyNameInput = document.getElementById("company_name");

    Is_SoldSelect.addEventListener("change", function () {
        if (Is_SoldSelect.value === "sell") {
            companyNameContainer.style.display = "block";
            companyNameInput.setAttribute("required", "required");
        } else {
            companyNameContainer.style.display = "none";
            companyNameInput.removeAttribute("required");
            companyNameInput.value = "";
        }
    });
</script>
</body>
</html>
