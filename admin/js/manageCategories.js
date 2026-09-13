const STORAGE_KEY = 'categories';
    const DEFAULT_CATEGORIES = ['Subject 1', 'Subject 2', 'Subject 3', 'Subject 4'];

    function getCategories() {
      const stored = localStorage.getItem(STORAGE_KEY);
      if (stored) {
        try {
          return JSON.parse(stored);
        } catch (err) {
          return DEFAULT_CATEGORIES;
        }
      }
      return DEFAULT_CATEGORIES;
    }

    function saveCategories(categories) {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(categories));
    }

    function renderCategories() {
      const grid = document.getElementById('categoriesGrid');
      const categories = getCategories();

      grid.innerHTML = '';

      categories.forEach((subjectName, index) => {
        const card = document.createElement('div');
        card.className = 'category-card';
        card.dataset.index = index;
        card.dataset.subject = subjectName;

        card.innerHTML = `
          <h2 class="card-title">${subjectName}</h2>
          <div class="card-actions">
            <button type="button" class="action-btn edit-btn">Edit</button>
            <button type="button" class="action-btn delete-btn">Delete</button>
          </div>
        `;

        grid.appendChild(card);
      });

      // Add Category card (always last in the grid)
      const addCard = document.createElement('div');
      addCard.className = 'category-card add-category-card';
      addCard.id = 'addCategoryBtn';
      addCard.innerHTML = `
        <span class="add-icon">+</span>
        <span class="add-text">Add Category</span>
      `;
      grid.appendChild(addCard);
    }

    document.addEventListener('DOMContentLoaded', () => {
      renderCategories();

      const gridContainer = document.getElementById('categoriesGrid');

      gridContainer.addEventListener('click', (e) => {
        // Add new category
        if (e.target.closest('#addCategoryBtn')) {
          const newName = prompt('Enter new category name:');
          if (newName && newName.trim() !== '') {
            const categories = getCategories();
            categories.push(newName.trim());
            saveCategories(categories);
            renderCategories();
          }
          return;
        }

        const card = e.target.closest('.category-card');
        if (!card || card.id === 'addCategoryBtn') return;

        const index = parseInt(card.dataset.index, 10);
        const categories = getCategories();
        const subjectName = categories[index];

        if (e.target.classList.contains('edit-btn')) {
          const newName = prompt(`Edit category name for "${subjectName}":`, subjectName);
          if (newName && newName.trim() !== '') {
            categories[index] = newName.trim();
            saveCategories(categories);
            renderCategories();
          }
        }

        if (e.target.classList.contains('delete-btn')) {
          const confirmDelete = confirm(`Are you sure you want to delete "${subjectName}"?`);
          if (confirmDelete) {
            categories.splice(index, 1);
            saveCategories(categories);
            renderCategories();
          }
        }
      });

      window.addEventListener('storage', (e) => {
        if (e.key === STORAGE_KEY) {
          renderCategories();
        }
      });
    });
  