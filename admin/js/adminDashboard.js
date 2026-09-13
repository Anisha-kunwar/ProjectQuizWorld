
    // ----- Temporary data layer (localStorage) -----
    // Once backend is ready, replace these getters with API calls.

    function getStat(key, fallback) {
      const value = localStorage.getItem(key);
      return value !== null ? value : fallback;
    }

    function loadDashboardStats() {
      document.getElementById('statAttempts').textContent = getStat('totalQuizAttempts', 150);
      document.getElementById('statStudents').textContent = getStat('totalStudents', 45);
      document.getElementById('statPassRate').textContent = getStat('totalPassRate', '79') + '%';
      document.getElementById('statVisitors').textContent = getStat('totalVisitors', 42);
    }

    // Load stats on page load
    document.addEventListener('DOMContentLoaded', loadDashboardStats);

    // Keep stats in sync if another tab/page updates localStorage
    window.addEventListener('storage', loadDashboardStats);
