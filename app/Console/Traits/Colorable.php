<?php

declare(strict_types=1);

namespace App\Console\Traits;

trait Colorable
{
    private function getColors(): array
    {
        return [
            '#FF0000' => "\033[41m  \033[0m - Red",
            '#00FF00' => "\033[42m  \033[0m - Green",
            '#0000FF' => "\033[44m  \033[0m - Blue",
            '#FFFF00' => "\033[43m  \033[0m - Yellow",
            '#00FFFF' => "\033[46m  \033[0m - Cyan",
            '#FF00FF' => "\033[45m  \033[0m - Magenta",
            '#FFFFFF' => "\033[47m  \033[0m - White",
            '#808080' => "\033[100m  \033[0m - Gray",
            '#800000' => "\033[43m  \033[0m - Brown",
            '#FFA500' => "\033[48;5;214m  \033[0m - Orange",
            '#800080' => "\033[48;5;93m  \033[0m - Purple",
            '#000000' => "\033[40m  \033[0m - Black",
        ];
    }
}
