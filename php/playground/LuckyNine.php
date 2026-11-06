<?php
declare(strict_types=1);

function playground_run_lucky_nine(array $input): array
{
    $action = (string) ($input['action'] ?? '');

    if ($action === 'leaderboard') {
        return [
            'label' => 'Lucky Nine leaderboard',
            'output' => playground_lucky_nine_leaderboard((string) ($input['history'] ?? '')),
        ];
    }

    if (!in_array($action, ['deal', 'draw', 'stand'], true)) {
        throw new InvalidArgumentException('Choose Deal cards, Draw another, or Stand.');
    }

    if ($action === 'deal') {
        $dealer = trim((string) ($input['dealer'] ?? ''));
        $cash = filter_var($input['cash'] ?? null, FILTER_VALIDATE_FLOAT);
        $players = filter_var($input['players'] ?? null, FILTER_VALIDATE_INT);
        if ($dealer === '' || strlen($dealer) > 30) {
            throw new InvalidArgumentException('Dealer name must contain 1 to 30 characters.');
        }
        if ($cash === false || $cash === null || $cash < 0) {
            throw new InvalidArgumentException('Cash fund must be a non-negative number.');
        }
        if ($players === false || $players === null || $players < 1 || $players > 20) {
            throw new InvalidArgumentException('Players must be a whole number from 1 to 20.');
        }
        $state = [
            'dealer' => $dealer,
            'cash' => (float) $cash,
            'players' => (int) $players,
            'dealer_total' => playground_lucky_nine_draw_total(),
            'user' => [
                'cards' => playground_lucky_nine_draw_cards(2),
                'total' => 0.0,
            ],
            'virtual' => [],
        ];
        $state['user']['total'] = playground_lucky_nine_total($state['user']['cards']);
        for ($player = 1; $player <= $players; $player++) {
            $cards = playground_lucky_nine_draw_cards(2);
            $state['virtual'][] = [
                'player' => $player,
                'cards' => $cards,
                'total' => playground_lucky_nine_total($cards),
                'bet' => random_int(20, 99),
            ];
        }

        return [
            'status' => 'NEXT_STEP',
            'next' => 'player-action',
            'label' => 'Cards dealt',
            'output' => playground_lucky_nine_deal_output($state),
            'data' => ['game' => base64_encode(json_encode($state, JSON_THROW_ON_ERROR))],
        ];
    }

    $encodedGame = (string) ($input['game'] ?? '');
    $decodedGame = base64_decode($encodedGame, true);
    $state = is_string($decodedGame) ? json_decode($decodedGame, true) : null;
    if (!is_array($state) || !isset($state['user'], $state['virtual'], $state['dealer_total'])) {
        throw new InvalidArgumentException('This game round expired. Deal a new round.');
    }
    if ($action === 'draw') {
        if (count($state['user']['cards']) >= 3) {
            throw new InvalidArgumentException('You can only draw one additional card.');
        }
        $state['user']['cards'][] = playground_lucky_nine_draw_card();
        $state['user']['total'] = playground_lucky_nine_total($state['user']['cards']);
        return [
            'status' => 'NEXT_STEP',
            'next' => 'player-action',
            'label' => 'Card drawn',
            'output' => playground_lucky_nine_deal_output($state),
            'data' => ['game' => base64_encode(json_encode($state, JSON_THROW_ON_ERROR))],
        ];
    }

    $rounds = [[
        'player' => 'You',
        'cards' => $state['user']['cards'],
        'total' => $state['user']['total'],
        'bet' => 0,
    ]];
    foreach ($state['virtual'] as &$virtual) {
        if ($virtual['total'] < 9 && count($virtual['cards']) < 3) {
            $virtual['cards'][] = playground_lucky_nine_draw_card();
            $virtual['total'] = playground_lucky_nine_total($virtual['cards']);
        }
        $rounds[] = $virtual;
    }
    unset($virtual);

    $winnerTotal = 0;
    $winnerPayout = 0.0;
    $loserPayout = 0.0;
    foreach ($rounds as &$round) {
        $round['category'] = $round['total'] > $state['dealer_total']
            ? 'Winner'
            : ($round['total'] < $state['dealer_total'] ? 'Loser' : 'Tie');
        $round['third'] = count($round['cards']) === 3;
        $payout = $round['player'] === 'You' ? 0 : ($round['total'] === 9.0 ? $round['bet'] * 3 : $round['bet']);
        if ($round['category'] === 'Winner') {
            $winnerTotal += 1;
            $winnerPayout += $payout;
        } elseif ($round['category'] === 'Loser') {
            $loserPayout += $payout;
        }
    }
    unset($round);

    $newCash = (float) $state['cash'] + $winnerPayout - $loserPayout;
    $lines = [
        "Dealer: {$state['dealer']}",
        "Dealer result: " . number_format($state['dealer_total'], 2),
        '',
        'Player results',
        '--------------------------',
    ];

    foreach ($rounds as $round) {
        $draw = $round['third'] ? ' · third card' : '';
        $playerLabel = $round['player'] === 'You' ? 'You' : "Player {$round['player']}";
        $lines[] = sprintf(
            '%s · %s · bet %.2f · %s%s',
            $playerLabel,
            $round['category'],
            $round['bet'],
            number_format($round['total'], 2),
            $draw
        );
    }

    $lines[] = '';
    $lines[] = 'Round summary';
    $lines[] = '--------------------------';
    $lines[] = "Winners: {$winnerTotal}";
    $lines[] = 'Winner payouts: ' . number_format($winnerPayout, 2);
    $lines[] = 'Loser payouts: ' . number_format($loserPayout, 2);
    $lines[] = "Dealer's ending cash: " . number_format($newCash, 2);

    return [
        'label' => 'Lucky Nine round',
        'output' => implode(PHP_EOL, $lines),
        'data' => [
        'dealer' => $state['dealer'],
            'cash' => round($newCash, 2),
            'winners' => (int) $winnerTotal,
        'players' => count($rounds),
        ],
    ];
}

function playground_lucky_nine_draw_total(): float
{
    return fmod((float) playground_lucky_nine_draw_card() + playground_lucky_nine_draw_card(), 10);
}

function playground_lucky_nine_draw_card(): int
{
    $rank = random_int(1, 13);
    return $rank > 9 ? 0 : $rank;
}

function playground_lucky_nine_draw_cards(int $count): array
{
    $cards = [];
    for ($index = 0; $index < $count; $index++) {
        $cards[] = playground_lucky_nine_draw_card();
    }
    return $cards;
}

function playground_lucky_nine_total(array $cards): float
{
    return fmod((float) array_sum($cards), 10);
}

function playground_lucky_nine_deal_output(array $state): string
{
    $lines = [
        "Dealer: {$state['dealer']}",
        'Dealer has dealt the table.',
        '',
        'Your cards: ' . implode(' · ', $state['user']['cards'])
            . ' · total ' . number_format($state['user']['total'], 2),
        '',
        'Virtual players',
    ];
    foreach ($state['virtual'] as $virtual) {
        $lines[] = sprintf(
            'Player %d: %s · total %s',
            $virtual['player'],
            implode(' · ', $virtual['cards']),
            number_format($virtual['total'], 2)
        );
    }
    $lines[] = '';
    $lines[] = count($state['user']['cards']) < 3
        ? 'Your move: draw one more card or stand.'
        : 'You have used your extra card. Stand to resolve the table.';
    return implode(PHP_EOL, $lines);
}

function playground_lucky_nine_leaderboard(string $encodedHistory): string
{
    $history = json_decode($encodedHistory, true);
    if (!is_array($history)) {
        throw new InvalidArgumentException('Leaderboard history is invalid.');
    }

    $history = array_values(array_filter($history, static function ($entry): bool {
        return is_array($entry)
            && is_string($entry['dealer'] ?? null)
            && strlen($entry['dealer']) <= 30
            && is_numeric($entry['cash'] ?? null);
    }));
    usort($history, static fn (array $left, array $right): int => (float) $right['cash'] <=> (float) $left['cash']);

    if ($history === []) {
        return "LEADERBOARD\n-------------------------------\nNo completed rounds yet.";
    }

    $lines = [
        'LEADERBOARD (TOP 10)',
        '-------------------------------',
    ];
    foreach (array_slice($history, 0, 10) as $index => $entry) {
        $lines[] = sprintf('%d. %s · cash %.2f', $index + 1, $entry['dealer'], (float) $entry['cash']);
    }
    return implode(PHP_EOL, $lines);
}
