<?php
declare(strict_types=1);

function playground_run_giga_pet(array $input): array
{
    $name = trim((string) ($input['name'] ?? ''));
    $type = trim((string) ($input['type'] ?? ''));
    $action = (string) ($input['action'] ?? '');

    if ($name === '' || strlen($name) > 30) {
        throw new InvalidArgumentException('Pet name must contain 1 to 30 characters.');
    }

    if ($type === '' || strlen($type) > 30) {
        throw new InvalidArgumentException('Pet type must contain 1 to 30 characters.');
    }

    if (!in_array($action, ['feed', 'play', 'status'], true)) {
        throw new InvalidArgumentException('Choose feed, play, or status.');
    }

    $hunger = 50;
    $happiness = 50;
    $lines = [];

    if ($action === 'feed' || $action === 'play') {
        $amount = filter_var($input['amount'] ?? null, FILTER_VALIDATE_INT);
        if ($amount === false || $amount === null || $amount < 1 || $amount > 10) {
            throw new InvalidArgumentException('Interaction count must be a whole number from 1 to 10.');
        }

        for ($counter = 0; $counter < $amount; $counter++) {
            if ($action === 'feed') {
                $lines[] = "{$name}: Yum!";
                $hunger -= 10;
                $happiness += 5;
            } else {
                $lines[] = "{$name}: Woooohoooo!";
                $hunger += 5;
                $happiness += 10;
            }
        }

        $verb = $action === 'feed' ? 'fed' : 'played with';
        $lines[] = "You {$verb} {$name} {$amount} time(s).";
    }

    $hunger = max(0, min(100, $hunger));
    $happiness = min(100, $happiness);
    $lines[] = '';
    $lines[] = '-------------------------------------------';
    $lines[] = "{$name} the {$type}'s Status:";
    $lines[] = '';
    $lines[] = "Hunger: {$hunger}/100";
    $lines[] = "Happiness: {$happiness}/100";
    $lines[] = '-------------------------------------------';

    return [
        'status' => 'COMPLETE',
        'label' => "GigaPet {$action} simulation completed.",
        'output' => implode(PHP_EOL, $lines),
    ];
}
