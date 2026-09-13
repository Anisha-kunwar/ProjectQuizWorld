    function changeRole() {

    const role = document.getElementById("role").value;

    if (role === "admin") {

        window.location.href = "../../admin/html/login.html";

    }

}
    document.addEventListener('DOMContentLoaded', () => {
      const togglePasswordBtn = document.getElementById('togglePasswordBtn');
      const passwordInput = document.getElementById('password');
      const identifierInput = document.getElementById('identifier');
      const errorMsg = document.getElementById('errorMsg');
      const loginForm = document.getElementById('loginForm');

      if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener('click', () => {
          const isPassword = passwordInput.getAttribute('type') === 'password';
          passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
          togglePasswordBtn.textContent = isPassword ? 'Hide' : 'Show';
        });
      }

      if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
          e.preventDefault();

          const identifier = identifierInput.value.trim();
          const password = passwordInput.value;
          const rememberMe = document.getElementById('rememberMe').checked;

          errorMsg.style.display = 'none';

          if (rememberMe) {
            localStorage.setItem('rememberedEmail', identifier);
          } else {
            localStorage.removeItem('rememberedEmail');
          }

          window.location.href = `adminDashboard.html`;
        });
      }

      // Pre-fill remembered email, if any
      const rememberedEmail = localStorage.getItem('rememberedEmail');
      if (rememberedEmail) {
        identifierInput.value = rememberedEmail;
        document.getElementById('rememberMe').checked = true;
      }
    });
  