const STORAGE_KEY = 'sigfrid-portfolio-theme';
const root = document.documentElement;
const themeToggle = document.querySelector('[data-theme-toggle]');
const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
const sidebar = document.querySelector('#sidebar-panel');
const sidebarCloseControls = document.querySelectorAll('[data-sidebar-close]');

function getPreferredTheme() {
    const savedTheme = localStorage.getItem(STORAGE_KEY);

    if (savedTheme === 'light' || savedTheme === 'dark') {
        return savedTheme;
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function applyTheme(theme) {
    root.dataset.theme = theme;

    if (themeToggle) {
        themeToggle.textContent = theme === 'dark' ? 'Light Mode' : 'Dark Mode';
        themeToggle.setAttribute('aria-label', `Switch to ${theme === 'dark' ? 'light' : 'dark'} theme`);
    }
}

applyTheme(getPreferredTheme());

themeToggle?.addEventListener('click', () => {
    const nextTheme = root.dataset.theme === 'dark' ? 'light' : 'dark';
    localStorage.setItem(STORAGE_KEY, nextTheme);
    applyTheme(nextTheme);
});

function setSidebarOpen(isOpen) {
    if (!sidebar || !sidebarToggle) return;
    sidebar.classList.toggle('is-open', isOpen);
    sidebar.setAttribute('aria-hidden', String(!isOpen));
    sidebarToggle.setAttribute('aria-expanded', String(isOpen));
    document.body.classList.toggle('sidebar-open', isOpen);
}

sidebarToggle?.addEventListener('click', () => setSidebarOpen(true));
sidebarCloseControls.forEach((control) => control.addEventListener('click', () => setSidebarOpen(false)));
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') setSidebarOpen(false);
});

document.querySelectorAll('[data-scroll-target]').forEach((link) => {
    link.addEventListener('click', (event) => {
        const targetSelector = link.getAttribute('href');
        const target = targetSelector ? document.querySelector(targetSelector) : null;

        if (!target) {
            return;
        }

        event.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});

// --- Option B async content loading ---

async function fetchJson(url) {
    const res = await fetch(url, { cache: 'no-store' });
    if (!res.ok) {
        throw new Error(`HTTP ${res.status}`);
    }
    const payload = await res.json();
    if (!payload.ok) {
        throw new Error(payload.error || 'Server error');
    }
    return payload.data;
}

function showLoading(el, message = 'Loading…') {
    el.textContent = message;
    el.style.display = 'block';
}

function hideLoading(el) {
    el.style.display = 'none';
}

function showError(el, msg) {
    el.textContent = msg;
    el.style.display = 'block';
}

function hideError(el) {
    el.style.display = 'none';
}

async function loadStack() {
    const loadingEl = document.querySelector('.stack-loading');
    const errorEl = document.querySelector('.stack-error');

    showLoading(loadingEl, 'Loading stack…');
    hideError(errorEl);

    try {
        const items = await fetchJson('php/getStack.php?featured=1');
        renderStack(items);
        hideLoading(loadingEl);
    } catch (err) {
        hideLoading(loadingEl);
        showError(errorEl, `Failed to load stack: ${err.message}`);
    }
}

function renderStack(items) {
    const section = document.getElementById('skills');
    if (!section) return;

    const existingChips = section.querySelector('.stack-chips');
    if (existingChips) {
        existingChips.remove();
    }

    const container = document.createElement('div');
    container.className = 'stack-chips';
    container.setAttribute('role', 'list');

    items.forEach((item) => {
        const chip = document.createElement('a');
        chip.className = 'stack-chip';
        chip.setAttribute('role', 'listitem');
        chip.href = 'stack.php';

        const iconPath = item.icon ? `images/icons/${escapeHtml(item.icon)}` : 'images/icons/placeholder.svg';

        chip.innerHTML = `
            <span class="stack-chip-icon">
                <img src="${iconPath}" alt="" onerror="this.src='images/icons/placeholder.svg'">
            </span>
            <span class="stack-chip-name">${escapeHtml(item.name)}</span>
        `;
        container.appendChild(chip);
    });

    // Add dashed "+ more" chip at the end
    const moreChip = document.createElement('a');
    moreChip.className = 'stack-chip stack-chip-more';
    moreChip.setAttribute('role', 'listitem');
    moreChip.href = 'stack.php';
    moreChip.innerHTML = `
        <span class="stack-chip-icon">+</span>
        <span class="stack-chip-name">more</span>
    `;
    container.appendChild(moreChip);

    const sectionContent = section.querySelector('.container');
    (sectionContent || section).appendChild(container);
}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

async function loadProjects() {
    const loadingEl = document.querySelector('.projects-loading');
    const errorEl = document.querySelector('.projects-error');

    showLoading(loadingEl, 'Loading projects…');
    hideError(errorEl);

    try {
        const projects = await fetchJson('php/getProjects.php');
        renderProjects(projects);
        hideLoading(loadingEl);
    } catch (err) {
        hideLoading(loadingEl);
        showError(errorEl, `Failed to load projects: ${err.message}`);
    }
}

function renderProjects(projects) {
    const section = document.getElementById('projects');
    if (!section) return;

    const insertionPoint = section.querySelector('.projects-error');
    if (!insertionPoint) return;

    // Remove existing grid if present
    const existingGrid = section.querySelector('.projects-grid');
    if (existingGrid) {
        existingGrid.remove();
    }

    const grid = document.createElement('div');
    grid.className = 'projects-grid';
    grid.setAttribute('role', 'list');

    projects.forEach((proj) => {
        const card = document.createElement('article');
        card.className = 'project-card';
        card.setAttribute('role', 'listitem');

        // Build image section if image_url exists
        let imageHtml = '';
        if (proj.image_url && !proj.image_url.includes('via.placeholder.com')) {
            const imagePath = escapeHtml(proj.image_url);
            imageHtml = `
                <div class="project-image">
                    <img src="${imagePath}" alt="${escapeHtml(proj.title)}" loading="lazy" onerror="this.parentElement.style.display='none'">
                </div>
            `;
        }

        // Build tech tags
        const tags = proj.tech_tags ? proj.tech_tags.split(',').map(t => t.trim()).filter(Boolean) : [];
        const tagsHtml = tags.length
            ? `<div class="project-tags">${tags.map(t => `<span class="project-tag">${escapeHtml(t)}</span>`).join('')}</div>`
            : '';

        // Build action buttons
        const githubBtn = proj.github_url
            ? `<a href="${escapeHtml(proj.github_url)}" class="project-btn" target="_blank" rel="noopener">GitHub</a>`
            : '';
        card.innerHTML = `
            ${imageHtml}
            <div class="project-body">
                <h3 class="project-title">${escapeHtml(proj.title)}</h3>
                <p class="project-desc">${escapeHtml(proj.description)}</p>
                ${tagsHtml}
                <div class="project-actions">
                    ${githubBtn}
                </div>
            </div>
        `;
        grid.appendChild(card);
    });

    insertionPoint.insertAdjacentElement('afterend', grid);
}

async function loadRadar() {
    const radarEl = document.getElementById('radar-chart');
    if (!radarEl) return;

    try {
        const radarData = await fetchJson('php/getRadar.php');
        renderRadarChart(radarEl, radarData);
    } catch (err) {
        radarEl.innerHTML = `<p class="skills-error" style="display:block;">Failed to load radar: ${escapeHtml(err.message)}</p>`;
    }
}

function renderRadarChart(container, data) {
    if (!data || data.length === 0) return;
    container.innerHTML = '';

    const size = 340;
    const center = size / 2;
    const radius = size * 0.36;
    const levels = 4;

    let svg = `<svg viewBox="0 0 ${size} ${size}" width="100%" height="100%" style="overflow: visible;">`;
    const total = data.length;
    
    // Draw background concentric polygon rings
    for (let l = 1; l <= levels; l++) {
        const r = (radius / levels) * l;
        let points = '';
        for (let i = 0; i < total; i++) {
            const angle = (Math.PI * 2 / total) * i - Math.PI / 2;
            const x = center + r * Math.cos(angle);
            const y = center + r * Math.sin(angle);
            points += `${x},${y} `;
        }
        svg += `<polygon points="${points.trim()}" fill="none" stroke="var(--border)" stroke-width="1" stroke-dasharray="3 3" opacity="0.6"/>`;
    }

    // Draw axis lines from center to each vertex
    for (let i = 0; i < total; i++) {
        const angle = (Math.PI * 2 / total) * i - Math.PI / 2;
        const x = center + radius * Math.cos(angle);
        const y = center + radius * Math.sin(angle);
        svg += `<line x1="${center}" y1="${center}" x2="${x}" y2="${y}" stroke="var(--border)" stroke-width="1" opacity="0.6"/>`;
    }

    // Calculate data polygon points
    let dataPoints = '';
    const vertexCoords = [];
    data.forEach((item, i) => {
        const val = Math.min(Math.max(item.value, 0), 100);
        const r = radius * (val / 100);
        const angle = (Math.PI * 2 / total) * i - Math.PI / 2;
        const x = center + r * Math.cos(angle);
        const y = center + r * Math.sin(angle);
        dataPoints += `${x},${y} `;
        vertexCoords.push({ x, y, label: item.label, value: item.value, index: i });
    });

    // Draw data polygon fill & stroke
    svg += `<polygon class="radar-data" points="${dataPoints.trim()}" fill="var(--accent-soft)" stroke="var(--accent)" stroke-width="2" opacity="0.9"/>`;

    // Draw vertex dots and labels
    vertexCoords.forEach((v) => {
        svg += `<circle class="radar-point radar-point-${v.index}" cx="${v.x}" cy="${v.y}" r="4.5" fill="var(--accent)" />`;
        
        const angle = (Math.PI * 2 / total) * v.index - Math.PI / 2;
        const labelRadius = radius + 32;
        const lx = center + labelRadius * Math.cos(angle);
        const ly = center + labelRadius * Math.sin(angle);
        
        let anchor = "middle";
        if (Math.abs(lx - center) > 12) {
            anchor = lx > center ? "start" : "end";
        }
        
        const capsuleWidth = Math.max(76, (String(v.label).length + String(v.value).length + 4) * 5.2 + 18);
        svg += `<g class="radar-node" data-radar-index="${v.index}" tabindex="0" role="button" aria-label="${escapeHtml(v.label)}: ${v.value}"><rect class="radar-node-capsule" x="${lx - capsuleWidth / 2}" y="${ly - 11}" width="${capsuleWidth}" height="22" rx="11"/><text x="${lx}" y="${ly}" fill="var(--text)" font-family="var(--font-code)" font-size="8.5" font-weight="600" text-anchor="middle" dominant-baseline="central">${escapeHtml(v.label)} (${v.value})</text></g>`;
    });

    svg += `</svg>`;
    container.innerHTML = svg;
    const chart = container.querySelector('svg');
    container.querySelectorAll('.radar-node').forEach((node) => {
        const setActive = (active) => {
            const index = node.dataset.radarIndex;
            node.classList.toggle('is-active', active);
            chart.classList.toggle('is-hovering', active);
            container.querySelector(`.radar-point-${index}`)?.classList.toggle('is-active', active);
        };
        node.addEventListener('mouseenter', () => setActive(true));
        node.addEventListener('mouseleave', () => setActive(false));
        node.addEventListener('focus', () => setActive(true));
        node.addEventListener('blur', () => setActive(false));
    });
}

// Initialize async loads on DOM ready
function initAsyncContent() {
    loadStack();
    loadProjects();
    loadRadar();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAsyncContent);
} else {
    initAsyncContent();
}
