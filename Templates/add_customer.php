<?php
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "amancom_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add/Update Customer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_customer'])) {
        $name = $_POST['name'];
        $odoo_so = $_POST['odoo_so'];
        $type = $_POST['type'];
        $server = $_POST['server'];
        $sim_serial = $_POST['sim_serial'];
        $phone_number = $_POST['phone_number'];

        // Add or Update Customer
        $check_query = "SELECT * FROM client_company WHERE Odoo_SO = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("s", $odoo_so);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update Existing Customer
            $update_query = "UPDATE client_company SET Name = ?, Class = ?, Server_Name = ?, SIM_Serial_no = ?, Client_num = ? WHERE Odoo_SO = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ssssss", $name, $type, $server, $sim_serial, $phone_number, $odoo_so);
            $stmt->execute();
            $success_message = "Customer updated successfully!";
        } else {
            // Add New Customer
            $insert_query = "INSERT INTO client_company (Name, Odoo_SO, Class, Server_Name, SIM_Serial_no, Client_num) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("ssssss", $name, $odoo_so, $type, $server, $sim_serial, $phone_number);
            $stmt->execute();
            $success_message = "Customer added successfully!";
        }
        $stmt->close();
    }

    // Handle Delete Customer
    if (isset($_POST['delete_customer'])) {
        $odoo_so = $_POST['odoo_so'];
        $delete_query = "DELETE FROM client_company WHERE Odoo_SO = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("s", $odoo_so);
        $stmt->execute();
        $success_message = "Customer deleted successfully!";
        $stmt->close();
    }
}

// Fetch Customers
$query = "SELECT * FROM client_company";
$result = $conn->query($query);

// Fetch Customer for Edit
$edit_customer = null;
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit_customer'])) {
    $odoo_so = $_GET['edit_customer'];
    $edit_query = "SELECT * FROM client_company WHERE Odoo_SO = ?";
    $stmt = $conn->prepare($edit_query);
    $stmt->bind_param("s", $odoo_so);
    $stmt->execute();
    $edit_result = $stmt->get_result();
    if ($edit_result->num_rows > 0) {
        $edit_customer = $edit_result->fetch_assoc();
    }
    $stmt->close();
}

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
      <header>
        <h1>AMANCOM</h1>
        <nav>
          <a href="Dashboard.php">Dashboard</a>
          <a href="#" class="active">Add Customer</a>
          <a href="Line_Manegement.php">Line Management</a>
        </nav>
      </header>
      <main>
        <div class="content">
          <section class="customer-form">
            <h2>Add/Update Customers</h2>
            <?php if (isset($success_message)): ?>
              <p style="color: green;"><?php echo $success_message; ?></p>
            <?php endif; ?>
            <form id="customerForm" method="POST">
              <label for="name">Name</label>
              <input type="text" id="name" name="name" value="<?php echo $edit_customer['Name'] ?? ''; ?>" required />
              <label for="odoo_so">Odoo SO</label>
              <input type="text" id="odoo_so" name="odoo_so" value="<?php echo $edit_customer['Odoo_SO'] ?? ''; ?>" required <?php echo isset($edit_customer) ? 'readonly' : ''; ?> />
              <label for="sim_serial">SIM Serial Number</label>
              <input type="text" id="sim_serial" name="sim_serial" value="<?php echo $edit_customer['SIM_Serial_no'] ?? ''; ?>" required />
              <label for="phone_number">Phone Number</label>
              <input type="text" id="phone_number" name="phone_number" value="<?php echo $edit_customer['Client_num'] ?? ''; ?>" required />
              <label>Customer Type</label>
              <div class="customer-type">
                  <input type="radio" id="large" name="type" value="Large Company" <?php echo (isset($edit_customer) && $edit_customer['Class'] === 'Large Company') ? 'checked' : ''; ?> />
                  <label for="large">Large Company</label>
                  <input type="radio" id="small" name="type" value="Small Client" <?php echo (isset($edit_customer) && $edit_customer['Class'] === 'Small Client') ? 'checked' : ''; ?> />
                  <label for="small">Small Client</label>
              </div>
              <div class="choose-server">
                  <label for="server">Associated Server</label>
                  <select id="server" name="server">
                      <option value="itrack" <?php echo (isset($edit_customer) && $edit_customer['Server_Name'] === 'itrack') ? 'selected' : ''; ?>>itrack</option>
                      <option value="fms" <?php echo (isset($edit_customer) && $edit_customer['Server_Name'] === 'fms') ? 'selected' : ''; ?>>fms</option>
                      <option value="track solid" <?php echo (isset($edit_customer) && $edit_customer['Server_Name'] === 'track solid') ? 'selected' : ''; ?>>track solid</option>
                      <option value="pro track" <?php echo (isset($edit_customer) && $edit_customer['Server_Name'] === 'pro track') ? 'selected' : ''; ?>>pro track</option>
                      <option value="whats gps" <?php echo (isset($edit_customer) && $edit_customer['Server_Name'] === 'whats gps') ? 'selected' : ''; ?>>whats gps</option>
                  </select>
              </div>
              <button type="submit" name="add_customer">Add/Update Customer</button>
            </form>
          </section>
          <section class="all-customers">
            <h2>All Customers</h2>
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Code</th>
                  <th>Customer Type</th>
                  <th>Associated Server</th>
                  <th>Actions</th>
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
                      <td>
                        <a class="edit" href="?edit_customer=<?php echo $row['Odoo_SO']; ?>" style="color: blue;">Edit</a>
                        <form method="POST" style="display:inline;">
                          <input type="hidden" name="odoo_so" value="<?php echo $row['Odoo_SO']; ?>" />
                          <button type="submit" name="delete_customer" style="color: red;">Delete</button>
                        </form>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5">No customers found</td>
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
    </footer>
  </body>
</html>
