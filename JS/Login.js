// إظهار/إخفاء كلمة المرور
const passwordInput = document.getElementById('password');
const showPassword = document.querySelector('.show-password');

showPassword.addEventListener('click', () => {
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
  } else {
    passwordInput.type = 'password';
  }
});