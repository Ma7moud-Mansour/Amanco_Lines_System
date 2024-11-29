// Array to hold all customers
let allCustomers = [
    { name: "Acme Corp", code: "AC123", type: "Large Company", server: "Server 01" },
    { name: "Beta Inc", code: "BI456", type: "Small Client", server: "Server 02" },
    { name: "Delta LLC", code: "DL789", type: "Large Company", server: "Server 03" },
];

// Variable to hold the current customer index for updates
let currentCustomerIndex = null;

// Function to populate the "All Customers" table
function populateAllCustomers() {
    const tableBody = document.getElementById("all-customers-list");
    tableBody.innerHTML = ""; // Clear any existing rows

    allCustomers.forEach((customer, index) => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${customer.name}</td>
            <td>${customer.code}</td>
            <td>${customer.type}</td>
            <td>${customer.server}</td>
        `;
        // Add click event to set "Current Customer"
        row.addEventListener("click", () => setCurrentCustomer(customer, index));
        tableBody.appendChild(row);
    });
}

// Function to set the current customer in the "Current Customer" table and populate the form
function setCurrentCustomer(customer, index) {
    document.getElementById("current-name").textContent = customer.name;
    document.getElementById("current-code").textContent = customer.code;
    document.getElementById("current-type").textContent = customer.type;
    document.getElementById("current-server").textContent = customer.server;

    // Populate form for update
    document.getElementById("name").value = customer.name;
    document.getElementById("odoo_so").value = customer.code;
    document.getElementById("server").value = customer.server;

    // Set the selected customer type
    const customerTypeRadio = document.querySelectorAll('input[name="type"]');
    customerTypeRadio.forEach(radio => {
        if (radio.value === customer.type) {
            radio.checked = true;
        }
    });

    // Save the index for updating later
    currentCustomerIndex = index;
}

// Function to add a new customer
function addCustomer() {
    const name = document.getElementById("name").value.trim();
    const odooSO = document.getElementById("odoo_so").value.trim();
    const server = document.getElementById("server").value.trim();
    const type = document.querySelector('input[name="type"]:checked');

    if (!name || !odooSO || !server || !type) {
        alert("Please fill out all fields and select a customer type.");
        return;
    }

    const newCustomer = {
        name: name,
        code: odooSO,
        type: type.value,
        server: server,
    };

    allCustomers.push(newCustomer); // Add the new customer to the array
    populateAllCustomers(); // Refresh the "All Customers" table
    clearForm(); // Clear the form fields
    alert("Customer added successfully!");
}

// Function to update an existing customer
function updateCustomer() {
    if (currentCustomerIndex === null) {
        alert("Please select a customer to update.");
        return;
    }

    const name = document.getElementById("name").value.trim();
    const odooSO = document.getElementById("odoo_so").value.trim();
    const server = document.getElementById("server").value.trim();
    const type = document.querySelector('input[name="type"]:checked');

    if (!name || !odooSO || !server || !type) {
        alert("Please fill out all fields and select a customer type.");
        return;
    }

    const updatedCustomer = {
        name: name,
        code: odooSO,
        type: type.value,
        server: server,
    };

    // Update the customer in the array
    allCustomers[currentCustomerIndex] = updatedCustomer;

    // Refresh the "All Customers" table
    populateAllCustomers();
    clearForm(); // Clear the form fields
    alert("Customer updated successfully!");
}

// Function to clear the form
function clearForm() {
    document.getElementById("customerForm").reset();
    currentCustomerIndex = null;
}

// Attach event listeners to buttons
document.getElementById("addCustomer").addEventListener("click", addCustomer);
document.getElementById("updateCustomer").addEventListener("click", updateCustomer);

// Initialize the page
populateAllCustomers();
