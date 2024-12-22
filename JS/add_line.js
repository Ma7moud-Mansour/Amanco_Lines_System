// تعريف العناصر المطلوبة
const statusSelect = document.getElementById("status");
const companyNameContainer = document.getElementById("company-name-container");
const companyNameInput = document.getElementById("company_name");

// حدث التغيير على القائمة المنسدلة
statusSelect.addEventListener("change", function () {
  if (statusSelect.value === "sell") {
    companyNameContainer.style.display = "block"; // عرض خانة اسم الشركة
    companyNameInput.setAttribute("required", "required"); // جعل الحقل مطلوبًا
  } else {
    companyNameContainer.style.display = "none"; // إخفاء خانة اسم الشركة
    companyNameInput.removeAttribute("required"); // إزالة كونه مطلوبًا
    companyNameInput.value = ""; // تفريغ قيمة الحقل
  }
});
