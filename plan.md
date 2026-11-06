# Portfolio Landing Page Plan

## Current baseline

- `index.php` and `css/style.css` do not exist yet.
- `js/main.js` already expects these page hooks:
  - `[data-theme-toggle]`
  - `[data-scroll-target]`
  - `#skills`, `.stack-loading`, `.stack-error`
  - `#projects`, `.projects-loading`, `.projects-error`
  - `#radar-chart`
- The JavaScript loads:
  - `php/getStack.php?featured=1` for the featured stack
  - `php/getProjects.php` for project cards
  - `php/getRadar.php` for the SVG radar
- `php/getSkills.php` exists for a future full skills view but is not currently consumed by `main.js`.
- The SQL schema defines `skills`, `projects`, and `skill_radar`. The seed data contains 18 skills, 12 featured skills, and six radar areas; project rows are expected to be populated independently.
- A CLI connection attempt to `127.0.0.1` failed with `ERROR 2002 (HY000): Can't connect to MySQL server on 'localhost' (10061)`, so the live row counts still need confirmation after MariaDB is started.

## Design contract

### Visual direction

- Nord light and dark themes using shared CSS custom properties.
- Readable sans-serif body text; JetBrains Mono only for navigation, section indices, metadata, tags, and terminal-like details.
- Restrained developer portfolio styling: muted blue-gray surfaces, frost/cyan accents, generous spacing, subtle borders, and limited motion.
- Cybersecurity identity should come from the content and small technical details, not neon, glitch, matrix, or fake-OS decoration.

### Content direction

The page should present Sigfrid as an IT student developing toward cybersecurity while retaining software development and networking strengths. Use evidence-based content and avoid professional or certification claims that are not supported by the actual record.

### Accessibility and behavior

- Use semantic landmarks, heading hierarchy, visible focus states, keyboard-operable controls, descriptive link text, and adequate contrast in both themes.
- Keep the theme toggle usable without JavaScript flash where practical and preserve the existing localStorage/system-preference behavior in `main.js`.
- Respect reduced-motion preferences.
- Every async section must have loading, success, empty, and error states that remain understandable without the visual styling.

## Loop 1 — Plan and page structure

### Build

Create `index.php` as a single beginner-defensible public page with:

1. Header/navigation with section links and the theme toggle.
2. Hero section with a concise student/cybersecurity introduction, primary calls to action, and the existing portrait asset.
3. About section with a short evidence-based profile and a `#radar-chart` container.
4. Skills section with a featured-stack loading state, error state, and a link to the future full stack page.
5. Selected projects section with loading and error states; `main.js` will insert `.projects-grid` after `.section-label`.
6. Cybersecurity/networking experience and achievements content based only on documented facts in `context.md`.
7. Education section.
8. Contact and social links, keeping contact-form async submission out of this first loop unless the required endpoint exists.
9. Footer and script/style references.

Ensure the markup preserves the exact selectors used by `main.js` and uses relative paths that work from the XAMPP document root.

### Verify

- Confirm every selector required by `main.js` exists exactly once where appropriate.
- Confirm every interactive control has a keyboard-accessible name and every section has a unique ID.
- Confirm project and skill containers are valid before JavaScript inserts content.

### Test with Playwright

Run the page through Apache at `http://localhost/portfolio/` and check:

- The document loads without console errors.
- Navigation links reach the intended sections.
- The theme toggle changes `data-theme`, updates its accessible label, and persists after reload.
- At least one loading state is observable before async content resolves when the network is throttled.

## Loop 2 — Nord design system and responsive layout

### Applied stack redesign

- Consolidated the Stack section into one centered glass toolkit shell.
- Added an inset chip field with centered desktop wrapping and left-aligned mobile wrapping.
- Added restrained hover lift while preserving live database rendering.
- Removed the solid surface beneath the Stack card so the fixed grid background remains visible around it.

### Applied projects redesign

- Featured only DeviceLabs, DiagnoSys, and iDoctor through the database `featured` flag.
- Added repository-derived descriptions, stack tags, GitHub/demo URLs, and local featured covers.
- Projects now alternate image/content sides in a centered glass-card layout.
- Added a `/playground` More projects route for future work.

### Applied projects balance pass

- Reframed each project as an image-led feature plate with a centered, enlarged cover image.
- Separated the visual and information layers with an inset border and a dedicated metadata band.
- Kept the content readable through a two-column desktop information layout, collapsing to a single column on mobile.
- Added restrained image zoom/saturation on hover and focus without changing the existing project data or links.
- Verified the three-project set at desktop and mobile sizes with no horizontal overflow; Impeccable detector returned no findings.

### Applied projects alignment pass

- Moved the dynamically inserted project grid out of the heading wrapper and into the section’s normal content flow.
- Centered the grid and constrained each feature plate to a consistent 980px maximum width.
- Added deliberate separation above the cards so the heading, intro, and first image read as distinct layers.
- Rechecked desktop and mobile alignment, overflow, PHP syntax, and the Impeccable detector.

### Applied Playground routing fix

- Added a direct `Playground` destination to the index page’s primary navigation.
- Changed the projects-section `More projects` action from the web-root `/playground` path to the project-relative `playground.php` route used by Apache.
- Verified both entry points resolve to the Playground page without changing in-page scroll behavior.

### Applied education section redesign

- Replaced the single two-column education block with a resume-inspired timeline.
- Added two aligned entries for Secondary Education and Tertiary Education, with a consistent marker rail, date rail, and location hierarchy.
- Marked 2028 as the expected graduation year and added a narrow-screen layout that keeps dates readable without horizontal overflow.

### Build

Create `css/style.css` with:

- Base reset, `box-sizing`, body typography, and readable measure.
- Nord light/dark token sets matching `context.md`.
- Shared layout tokens for content width, spacing, radius, borders, shadows, and transitions.
- Header, hero, section labels, buttons, cards, tags, stack chips, radar container, and footer styles.
- Desktop-first composition that collapses cleanly at tablet and mobile widths.
- Portrait styling that keeps the current intended proportions and uses `object-fit: cover`.
- Empty/loading/error styling for async sections.
- `:focus-visible`, hover, disabled, and reduced-motion behavior.

Avoid hard-coding theme colors separately in each component. Use the token layer so the radar SVG and dynamically generated cards inherit the active theme.

### Verify

- Check light and dark contrast for body text, muted text, borders, links, controls, tags, and error states.
- Check that long project titles, missing images, absent URLs, and empty API arrays do not break the layout.
- Check overflow and readable line length at mobile widths.

### Test with Playwright

Capture and inspect the rendered page at:

- 1440 × 1000 desktop
- 1024 × 900 tablet
- 390 × 844 mobile

For each viewport, verify no horizontal scrollbar, no clipped controls, visible focus indication, and stable section spacing. Repeat the checks in both themes and with reduced motion enabled.

## Loop 3 — Async data integration

### Build

Wire the page to the existing endpoints without changing their public response shape:

- Render featured skills into `#skills` from `getStack.php?featured=1`.
- Render all returned projects into `#projects` from `getProjects.php`.
- Render the radar SVG into `#radar-chart` from `getRadar.php`.
- Keep loading and error messages visible only while relevant.
- Preserve `escapeHtml()` and the existing external-link `noopener` behavior.

Use the existing database fields as follows:

- Skills: `name`, `icon`, `featured`, `sort_order`.
- Projects: `title`, `description`, `tech_tags`, `cover_image` mapped to `image_url`, `github_url`, `demo_url`.
- Radar: `label`, `value`, `sort_order`.

Do not add an admin panel, framework, or new API abstraction in this loop.

### Verify

- Start Apache and MariaDB in XAMPP.
- Run the SQL inspection command below and compare the live tables/rows with `database/portfolio.sql`.
- Open each endpoint directly and verify valid JSON with `ok: true`.
- Confirm icon and image paths resolve or degrade to the existing placeholder behavior.

```powershell
& 'C:\xampp\mysql\bin\mysql.exe' -uroot -e "USE sigfrid_portfolio; SHOW TABLES; SELECT COUNT(*) AS skills_count FROM skills; SELECT COUNT(*) AS featured_count FROM skills WHERE featured=1; SELECT COUNT(*) AS projects_count FROM projects; SELECT COUNT(*) AS radar_count FROM skill_radar;"
```

### Test with Playwright

- Wait for `.stack-chips`, `.projects-grid`, and the radar SVG.
- Assert the rendered counts match the API response.
- Intercept each endpoint with a server-error response and verify the corresponding user-readable error state.
- Intercept empty successful arrays and verify the page remains usable rather than showing stale content.
- Check that external project links have the expected target and rel attributes.

## Loop 4 — Content quality and interaction hardening

### Build

- Replace placeholder copy with concise, factual portfolio content.
- Add meaningful `alt` text for the portrait and project images; keep decorative icon alt text empty.
- Add `aria-labelledby`/`aria-describedby` relationships where they improve section comprehension.
- Ensure nav links, CTAs, social links, and project actions have clear destinations.
- Keep animations subtle and disable nonessential motion under `prefers-reduced-motion`.
- Add only small terminal/IDE-inspired details that support the Nord developer identity; do not make the page depend on terminal commands.

### Verify

- Run a keyboard-only pass through the entire page.
- Confirm the first meaningful heading, navigation, and primary CTA are available in a sensible order.
- Confirm errors do not expose database details.
- Confirm the page remains useful when JavaScript is unavailable, at minimum retaining the static structure and primary contact/social links.

### Test with Playwright

- Test keyboard tab order and activation of the theme toggle and navigation.
- Test browser reload after selecting each theme.
- Test mobile menu behavior if a collapsible menu is introduced; otherwise do not add one.
- Record console errors, failed requests, and layout failures at all three target viewports.

## Loop 5 — Final verification and documentation

### Build

- Run the Impeccable detector once against the changed UI files after implementation:

```powershell
& 'C:\Users\User\.agents\skills\impeccable\scripts\impeccable.cmd' detect --json index.php css/style.css
```

- Fix only findings caused by the new page/styles.
- Update README/setup documentation later with XAMPP, database import, and local URL instructions; do not add that scope to the first page build unless requested.

### Verify

- Re-run the focused Playwright checks after any detector-driven fixes.
- Confirm Apache/PHP renders the page, the async endpoints return JSON, and both themes remain functional.
- Confirm no secrets are added and the public page exposes no database connection information.

### Completion criteria

- `index.php` and `css/style.css` exist and follow the current JS/PHP contracts.
- The page is responsive and accessible at the three target viewport sizes.
- Theme switching is event-driven and persistent.
- Skills, projects, and radar data load asynchronously with explicit loading/error handling.
- Playwright verifies the success and failure paths rather than only checking that the page opens.
- MariaDB live verification is completed once the XAMPP service is running.

---

# Playground Plan

## Current baseline

- The existing portfolio already links to `/playground` from the selected projects section.
- The project directory is named `playround_projects`; preserve that existing path unless a deliberate rename is performed.
- Current project inventory includes:
  - `DavidStar.java`
  - `BarreraInputBasedNumberedTriangle.java`
  - `BarreraLootMasterLootGenerator.java`
  - `MidtermPractice.java`
  - `LuckyNineBarrera/`
  - `gigapet/`
- `DavidStar.java` is a console-based Java program that uses `Scanner`, validates an odd size from 7 to 15, and prints a David's Star pattern.
- Some project folders contain compiled `.class` files without corresponding source files. These must remain browseable but must not be presented as runnable source projects until their source/runtime requirements are confirmed.
- The portfolio's existing design contract requires Nord light/dark themes, restrained technical details, explicit async loading/error states, keyboard access, and reduced-motion support.

## Technical direction

### Runtime and UI libraries

- Use [CheerpJ](https://cheerpj.com/docs/overview) as the primary Java runtime candidate. It runs Java bytecode in the browser through WebAssembly and supports console applications from compiled JARs.
- Use [xterm.js](https://xtermjs.org/docs/) for the virtual terminal, including keyboard input, terminal output, scrollback, fit behavior, and terminal state.
- Use [Monaco Editor](https://github.com/microsoft/monaco-editor) for the read-only code viewer with Java syntax highlighting, line numbers, and folding.
- Treat CheerpJ as a technical dependency that must pass a focused `Scanner`/`System.in` proof of concept before the full playground is built.
- Keep a clearly labelled simulation fallback for browsers or projects where the actual Java runtime cannot initialize. Never imply that a simulation executed the original Java program.
- Do not use a server endpoint for arbitrary Java compilation or execution.

### Visual direction

- Extend the existing Nord light/dark tokens and typography from `css/style.css`.
- Present the playground as an experience surface centered on the artifact, not as a generic fake operating system.
- Use a restrained IDE/terminal composition:
  - project explorer
  - source viewer
  - virtual terminal
  - execution status
- Make asynchronousness visible through a small state pipeline:
  - `READY`
  - `QUEUED`
  - `RUNNING`
  - `COMPLETE`
  - `ERROR`
- Use motion only to communicate state or progressive output; respect `prefers-reduced-motion`.

### Content and safety direction

- Preserve the original source files as the source of truth.
- Do not rewrite code solely to make it look more polished in the viewer.
- Describe each project factually using its inputs, output, language, and current availability.
- Treat source files and compiled artifacts as static portfolio assets.
- Validate project identifiers against a fixed manifest. Do not allow user input to select arbitrary server paths or commands.
- Do not expose Java stack traces, filesystem paths, server configuration, or database details to visitors.

## Loop 1 — Runtime proof of concept and project inventory

### Gate status — 2026-11-06

- Inventory completed: four standalone Java sources, two source-backed `gigapet` classes, and a compiled-only `LuckyNineBarrera` directory.
- `DavidStar.java` compiles reproducibly with JDK 22 into `playround_projects/DavidStar.jar`.
- Local CLI execution passed with input `7` and produced the expected David's Star output.
- CheerpJ 5.0 loaded through Apache and fetched the JAR successfully.
- CheerpJ does not expose a documented live `System.in` bridge for `cheerpjRunMain`, so the approved runtime-adapter boundary supplies the browser input through a fixed argument and restores it as `System.in` before invoking the original `DavidStar.main`.
- Browser proof now passes through Apache with Java 17-compatible bytecode: input `7` produces the expected star output, and input `6` produces the original validation message.
- CheerpJ runtime loading and output capture pass; the full Playground page is ready for the next approval gate.

### Build

Create a narrow proof of concept before the full page:

1. Compile `DavidStar.java` into a browser-loadable JAR with a known main class.
2. Load CheerpJ from its documented distribution.
3. Confirm that the Java program can write to stdout and receive typed input through `Scanner`.
4. Define the initial project manifest with:
   - stable project ID
   - title and description
   - source path
   - compiled artifact path
   - main class
   - Java version
   - input fields and validation
   - runtime/source availability
5. Inventory every file and folder under `playround_projects`, distinguishing:
   - source-backed runnable projects
   - source-backed projects that need compilation
   - compiled-only projects
   - projects with unsupported or unknown runtime behavior.

Do not build the complete page until the `DavidStar.java` proof of concept demonstrates working input and output.

### Verify

- Confirm the Java compiler output is reproducible and does not include unrelated files.
- Confirm CheerpJ initialization completes through the local Apache server.
- Confirm `System.out.print()` and `System.out.println()` appear in the browser.
- Confirm valid input produces the expected pattern.
- Confirm invalid values such as `6`, `8`, and `16` are rejected according to the original program.
- Confirm the program can complete and the page remains responsive.
- Confirm no arbitrary source path, class name, or command can be supplied through the browser.

### Test with Playwright

- Load the proof-of-concept route through `http://localhost/portfolio/`.
- Type valid and invalid input into the terminal.
- Assert that the expected prompt, validation message, and output appear.
- Record runtime initialization time, console errors, failed network requests, and uncaught exceptions.
- Test at least one current Chromium-based browser and one additional supported browser where available.

## Loop 2 — Playground page structure and responsive layout

### Build

Create the initial playground surface with:

1. `playground.php` containing semantic page structure and progressive enhancement content.
2. A page header introducing the projects as small working experiments.
3. A project explorer listing all manifest entries and clearly marking unavailable projects.
4. A read-only code viewer region.
5. A virtual terminal region.
6. A visible execution status element and async pipeline indicator.
7. Run, clear, reset, and cancel controls where the active runtime supports them.
8. Static project metadata and source fallback content that remains useful before JavaScript loads.

Create:

- `css/playground.css` for the page-specific layout and terminal/editor styling.
- `js/playground.js` for selection, state transitions, rendering, and runtime coordination.
- `js/playground-projects.js` for the fixed project manifest and runtime adapters.
- `playround_projects/manifest.json` only if the final implementation benefits from a static manifest rather than bundling the same metadata in JavaScript.

Use the following desktop composition:

```text
┌────────────────────┬──────────────────────────┐
│ Project explorer    │ Read-only code viewer    │
│                    ├──────────────────────────┤
│                    │ Virtual terminal         │
└────────────────────┴──────────────────────────┘
```

On narrow screens, stack the explorer, code viewer, and terminal in that order. Constrain horizontal scrolling to code and terminal regions rather than the page.

### Verify

- Confirm the page has one clear `h1`, labelled landmarks, and a predictable focus order.
- Confirm the active project uses `aria-selected` and a visible selected state.
- Confirm the code viewer and terminal have accessible names.
- Confirm unavailable projects can be selected without causing a runtime error.
- Confirm the page does not depend on CheerpJ or Monaco to expose its basic project information.

### Gate result

- **Approved:** full Playground construction began after the adapter-backed runtime proof passed.
- **Passed:** `playground.php`, `css/playground.css`, `js/playground.js`, and `js/playground-projects.js` now provide the semantic explorer/source/terminal composition, progressive enhancement fallback, fixed manifest, explicit availability states, accessible labels, and responsive stacking rules.
- **Passed in Playwright:** page route loads through Apache, all seven manifest entries are reachable, unavailable projects select safely, theme switching works, and the proven DavidStar runtime reaches `COMPLETE` for valid input `7` and invalid input `6`.
- **Known scope:** live character-by-character stdin forwarding and xterm.js/Monaco integration remain later hardening work; the current runtime uses the verified adapter argument bridge and accessible `<pre>` fallback.

### Test with Playwright

Capture and inspect:

- 1440 × 1000 desktop
- 1024 × 900 tablet
- 390 × 844 mobile

For each viewport, verify:

- no horizontal page overflow
- no clipped run controls
- readable code and terminal content
- stable explorer-to-content layout
- usable keyboard focus indicators
- both light and dark themes

## Loop 3 — Real Java execution and terminal integration

### Build

Connect the page to the runtime in this order:

1. Initialize CheerpJ once and expose an explicit loading state.
2. Load the selected compiled JAR from the fixed manifest.
3. Start the selected main class.
4. Connect Java stdout/stderr to xterm.js.
5. Forward terminal input to Java stdin.
6. Update the execution state as the process moves through initialization, input, output, completion, and failure.
7. Prevent duplicate execution while a process is active.
8. Clear or reset the terminal without losing the selected source.

Keep the runtime adapter separate from the page renderer so each project declares its own:

- main class
- input expectations
- validation boundaries
- runtime availability
- cleanup behavior

Support the first source-backed projects in this order:

1. `DavidStar.java`
2. `BarreraInputBasedNumberedTriangle.java`
3. `BarreraLootMasterLootGenerator.java`
4. `MidtermPractice.java`

Do not claim that `LuckyNineBarrera/` or `gigapet/` runs until their source, entry point, dependencies, and UI/runtime behavior are verified.

### Verify

- Confirm stdout and stderr are visually distinguishable where supported.
- Confirm typed input is delivered only to the active Java process.
- Confirm output remains ordered and readable when multiple lines arrive quickly.
- Confirm completion returns the interface to `READY`.
- Confirm runtime exceptions produce a user-readable error without exposing implementation details.
- Confirm cancel/reset releases listeners and does not leave a process writing into a new run.
- Confirm a failed CheerpJ initialization activates the explicitly labelled fallback mode.

### Test with Playwright

- Run each supported project with valid input.
- Test invalid input and boundary values.
- Test rapid repeated clicks on `Run`.
- Test clearing and resetting while output is visible.
- Intercept runtime/JAR failures and verify the error state.
- Reload after a completed and failed run and verify the page starts cleanly.
- Assert that terminal output is not silently replaced by a fake success message.

## Loop 4 — Code viewer, project coverage, and asynchronous interaction hardening

### Build

- Load the selected source into Monaco in read-only mode with Java language highlighting.
- Provide line numbers, scrolling, code folding where supported, and a copy-source action.
- Use a lightweight `<pre>` fallback for unavailable Monaco/mobile contexts.
- Display source metadata:
  - filename
  - line count
  - language
  - input requirements
  - execution availability
- Add progressive output delays only when needed to make execution state understandable; do not distort actual program output.
- Add a fallback simulation only for explicitly declared projects and label it `SIMULATION MODE`.
- Add empty, unavailable, loading, complete, and error states that remain understandable without color or animation.
- Preserve the original project behavior where feasible rather than replacing complex programs with generic mock output.

### Verify

- Confirm source content matches the files in `playround_projects`.
- Confirm copy-source preserves the complete source text.
- Confirm all manifest entries are reachable and have a meaningful availability state.
- Confirm no project can load a path outside the approved project manifest.
- Confirm reduced motion removes nonessential output animation while retaining state updates.
- Confirm terminal output and status changes are announced appropriately to assistive technology.

### Test with Playwright

- Select every project entry.
- Compare displayed source text and line count with the source files.
- Test copy-source from keyboard and mouse.
- Test the unavailable state for compiled-only projects.
- Test the labelled simulation fallback by forcing CheerpJ initialization failure.
- Test keyboard-only project selection, source scrolling, terminal input, and action controls.

## Loop 5 — Final verification, performance, accessibility, and documentation

### Build

- Add only the required runtime assets and preserve their license notices.
- Document CheerpJ, xterm.js, and Monaco dependencies, including CheerpJ's personal/educational/non-commercial licensing boundary.
- Document the local XAMPP setup and the requirement that the server supports the runtime's requested static assets/range behavior.
- Keep the playground independent from the portfolio database unless project metadata is intentionally migrated later.
- Run the Impeccable detector once against changed UI files after implementation:

```powershell
& 'C:\Users\User\.agents\skills\impeccable\scripts\impeccable.cmd' detect --json playground.php css/playground.css js/playground.js js/playground-projects.js
```

- Fix only findings caused by the playground changes.

### Verify

- Re-run the focused runtime and Playwright checks after detector-driven fixes.

---

# Superseding direction: PHP Playground

**Decision date:** 2026-11-06  
**Status:** Planning only; implementation requires a new approval gate.

The CheerpJ/Java runtime direction is superseded because it makes the Playground difficult to explain and maintain. The new direction is to translate the source-backed experiments into small, explicit PHP implementations and run those implementations directly through the existing Apache/PHP stack.

## Product decision

The Playground should be explainable as:

> “These are small experiments rewritten as PHP handlers so you can run them directly in the browser.”

The original Java files remain preserved as portfolio evidence, but they are no longer the execution runtime. The interface should say **PHP translation** or **PHP implementation**, not imply that Java is running.

## Scope and availability

### Translate and run

1. `DavidStar.java` → `DavidStar.php`
   - One integer input.
   - Preserve the original validation: odd number from 7 to 15.
   - Preserve the original David’s Star output.
2. `BarreraInputBasedNumberedTriangle.java` → `NumberedTriangle.php`
   - One positive integer height.
   - Preserve the original ascending and descending numbered triangle output.
3. `BarreraLootMasterLootGenerator.java` → `LootMaster.php`
   - Use a staged form flow rather than pretending to stream console input:
     - choose skill;
     - choose whether to open crates;
     - render the generated loot result.
   - Keep random loot generation server-side and use the original rarity intent.
4. `MidtermPractice.java` → `MidtermPractice.php`
   - Preserve the deterministic ATM setup and account 20 balance inquiry.
   - Present the result as a small inspectable demo, not an interactive banking product.
5. `gigapet/GigaPet.java` + `gigapet/GigaPetGame.java` → PHP translation only after their actual source and flow are reviewed.
   - Keep this entry marked “planned” until the source-backed behavior is mapped.

### Browse only / do not fabricate

- `LuckyNineBarrera/` remains archived/unavailable because only compiled classes are present.
- Any project without enough source or a clear entry point remains browseable as an archive, not runnable.

## Architecture

### Fixed project manifest

Replace the Java/CheerpJ manifest fields with:

- stable project ID;
- display title;
- original source path;
- PHP implementation path;
- description;
- input schema;
- availability (`ready`, `planned`, `archived`);
- output format (`text`, `steps`, or `result`);
- whether the implementation uses randomness.

The browser may submit only a fixed project ID and declared input names. It must never submit a PHP path, function name, shell command, or source code.

### Safe execution model

Create one explicit PHP dispatcher, for example `php/playground-run.php`:

1. Accept `POST` requests only.
2. Validate the CSRF/session strategy used by the site, or document why the local portfolio does not need a session-backed CSRF token.
3. Validate the project ID against the fixed manifest.
4. Validate and normalize each declared input using project-specific bounds.
5. Dispatch to a named PHP handler through a fixed allowlist.
6. Return a consistent JSON response:
   - `ok`;
   - `project`;
   - `status`;
   - `output`;
   - optional structured `steps`;
   - user-safe `error`.
7. Never use `eval`, `include` from request data, shell execution, arbitrary file reads, or dynamic function names from request data.

Prefer direct handler calls over a generic interpreter. Each translation should be independently readable and testable.

### Interaction model

Replace the terminal metaphor with a lightweight “run experiment” panel:

- project explorer;
- original-source reference;
- PHP implementation label;
- simple input form generated from the fixed manifest;
- Run button;
- Reset button;
- result/output panel;
- status states: `READY`, `RUNNING`, `COMPLETE`, `ERROR`.

The code viewer may remain as a read-only reference panel, but it should show the PHP translation by default and identify the original Java file as the source reference. Remove the CheerpJ runtime status, Java runtime language, terminal input claims, and xterm/Monaco requirements.

## Implementation loops and approval gates

### Loop A — Translation contract gate

**Plan**

- Confirm the user approves PHP as the execution language.
- Decide whether the UI should show:
  - PHP translation only; or
  - PHP translation with an expandable original Java reference.
- Define the shared JSON response shape and fixed manifest.
- Confirm archived/compiled-only projects are not falsely translated.

**Build**

- Add the manifest and dispatcher skeleton.
- Add one handler for `DavidStar`.
- Keep the existing Playground page untouched until the handler contract is proven.

**Verify**

- PHP lint all new files.
- Submit valid, invalid, missing, and unknown project inputs.
- Confirm unknown project IDs cannot select files or handlers.
- Confirm no Java runtime assets are needed.

**Gate**

- Do not migrate the rest of the projects until the `DavidStar` PHP handler returns the correct output and safe errors.

### Loop A result — 2026-11-06

- **Passed:** added the fixed manifest at `php/playground-manifest.php`.
- **Passed:** added the explicit `DavidStar` translation at `php/playground/DavidStar.php`.
- **Passed:** added the POST-only JSON dispatcher at `php/playground-run.php`.
- **Passed:** PHP lint passes for all three new files.
- **Passed:** valid input `7` returns the David's Star output with `COMPLETE`.
- **Passed:** invalid input `6` returns HTTP `422` with a user-safe validation error.
- **Passed:** unknown project IDs return HTTP `404`; GET requests return HTTP `405` with `Allow: POST`.
- **Security boundary:** the endpoint uses a fixed project allowlist and direct handler call. It does not evaluate request input, load request-controlled files, execute shell commands, or expose Java/CheerpJ runtime behavior.
- **CSRF note:** this first handler is a stateless computation endpoint with no authenticated session or persistent mutation. If the site later adds session-backed state to the Playground, add the site's CSRF strategy before enabling that state.

**Next gate:** approve Loop B before translating the deterministic triangle and ATM projects.

### Loop B — Translate deterministic projects

**Build**

- Implement `NumberedTriangle.php` and `MidtermPractice.php`.
- Add their schemas and fixed dispatch entries.
- Keep output formatting equivalent to the original behavior.

**Verify**

- Compare representative outputs against the original Java behavior:
  - David’s Star: `7`, `6`, `8`, `16`;
  - triangle: positive, zero, and negative heights;
  - ATM: account 20 before/after withdrawal where applicable.
- Add focused CLI or HTTP tests for response shape and validation.

**Gate**

- Approve deterministic project parity before adding multi-step/random behavior.

### Loop B result — 2026-11-06

- **Passed:** added `NumberedTriangle.php` with a bounded positive height input from 1 to 30.
- **Passed:** added `MidtermPractice.php` with the original account 20 BPI inquiry, withdrawal, and second inquiry.
- **Passed:** added both projects to the fixed manifest and direct dispatcher contract.
- **Passed:** PHP lint passes for both handlers and the dispatcher.
- **Passed:** triangle height `3` returns the expected ascending/descending pattern.
- **Passed:** triangle height `0` and missing height return HTTP `422` with safe validation errors.
- **Passed:** ATM output reports BPI account 20 changing from `300` to `200`.

**Next gate:** approve Loop C before translating the multi-step random LootMaster flow.

### Loop C — Translate LootMaster as a staged flow

**Build**

- Convert console prompts into explicit form steps.
- Keep the selected skill and crate decision in a short-lived server-side flow or signed/validated hidden state.
- Generate loot only after the user submits the crate decision.
- Use a safe random source and preserve the original rarity categories.

**Verify**

- Test valid skills, exit/other skill, open crates, skip crates, missing fields, and malformed values.
- Confirm random output is labelled as generated output, not a deterministic claim.
- Confirm repeated submission does not accidentally reuse stale state.

**Gate**

- Approve the staged interaction before migrating the packaged GigaPet project.

### Loop C result — 2026-11-06

- **Passed:** added `LootMaster.php` as a two-step server-side flow.
- **Passed:** skill selection accepts only `1`, `2`, or `3`.
- **Passed:** the crate step validates the open/skip decision before generating output.
- **Passed:** loot quantity, boss health, and rarity selection are generated server-side with `random_int`.
- **Passed:** successful runs return structured `steps`; opening crates returns generated items, while skipping crates returns a clear completion message.
- **Passed:** invalid skill input returns HTTP `422` with a safe error.
- **Scope note:** the flow is intentionally stateless between requests; the second request revalidates the submitted skill and generates the encounter on the server. No client-supplied loot, quantity, or file path is trusted.

**Next gate:** approve Loop D before replacing the Java/CheerpJ Playground UI with the PHP form-and-results interface.

### Loop D — Replace the Playground UI

**Build**

- Update `playground.php`, `css/playground.css`, `js/playground-projects.js`, and `js/playground.js`.
- Remove CheerpJ loader calls, JAR paths, Java runtime initialization, and terminal-specific copy.
- Add a normal form/JSON request flow to `php/playground-run.php`.
- Render project-specific fields, validation errors, output, and structured steps.
- Keep the Nord light/dark theme, responsive layout, keyboard focus, reduced motion, and no page overflow.

**Verify with Playwright**

- Desktop: `1440 × 1000`.
- Tablet: `1024 × 900`.
- Mobile: `390 × 844`.
- Select each project.
- Run valid and invalid inputs.
- Confirm archived projects show a clear unavailable state.
- Confirm loading, success, and error states.
- Confirm repeated clicks do not duplicate results.
- Confirm theme switching and keyboard navigation.
- Confirm no Java/CDN runtime dependency is requested.

### Loop D result — 2026-11-06

- **Passed:** replaced the CheerpJ/Java terminal surface with a PHP form-and-results interface.
- **Passed:** fixed project metadata and read-only PHP source snippets render for all seven discoverable entries.
- **Passed:** David's Star runs through the browser and renders its generated output.
- **Passed:** LootMaster runs through both browser steps, including generated crate output.
- **Passed:** planned and archived projects show honest unavailable states with disabled execution controls.
- **Passed:** theme switching updates the document theme and persists through the existing local-storage key; the initial button label is synchronized on load.
- **Passed:** the fresh browser context loaded the page without visible runtime errors, and the previously observed click timeout was isolated to a stale browser page rather than the rebuilt UI.
- **Scope note:** the browser automation bridge could not expose viewport resizing or request/console details reliably in the stale page, so responsive and network checks are supplemented by CSS inspection, fresh-page interaction, Apache response checks, and direct endpoint tests.

### Loop E — Final hardening and documentation

**Build**

- Add focused documentation in `plan.md` or the project’s existing documentation surface explaining:
  - translations are PHP implementations;
  - original Java files are preserved as references;
  - archived projects are not executable;
  - the dispatcher uses a fixed allowlist;
  - no arbitrary PHP execution is supported.
- Remove obsolete CheerpJ proof artifacts and generated JAR/build files only after confirming they are no longer referenced.

**Verify**

- PHP lint every changed PHP file.
- Run focused handler tests and Playwright checks.
- Run the Impeccable detector once against the final Playground UI files.
- Inspect console errors, failed requests, response codes, overflow, and accessibility labels.

### Loop E result — 2026-11-06

- **Passed:** all new and changed PHP handlers pass `php -l`.
- **Passed:** direct dispatcher tests cover valid and invalid David's Star input, Numbered Triangle validation, ATM output, LootMaster steps, unknown projects, GET rejection, and JSON response shape.
- **Passed:** fresh Playwright interaction checks cover David's Star, LootMaster's staged flow, planned-project disabling, project switching, and theme toggling.
- **Passed:** the Playground stylesheet has explicit desktop, tablet, mobile, focus, disabled, and reduced-motion behavior with wrapped controls and no fixed-width content surface.
- **Passed:** no CheerpJ loader, CDN runtime, arbitrary execution path, or request-controlled include appears in the rebuilt Playground page or dispatcher.
- **Passed:** obsolete temporary CheerpJ proof page and temporary runtime build directory were removed; original Java source remains preserved as reference evidence.

### Loop F — Translate GigaPet

**Build**

- Added `php/playground/GigaPet.php` as an explicit PHP translation of the `GigaPet` model behavior.
- Added validated pet name, pet type, action, and interaction count inputs.
- Preserved the original 50/50 starting stats, feed/play messages, status clamping, and action output.
- Added GigaPet to the fixed manifest and dispatcher; it is now marked PHP ready in the Playground.
- Kept the interaction stateless and transparent: each run starts a fresh pet simulation rather than pretending to persist a Java object between HTTP requests.
- Hide the interaction-count field when the status-only action is selected.

**Verify**

- PHP lint passes for the new handler, manifest, and dispatcher.
- HTTP tests pass for feed, play, status, and invalid pet-name input.
- Fresh Playwright page shows GigaPet as PHP ready and runs the feed simulation successfully.

### Loop G — Make Playground interactions reactive

**Plan**

- Remove the manual Run Experiment control from the Playground.
- Auto-submit deterministic numeric experiments after valid or invalid input changes, with a short debounce to avoid request bursts while typing.
- Replace LootMaster's generic form submission with direct skill and crate-decision buttons while preserving its staged server flow.
- Replace GigaPet's action select with Feed, Play, and Check Status buttons.
- Update GigaPet's local readiness message immediately as the pet name and type fields change; submit those current values only when an action is chosen.
- Remove the irrelevant MidtermPractice entry from the active manifest, metadata, dispatcher, and handler.

**Build**

- Added debounced `input`/`change` listeners for David's Star and Numbered Triangle.
- Added direct LootMaster skill and crate-decision controls.
- Added direct GigaPet action controls and local name/type preview updates.
- Removed the obsolete Run Experiment button and MidtermPractice implementation.
- Added shared action-group and prompt styles while retaining responsive wrapping and keyboard focus states.

**Verify**

- JavaScript diagnostics and syntax checks pass.
- PHP lint passes for the manifest and dispatcher.
- Fresh Playwright checks pass for:
  - David's Star valid input auto-run and invalid even-size validation.
  - Numbered Triangle height auto-run.
  - LootMaster skill selection followed by crate opening.
  - GigaPet Feed action with the default pet values.
- Rendered markup contains no Run Experiment control and no MidtermPractice entry.
- Final responsive and Impeccable checks remain required before closing the Playground work.

### Loop H — Restore Lucky Nine as a reactive PHP experiment

**Plan**

- Reuse the reverted `LuckyNineBarrera-fixed` bytecode as the behavior source of truth.
- Translate its dealer setup, randomized deck draws, optional third-card rule, winner/loser/tie comparison, payouts, and cash balance into an explicit PHP handler.
- Replace the archived browser entry with a PHP-ready entry without exposing compiled classes as executable browser code.
- Use direct action buttons instead of a generic Run control.
- Keep each round request stateless; send completed dealer results back to the browser so the leaderboard can be rendered through a separate async request.

**Build**

- Added `php/playground/LuckyNine.php`.
- Added Lucky Nine to the fixed manifest and POST dispatcher.
- Added dealer name, cash fund, and 1–20 player controls.
- Added live local input feedback plus Deal round and View leaderboard buttons.
- Added a browser-held completed-round history for the leaderboard request.
- Included Lucky Nine response data in the shared dispatcher response without changing existing fields.

**Verify**

- PHP lint passes for the new handler, manifest, and dispatcher.
- Direct HTTP checks pass for randomized round output and sorted leaderboard output.
- Playwright verifies Lucky Nine appears as PHP ready, live dealer input updates the preview, Deal round completes asynchronously, and View leaderboard lists the completed round.
- The old archived LuckyNine and compiled GigaPet archive entries are no longer shown.
- Final responsive and Impeccable checks remain required before closing the Playground work.

### Loop I — Make Lucky Nine playable turn-by-turn

**Plan**

- Change Lucky Nine from a one-click simulation into a staged hand.
- Deal two cards to the user and each virtual player.
- Let the user draw at most one additional card or stand.
- Resolve virtual-player extra draws and compare every hand against the dealer result only after the user stands.

**Build**

- Added stateless `deal`, `draw`, and `stand` actions to the PHP handler.
- Returned an encoded round state between requests so no PHP session or hidden server state is required.
- Added visible user cards, virtual-player cards, current totals, and turn guidance.
- Replaced the single Deal round action with Deal cards, Draw another, and Stand controls.
- Disabled Draw another after the user receives the permitted third card.
- Preserved the separate browser-held leaderboard after a hand resolves.

**Verify**

- Deal, Draw, and Stand endpoint transitions pass with the expected `NEXT_STEP` and `COMPLETE` states.
- Invalid game state returns an explicit expired-round error.
- Playwright verifies cards are displayed, Draw another updates the user's hand, Stand resolves the virtual table, and controls return to the new-round state.

## Recommended implementation order

1. Approve the translation contract.
2. Prove `DavidStar` through the fixed PHP dispatcher.
3. Translate deterministic projects.
4. Translate LootMaster as a staged flow.
5. Rebuild the Playground UI around forms and results.
6. Decide separately whether GigaPet is worth translating.
7. Archive/remove obsolete Java-runtime artifacts after final verification.

### PHP Playground completion criteria

- `/playground` is linked from the existing portfolio and loads through Apache.
- Every project in `playround_projects` is discoverable with an honest PHP-ready, planned, or archived state.
- PHP translations use a fixed manifest and dispatcher; arbitrary PHP execution is not supported.
- Original Java source and compiled artifacts remain references only, not browser runtimes.
- Keyboard, reduced-motion, responsive, light-theme, dark-theme, failure, and unavailable-project paths are represented in the implementation and verified as far as the browser harness permits.

# GitHub repository and Student Developer Pack deployment plan

## Deployment decision

The portfolio should be uploaded to a GitHub repository, but it should **not** be deployed with GitHub Pages in its current form. GitHub Pages is appropriate for static HTML/CSS/JavaScript, while this project currently requires PHP execution and a MySQL/MariaDB database for the portfolio API and Playground dispatcher.

### Recommended route: GitHub + Azure App Service + Azure Database for MySQL

Use GitHub as the source-of-truth repository, Azure App Service on Linux for the PHP web application, and Azure Database for MySQL Flexible Server for the database. This keeps the current PHP/API architecture while avoiding the billing requirement and unavailable DigitalOcean offer.

This route is recommended because it:

- supports the existing `.php` pages and POST-only Playground endpoint;
- supports the existing PDO/MySQL API with a managed MySQL database;
- provides HTTPS, deployment slots/options, application settings, logs, and GitHub Actions integration;
- uses the Azure student credits already available to the owner;
- avoids managing an operating-system server, SSH hardening, and MariaDB patching.

Azure costs and student benefits are account-, region-, quota-, and date-dependent. Before creating resources, confirm the remaining credit and the current free-service quotas in the Azure portal. Do not upgrade to pay-as-you-go merely to raise a quota without understanding the billing consequence.

References:

- Azure for Students: <https://azure.microsoft.com/en-us/free/students/>
- Azure student offer details: <https://azure.microsoft.com/en-us/pricing/offers/ms-azr-0170p/>
- Deploy PHP, MySQL, and Redis to Azure App Service: <https://learn.microsoft.com/en-us/azure/app-service/tutorial-php-mysql>
- Azure Database for MySQL Flexible Server: <https://learn.microsoft.com/en-us/azure/mysql/flexible-server/overview>
- GitHub Pages reference and limitation check: <https://docs.github.com/pages>

### Why not GitHub Pages or Heroku

GitHub Pages cannot run the current PHP files or connect to MySQL, so it remains unsuitable unless the portfolio is rewritten as a static frontend with a separate API. Heroku is excluded for now because the current account requires billing details and the project has no budget for that requirement. DigitalOcean is also excluded because its relevant Student Pack offer is not available in the account.

## Loop J — Prepare the repository safely

### Build

1. Decide the repository name, visibility, and default branch. Recommended: `sigfrid-portfolio`, public, `main`.
2. Create a `.gitignore` before the first commit. Exclude local credentials, `.env` files, XAMPP logs, IDE metadata, OS files, generated caches, and local database dumps containing credentials.
3. Keep `database/portfolio.sql` as a schema/seed file, but review it before committing and never place production passwords in it.
4. Refactor `php/db.php` to read production connection values from environment variables with local XAMPP defaults only for development. Include the Azure MySQL hostname, port, and TLS configuration without committing the production hostname, password, or API tokens.
5. Add a `README.md` containing:
   - project purpose and screenshots;
   - local XAMPP setup;
   - database import instructions;
   - PHP version and required extensions;
   - deployment target and environment variable names;
   - verification commands and known limitations.
6. Remove or explicitly classify reference-only artifacts before publishing. Java source files remain as references, while compiled `.class` and `.jar` outputs are excluded because PHP is the runnable Playground layer.
7. Check the repository for secrets and personal data before staging.

**Progress:** Environment-aware database configuration, `.gitignore`, `.env.example`, and the initial deployment README are prepared locally. The GitHub repository is published at `https://github.com/Sigfreedz/sigfrid-portfolio`; Azure resources and automatic deployment are not configured yet.

### Verify

- `git status` contains only intended files.
- A secret scan returns no credentials.
- `php -l` passes for every committed PHP file.
- `database/portfolio.sql` imports into a clean local database.
- The project still works against XAMPP after the environment-variable change.

### Decision gate J

Do not create the first public commit until the secret scan is clean and the production configuration is separated from local XAMPP defaults.

## Loop K — Create and publish the GitHub repository

### Build

1. Create the empty GitHub repository without adding a generated README or license if the local project will supply them.
2. Initialize Git locally, create `main`, add the reviewed files, and create the first commit.
3. Add the GitHub `origin` remote and push `main`.
4. Enable branch protection after the first successful push: pull request review where practical, status checks, and no direct force-pushes to `main`.
5. Add repository metadata: description, topics, homepage URL after deployment, and a concise project README.
6. Enable Dependabot or GitHub security features where applicable.

### Verify

- The GitHub tree contains the expected PHP, CSS, JS, image, and SQL files.
- The README setup instructions work from a fresh clone.
- No `.env`, password, private key, or local-only database credential is visible in the repository history.
- Clone the repository into a clean folder and run the local setup checklist.

### Decision gate K

The repository is ready for deployment only when a clean clone can be configured without copying hidden files or manually editing source code.

## Loop K.1 — Connect GitHub updates to Azure deployment

### Build

1. Use Azure App Service **Deployment Center** with GitHub as the source, or configure the equivalent GitHub Actions workflow from the App Service.
2. Authorize Azure to access `Sigfreedz/sigfrid-portfolio`.
3. Select the `main` branch and have Azure create the deployment workflow in `.github/workflows/`.
4. Keep production database settings in App Service **Environment variables/Application settings**. The workflow must deploy source files only and must never contain database passwords.
5. Treat pushes to `main` as production deployments. Use pull requests and branch protection for changes that should be reviewed before deployment.
6. Confirm the workflow reports a successful deployment and that the App Service restart does not remove persistent database data.

### Verify

- The GitHub repository contains the generated Azure deployment workflow.
- A controlled commit to `main` triggers one workflow run.
- The workflow completes successfully and the App Service serves the new commit.
- A failed deployment is visible in GitHub Actions and App Service deployment logs.
- Database credentials remain absent from Git history, workflow YAML, and build logs.

### Decision gate K.1

Do not treat the Azure site as production-ready until a harmless visible change has been deployed from GitHub by the workflow and the prior version can be restored through Git history or a redeploy.

## Loop L — Provision Azure without introducing billing risk

### Build

1. Open the Azure portal and record the subscription type, remaining credit, credit expiry date, and current spending limit.
2. Select one region for both App Service and MySQL Flexible Server. Prefer a region that supports the desired free/low-cost tiers.
3. Create one resource group to contain the portfolio resources. Use clear names such as `sigfrid-portfolio-rg`, `sigfrid-portfolio-app`, and `sigfrid-portfolio-db`.
4. Create an App Service Linux PHP app using the lowest supported plan that is covered by the available student benefits. Confirm that the selected tier does not require pay-as-you-go.
5. Create Azure Database for MySQL Flexible Server using the smallest eligible development tier, no high availability, minimal storage, and automatic stop/start if available. Confirm the portal's estimated cost before creation.
6. Create a production database and least-privilege MySQL user. Do not reuse the local XAMPP `root` account.
7. Allow the App Service to connect to MySQL using the server firewall/private networking option supported by the selected tiers. Do not open the database to the entire internet unless Azure requires it temporarily for setup, and remove temporary access afterward.
8. Import `database/portfolio.sql`, verify the three featured projects and current skills/radar rows, and record the database host/name/user in Azure configuration only.
9. Configure App Service application settings for `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`, and the required TLS/CA setting. The application must read these values in production and must not commit them.
10. Configure the App Service deployment source as the GitHub repository or use a GitHub Actions workflow with the minimum required deployment permissions.
11. Confirm the web root and relative paths so `index.php`, `playground.php`, `css/`, `js/`, `images/`, and `php/` resolve at the domain root.
12. Enable the default Azure HTTPS hostname first. Add a custom domain only if a domain is available without a new paid commitment.

### Decision gate L

Do not create the App Service or MySQL server until the Azure portal shows that the chosen tiers fit the remaining student credit or free quota. If the managed MySQL tier would consume the budget too quickly, use a single small Azure Linux VM with PHP and MariaDB only after confirming its projected cost and agreeing to the additional server-maintenance work.

## Loop M — Production hardening before first public release

### Build

- Replace the local `root`/blank-password fallback in production with required environment values and fail explicitly when they are missing.
- Disable PHP error display in production while retaining server-side logging.
- Review CORS, request methods, input bounds, output encoding, and the fixed Playground allowlist.
- Add database backups and test one restore.
- Add a health check for the homepage, JSON endpoints, and Playground POST endpoint.
- Add a deployment rollback procedure using the previous Git commit.
- Confirm Apache/Nginx serves only intended public assets and does not expose `.git`, configuration files, logs, or database dumps.

### Verify with Playwright

Run the deployed URL at:

- desktop: 1440 × 1000;
- tablet: 1024 × 900;
- mobile: 390 × 844.

Check:

- index page loads with no console errors;
- Stack, Radar, and Projects resolve from the production database;
- navbar Playground and More projects both reach `playground.php`;
- Playground Deal/Draw/Stand flow completes;
- light/dark themes persist;
- no horizontal overflow or broken image paths;
- HTTPS is active and HTTP redirects to HTTPS.

### Decision gate M

Publish the domain only after the clean-clone, production smoke test, Playwright checks, database restore test, and rollback rehearsal all pass.

## Recommended implementation order

1. Approve Azure App Service plus MySQL Flexible Server as the deployment target.
2. Add `.gitignore`, environment-based database configuration, and `README.md`.
3. Run the secret scan, PHP lint, SQL import, and local smoke tests.
4. Create and push the GitHub repository.
5. Provision the Azure App Service and MySQL resources within the existing student-credit limit.
6. Deploy the application and database.
7. Configure HTTPS, backups, and controlled GitHub deployment.
8. Run production Playwright verification.
9. Add the final deployed URL to the GitHub repository metadata and portfolio.

## Note: PLV university tenant restrictions

The `@plv.edu.ph` Azure for Students subscription has institutional policies that zero out App Service quotas (both Free F1 and Basic B1) in all regions including Southeast Asia. Only Azure VM resources are permitted by the tenant policy.

**Resolution chosen:** Use a personal Microsoft/email account to create a separate Azure free account.

- Personal Azure free account: $200 credits for 30 days + 12 months of free services
- Requires a personal email (not @plv.edu.ph), phone number, and a debit/credit card for identity verification (no charges during free trial)
- Sign up at: https://azure.microsoft.com/en-us/free/
- No App Service quota restrictions on personal accounts
- Free F1 App Service + MySQL Flexible Server Burstable tier covered by free credits

**Resource group already created** (`sigfrid-portfolio-rg`) in the university subscription — it can be deleted or ignored. All new resources will be created in the personal subscription after sign-up.

### Post-signup provisioning sequence (personal account)

1. Sign in to Azure portal with personal account.
2. Create resource group: `sigfrid-portfolio-rg`, region: Southeast Asia.
3. Create App Service plan: `sigfrid-portfolio-plan`, Free F1, Linux, Southeast Asia.
4. Create Web App: `sigfrid-portfolio`, PHP 8.2, Linux, Southeast Asia.
5. Create Azure Database for MySQL Flexible Server: `sigfrid-portfolio-db`, Burstable B1ms, Southeast Asia.
6. Set App Service Application settings: `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_SSL_VERIFY`.
7. Import `database/portfolio.sql` into the Azure MySQL instance.
8. Connect App Service Deployment Center to GitHub `Sigfreedz/sigfrid-portfolio`, branch `main`.
9. Fix web root (app serves from `/` on Azure, not `/portfolio/` as on XAMPP).
10. Verify live site with Playwright smoke tests.

## InfinityFree deployment route

The Azure App Service route is blocked by the university tenant's App Service quota policy, and a second personal Azure account is not being created. The selected no-cost deployment target is InfinityFree.

### InfinityFree configuration

- Site: `sigfrid.freehosting.dev`
- FTP host: `ftpupload.net`
- MySQL host: `sql105.infinityfree.com`
- Database: `if0_43239970_sigfrid_portfolio`
- Database user: `if0_43239970`
- Credentials: supplied privately by the owner; never commit them

### Build

1. Keep the GitHub source free of hosting passwords. The committed `php/db.php` remains environment-aware and local-safe.
2. Create a temporary production-only `php/db.php` for the InfinityFree upload with the hosting database values, then remove the local temporary file after upload.
3. Upload the application files into the InfinityFree `htdocs/` directory using FTP. Do not upload `.git/`, compiled Java artifacts, local environment files, or deployment scripts.
4. Import `database/portfolio.sql` into the InfinityFree database through a short-lived protected importer or the hosting phpMyAdmin interface. Delete any importer immediately after use.
5. Confirm the hosting root serves `index.php` at `https://sigfrid.freehosting.dev/`, not under `/portfolio/`.
6. Configure HTTPS in the InfinityFree panel and confirm the site redirects from HTTP when the provider supports the redirect.

**MySQL Import (Manual - Required):**
Due to SSL handshake errors and anti-bot challenges blocking programmatic import, complete schema upload via phpMyAdmin:

1. Log in to InfinityFree control panel at https://www.infinityfree.com/
2. Navigate to MySQL Databases → Open phpMyAdmin
3. Select database `if0_43239970_sigfrid_portfolio`
4. Click Import tab → Choose File → Select `database/portfolio.sql`
5. Click Go to execute import
6. Verify three tables created: `skills`, `projects`, `skill_radar`
7. Browse each table to confirm data is populated

**Status:** Files uploaded via FTP ✅ | Database schema pending manual import ⏳

### Verify with Playwright

- Homepage, CSS, JavaScript, images, and favicon load from the site root.
- Skills, Stack, Radar, and Projects resolve from the remote database.
- Navbar Playground and More projects both reach `playground.php`.
- Playground Deal/Draw/Stand flow completes.
- Light/dark theme persistence works.
- Mobile viewport has no horizontal overflow.
- No PHP warnings, database errors, broken image paths, or exposed installer/configuration files are present.

### Decision gate InfinityFree

Do not announce the hosted URL as complete until the schema is imported, the database-backed sections return populated data, the temporary importer is deleted, and the production Playwright smoke test passes. Rotate the hosting password after deployment because it was shared during setup.
