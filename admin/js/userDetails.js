const CATEGORY_KEY = 'categories';
    const ATTEMPTS_KEY = 'quizAttempts';

    function getCategories() {
      const stored = localStorage.getItem(CATEGORY_KEY);
      return stored ? JSON.parse(stored) : [];
    }

    function getAllAttempts() {
      const stored = localStorage.getItem(ATTEMPTS_KEY);
      return stored ? JSON.parse(stored) : [];
    }

    function populateSubjectFilter() {
      const select = document.getElementById('subjectFilter');
      const categories = getCategories();
      select.innerHTML = '<option value="">-- All Subjects --</option>';
      categories.forEach(subject => {
        const opt = document.createElement('option');
        opt.value = subject;
        opt.textContent = subject;
        select.appendChild(opt);
      });
    }

    function renderRecords(subjectFilter) {
      const attempts = getAllAttempts();
      const filtered = subjectFilter
        ? attempts.filter(a => a.subject === subjectFilter)
        : attempts;

      // Group attempts by student (studentId)
      const studentsMap = {};
      filtered.forEach(a => {
        if (!studentsMap[a.studentId]) {
          studentsMap[a.studentId] = {
            studentId: a.studentId,
            studentName: a.studentName,
            studentEmail: a.studentEmail,
            attemptCount: 0
          };
        }
        studentsMap[a.studentId].attemptCount++;
      });

      const students = Object.values(studentsMap);
      const tbody = document.getElementById('recordsBody');
      const emptyMessage = document.getElementById('emptyMessage');
      tbody.innerHTML = '';

      if (students.length === 0) {
        emptyMessage.style.display = 'block';
        return;
      }
      emptyMessage.style.display = 'none';

      students.forEach(student => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${student.studentId}</td>
          <td>${student.studentName}</td>
          <td>${student.studentEmail}</td>
          <td>${student.attemptCount}</td>
          <td><a href="studentDetails.html?studentId=${encodeURIComponent(student.studentId)}&subject=${encodeURIComponent(subjectFilter)}" class="view-link">View Details</a></td>
        `;
        tbody.appendChild(row);
      });
    }

    document.addEventListener('DOMContentLoaded', () => {
      populateSubjectFilter();
      renderRecords('');

      document.getElementById('subjectFilter').addEventListener('change', (e) => {
        renderRecords(e.target.value);
      });

      // Refresh automatically if a student completes a quiz in another tab
      window.addEventListener('storage', (e) => {
        if (e.key === ATTEMPTS_KEY || e.key === CATEGORY_KEY) {
          populateSubjectFilter();
          renderRecords(document.getElementById('subjectFilter').value);
        }
      });

      // Also refresh when this tab regains focus
      window.addEventListener('focus', () => {
        renderRecords(document.getElementById('subjectFilter').value);
      });
    });