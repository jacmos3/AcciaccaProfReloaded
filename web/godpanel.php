<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GodPanel</title>
  <style>
    :root {
      --bg: #f3efe3;
      --panel: #fffaf0;
      --border: #b6aa8a;
      --text: #201b12;
      --muted: #6d6250;
      --accent: #8a2f12;
      --accent-2: #284f7a;
      --danger: #9e1c1c;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: Tahoma, "MS Sans Serif", sans-serif;
      color: var(--text);
      background:
        radial-gradient(circle at top left, rgba(255,255,255,0.7), transparent 32%),
        linear-gradient(180deg, #d9ccb0 0%, #f3efe3 100%);
    }
    .shell {
      max-width: 1480px;
      margin: 0 auto;
      padding: 22px;
    }
    .hero, .panel {
      background: var(--panel);
      border: 1px solid var(--border);
      box-shadow: 3px 3px 0 rgba(77, 59, 32, 0.18);
    }
    .hero {
      display: flex;
      justify-content: space-between;
      gap: 18px;
      align-items: center;
      padding: 18px 20px;
      margin-bottom: 18px;
    }
    .hero h1 {
      margin: 0 0 4px;
      font-size: 28px;
      color: var(--accent);
    }
    .hero p, .meta {
      margin: 0;
      color: var(--muted);
      font-size: 13px;
    }
    .grid {
      display: grid;
      grid-template-columns: minmax(300px, 360px) minmax(0, 1fr);
      gap: 18px;
    }
    .stack {
      display: grid;
      gap: 18px;
    }
    .panel {
      padding: 14px;
    }
    .panel h2, .panel h3 {
      margin: 0 0 12px;
      font-size: 18px;
    }
    .login-panel {
      max-width: 420px;
      margin: 70px auto;
    }
    label {
      display: block;
      margin-bottom: 6px;
      font-size: 12px;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }
    input, textarea, select {
      width: 100%;
      border: 1px solid #9f9476;
      background: #fff;
      color: var(--text);
      font: inherit;
      padding: 9px 10px;
      margin-bottom: 12px;
    }
    textarea {
      min-height: 120px;
      resize: vertical;
    }
    .toolbar, .actions {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: center;
    }
    button {
      border: 1px solid #8f8161;
      background: linear-gradient(180deg, #fff9eb 0%, #d9ccb0 100%);
      color: var(--text);
      font: inherit;
      padding: 8px 11px;
      cursor: pointer;
    }
    button.primary {
      background: linear-gradient(180deg, #fff4e7 0%, #df8b62 100%);
      border-color: #8a2f12;
      color: #3c160b;
    }
    button.secondary {
      background: linear-gradient(180deg, #f8fbff 0%, #bfd5ea 100%);
      border-color: #476d92;
      color: #163550;
    }
    button.danger {
      background: linear-gradient(180deg, #fff0f0 0%, #ec9f9f 100%);
      border-color: var(--danger);
      color: #541212;
    }
    .hidden { display: none !important; }
    .list-panel {
      min-height: 640px;
    }
    .list-wrap {
      border: 1px solid var(--border);
      background: #fff;
      overflow: auto;
      max-height: 620px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 12px;
    }
    th, td {
      padding: 8px 9px;
      border-bottom: 1px solid #e0d7c3;
      text-align: left;
      vertical-align: top;
    }
    th {
      position: sticky;
      top: 0;
      background: #efe5d0;
      z-index: 1;
    }
    tbody tr {
      cursor: pointer;
    }
    tbody tr:hover {
      background: #f6efe0;
    }
    tbody tr.active {
      background: #dcecff;
    }
    .split {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 10px;
    }
    .status {
      min-height: 18px;
      font-size: 12px;
      color: var(--accent-2);
    }
    .status.error {
      color: var(--danger);
    }
    .pill {
      display: inline-block;
      padding: 3px 7px;
      border: 1px solid #b9ab88;
      background: #efe7d4;
      font-size: 11px;
      color: var(--muted);
    }
    @media (max-width: 980px) {
      .grid { grid-template-columns: 1fr; }
      .list-panel { min-height: 0; }
      .list-wrap { max-height: 360px; }
      .split { grid-template-columns: 1fr; }
      .hero { flex-direction: column; align-items: flex-start; }
    }
  </style>
</head>
<body>
  <div class="shell">
    <section id="loginView" class="panel login-panel hidden">
      <h2>GodPanel</h2>
      <p class="meta">Accesso admin per commenti e leaderboard.</p>
      <label for="passwordInput">Password admin</label>
      <input id="passwordInput" type="password" autocomplete="current-password">
      <div class="actions">
        <button id="loginButton" class="primary" type="button">Accedi</button>
      </div>
      <div id="loginStatus" class="status"></div>
    </section>

    <div id="panelView" class="hidden">
      <section class="hero">
        <div>
          <h1>GodPanel</h1>
          <p>Gestione completa di commenti e leaderboard.</p>
        </div>
        <div class="toolbar">
          <span class="pill">admin</span>
          <button id="refreshAll" class="secondary" type="button">Aggiorna</button>
          <button id="logoutButton" type="button">Esci</button>
        </div>
      </section>

      <div class="grid">
        <div class="stack">
          <section class="panel">
            <h3>Commento</h3>
            <div class="split">
              <div>
                <label for="commentId">ID</label>
                <input id="commentId" type="text">
              </div>
              <div>
                <label for="commentTarget">Target</label>
                <input id="commentTarget" type="text" placeholder="game, cena1, videos...">
              </div>
            </div>
            <div class="split">
              <div>
                <label for="commentUser">Utente</label>
                <input id="commentUser" type="text">
              </div>
              <div>
                <label for="commentSession">Sessione</label>
                <input id="commentSession" type="text" placeholder="generic, va, legacy...">
              </div>
            </div>
            <label for="commentDate">Data ISO</label>
            <input id="commentDate" type="text" placeholder="2026-03-11T18:30:00+01:00">
            <label for="commentMessage">Messaggio</label>
            <textarea id="commentMessage"></textarea>
            <div class="actions">
              <button id="commentNew" type="button">Nuovo</button>
              <button id="commentSave" class="primary" type="button">Salva commento</button>
              <button id="commentDelete" class="danger" type="button">Cancella commento</button>
            </div>
          </section>

          <section class="panel">
            <h3>Punteggio</h3>
            <div class="split">
              <div>
                <label for="scoreId">ID</label>
                <input id="scoreId" type="text">
              </div>
              <div>
                <label for="scoreUser">Utente</label>
                <input id="scoreUser" type="text">
              </div>
            </div>
            <div class="split">
              <div>
                <label for="scoreSession">Sessione</label>
                <input id="scoreSession" type="text" placeholder="generic o va">
              </div>
              <div>
                <label for="scoreDate">Data ISO</label>
                <input id="scoreDate" type="text">
              </div>
            </div>
            <div class="split">
              <div>
                <label for="scorePoints">Punti</label>
                <input id="scorePoints" type="number" step="0.01">
              </div>
              <div>
                <label for="scoreVote">Voto</label>
                <input id="scoreVote" type="number" step="0.01">
              </div>
            </div>
            <label for="scoreTitle">Titolo report</label>
            <input id="scoreTitle" type="text">
            <label for="scoreStatus">Stato</label>
            <input id="scoreStatus" type="text">
            <label for="scoreText">Report testo</label>
            <textarea id="scoreText"></textarea>
            <div class="actions">
              <button id="scoreNew" type="button">Nuovo</button>
              <button id="scoreSave" class="primary" type="button">Salva punteggio</button>
              <button id="scoreDelete" class="danger" type="button">Cancella punteggio</button>
              <button id="clearAllScores" class="danger" type="button">Svuota leaderboard</button>
            </div>
          </section>

          <div id="panelStatus" class="status"></div>
        </div>

        <div class="stack">
          <section class="panel list-panel">
            <div class="toolbar">
              <h3 style="margin-right:auto;">Commenti</h3>
              <input id="commentsFilter" type="search" placeholder="Filtra commenti..." style="max-width:260px; margin:0;">
            </div>
            <div class="list-wrap">
              <table>
                <thead>
                  <tr>
                    <th>Data</th>
                    <th>Utente</th>
                    <th>Target</th>
                    <th>Sessione</th>
                    <th>Messaggio</th>
                  </tr>
                </thead>
                <tbody id="commentsBody"></tbody>
              </table>
            </div>
          </section>

          <section class="panel list-panel">
            <div class="toolbar">
              <h3 style="margin-right:auto;">Leaderboard</h3>
              <input id="scoresFilter" type="search" placeholder="Filtra punteggi..." style="max-width:260px; margin:0;">
            </div>
            <div class="list-wrap">
              <table>
                <thead>
                  <tr>
                    <th>Data</th>
                    <th>Utente</th>
                    <th>Sessione</th>
                    <th>Punti</th>
                    <th>Voto</th>
                    <th>Stato</th>
                  </tr>
                </thead>
                <tbody id="scoresBody"></tbody>
              </table>
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>

  <script>
    const state = {
      admin: false,
      comments: [],
      leaderboard: [],
      activeCommentId: '',
      activeScoreId: ''
    };

    const refs = {
      loginView: document.getElementById('loginView'),
      panelView: document.getElementById('panelView'),
      passwordInput: document.getElementById('passwordInput'),
      loginButton: document.getElementById('loginButton'),
      loginStatus: document.getElementById('loginStatus'),
      logoutButton: document.getElementById('logoutButton'),
      refreshAll: document.getElementById('refreshAll'),
      panelStatus: document.getElementById('panelStatus'),
      commentsFilter: document.getElementById('commentsFilter'),
      scoresFilter: document.getElementById('scoresFilter'),
      commentsBody: document.getElementById('commentsBody'),
      scoresBody: document.getElementById('scoresBody'),
      commentId: document.getElementById('commentId'),
      commentTarget: document.getElementById('commentTarget'),
      commentUser: document.getElementById('commentUser'),
      commentSession: document.getElementById('commentSession'),
      commentDate: document.getElementById('commentDate'),
      commentMessage: document.getElementById('commentMessage'),
      commentNew: document.getElementById('commentNew'),
      commentSave: document.getElementById('commentSave'),
      commentDelete: document.getElementById('commentDelete'),
      scoreId: document.getElementById('scoreId'),
      scoreUser: document.getElementById('scoreUser'),
      scoreSession: document.getElementById('scoreSession'),
      scoreDate: document.getElementById('scoreDate'),
      scorePoints: document.getElementById('scorePoints'),
      scoreVote: document.getElementById('scoreVote'),
      scoreTitle: document.getElementById('scoreTitle'),
      scoreStatus: document.getElementById('scoreStatus'),
      scoreText: document.getElementById('scoreText'),
      scoreNew: document.getElementById('scoreNew'),
      scoreSave: document.getElementById('scoreSave'),
      scoreDelete: document.getElementById('scoreDelete'),
      clearAllScores: document.getElementById('clearAllScores')
    };

    function escapeHtml(value) {
      return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
    }

    async function api(action, payload = {}, method = 'POST') {
      const options = { method, headers: { 'Content-Type': 'application/json' } };
      let url = `api/godpanel.php?action=${encodeURIComponent(action)}`;
      if (method === 'POST') {
        options.body = JSON.stringify({ action, ...payload });
      }
      const response = await fetch(url, options);
      const data = await response.json().catch(() => ({}));
      if (!response.ok) throw new Error(data.error || 'Operazione fallita');
      return data;
    }

    function setStatus(message, isError = false, target = refs.panelStatus) {
      target.textContent = message || '';
      target.classList.toggle('error', !!isError);
    }

    function toggleViews() {
      refs.loginView.classList.toggle('hidden', state.admin);
      refs.panelView.classList.toggle('hidden', !state.admin);
      if (!state.admin) refs.passwordInput.focus();
    }

    function filteredComments() {
      const q = refs.commentsFilter.value.trim().toLowerCase();
      if (!q) return state.comments;
      return state.comments.filter((entry) => JSON.stringify(entry).toLowerCase().includes(q));
    }

    function filteredScores() {
      const q = refs.scoresFilter.value.trim().toLowerCase();
      if (!q) return state.leaderboard;
      return state.leaderboard.filter((entry) => JSON.stringify(entry).toLowerCase().includes(q));
    }

    function renderComments() {
      refs.commentsBody.innerHTML = filteredComments().map((entry) => `
        <tr data-id="${escapeHtml(entry.id)}" class="${entry.id === state.activeCommentId ? 'active' : ''}">
          <td>${escapeHtml(entry.createdAt || '')}</td>
          <td>${escapeHtml(entry.userName || '')}</td>
          <td>${escapeHtml(entry.target || '')}</td>
          <td>${escapeHtml(entry.sessionType || '')}</td>
          <td>${escapeHtml((entry.message || '').slice(0, 180))}</td>
        </tr>
      `).join('') || '<tr><td colspan="5">Nessun commento.</td></tr>';
    }

    function renderScores() {
      refs.scoresBody.innerHTML = filteredScores().map((entry) => `
        <tr data-id="${escapeHtml(entry.id)}" class="${entry.id === state.activeScoreId ? 'active' : ''}">
          <td>${escapeHtml(entry.endedAt || '')}</td>
          <td>${escapeHtml(entry.userName || '')}</td>
          <td>${escapeHtml(entry.sessionType || '')}</td>
          <td>${escapeHtml(entry.points ?? '')}</td>
          <td>${escapeHtml(entry.vote ?? '')}</td>
          <td>${escapeHtml(entry.status || '')}</td>
        </tr>
      `).join('') || '<tr><td colspan="6">Nessun punteggio.</td></tr>';
    }

    function fillComment(entry = {}) {
      state.activeCommentId = entry.id || '';
      refs.commentId.value = entry.id || '';
      refs.commentTarget.value = entry.target || 'game';
      refs.commentUser.value = entry.userName || '';
      refs.commentSession.value = entry.sessionType || '';
      refs.commentDate.value = entry.createdAt || '';
      refs.commentMessage.value = entry.message || '';
      renderComments();
    }

    function fillScore(entry = {}) {
      state.activeScoreId = entry.id || '';
      refs.scoreId.value = entry.id || '';
      refs.scoreUser.value = entry.userName || '';
      refs.scoreSession.value = entry.sessionType || 'generic';
      refs.scoreDate.value = entry.endedAt || '';
      refs.scorePoints.value = entry.points ?? 0;
      refs.scoreVote.value = entry.vote ?? 1;
      refs.scoreTitle.value = entry.reportTitle || 'Report sessione';
      refs.scoreStatus.value = entry.status || '';
      refs.scoreText.value = entry.reportText || '';
      renderScores();
    }

    async function loadDashboard() {
      const payload = await api('dashboard', {}, 'GET');
      state.comments = Array.isArray(payload.comments) ? payload.comments : [];
      state.leaderboard = Array.isArray(payload.leaderboard) ? payload.leaderboard : [];
      renderComments();
      renderScores();
      if (state.activeCommentId) {
        const comment = state.comments.find((entry) => entry.id === state.activeCommentId);
        if (comment) fillComment(comment);
      }
      if (state.activeScoreId) {
        const score = state.leaderboard.find((entry) => entry.id === state.activeScoreId);
        if (score) fillScore(score);
      }
    }

    async function refreshAll() {
      try {
        await loadDashboard();
        setStatus('Dati aggiornati.');
      } catch (error) {
        setStatus(error.message || 'Aggiornamento fallito', true);
      }
    }

    async function checkStatus() {
      try {
        const payload = await api('status', {}, 'GET');
        state.admin = !!payload.admin?.authenticated;
        toggleViews();
        if (state.admin) await loadDashboard();
      } catch (_) {
        state.admin = false;
        toggleViews();
      }
    }

    refs.loginButton.addEventListener('click', async () => {
      setStatus('', false, refs.loginStatus);
      try {
        const payload = await api('login', { password: refs.passwordInput.value });
        state.admin = !!payload.admin?.authenticated;
        refs.passwordInput.value = '';
        toggleViews();
        await loadDashboard();
        setStatus('Accesso effettuato.');
      } catch (error) {
        setStatus(error.message || 'Accesso fallito', true, refs.loginStatus);
      }
    });

    refs.logoutButton.addEventListener('click', async () => {
      await api('logout', {});
      state.admin = false;
      toggleViews();
    });

    refs.refreshAll.addEventListener('click', refreshAll);
    refs.commentsFilter.addEventListener('input', renderComments);
    refs.scoresFilter.addEventListener('input', renderScores);

    refs.commentsBody.addEventListener('click', (event) => {
      const row = event.target.closest('tr[data-id]');
      if (!row) return;
      const entry = state.comments.find((item) => item.id === row.dataset.id);
      if (entry) fillComment(entry);
    });

    refs.scoresBody.addEventListener('click', (event) => {
      const row = event.target.closest('tr[data-id]');
      if (!row) return;
      const entry = state.leaderboard.find((item) => item.id === row.dataset.id);
      if (entry) fillScore(entry);
    });

    refs.commentNew.addEventListener('click', () => fillComment({ target: 'game', sessionType: 'generic' }));
    refs.scoreNew.addEventListener('click', () => fillScore({ sessionType: 'generic', reportTitle: 'Report sessione', points: 0, vote: 1 }));

    refs.commentSave.addEventListener('click', async () => {
      try {
        const payload = await api('save-comment', {
          id: refs.commentId.value,
          target: refs.commentTarget.value,
          userName: refs.commentUser.value,
          sessionType: refs.commentSession.value,
          createdAt: refs.commentDate.value,
          message: refs.commentMessage.value
        });
        state.comments = Array.isArray(payload.comments) ? payload.comments : state.comments;
        const updated = state.comments.find((entry) => entry.id === (refs.commentId.value || state.activeCommentId)) || state.comments[0];
        fillComment(updated || {});
        setStatus('Commento salvato.');
      } catch (error) {
        setStatus(error.message || 'Salvataggio commento fallito', true);
      }
    });

    refs.commentDelete.addEventListener('click', async () => {
      const id = refs.commentId.value.trim();
      if (!id) return;
      if (!window.confirm('Cancellare questo commento?')) return;
      try {
        const payload = await api('delete-comment', { id });
        state.comments = Array.isArray(payload.comments) ? payload.comments : [];
        fillComment({ target: 'game', sessionType: 'generic' });
        setStatus('Commento cancellato.');
      } catch (error) {
        setStatus(error.message || 'Cancellazione commento fallita', true);
      }
    });

    refs.scoreSave.addEventListener('click', async () => {
      try {
        const payload = await api('save-leaderboard', {
          id: refs.scoreId.value,
          userName: refs.scoreUser.value,
          sessionType: refs.scoreSession.value,
          endedAt: refs.scoreDate.value,
          points: refs.scorePoints.value,
          vote: refs.scoreVote.value,
          reportTitle: refs.scoreTitle.value,
          status: refs.scoreStatus.value,
          reportText: refs.scoreText.value
        });
        state.leaderboard = Array.isArray(payload.leaderboard) ? payload.leaderboard : state.leaderboard;
        const updated = state.leaderboard.find((entry) => entry.id === (refs.scoreId.value || state.activeScoreId)) || state.leaderboard[0];
        fillScore(updated || {});
        setStatus('Punteggio salvato.');
      } catch (error) {
        setStatus(error.message || 'Salvataggio punteggio fallito', true);
      }
    });

    refs.scoreDelete.addEventListener('click', async () => {
      const id = refs.scoreId.value.trim();
      if (!id) return;
      if (!window.confirm('Cancellare questo punteggio?')) return;
      try {
        const payload = await api('delete-leaderboard', { id });
        state.leaderboard = Array.isArray(payload.leaderboard) ? payload.leaderboard : [];
        fillScore({ sessionType: 'generic', reportTitle: 'Report sessione', points: 0, vote: 1 });
        setStatus('Punteggio cancellato.');
      } catch (error) {
        setStatus(error.message || 'Cancellazione punteggio fallita', true);
      }
    });

    refs.clearAllScores.addEventListener('click', async () => {
      if (!window.confirm('Svuotare tutta la leaderboard?')) return;
      try {
        const payload = await api('clear-leaderboard', {});
        state.leaderboard = Array.isArray(payload.leaderboard) ? payload.leaderboard : [];
        fillScore({ sessionType: 'generic', reportTitle: 'Report sessione', points: 0, vote: 1 });
        setStatus('Leaderboard svuotata.');
      } catch (error) {
        setStatus(error.message || 'Svuotamento leaderboard fallito', true);
      }
    });

    refs.passwordInput.addEventListener('keydown', (event) => {
      if (event.key === 'Enter') refs.loginButton.click();
    });

    checkStatus();
  </script>
</body>
</html>
