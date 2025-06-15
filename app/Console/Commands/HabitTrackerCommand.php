<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Interfaces\MenuInterface;
use App\Console\Interfaces\MenuItemInterface;
use App\Console\Interfaces\TaskInterface;
use App\Console\Services\AuthService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Auth\Authenticatable;
use RuntimeException;
use Throwable;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\pause;
use function Laravel\Prompts\select;
use function Laravel\Prompts\warning;

final class HabitTrackerCommand extends Command
{
    protected $signature = 'habit:tracker';

    protected $description = 'Console app to track habits';

    public function getTitle(): string
    {
        return 'Habits Tracker';
    }

    public function getTaskKey(): string
    {
        return 'console-tasks';
    }

    public function handle(): void
    {
        try {
            clear();

            $this->line('');
            intro($this->getTitle());

            $hasToLogin = false;
            if (! AuthService::checkAccess()) {
                $hasToLogin = true;
                warning("\nYou need to login first");

                $user = AuthService::login();
                if (! $user instanceof Authenticatable) {
                    throw new RuntimeException('Unable to login');
                }
            }

            while (true) {
                if ($hasToLogin) {
                    clear();
                    intro($this->getTitle());
                }

                $tasks = app($this->getTaskKey());

                $options = [];
                foreach ($tasks as $task) {
                    $key = $this->parseClassName($task);
                    $options[$key] = $task;
                }

                $options['Quit'] = 'Quit';

                $key = $this->getSelection('Select a task to run', $options);

                $this->processSelection($key, $options[$key]);
            }
        } catch (Throwable $throwable) {
            error("\nSomething went wrong:\n".$throwable->getMessage());
        } finally {
            $this->line('');
        }
    }

    private function parseClassName(mixed $task): string
    {
        $word = collect(explode('\\', (string) $task))->last();
        $parts = (array) preg_split('/(?=[A-Z])/', (string) $word);

        return str(implode(' ', $parts))
            ->replace('Menu', '')
            ->trim()
            ->value();
    }

    private function getSelection(string $label, array $list): string
    {
        $key = select(
            label: $label,
            options: array_keys($list),
            scroll: 10,
        );

        $this->checkQuit($key);

        return $key;
    }

    private function checkQuit(string $key): void
    {
        if ($key !== 'Quit') {
            return;
        }

        outro('Bye');

        exit(0);
    }

    private function processSelection(string $key, string|TaskInterface $selectedClass): void
    {
        clear();
        $this->line('');
        intro($key);

        if ($selectedClass instanceof TaskInterface) {
            info(sprintf('Running %s...', $key));

            $this->runTask($selectedClass);

            return;
        }

        $selectedInterface = app($selectedClass);

        if ($selectedInterface instanceof MenuInterface) {
            $this->runMenu($key, $selectedInterface);

            return;
        }

        throw new RuntimeException(sprintf("Invalid Selection: %s", $selectedInterface::class));
    }

    private function runTask(TaskInterface $selected): void
    {
        try {
            $response = $selected->handle();
            clear();

            if (! $response->success) {
                error($response->message);

                pause('Press ENTER to continue.');

                return;
            }

            info($response->message);
        } finally {
            intro($this->getTitle());
        }
    }

    private function runMenu(string $title, MenuInterface $selected): void
    {
        $menu = $selected->getMenuItems();
        if ($menu->isEmpty()) {
            throw new RuntimeException(sprintf("%s has no menu items", $title));
        }

        $options = [];
        foreach ($menu as $item) {
            if ($item instanceof MenuItemInterface) {
                $options[$item->itemName()] = $item->task();

                continue;
            }

            $key = $this->parseClassName($item);
            $options[$key] = $item;
        }

        $options['Quit'] = 'Quit';
        $key = $this->getSelection(sprintf('Select a %s Option', $title), $options);
        $this->processSelection($key, $options[$key]);
    }
}
