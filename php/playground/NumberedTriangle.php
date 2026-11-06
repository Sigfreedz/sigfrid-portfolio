<?php
declare(strict_types=1);

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

    $spaceCounter += 2;
    for ($heightCounter = $height - 1; $heightCounter > 0; $heightCounter--) {
        $numbers = [];
        for ($numberCounter = $heightCounter; $numberCounter > 0; $numberCounter--) {
            $numbers[] = (string) $numberCounter;
        }
        $lines[] = str_repeat(' ', $spaceCounter) . implode(' ', $numbers) . ' ';
        $spaceCounter++;
    }

    return [
        'output' => implode(PHP_EOL, $lines),
        'input' => ['height' => $height],
        'label' => "Numbered triangle generated for height {$height}.",
    ];
}
