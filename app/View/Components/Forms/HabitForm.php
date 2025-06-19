<?php

namespace App\View\Components\Forms;

use App\Services\CategoryService;
use App\Services\PeriodService;
use App\Services\UnitService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class HabitForm extends Component
{
    public Collection $categories;

    public Collection $periods;

    public Collection $units;

    private readonly UnitService $unitService;

    private readonly PeriodService $periodService;

    private readonly CategoryService $categoryService;

    public function __construct(public string $model, public string $submitAction)
    {
        $this->categoryService = app(CategoryService::class);
        $this->unitService = app(UnitService::class);
        $this->periodService = app(PeriodService::class);

        $this->getCategories();
        $this->getUnits();
        $this->getPeriods();
    }

    public function getCategories(): void
    {
        $this->categories = $this->categoryService->getList();
    }

    public function getUnits(): void
    {
        $this->units = $this->unitService->getList();
    }

    public function getPeriods(): void
    {
        $this->periods = $this->periodService->getList();
    }

    public function render(): View|Closure|string
    {
        return view('components.forms.habit-form');
    }
}
