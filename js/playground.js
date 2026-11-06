(function () {
    'use strict';

    const projects = window.PLAYGROUND_PROJECTS || [];
    const state = {
        selectedId: projects[0]?.id || null,
        running: false,
        lootStep: 'skill',
        lootSkill: null,
        luckyHistory: [],
        luckyStep: null,
        luckyGame: null,
        requestId: 0,
        debounceTimer: null
    };
    const list = document.querySelector('#project-list');
    const viewer = document.querySelector('#code-viewer code');
    const title = document.querySelector('#code-title');
    const sourceMeta = document.querySelector('#source-meta');
    const output = document.querySelector('#terminal-output');
    const status = document.querySelector('#execution-status');
    const fields = document.querySelector('#experiment-fields');
    const form = document.querySelector('#terminal-form');
    const help = document.querySelector('#terminal-help');
    const themeToggle = document.querySelector('[data-theme-toggle]');

    const selectedProject = () => projects.find((project) => project.id === state.selectedId) || projects[0];

    function setStatus(value) {
        status.textContent = value;
        status.className = `execution-status status-${value.toLowerCase()}`;
    }

    function renderList() {
        list.innerHTML = '';
        projects.forEach((project) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `project-item${project.id === state.selectedId ? ' is-selected' : ''}`;
            button.setAttribute('aria-selected', String(project.id === state.selectedId));
            button.dataset.projectId = project.id;
            button.innerHTML = `<span class="project-item-name">${project.filename}</span><span class="project-item-meta">${project.availability === 'ready' ? 'PHP ready' : project.availability === 'planned' ? 'Planned' : 'Archived'}</span>`;
            button.addEventListener('click', () => selectProject(project.id));
            list.appendChild(button);
        });
        document.querySelector('#project-count').textContent = projects.length;
    }

    function renderFields(project) {
        fields.innerHTML = '';
        project.fields.forEach((field) => {
            const label = document.createElement('label');
            label.htmlFor = `field-${field.name}`;
            label.textContent = field.label;
            fields.appendChild(label);
            const control = field.type === 'select' ? document.createElement('select') : document.createElement('input');
            control.id = `field-${field.name}`;
            control.name = field.name;
            if (field.type === 'select') {
                field.options.forEach(([value, text]) => control.add(new Option(`${value} · ${text}`, value)));
            } else {
                control.type = field.type;
                control.value = field.value;
                control.min = field.min;
                control.max = field.max;
            }
            fields.appendChild(control);
        });

        if (project.id === 'loot-master' && state.lootStep === 'skill') {
            fields.innerHTML = '';
            const prompt = document.createElement('span');
            prompt.className = 'experiment-prompt';
            prompt.textContent = 'Choose a skill';
            fields.appendChild(prompt);
            [['1', 'Abyssal Arrow'], ['2', 'Atomic Bomb'], ['3', 'Infinite Void']].forEach(([value, text]) => {
                const control = document.createElement('button');
                control.type = 'button';
                control.className = 'experiment-action';
                control.dataset.skill = value;
                control.textContent = `${value} · ${text}`;
                control.addEventListener('click', () => {
                    state.lootSkill = value;
                    runProject({project, values: {skill: value, step: 'skill'}});
                });
                fields.appendChild(control);
            });
        } else if (project.id === 'loot-master' && state.lootStep === 'crates') {
            fields.innerHTML = '';
            const prompt = document.createElement('span');
            prompt.className = 'experiment-prompt';
            prompt.textContent = 'Crates ready';
            fields.appendChild(prompt);
            [['true', 'Open crates'], ['false', 'Leave unopened']].forEach(([value, text]) => {
                const control = document.createElement('button');
                control.type = 'button';
                control.className = 'experiment-action';
                control.textContent = text;
                control.addEventListener('click', () => {
                    runProject({project, values: {skill: state.lootSkill, step: 'crates', open_crates: value}});
                });
                fields.appendChild(control);
            });
        }

        if (project.id === 'gigapet') {
            const amountControl = fields.querySelector('#field-amount');
            const actionGroup = document.createElement('div');
            actionGroup.className = 'experiment-action-group';
            actionGroup.setAttribute('aria-label', 'GigaPet actions');
            [['feed', 'Feed'], ['play', 'Play'], ['status', 'Check status']].forEach(([value, text]) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'experiment-action';
                button.textContent = text;
                button.addEventListener('click', () => runProject({project, values: collectFields(), action: value}));
                actionGroup.appendChild(button);
            });
            fields.appendChild(actionGroup);
            amountControl.addEventListener('input', updateGigaPetPreview);
            fields.querySelectorAll('input[type="text"]').forEach((control) => {
                control.addEventListener('input', updateGigaPetPreview);
            });
        }

        if (project.id === 'lucky-nine') {
            const actionGroup = document.createElement('div');
            actionGroup.className = 'experiment-action-group';
            actionGroup.setAttribute('aria-label', 'Lucky Nine actions');
            [['deal', 'Deal cards'], ['leaderboard', 'View leaderboard']].forEach(([action, text]) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'experiment-action';
                button.textContent = text;
                button.addEventListener('click', () => {
                    const values = collectFields();
                    if (action === 'leaderboard') {
                        values.history = JSON.stringify(state.luckyHistory);
                    }
                    runProject({project, values, action});
                });
                actionGroup.appendChild(button);
            });
            fields.appendChild(actionGroup);
            fields.querySelectorAll('input').forEach((control) => {
                control.addEventListener('input', updateLuckyNinePreview);
            });
        }

        if (project.id === 'lucky-nine' && state.luckyStep === 'player-action') {
            const actionGroup = document.createElement('div');
            actionGroup.className = 'experiment-action-group';
            actionGroup.setAttribute('aria-label', 'Your Lucky Nine turn');
            [['draw', 'Draw another'], ['stand', 'Stand']].forEach(([action, text]) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'experiment-action';
                button.textContent = text;
                if (action === 'draw' && state.luckyGame) {
                    try {
                        const game = JSON.parse(atob(state.luckyGame));
                        button.disabled = game.user.cards.length >= 3;
                    } catch {
                        button.disabled = true;
                    }
                }
                button.addEventListener('click', () => runProject({
                    project,
                    values: {game: state.luckyGame},
                    action
                }));
                actionGroup.appendChild(button);
            });
            fields.innerHTML = '';
            fields.appendChild(actionGroup);
        }

        if (project.id === 'loot-master' && state.lootStep === 'skill') {
            return;
        }
        if (project.id === 'loot-master' && state.lootStep === 'crates') {
            return;
        }

        if (project.id === 'gigapet') {
            return;
        }

        if (project.id === 'lucky-nine') {
            return;
        }

        fields.querySelectorAll('input, select').forEach((control) => {
            control.addEventListener('input', scheduleReactiveRun);
            control.addEventListener('change', scheduleReactiveRun);
        });
    }

    function collectFields() {
        return Object.fromEntries(new FormData(form).entries());
    }

    function updateGigaPetPreview() {
        const name = document.querySelector('#field-name')?.value.trim() || 'Your pet';
        const type = document.querySelector('#field-type')?.value.trim() || 'pet';
        output.textContent = `Ready to interact with ${name} the ${type}. Choose an action.`;
        setStatus('READY');
    }

    function updateLuckyNinePreview() {
        const dealer = document.querySelector('#field-dealer')?.value.trim() || 'Your dealer';
        const players = document.querySelector('#field-players')?.value || '0';
        output.textContent = `Ready for ${dealer}'s round with ${players} player(s). Choose an action.`;
        setStatus('READY');
    }

    function scheduleReactiveRun() {
        window.clearTimeout(state.debounceTimer);
        state.debounceTimer = window.setTimeout(() => {
            const project = selectedProject();
            if (project?.availability === 'ready') {
                runProject({project, values: collectFields()});
            }
        }, 180);
    }

    function renderProject() {
        const project = selectedProject();
        if (!project) return;
        state.lootStep = 'skill';
        state.lootSkill = null;
        title.textContent = project.filename;
        viewer.textContent = project.source;
        sourceMeta.textContent = `${project.language} · ${project.availability} · reference: ${project.original}`;
        help.textContent = project.inputHint;
        renderFields(project);
        setStatus('READY');
        output.textContent = project.availability === 'ready'
            ? 'Change an input or choose an action to run the PHP translation.'
            : project.inputHint;
        if (project.id === 'gigapet') updateGigaPetPreview();
        if (project.id === 'lucky-nine') updateLuckyNinePreview();
    }

    function selectProject(id) {
        if (state.running) return;
        state.selectedId = id;
        renderList();
        renderProject();
    }

    async function runProject({project, values, action = null}) {
        if (!project || project.availability !== 'ready' || state.running) return;
        state.running = true;
        const currentRequest = ++state.requestId;
        setStatus('RUNNING');
        output.textContent = 'Running PHP translation…';
        const request = new FormData();
        Object.entries(values).forEach(([name, value]) => request.set(name, value));
        request.set('project', project.id);
        if (action) request.set('action', action);
        try {
            const response = await fetch('php/playground-run.php', {
                method: 'POST',
                body: request,
                headers: {Accept: 'application/json'}
            });
            const payload = await response.json();
            if (currentRequest !== state.requestId) return;
            if (!response.ok || !payload.ok) {
                throw new Error(payload.error || 'The experiment could not be completed.');
            }
            output.textContent = payload.output || payload.steps.join('\n');
            setStatus(payload.status === 'NEXT_STEP' ? 'READY' : 'COMPLETE');
            if (project.id === 'loot-master' && payload.status === 'NEXT_STEP') {
                state.lootStep = payload.next;
                help.textContent = 'The skill is accepted. Choose whether to open the generated crates.';
                renderFields(project);
            }
            if (project.id === 'lucky-nine' && action === 'stand' && payload.data) {
                state.luckyHistory.push(payload.data);
            }
            if (project.id === 'lucky-nine' && payload.data?.game) {
                state.luckyGame = payload.data.game;
                state.luckyStep = payload.next || null;
                help.textContent = action === 'stand'
                    ? 'Round complete. Deal again to start a new hand.'
                    : 'Choose Draw another or Stand.';
                renderFields(project);
            }
            if (project.id === 'lucky-nine' && action === 'stand') {
                state.luckyStep = null;
                state.luckyGame = null;
                renderFields(project);
            }
        } catch (error) {
            if (currentRequest !== state.requestId) return;
            output.textContent = error instanceof Error ? error.message : 'The experiment could not be completed.';
            setStatus('ERROR');
        } finally {
            if (currentRequest === state.requestId) state.running = false;
        }
    }
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        scheduleReactiveRun();
    });

    document.querySelector('#clear-terminal').addEventListener('click', () => { output.textContent = ''; });
    document.querySelector('#reset-project').addEventListener('click', renderProject);
    document.querySelector('#copy-source').addEventListener('click', async (event) => {
        try {
            await navigator.clipboard.writeText(selectedProject().source);
            event.currentTarget.textContent = 'Copied';
            window.setTimeout(() => { event.currentTarget.textContent = 'Copy source'; }, 1400);
        } catch {
            event.currentTarget.textContent = 'Copy unavailable';
        }
    });
    function syncThemeToggle() {
        themeToggle.textContent = document.documentElement.dataset.theme === 'dark' ? 'Light Mode' : 'Dark Mode';
    }

    themeToggle.addEventListener('click', (event) => {
        const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
        document.documentElement.dataset.theme = nextTheme;
        localStorage.setItem('sigfrid-portfolio-theme', nextTheme);
        event.currentTarget.textContent = nextTheme === 'dark' ? 'Light Mode' : 'Dark Mode';
    });

    syncThemeToggle();
    renderList();
    renderProject();
})();
