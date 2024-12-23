
const statusSelect = document.getElementById("status");
const companyNameContainer = document.getElementById("company-name-container");
const companyNameInput = document.getElementById("company_name");

statusSelect.addEventListener("change", function () {
  if (statusSelect.value === "sell") {
    companyNameContainer.style.display = "block"; 
    companyNameInput.setAttribute("required", "required"); 
  } else {
    companyNameContainer.style.display = "none"; 
    companyNameInput.removeAttribute("required"); 
    companyNameInput.value = ""; 
  }
});
