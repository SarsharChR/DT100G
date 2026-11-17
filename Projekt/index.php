<?php
session_start();
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>StudyPlanner</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="StudyPlanner hjälper studenter att organisera uppgifter, deadlines och status på ett enkelt sätt.">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="app-container">
    <header class="app-header">
      <div>
        <p class="eyebrow">Student Task Manager</p>
        <h1>StudyPlanner</h1>
      </div>
      <div id="user-info" class="user-info hidden" aria-live="polite">
        <span id="current-user-name"></span>
        <button id="logout-btn" class="btn btn-secondary btn-small" type="button">Logga ut</button>
      </div>
    </header>

    <main>
      <section class="hero card">
        <div>
          <h2>Planera dina studier på ett smartare sätt</h2>
          <p>
            Håll koll på uppgifter, ämnen, deadlines och status i ett och samma verktyg.
          </p>
        </div>
      </section>

      <section id="auth-section">
        <div class="card auth-card">
          <div class="tabs" role="tablist" aria-label="Inloggning eller registrering">
            <button
              class="tab active"
              type="button"
              id="tab-login"
              data-tab="login-panel"
              role="tab"
              aria-selected="true"
              aria-controls="login-panel"
            >
              Logga in
            </button>

            <button
              class="tab"
              type="button"
              id="tab-signup"
              data-tab="signup-panel"
              role="tab"
              aria-selected="false"
              aria-controls="signup-panel"
            >
              Registrera
            </button>
          </div>

          <div id="login-panel" class="tab-content active" role="tabpanel" aria-labelledby="tab-login">
            <h2>Logga in på ditt konto</h2>
            <form id="login-form" novalidate>
              <div class="form-group">
                <label for="login-email">E-post</label>
                <input type="email" id="login-email" name="email" autocomplete="email" required>
              </div>
              <div class="form-group">
                <label for="login-password">Lösenord</label>
                <input type="password" id="login-password" name="password" autocomplete="current-password" required>
              </div>
              <button type="submit" class="btn btn-primary full-width">Logga in</button>
              <p class="helper-text">Har du inget konto? Välj fliken Registrera.</p>
            </form>
          </div>

          <div id="signup-panel" class="tab-content" role="tabpanel" aria-labelledby="tab-signup">
            <h2>Skapa nytt konto</h2>
            <form id="signup-form" novalidate>
              <div class="form-group">
                <label for="signup-name">Namn</label>
                <input type="text" id="signup-name" name="name" autocomplete="name" minlength="2" required>
              </div>
              <div class="form-group">
                <label for="signup-email">E-post</label>
                <input type="email" id="signup-email" name="email" autocomplete="email" required>
              </div>
              <div class="form-group">
                <label for="signup-password">Lösenord</label>
                <input type="password" id="signup-password" name="password" autocomplete="new-password" minlength="6" required>
              </div>
              <button type="submit" class="btn btn-primary full-width">Registrera</button>
            </form>
          </div>
        </div>
      </section>

      <section id="tasks-section" class="hidden">
        <div class="dashboard-grid">
          <div class="stats card">
            <h2>Översikt</h2>
            <div class="stats-grid">
              <div class="stat-box">
                <span class="stat-label">Totalt</span>
                <strong id="stat-total">0</strong>
              </div>
              <div class="stat-box">
                <span class="stat-label">Pågående</span>
                <strong id="stat-pending">0</strong>
              </div>
              <div class="stat-box">
                <span class="stat-label">Klara</span>
                <strong id="stat-done">0</strong>
              </div>
              <div class="stat-box">
                <span class="stat-label">Inställda</span>
                <strong id="stat-canceled">0</strong>
              </div>
            </div>
          </div>

          <div class="layout">
            <div class="card">
              <h2>Lägg till eller redigera uppgift</h2>
              <form id="task-form" novalidate>
                <input type="hidden" id="task-id" name="id">

                <div class="form-group">
                  <label for="task-title">Titel</label>
                  <input type="text" id="task-title" name="title" maxlength="150" required>
                </div>

                <div class="form-group">
                  <label for="task-subject">Ämne</label>
                  <input type="text" id="task-subject" name="subject" maxlength="100" placeholder="t.ex. Webbutveckling" required>
                </div>

                <div class="form-group">
                  <label for="task-deadline">Deadline</label>
                  <input type="date" id="task-deadline" name="deadline" required>
                </div>

                <div class="form-group">
                  <label for="task-status">Status</label>
                  <select id="task-status" name="status">
                    <option value="pending">Pågående</option>
                    <option value="done">Klar</option>
                    <option value="canceled">Inställd</option>
                  </select>
                </div>

                <div class="form-group">
                  <label for="task-description">Beskrivning</label>
                  <textarea id="task-description" name="description" rows="4" placeholder="Skriv en kort beskrivning av uppgiften"></textarea>
                </div>

                <button type="submit" class="btn btn-primary full-width" id="task-submit-btn">Spara uppgift</button>
                <button type="button" class="btn btn-secondary full-width" id="task-reset-btn">Rensa formulär</button>
              </form>
            </div>

            <div class="card">
              <div class="tasks-header">
                <h2>Mina uppgifter</h2>
                <div class="filters">
                  <div class="form-group inline">
                    <label for="filter-subject">Ämne</label>
                    <input type="text" id="filter-subject" placeholder="Filtrera efter ämne">
                  </div>
                  <div class="form-group inline">
                    <label for="filter-status">Status</label>
                    <select id="filter-status">
                      <option value="all">Alla</option>
                      <option value="pending">Pågående</option>
                      <option value="done">Klar</option>
                      <option value="canceled">Inställd</option>
                    </select>
                  </div>
                  <button id="clear-filters-btn" class="btn btn-secondary btn-small" type="button">Rensa filter</button>
                </div>
              </div>

              <div id="tasks-list" class="tasks-list" aria-live="polite"></div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <div id="toast" class="toast hidden" role="status" aria-live="polite"></div>
  </div>

  <script src="js/app.js" defer></script>
</body>
</html>