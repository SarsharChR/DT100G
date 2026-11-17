const state = {
  user: null,
  tasks: [],
  editingId: null,
};

const $ = (selector) => document.querySelector(selector);
const tabs = document.querySelectorAll('.tab');
const tabContents = document.querySelectorAll('.tab-content');

const authSection = $('#auth-section');
const tasksSection = $('#tasks-section');
const userInfo = $('#user-info');
const currentUserName = $('#current-user-name');
const toast = $('#toast');

const loginForm = $('#login-form');
const signupForm = $('#signup-form');
const logoutBtn = $('#logout-btn');
const taskForm = $('#task-form');
const taskResetBtn = $('#task-reset-btn');
const tasksList = $('#tasks-list');
const filterSubject = $('#filter-subject');
const filterStatus = $('#filter-status');
const clearFiltersBtn = $('#clear-filters-btn');

const stats = {
  total: $('#stat-total'),
  pending: $('#stat-pending'),
  done: $('#stat-done'),
  canceled: $('#stat-canceled'),
};

function showToast(message) {
  toast.textContent = message;
  toast.classList.remove('hidden');
  requestAnimationFrame(() => toast.classList.add('visible'));
  clearTimeout(showToast.timeout);
  showToast.timeout = setTimeout(() => {
    toast.classList.remove('visible');
    setTimeout(() => toast.classList.add('hidden'), 200);
  }, 2600);
}

async function api(url, options = {}) {
  const response = await fetch(url, {
    headers: { 'Content-Type': 'application/json' },
    credentials: 'same-origin',
    ...options,
  });

  const data = await response.json();

  if (!response.ok || !data.success) {
    throw new Error(data.message || 'Ett fel uppstod.');
  }

  return data;
}

// Växlar mellan flikarna för inloggning och registrering

function setActiveTab(tabId) {
  tabs.forEach((tab) => {
    const active = tab.dataset.tab === tabId;
    tab.classList.toggle('active', active);
    tab.setAttribute('aria-selected', String(active));
  });

  tabContents.forEach((content) => {
    content.classList.toggle('active', content.id === tabId);
  });
}

// Skyddar text innan den skrivs ut i HTML
function escapeHtml(value = '') {
  return String(value)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

// Skyddar text innan den skrivs ut i HTML
function formatDate(value) {
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return value;

  return new Intl.DateTimeFormat('sv-SE', {
    dateStyle: 'medium',
  }).format(date);
}

// Formaterar datum och tid
function formatDateTime(value) {
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return value;

  return new Intl.DateTimeFormat('sv-SE', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(date);
}

// Uppdaterar statistiken över uppgifterna
function updateStats(tasks) {
  stats.total.textContent = tasks.length;
  stats.pending.textContent = tasks.filter((task) => task.status === 'pending').length;
  stats.done.textContent = tasks.filter((task) => task.status === 'done').length;
  stats.canceled.textContent = tasks.filter((task) => task.status === 'canceled').length;
}

// Uppdaterar statistiken över uppgifterna
function getFilteredTasks() {
  const subjectFilter = filterSubject.value.trim().toLowerCase();
  const statusFilter = filterStatus.value;

  return state.tasks.filter((task) => {
    const subjectMatches =
      !subjectFilter || task.subject.toLowerCase().includes(subjectFilter);

    const statusMatches =
      statusFilter === 'all' || task.status === statusFilter;

    return subjectMatches && statusMatches;
  });
}

function renderTasks() {
  const filteredTasks = getFilteredTasks();
  updateStats(state.tasks);

  if (!filteredTasks.length) {
    tasksList.innerHTML =
      '<div class="task-empty">Inga uppgifter matchar dina filter ännu.</div>';
    return;
  }

  tasksList.innerHTML = filteredTasks
    .map(
      (task) => `
        <article class="task-item">
          <div class="task-main">
            <h3 class="task-title">${escapeHtml(task.title)}</h3>
            <div class="task-subject">Ämne: ${escapeHtml(task.subject)}</div>
            ${
              task.description
                ? `<p class="task-description">${escapeHtml(task.description)}</p>`
                : ''
            }
          </div>

          <div class="task-meta">
            <span class="badge badge-status-${escapeHtml(task.status)}">
              ${
                task.status === 'pending'
                  ? 'Pågående'
                  : task.status === 'done'
                  ? 'Klar'
                  : 'Inställd'
              }
            </span>
            <span class="badge badge-deadline">
              Deadline: ${escapeHtml(formatDate(task.deadline))}
            </span>
            <span>
              Skapad: ${escapeHtml(formatDateTime(task.created_at))}
            </span>
          </div>

          <div class="task-actions">
            <button
              type="button"
              class="btn btn-secondary btn-small"
              data-action="edit"
              data-id="${task.id}"
            >
              Redigera
            </button>
            <button
              type="button"
              class="btn btn-danger btn-small"
              data-action="delete"
              data-id="${task.id}"
            >
              Ta bort
            </button>
          </div>
        </article>
      `
    )
    .join('');
}

function setLoggedInUI() {
  authSection.classList.add('hidden');
  tasksSection.classList.remove('hidden');
  userInfo.classList.remove('hidden');
  currentUserName.textContent = `Inloggad som ${state.user?.name ?? ''}`;
}


// Återställer gränssnittet när användaren loggar ut
function setLoggedOutUI() {
  state.user = null;
  state.tasks = [];
  state.editingId = null;

  authSection.classList.remove('hidden');
  tasksSection.classList.add('hidden');
  userInfo.classList.add('hidden');
  currentUserName.textContent = '';

  renderTasks();
  updateStats([]);
  taskForm.reset();
}

// Fyller formuläret med data när en uppgift ska redigeras
function fillTaskForm(task) {
  state.editingId = task.id;
  $('#task-id').value = task.id;
  $('#task-title').value = task.title;
  $('#task-subject').value = task.subject;
  $('#task-deadline').value = task.deadline;
  $('#task-status').value = task.status;
  $('#task-description').value = task.description ?? '';
  $('#task-submit-btn').textContent = 'Uppdatera uppgift';
  $('#task-title').focus();
}

// Tömmer formuläret och återställer läget
function resetTaskForm() {
  state.editingId = null;
  taskForm.reset();
  $('#task-id').value = '';
  $('#task-submit-btn').textContent = 'Spara uppgift';
}

// Hämtar aktiv session när sidan laddas
async function fetchSession() {
  try {
    const data = await api('api/session.php');
    state.user = data.user;
    state.tasks = data.tasks;
    setLoggedInUI();
    renderTasks();
  } catch {
    setLoggedOutUI();
  }
}

loginForm.addEventListener('submit', async (event) => {
  event.preventDefault();

  const payload = {
    email: $('#login-email').value.trim(),
    password: $('#login-password').value,
  };

  try {
    const data = await api('api/login.php', {
      method: 'POST',
      body: JSON.stringify(payload),
    });

    state.user = data.user;
    state.tasks = data.tasks;

    loginForm.reset();
    setLoggedInUI();
    renderTasks();
    showToast('Du är nu inloggad.');
  } catch (error) {
    showToast(error.message);
  }
});

// Hanterar registrering av ny användare
signupForm.addEventListener('submit', async (event) => {
  event.preventDefault();

  const payload = {
    name: $('#signup-name').value.trim(),
    email: $('#signup-email').value.trim(),
    password: $('#signup-password').value,
  };

  try {
    const data = await api('api/register.php', {
      method: 'POST',
      body: JSON.stringify(payload),
    });

    state.user = data.user;
    state.tasks = [];

    signupForm.reset();
    setLoggedInUI();
    renderTasks();
    showToast('Konto skapat. Välkommen!');
  } catch (error) {
    showToast(error.message);
  }
});

logoutBtn.addEventListener('click', async () => {
  try {
    await api('api/logout.php', { method: 'POST' });
  } catch {}

  setLoggedOutUI();
  showToast('Du har loggat ut.');
});

// Hanterar skapande och uppdatering av uppgifter
taskForm.addEventListener('submit', async (event) => {
  event.preventDefault();

  const payload = {
    id: $('#task-id').value || null,
    title: $('#task-title').value.trim(),
    subject: $('#task-subject').value.trim(),
    deadline: $('#task-deadline').value,
    status: $('#task-status').value,
    description: $('#task-description').value.trim(),
  };

  try {
    const data = await api('api/tasks.php', {
      method: state.editingId ? 'PUT' : 'POST',
      body: JSON.stringify(payload),
    });

    const wasEditing = Boolean(state.editingId);

    state.tasks = data.tasks;
    resetTaskForm();
    renderTasks();

    showToast(
      wasEditing ? 'Uppgiften har uppdaterats.' : 'Uppgiften har sparats.'
    );
  } catch (error) {
    showToast(error.message);
  }
});

taskResetBtn.addEventListener('click', resetTaskForm);

clearFiltersBtn.addEventListener('click', () => {
  filterSubject.value = '';
  filterStatus.value = 'all';
  renderTasks();
});

filterSubject.addEventListener('input', renderTasks);
filterStatus.addEventListener('change', renderTasks);

tasksList.addEventListener('click', async (event) => {
  const button = event.target.closest('button[data-action]');
  if (!button) return;

  const id = Number(button.dataset.id);
  const action = button.dataset.action;
  const task = state.tasks.find((item) => Number(item.id) === id);

  if (!task) return;

  if (action === 'edit') {
    fillTaskForm(task);
    return;
  }

  if (action === 'delete') {
    const confirmed = window.confirm(
      'Är du säker på att du vill ta bort uppgiften?'
    );

    if (!confirmed) return;

    try {
      const data = await api('api/tasks.php', {
        method: 'DELETE',
        body: JSON.stringify({ id }),
      });

      state.tasks = data.tasks;
      renderTasks();
      showToast('Uppgiften togs bort.');
    } catch (error) {
      showToast(error.message);
    }
  }
});

tabs.forEach((tab) => {
  tab.addEventListener('click', () => setActiveTab(tab.dataset.tab));
});

fetchSession();