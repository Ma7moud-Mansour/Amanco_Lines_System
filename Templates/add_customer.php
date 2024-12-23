<?php
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "amancom_db";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_customer'])) {
    $name = $_POST['name'];
    $odoo_so = $_POST['odoo_so'];
    $type = $_POST['type'];
    $server = $_POST['server'];
    $sim_serial = $_POST['sim_serial'];
    $phone_number = isset($_POST['phone_number']) ? $_POST['phone_number'] : null;

    // BE SURE THE NUMBER FILS ISN'T EMPTY
    if (empty($phone_number)) {
        $error_message = "Error: Phone Number is required.";
    } else {
        // CHECK IF THE SERIAL IS ALREADE EXIST
        $check_sim_query = "SELECT * FROM sim_inventory WHERE Serial_no = ?";
        $stmt = $conn->prepare($check_sim_query);
        $stmt->bind_param("s", $sim_serial);
        $stmt->execute();
        $sim_result = $stmt->get_result();

        if ($sim_result->num_rows === 0) {
            $error_message = "Error: SIM Serial Number does not exist or is not available in SIM Inventory.";
        } else {
            // ODOO SO
            $odoo_check_query = "SELECT * FROM odoo WHERE Odoo_SO = ?";
            $stmt = $conn->prepare($odoo_check_query);
            $stmt->bind_param("s", $odoo_so);
            $stmt->execute();
            $odoo_result = $stmt->get_result();

            if ($odoo_result->num_rows === 0) {
                // INSERT ODOO DETAILS
                $insert_odoo_query = "INSERT INTO odoo (Odoo_SO) VALUES (?)";
                $stmt = $conn->prepare($insert_odoo_query);
                $stmt->bind_param("s", $odoo_so);
                $stmt->execute();
            }

            // IF EVERY THING IS GOOD
            $insert_query = "INSERT INTO client_company (Name, Odoo_SO, Class, Server_Name, SIM_Serial_no, Client_num) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("ssssss", $name, $odoo_so, $type, $server, $sim_serial, $phone_number);

            if ($stmt->execute()) {
                $success_message = "Customer added successfully!";
                // UPDATE 
                $update_sim_query = "UPDATE sim_inventory SET status = 'assigned' WHERE Serial_no = ?";
                $stmt = $conn->prepare($update_sim_query);
                $stmt->bind_param("s", $sim_serial);
                $stmt->execute();
            } else {
                $error_message = "Error adding customer: " . $conn->error;
            }
        }
    }
    if (isset($stmt)) {
        $stmt->close();
    }
}


$query = "SELECT * FROM client_company";
$result = $conn->query($query);

$conn->close();
?>






<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Customer Management</title>
    <link rel="stylesheet" href="../Style/Add_customer.css" />
  </head>
  <body>
    <div class="container">
      <!-- Header -->
      <header>
        <h1>AMANCOM</h1>
        <nav>
          <a href="Dashboard.php">Dashboard</a>
          <a href="#" class="active">Add Customer</a>
          <a href="Line_Manegement.php">Line Management</a>
        </nav>
      </header>

      <!-- Main Content -->
      <main>
        <div class="content">
          <!-- Add/Update Customers -->
          <section class="customer-form">
            <h2>Add/Update Customers</h2>
            <?php if (isset($success_message)): ?>
              <p style="color: green;"><?php echo $success_message; ?></p>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
              <p style="color: red;"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <form id="customerForm" method="POST">
              <label for="name">Name</label>
              <input type="text" id="name" name="name" required />

              <label for="odoo_so">Odoo SO</label>
              <input type="text" id="odoo_so" name="odoo_so" required />

              <!-- <label for="sim_serial">SIM Serial Number</label>
              <input type="text" id="sim_serial" name="sim_serial" required /> -->

              <label for="phone_number">Phone Number</label>
              <input type="text" id="phone_number" name="phone_number" required />

              <label>Customer Type</label>
              <div class="customer-type">
                  <input type="radio" id="large" name="type" value="Large Company" />
                  <label for="large">Large Company</label>
                  <input type="radio" id="small" name="type" value="Small Client" />
                  <label for="small">Small Client</label>
              </div>
              

              <div class="choose-server">
                  <label for="server">Associated Server</label>
                  <select id="server" name="server">
                      <option value="itrack">itrack</option>
                      <option value="fms">fms</option>
                      <option value="track solid">track solid</option>
                      <option value="pro track">pro track</option>
                      <option value="whats gps">whats gps</option>
                  </select>
              </div>


              <button type="submit" name="add_customer">Add Customer</button>
            </form>


          
          <?php if (isset($success_message)): ?>
              <p style="color: green;"><?php echo $success_message; ?></p>
          <?php endif; ?>
          <?php if (isset($error_message)): ?>
              <p style="color: red;"><?php echo $error_message; ?></p>
          <?php endif; ?>


          </section>

          <!-- All Customers List -->
          <section class="all-customers">
            <h2>All Customers</h2>
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Code</th>
                  <th>Customer Type</th>
                  <th>Associated Server</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($result->num_rows > 0): ?>
                  <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                      <td><?php echo $row['Name']; ?></td>
                      <td><?php echo $row['Odoo_SO']; ?></td>
                      <td><?php echo $row['Class']; ?></td>
                      <td><?php echo $row['Server_Name']; ?></td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="4">No customers found</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </section>
        </div>
      </main>
    </div>
    <footer>
      <p>&copy; 2023 Amancom. All rights reserved.</p>
      <p>Contact us: amancom@amancom.com</p>
    </footer>
  </body>
</html>
