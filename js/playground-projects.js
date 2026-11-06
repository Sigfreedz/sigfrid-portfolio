window.PLAYGROUND_PROJECTS = Object.freeze([
    {
        id: 'david-star',
        filename: 'DavidStar.php',
        original: 'DavidStar.java',
        title: "David's Star",
        source: `<?php
function playground_run_david_star(int $size): array
{
    if ($size < 7 || $size > 15 || $size % 2 === 0) {
        throw new InvalidArgumentException('Size must be an odd number from 7 to 15.');
    }

    $lines = [];
    $starCount = 1;
    $lineCount = intdiv($size, 2);

    while ($lineCount >= 3) {
        $lines[] = str_repeat('_', $lineCount)
            . str_repeat('*', $starCount)
            . str_repeat('_', $lineCount);
        $starCount += 2;
        $lineCount--;
    }

    $lines[] = str_repeat('*', $size);
    $lines[] = '_' . str_repeat('*', $size - 2) . '_';
    $lines[] = str_repeat('*', $size);

    return ['output' => implode(PHP_EOL, $lines)];
}`,
        language: 'PHP',
        availability: 'ready',
        inputHint: "David's Star accepts an odd integer from 7 to 15.",
        fields: [{name: 'size', label: 'Size', type: 'number', value: 7, min: 7, max: 15}]
    },
    {
        id: 'numbered-triangle',
        filename: 'NumberedTriangle.php',
        original: 'BarreraInputBasedNumberedTriangle.java',
        title: 'Numbered Triangle',
        source: `<?php
function playground_run_numbered_triangle(int $height): array
{
    if ($height < 1 || $height > 30) {
        throw new InvalidArgumentException('Height must be a whole number from 1 to 30.');
    }

    $lines = [];
    $spaceCounter = $height;
    for ($heightCounter = 1; $heightCounter <= $height; $heightCounter++) {
        $numbers = [];
        for ($numberCounter = 1; $numberCounter <= $heightCounter; $numberCounter++) {
            $numbers[] = (string) $numberCounter;
        }
        $lines[] = str_repeat(' ', $spaceCounter) . implode(' ', $numbers) . ' ';
        $spaceCounter--;
    }
    return ['output' => implode(PHP_EOL, $lines)];
}`,
        language: 'PHP',
        availability: 'ready',
        inputHint: 'Numbered Triangle accepts a height from 1 to 30.',
        fields: [{name: 'height', label: 'Height', type: 'number', value: 3, min: 1, max: 30}]
    },
    {
        id: 'loot-master',
        filename: 'LootMaster.php',
        original: 'BarreraLootMasterLootGenerator.java',
        title: 'Loot Master',
        source: `<?php
function playground_run_loot_master(array $input): array
{
    $step = $input['step'] ?? '';
    $skill = filter_var($input['skill'] ?? null, FILTER_VALIDATE_INT);
    if ($skill === false || $skill === null || $skill < 1 || $skill > 3) {
        throw new InvalidArgumentException('Choose skill 1, 2, or 3.');
    }
    if ($step === 'skill') {
        return ['status' => 'NEXT_STEP', 'next' => 'crates'];
    }
    // The crate result is generated only after the second form step.
}`,
        language: 'PHP',
        availability: 'ready',
        inputHint: 'Choose a skill, then decide whether to open the generated crates.',
        fields: [{name: 'skill', label: 'Skill', type: 'select', options: [['1', 'Abyssal Arrow'], ['2', 'Atomic Bomb'], ['3', 'Infinite Void']]}]
    },
    {
        id: 'gigapet',
        filename: 'GigaPet.php',
        original: 'gigapet/GigaPet.java + GigaPetGame.java',
        title: 'GigaPet',
        source: `<?php
function playground_run_giga_pet(array $input): array
{
    $name = trim((string) ($input['name'] ?? ''));
    $type = trim((string) ($input['type'] ?? ''));
    $action = $input['action'] ?? '';
    $hunger = 50;
    $happiness = 50;
    // Validate the pet, apply feed/play, then clamp status values.
}`,
        language: 'PHP',
        availability: 'ready',
        inputHint: 'Create a pet simulation, then choose feed, play, or status. Each run starts at 50/50.',
        fields: [
            {name: 'name', label: 'Pet name', type: 'text', value: 'Pixel', max: 30},
            {name: 'type', label: 'Pet type', type: 'text', value: 'Cat', max: 30},
            {name: 'amount', label: 'Times', type: 'number', value: 2, min: 1, max: 10}
        ]
    },
    {
        id: 'lucky-nine',
        filename: 'LuckyNine.php',
        original: 'LuckyNineBarrera-fixed/*.class',
        title: 'Lucky Nine',
        source: `<?php
function playground_run_lucky_nine(array $input): array
{
    $action = $input['action'] ?? '';
    // Validate the dealer round or sort the browser's completed-round history.
    // Each request uses a fresh deck, matching the original random game flow.
}`,
        language: 'PHP',
        availability: 'ready',
        inputHint: 'Deal your cards, then choose Draw another or Stand against the virtual players.',
        fields: [
            {name: 'dealer', label: 'Dealer name', type: 'text', value: 'Sigfrid', max: 30},
            {name: 'cash', label: 'Cash fund', type: 'number', value: 1000, min: 0, step: '0.01'},
            {name: 'players', label: 'Players', type: 'number', value: 3, min: 1, max: 20}
        ]
    }
]);
