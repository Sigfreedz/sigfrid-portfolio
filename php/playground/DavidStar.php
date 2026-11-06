<?php
declare(strict_types=1);

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

    $starCount -= 2;
    $lineCount++;

    while ($lineCount <= intdiv($size, 2)) {
        $lines[] = str_repeat('_', $lineCount)
            . str_repeat('*', $starCount)
            . str_repeat('_', $lineCount);
        $starCount -= 2;
        $lineCount++;
    }

    return [
        'output' => implode(PHP_EOL, $lines),
        'input' => ['size' => $size],
        'label' => "David's Star generated for size {$size}.",
    ];
}
