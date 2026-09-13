const CATEGORY_KEY = 'categories';
    const SETS_KEY = 'questionSets';
    const TIMERS_KEY = 'setTimers';

    let currentSubject = '';
    let currentSet = '';

    function getCategories() {
      const stored = localStorage.getItem(CATEGORY_KEY);
      return stored ? JSON.parse(stored) : ['Subject 1', 'Subject 2', 'Subject 3', 'Subject 4'];
    }

    function getAllSets() {
      const stored = localStorage.getItem(SETS_KEY);
      return stored ? JSON.parse(stored) : {};
    }

    function saveAllSets(setsObj) {
      localStorage.setItem(SETS_KEY, JSON.stringify(setsObj));
    }

    function getSetsForSubject(subject) {
      const allSets = getAllSets();
      return allSets[subject] || [];
    }

    function questionKey(subject, set) {
      return subject + '||' + set;
    }

    function getAllTimers() {
      const stored = localStorage.getItem(TIMERS_KEY);
      return stored ? JSON.parse(stored) : {};
    }

    function saveAllTimers(timersObj) {
      localStorage.setItem(TIMERS_KEY, JSON.stringify(timersObj));
    }

    function getTimer(subject, set) {
      const all = getAllTimers();
      return all[questionKey(subject, set)] || { hr: 0, min: 0, sec: 0 };
    }

    function saveTimer(subject, set, timerObj) {
      const all = getAllTimers();
      all[questionKey(subject, set)] = timerObj;
      saveAllTimers(all);
    }

    function populateSubjectDropdown() {
      const select = document.getElementById('subjectSelect');
      const categories = getCategories();
      select.innerHTML = '<option value="">-- Select Subject --</option>';
      categories.forEach(subject => {
        const opt = document.createElement('option');
        opt.value = subject;
        opt.textContent = subject;
        select.appendChild(opt);
      });
    }

    function populateSetDropdown(subject) {
      const select = document.getElementById('setSelect');
      select.innerHTML = '<option value="">-- Select Set --</option>';

      if (!subject) {
        select.disabled = true;
        return;
      }

      select.disabled = false;
      const sets = getSetsForSubject(subject);
      sets.forEach(setName => {
        const opt = document.createElement('option');
        opt.value = setName;
        opt.textContent = setName;
        select.appendChild(opt);
      });

      const addOpt = document.createElement('option');
      addOpt.value = '__new__';
      addOpt.textContent = '+ Add New Set';
      select.appendChild(addOpt);
    }

    function showTimerAndActions() {
      const timer = getTimer(currentSubject, currentSet);
      document.getElementById('timerHr').value = timer.hr;
      document.getElementById('timerMin').value = timer.min;
      document.getElementById('timerSec').value = timer.sec;
      document.getElementById('timerSavedMsg').style.display = 'none';
      document.getElementById('timerRow').style.display = 'block';
      document.getElementById('actionButtons').style.display = 'flex';
    }

    function hideTimerAndActions() {
      document.getElementById('timerRow').style.display = 'none';
      document.getElementById('actionButtons').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
      populateSubjectDropdown();

      const subjectSelect = document.getElementById('subjectSelect');
      const setSelect = document.getElementById('setSelect');

      subjectSelect.addEventListener('change', () => {
        currentSubject = subjectSelect.value;
        currentSet = '';
        populateSetDropdown(currentSubject);
        hideTimerAndActions();
      });

      setSelect.addEventListener('change', () => {
        if (setSelect.value === '__new__') {
          const newSetName = prompt('Enter new set name (e.g., Set 1):');
          if (newSetName && newSetName.trim() !== '') {
            const allSets = getAllSets();
            if (!allSets[currentSubject]) allSets[currentSubject] = [];
            allSets[currentSubject].push(newSetName.trim());
            saveAllSets(allSets);
            populateSetDropdown(currentSubject);
            setSelect.value = newSetName.trim();
            currentSet = newSetName.trim();
            showTimerAndActions();
          } else {
            setSelect.value = '';
            currentSet = '';
            hideTimerAndActions();
          }
        } else {
          currentSet = setSelect.value;
          if (currentSet) {
            showTimerAndActions();
          } else {
            hideTimerAndActions();
          }
        }
      });

      document.getElementById('saveTimerBtn').addEventListener('click', () => {
        const hr = parseInt(document.getElementById('timerHr').value, 10) || 0;
        const min = parseInt(document.getElementById('timerMin').value, 10) || 0;
        const sec = parseInt(document.getElementById('timerSec').value, 10) || 0;

        if (hr === 0 && min === 0 && sec === 0) {
          alert('Please set a time greater than 00hr:00min:00sec.');
          return;
        }

        saveTimer(currentSubject, currentSet, { hr, min, sec });
        document.getElementById('timerSavedMsg').style.display = 'block';
      });

      document.getElementById('addQuestionsBtn').addEventListener('click', () => {
        const url = `addQuestion.html?subject=${encodeURIComponent(currentSubject)}&set=${encodeURIComponent(currentSet)}`;
        window.location.href = url;
      });

      document.getElementById('viewQuestionsBtn').addEventListener('click', () => {
        const url = `viewQuestion.html?subject=${encodeURIComponent(currentSubject)}&set=${encodeURIComponent(currentSet)}`;
        window.location.href = url;
      });

      window.addEventListener('storage', (e) => {
        if (e.key === CATEGORY_KEY) {
          populateSubjectDropdown();
        }
      });
    });