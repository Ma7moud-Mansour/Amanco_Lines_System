<?php
// إعداد اتصال قاعدة البيانات
$host = "localhost";
$username = "root";
$password = "";
$dbname = "amancom_db";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// التحقق من إرسال النموذج لإضافة جهاز
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Serial_no = $_POST['Serial_no'];
    $Device_type = $_POST['Device_type'];
    $SIM_Serial_no = empty($_POST['SIM_Serial_no']) ? null : $_POST['SIM_Serial_no'];
    $company_name = $_POST['company_name'];

    // التحقق من أن الرقم التسلسلي غير موجود في جدول الأجهزة
    $check_serial_query = "SELECT * FROM device WHERE Serial_no = ?";
    $stmt = $conn->prepare($check_serial_query);
    $stmt->bind_param("s", $Serial_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Device serial number already exists!');</script>";
    } else {
        // جلب معرف الشركة
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

            // التحقق من إدخال الرقم التسلسلي لـ SIM
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

            // إدخال الجهاز في جدول الأجهزة
            $insert_device_query = "INSERT INTO device (Serial_no, Device_type, SIM_Serial_no, client_id) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_device_query);
            $stmt->bind_param("sssi", $Serial_no, $Device_type, $SIM_Serial_no, $client_id);

            if ($stmt->execute()) {
                // الانتقال إلى صفحة الاشتراك
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
    <style>
        /* General Styles */
        html, body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .container {
            width: 90%;
            margin: auto;
            background-color: white;
            border-radius: 10px;
            padding: 10px 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            font-size: 24px;
            margin: 0;
        }

        main {
            margin-top: 20px;
        }

        section {
            margin-bottom: 20px;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            margin-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-size: 16px;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            background-color: #337cd1;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2859a9;
        }
    </style>
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
            // جلب قائمة الشركات
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
        <p>&copy; 2024 Your Company. All Rights Reserved.</p>
    </footer>
</div>
</body>
</html>
