<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A small interactive playground for Sigfrid Barrera's PHP translations.">
    <title>Playground | Sigfrid Barrera</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/playground.css">
    <script>
        (() => {
            const saved = localStorage.getItem('sigfrid-portfolio-theme');
            document.documentElement.dataset.theme = saved === 'dark' ? 'dark' : 'light';
        })();
    </script>
</head>
<body class="playground-page">
    <a class="skip-link" href="#playground-main">Skip to playground</a>
    <header class="playground-header">
        <div class="container playground-header-inner">
            <a class="playground-brand" href="index.php" aria-label="Return to Sigfrid Barrera portfolio">
                <span class="brand-mark" aria-hidden="true">/</span>
                <span>Sigfrid Barrera</span>
            </a>
            <div class="playground-header-actions">
                <a class="text-link" href="index.php">Back to portfolio</a>
                <button class="theme-toggle" type="button" data-theme-toggle>Dark Mode</button>
            </div>
        </div>
    </header>

    <main id="playground-main" class="playground-main">
        <div class="container">
            <section class="playground-intro" aria-labelledby="playground-title">
                <div>
                    <p class="playground-kicker">Small working experiments</p>
                    <h1 id="playground-title">Playground</h1>
                    <p class="playground-lede">Browse small PHP translations of old experiments, provide an input, and see the result directly through the portfolio's local server.</p>
                </div>
                <div class="runtime-summary" aria-label="Runtime summary">
                    <span class="runtime-dot" aria-hidden="true"></span>
                    <span>PHP handlers <strong id="runtime-summary-state">ready</strong></span>
                </div>
            </section>

            <section class="playground-shell" aria-label="PHP playground">
                <aside class="project-explorer" aria-labelledby="explorer-title">
                    <div class="panel-heading">
                        <div>
                            <p class="panel-label">Explorer</p>
                            <h2 id="explorer-title">Projects</h2>
                        </div>
                        <span class="project-count" id="project-count">7</span>
                    </div>
                    <p class="panel-note">PHP translations, planned work, and archived source.</p>
                    <nav id="project-list" class="project-list" aria-label="Playground projects"></nav>
                    <p class="explorer-footnote">The original Java files remain preserved as references, but the runnable layer is PHP.</p>
                </aside>

                <div class="playground-workspace">
                    <section class="code-panel" aria-labelledby="code-title">
                        <div class="panel-heading panel-heading-compact">
                            <div>
                                <p class="panel-label">PHP implementation</p>
                                <h2 id="code-title">DavidStar.php</h2>
                            </div>
                            <button id="copy-source" class="text-button" type="button">Copy source</button>
                        </div>
                        <div class="source-meta" id="source-meta">PHP · ready</div>
                        <pre class="code-viewer" id="code-viewer" tabindex="0" aria-label="Read-only PHP implementation"><code>Loading PHP implementation...</code></pre>
                    </section>

                    <section class="terminal-panel" aria-labelledby="terminal-title">
                        <div class="panel-heading panel-heading-compact">
                            <div>
                                <p class="panel-label">Result</p>
                                <h2 id="terminal-title">Experiment output</h2>
                            </div>
                            <span class="execution-status status-ready" id="execution-status" role="status" aria-live="polite">READY</span>
                        </div>
                        <div class="terminal-window" id="terminal-window" role="log" aria-live="polite" aria-label="Experiment output">
                            <pre id="terminal-output">Choose an experiment to see its PHP input form.</pre>
                        </div>
                        <form class="terminal-controls" id="terminal-form">
                            <div id="experiment-fields" class="experiment-fields"></div>
                            <button class="button button-secondary" id="clear-terminal" type="button">Clear</button>
                            <button class="button button-secondary" id="reset-project" type="button">Reset</button>
                        </form>
                        <p class="terminal-help" id="terminal-help">David's Star is translated to PHP and accepts an odd integer from 7 to 15.</p>
                    </section>
                </div>
            </section>
        </div>
    </main>
    <script src="js/playground-projects.js"></script>
    <script src="js/playground.js"></script>
</body>
</html>
