<?php
declare(strict_types=1);

/**
 * Fixed execution contract for the PHP Playground.
 *
 * This file intentionally contains no request-controlled paths, callables, or
 * source code. The dispatcher may execute only handlers listed here.
 */
return [
    'david-star' => [
        'title' => "David's Star",
        'handler' => 'playground_run_david_star',
        'implementation' => 'playground/DavidStar.php',
        'source' => 'playround_projects/DavidStar.java',
        'input' => [
            'name' => 'size',
            'type' => 'integer',
            'min' => 7,
            'max' => 15,
        ],
        'availability' => 'ready',
        'output_format' => 'text',
        'random' => false,
    ],
    'numbered-triangle' => [
        'title' => 'Numbered Triangle',
        'handler' => 'playground_run_numbered_triangle',
        'implementation' => 'playground/NumberedTriangle.php',
        'source' => 'playround_projects/BarreraInputBasedNumberedTriangle.java',
        'input' => [
            'name' => 'height',
            'type' => 'integer',
            'min' => 1,
            'max' => 30,
        ],
        'availability' => 'ready',
        'output_format' => 'text',
        'random' => false,
    ],
    'loot-master' => [
        'title' => 'Loot Master',
        'handler' => 'playground_run_loot_master',
        'implementation' => 'playground/LootMaster.php',
        'source' => 'playround_projects/BarreraLootMasterLootGenerator.java',
        'input' => [
            'flow' => ['skill', 'crates'],
            'skill' => [1, 2, 3],
        ],
        'availability' => 'ready',
        'output_format' => 'steps',
        'random' => true,
    ],
    'gigapet' => [
        'title' => 'GigaPet',
        'handler' => 'playground_run_giga_pet',
        'implementation' => 'playground/GigaPet.php',
        'source' => 'playround_projects/gigapet/GigaPet.java + GigaPetGame.java',
        'input' => [
            'actions' => ['feed', 'play', 'status'],
            'amount_min' => 1,
            'amount_max' => 10,
        ],
        'availability' => 'ready',
        'output_format' => 'text',
        'random' => false,
    ],
    'lucky-nine' => [
        'title' => 'Lucky Nine',
        'handler' => 'playground_run_lucky_nine',
        'implementation' => 'playground/LuckyNine.php',
        'source' => 'playround_projects/LuckyNineBarrera-fixed/*.class',
        'input' => [
            'actions' => ['deal', 'draw', 'stand', 'leaderboard'],
            'players_min' => 1,
            'players_max' => 20,
        ],
        'availability' => 'ready',
        'output_format' => 'text',
        'random' => true,
    ],
];
