
    const QUESTIONS_KEY = 'questionsData';

    const params = new URLSearchParams(window.location.search);
    const currentSubject = params.get('subject') || '';
    const currentSet = params.get('set') || '';
    const editIndex = params.has('edit') ? parseInt(params.get('edit'), 10) : null;

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

    function updateContext() {
      if (editIndex !== null) {
        document.getElementById('formContext').textContent =
          `Subject: ${currentSubject} | Set: ${currentSet} | Editing Question #${editIndex + 1}`;
      } else {
        document.getElementById('formContext').textContent =
          `Subject: ${currentSubject} | Set: ${currentSet} | Question #${getQuestions().length + 1}`;
      }
    }

    function readFormAsQuestion() {
      const questionText = document.getElementById('questionText').value.trim();
      const options = [
        document.getElementById('optionText0').value.trim(),
        document.getElementById('optionText1').value.trim(),
        document.getElementById('optionText2').value.trim(),
        document.getElementById('optionText3').value.trim()
      ];
      const correctRadio = document.querySelector('input[name="correctAnswer"]:checked');

      if (!questionText || options.some(o => !o) || !correctRadio) {
        alert('Please fill in the question, all 4 options, and select the correct answer.');
        return null;
      }

      return { question: questionText, options: options, correct: parseInt(correctRadio.value, 10) };
    }

    document.addEventListener('DOMContentLoaded', () => {
      if (!currentSubject || !currentSet) {
        alert('No subject/set selected. Redirecting to Manage Questions.');
        window.location.href = 'manageQuestion.html';
        return;
      }

      // If editing, pre-fill form
      if (editIndex !== null) {
        const q = getQuestions()[editIndex];
        if (q) {
          document.getElementById('questionText').value = q.question;
          document.getElementById('optionText0').value = q.options[0];
          document.getElementById('optionText1').value = q.options[1];
          document.getElementById('optionText2').value = q.options[2];
          document.getElementById('optionText3').value = q.options[3];
          document.getElementById('correctRadio' + q.correct).checked = true;
        }
        document.getElementById('formTitle').textContent = 'Edit Question';
        document.getElementById('nextBtn').style.display = 'none';
        document.getElementById('saveBtn').textContent = 'Save Changes';
        document.getElementById('cancelBtn').style.display = 'inline-block';
      }

      updateContext();

      document.getElementById('backBtn').addEventListener('click', () => {
        window.location.href = 'manageQuestion.html';
      });

      document.getElementById('cancelBtn').addEventListener('click', () => {
        window.location.href = `viewQuestion.html?subject=${encodeURIComponent(currentSubject)}&set=${encodeURIComponent(currentSet)}`;
      });

      // Next: save and clear form for another question (only available when adding, not editing)
      document.getElementById('questionForm').addEventListener('submit', (e) => {
        e.preventDefault();
        const questionObj = readFormAsQuestion();
        if (!questionObj) return;

        const list = getQuestions();
        list.push(questionObj);
        saveQuestions(list);

        document.getElementById('questionForm').reset();
        updateContext();
      });

      // Save: save (new or edited) and go to View Questions
      document.getElementById('saveBtn').addEventListener('click', () => {
        const questionObj = readFormAsQuestion();
        if (!questionObj) return;

        const list = getQuestions();
        if (editIndex !== null) {
          list[editIndex] = questionObj;
        } else {
          list.push(questionObj);
        }
        saveQuestions(list);

        window.location.href = `viewQuestion.html?subject=${encodeURIComponent(currentSubject)}&set=${encodeURIComponent(currentSet)}`;
      });
    });
  