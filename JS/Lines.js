// Select DOM elements
const filterButton = document.getElementById("apply-filters");
const tableRows = document.querySelectorAll("tbody tr");

// Function to apply filters
function applyFilters() {
    // Get selected filter values
    const statusFilter = document.getElementById("status").value.toLowerCase();
    const typeFilter = document.getElementById("type").value.toLowerCase();
    const companyCodeFilter = document.getElementById("company-code").value.toLowerCase().trim();

    // Loop through table rows
    tableRows.forEach((row) => {
        // Get row data
        const status = row.cells[6].textContent.toLowerCase();
        const type = row.cells[2].textContent.toLowerCase();
        const companyCode = row.cells[3].textContent.toLowerCase();

        // Check if row matches filters
        const matchesStatus = statusFilter === "all" || status === statusFilter;
        const matchesType = typeFilter === "all" || type === typeFilter;
        const matchesCompanyCode = companyCodeFilter === "" || companyCode.includes(companyCodeFilter);

        // Show or hide row based on filters
        if (matchesStatus && matchesType && matchesCompanyCode) {
            row.style.display = ""; // Show row
        } else {
            row.style.display = "none"; // Hide row
        }
    });
}


// Event listener for filter button
filterButton.addEventListener("click", applyFilters);
