const QUESTIONS_KEY = 'questionsData';

        const params = new URLSearchParams(window.location.search);
        const currentSubject = params.get('subject') || '';
        const currentSet = params.get('set') || '';

        function questionKey(subject, set) {
            return subject + '||' + set;
        }

        function getAllQuestions() {
            const stored = localStorage.getItem(QUESTIONS_KEY);
            return stored ? JSON.parse(stored) : {};
        }

        function saveAllQuestions(questionsObj) {
            localStorage.setItem(QUESTIONS_KEY, JSON.stringify(questionsObj));
        }

        function getQuestions() {
            const all = getAllQuestions();
            return all[questionKey(currentSubject, currentSet)] || [];
        }

        function saveQuestions(list) {
            const all = getAllQuestions();
            all[questionKey(currentSubject, currentSet)] = list;
            saveAllQuestions(all);
        }

        function renderQuestionsList() {
            document.getElementById('viewTitle').textContent = `Questions - ${currentSubject} (${currentSet})`;
            const list = getQuestions();
            const container = document.getElementById('questionsList');
            container.innerHTML = '';

            if (list.length === 0) {
                container.innerHTML = '<p class="empty-message">No questions added yet for this set.</p>';
                return;
            }

            list.forEach((q, index) => {
                const card = document.createElement('div');
                card.className = 'question-card';

                const optionsHtml = q.options.map((opt, i) =>
                    `<li class="${i === q.correct ? 'correct-option' : ''}">${opt}${i === q.correct ? ' &#10003;' : ''}</li>`
                ).join('');

                card.innerHTML = `
          <div class="question-card-header">
            <h3>Q${index + 1}. ${q.question}</h3>
            <div class="card-actions">
              <button type="button" class="action-btn edit-btn" data-index="${index}">Edit</button>
              <button type="button" class="action-btn delete-btn" data-index="${index}">Delete</button>
            </div>
          </div>
          <ul class="options-list">${optionsHtml}</ul>
        `;
                container.appendChild(card);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (!currentSubject || !currentSet) {
                alert('No subject/set selected. Redirecting to Manage Questions.');
                window.location.href = 'manageQuestion.html';
                return;
            }

            renderQuestionsList();

            document.getElementById('backBtn').addEventListener('click', () => {
                window.location.href = 'manageQuestion.html';
            });

            document.getElementById('addQuestionBtn').addEventListener('click', () => {
                window.location.href = `addQuestion.html?subject=${encodeURIComponent(currentSubject)}&set=${encodeURIComponent(currentSet)}`;
            });

            document.getElementById('questionsList').addEventListener('click', (e) => {
                const index = e.target.dataset.index;
                if (index === undefined) return;

                if (e.target.classList.contains('edit-btn')) {
                    window.location.href = `addQuestion.html?subject=${encodeURIComponent(currentSubject)}&set=${encodeURIComponent(currentSet)}&edit=${index}`;
                }

                if (e.target.classList.contains('delete-btn')) {
                    const confirmDelete = confirm('Are you sure you want to delete this question?');
                    if (confirmDelete) {
                        const list = getQuestions();
                        list.splice(parseInt(index, 10), 1);
                        saveQuestions(list);
                        renderQuestionsList();
                    }
                }
            });
        });
