<?php
declare(strict_types=1);

function playground_run_loot_master(array $input): array
{
    $step = $input['step'] ?? '';
    $skill = filter_var($input['skill'] ?? null, FILTER_VALIDATE_INT);

    if ($skill === false || $skill === null || $skill < 1 || $skill > 3) {
        throw new InvalidArgumentException('Choose skill 1, 2, or 3.');
    }

    if ($step === 'skill') {
        return [
            'status' => 'NEXT_STEP',
            'label' => 'Skill accepted. Decide whether to open the generated crates.',
            'steps' => [
                'You used skill ' . $skill . '.',
                'The boss was defeated and a crate reward is ready.',
            ],
            'next' => 'crates',
        ];
    }

    if ($step !== 'crates') {
        throw new InvalidArgumentException('Choose a valid LootMaster step.');
    }

    $openCrates = filter_var($input['open_crates'] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    if ($openCrates === null) {
        throw new InvalidArgumentException('Choose whether to open the crates.');
    }

    $bossHealth = random_int(1000, 10998);
    $quantity = random_int(1, 10);
    $steps = [
        "You used skill {$skill} against a {$bossHealth}HP boss.",
        'You dealt 999999 damage and defeated the boss.',
        'You leveled up (+2) and found ' . $quantity . ' crate(s).',
    ];

    if (!$openCrates) {
        $steps[] = 'You escaped with the unopened crates. Thanks for playing LootMaster.';
        return [
            'status' => 'COMPLETE',
            'label' => 'LootMaster finished without opening the crates.',
            'steps' => $steps,
            'output' => implode(PHP_EOL, $steps),
        ];
    }

    $items = [
        '[Common] Rusty Sword',
        '[Uncommon] Iron Dagger',
        '[Rare] Orc Axe',
        '[Epic] Phoenix Spear',
        '[Legendary] Mjolnir',
        '[Mythical] Gungnir',
    ];
    $loot = [];

    for ($index = 1; $index <= $quantity; $index++) {
        $seed = random_int(1, 99);
        $rarity = match (true) {
            $seed < 40 => 0,
            $seed < 70 => 1,
            $seed < 90 => 3,
            $seed < 97 => 4,
            default => 5,
        };
        $loot[] = "Item {$index}: {$items[$rarity]}";
    }

    $steps[] = 'You opened the crates:';
    $steps = array_merge($steps, $loot);

    return [
        'status' => 'COMPLETE',
        'label' => "LootMaster generated {$quantity} crate(s).",
        'steps' => $steps,
        'output' => implode(PHP_EOL, $steps),
    ];
}
