document.addEventListener('DOMContentLoaded', () => {
      const togglePasswordBtn = document.querySelector('.toggle-password');
      const passwordInput = document.getElementById('password');
      if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener('click', () => {
          const isPassword = passwordInput.getAttribute('type') === 'password';
          passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
          togglePasswordBtn.textContent = isPassword ? 'Hide' : 'Show';
        });
      }
      const loginForm = document.getElementById('loginForm');

      if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
          e.preventDefault();
          const password = passwordInput?.value;


          console.log('Logging in with:', { password });
          window.location.href = 'adminlogin.html';
        });
      }
    });
  