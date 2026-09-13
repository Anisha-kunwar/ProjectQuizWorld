 const ATTEMPTS_KEY = 'quizAttempts';

        const params = new URLSearchParams(window.location.search);
        const studentId = params.get('studentId') || '';
        const subjectFilter = params.get('subject') || '';

        function getAllAttempts() {
            const stored = localStorage.getItem(ATTEMPTS_KEY);
            return stored ? JSON.parse(stored) : [];
        }

        function formatTime(seconds) {
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;
            return `${String(h).padStart(2, '0')}h:${String(m).padStart(2, '0')}m:${String(s).padStart(2, '0')}s`;
        }

        function formatDate(dateStr) {
            const d = new Date(dateStr);
            if (isNaN(d)) return dateStr;
            return d.toLocaleString();
        }

        function renderAttempts() {
            const attempts = getAllAttempts();
            let studentAttempts = attempts.filter(a => a.studentId === studentId);

            if (subjectFilter) {
                studentAttempts = studentAttempts.filter(a => a.subject === subjectFilter);
            }

            if (studentAttempts.length > 0) {
                document.getElementById('studentTitle').textContent = studentAttempts[0].studentName;
                document.getElementById('studentContext').textContent =
                    `Student ID: ${studentAttempts[0].studentId} | Email: ${studentAttempts[0].studentEmail}`;
            } else {
                document.getElementById('studentTitle').textContent = 'Student Details';
                document.getElementById('studentContext').textContent = `Student ID: ${studentId}`;
            }

            const tbody = document.getElementById('attemptsBody');
            const emptyMessage = document.getElementById('emptyMessage');
            tbody.innerHTML = '';

            if (studentAttempts.length === 0) {
                emptyMessage.style.display = 'block';
                return;
            }
            emptyMessage.style.display = 'none';

            studentAttempts.forEach(a => {
                const row = document.createElement('tr');
                row.innerHTML = `
          <td>${a.subject}</td>
          <td>${a.set}</td>
          <td>${a.score} / ${a.totalQuestions}</td>
          <td>${formatTime(a.timeTakenSeconds)}</td>
          <td>${formatDate(a.dateAttempted)}</td>
        `;
                tbody.appendChild(row);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (!studentId) {
                alert('No student selected. Redirecting to User Details.');
                window.location.href = 'userDetails.html';
                return;
            }

            renderAttempts();

            document.getElementById('backBtn').addEventListener('click', () => {
                window.location.href = 'userDetails.html';
            });

            window.addEventListener('storage', (e) => {
                if (e.key === ATTEMPTS_KEY) {
                    renderAttempts();
                }
            });
        });
    
