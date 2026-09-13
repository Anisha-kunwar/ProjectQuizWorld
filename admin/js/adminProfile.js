const CREDENTIALS_KEY = 'adminCredentials';
    const DEFAULT_CREDENTIALS = {
      name: 'Admin',
      email: 'admin@quizapp.com',
      password: 'Admin@123'
    };

    function getCredentials() {
      const stored = localStorage.getItem(CREDENTIALS_KEY);
      if (stored) {
        try {
          return JSON.parse(stored);
        } catch (err) {
          return { ...DEFAULT_CREDENTIALS };
        }
      }
      return { ...DEFAULT_CREDENTIALS };
    }

    function saveCredentials(creds) {
      localStorage.setItem(CREDENTIALS_KEY, JSON.stringify(creds));
    }

    document.addEventListener('DOMContentLoaded', () => {
      const editNameBtn = document.getElementById('editNameBtn');
      const usernameInput = document.getElementById('username');
      const emailInput = document.getElementById('email');
      const changePasswordBtn = document.getElementById('changePasswordBtn');
      const passwordSection = document.getElementById('passwordSection');
      const saveBtn = document.getElementById('saveBtn');
      const passwordError = document.getElementById('passwordError');
      const nameSavedMsg = document.getElementById('nameSavedMsg');
      const logoutBtn = document.getElementById('logoutBtn');

      // Load current credentials into the form
      let credentials = getCredentials();
      usernameInput.value = credentials.name;
      emailInput.value = credentials.email;

      changePasswordBtn.addEventListener('click', () => {
        passwordSection.classList.toggle('hidden');
        if (!passwordSection.classList.contains('hidden')) {
          document.getElementById('current-password').focus();
        }
      });

      editNameBtn.addEventListener('click', () => {
        if (usernameInput.hasAttribute('readonly')) {
          usernameInput.removeAttribute('readonly');
          usernameInput.focus();
          editNameBtn.textContent = 'done';
          editNameBtn.style.color = '#2563eb';
        } else {
          usernameInput.setAttribute('readonly', 'true');
          editNameBtn.textContent = 'edit';
          editNameBtn.style.color = '#888';

          const newName = usernameInput.value.trim();
          if (newName && newName !== credentials.name) {
            credentials.name = newName;
            saveCredentials(credentials);
            nameSavedMsg.style.display = 'block';
            setTimeout(() => { nameSavedMsg.style.display = 'none'; }, 2000);
          }
        }
      });

      saveBtn.addEventListener('click', () => {
        const currentPass = document.getElementById('current-password').value;
        const newPass = document.getElementById('new-password').value;
        passwordError.style.display = 'none';

        if (!currentPass || !newPass) {
          passwordError.textContent = 'Please fill out both current and new password fields.';
          passwordError.style.display = 'block';
          return;
        }

        credentials = getCredentials();

        if (currentPass !== credentials.password) {
          passwordError.textContent = 'Current password is incorrect.';
          passwordError.style.display = 'block';
          return;
        }

        credentials.password = newPass;
        saveCredentials(credentials);

        alert('Password updated successfully!');
        document.getElementById('current-password').value = '';
        document.getElementById('new-password').value = '';
        passwordSection.classList.add('hidden');
      });

      logoutBtn.addEventListener('click', (e) => {
        e.preventDefault();
        localStorage.removeItem('currentUser');
        window.location.href = 'adminlogin.html';
      });
    });