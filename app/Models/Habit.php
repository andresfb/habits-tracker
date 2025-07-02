<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\DateAttributable;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

/**
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property int $unit_id
 * @property int $period_id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $icon
 * @property int $target_value
 * @property int $default_value
 * @property bool $allow_multiple_times
 * @property string $notes
 * @property int $order_by
 * @property Unit $unit
 * @property Period $period
 * @property Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class Habit extends SluggableModel
{
    use HasFactory;
    use SoftDeletes;
    use DateAttributable;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(HabitEntry::class);
    }

    public function scopeWithInfo(Builder $query): Builder
    {
        return $query->with([
            'user',
            'unit',
            'period',
            'category',
        ]);
    }

    public function scopeWithEntriesOnDay(Builder $query, ?CarbonImmutable $asOfDate = null): Builder
    {
        $recordedTimezone = Config::string('app.timezone');
        $timezone = Config::string('constants.default_timezone');

        $sourceDate = $asOfDate instanceof CarbonImmutable
            ? $asOfDate->startOfDay()
            : Carbon::now()->startOfDay();

        $fromDate = CarbonImmutable::parse($sourceDate, $timezone)
            ->timezone($recordedTimezone)
            ->toDateTimeString();

        return $query->with(['entries' => function ($q) use ($fromDate): void {
            $q->join('habits', 'habit_entries.habit_id', '=', 'habits.id')
                ->join('periods', 'habits.period_id', '=', 'periods.id')
                ->select('habit_entries.*')
                ->whereRaw(
                    'DATEDIFF(?, DATE(habit_entries.logged_at))
                    BETWEEN 0 AND periods.interval_days - 1',
                    [$fromDate]
                );
        }]);
    }

    public function scopeWithEntriesOnMonth(Builder $query, CarbonImmutable $asOfDate): Builder
    {
        $recordedTimezone = Config::string('app.timezone');
        $timezone = Config::string('constants.default_timezone');

        $fromDate = CarbonImmutable::parse($asOfDate->startOfMonth(), $timezone)
            ->timezone($recordedTimezone)
            ->toDateTimeString();

        $toDate = CarbonImmutable::parse($asOfDate->endOfMonth(), $timezone)
            ->timezone($recordedTimezone)
            ->toDateTimeString();

        return $query->with(['entries' => function ($q) use ($fromDate, $toDate): void {
            $q->join('habits', 'habit_entries.habit_id', '=', 'habits.id')
                ->join('periods', 'habits.period_id', '=', 'periods.id')
                ->select('habit_entries.*')
                ->whereRaw(
                    'habit_entries.logged_at BETWEEN DATE_SUB(?, INTERVAL periods.interval_days -1 DAY) AND ? ',
                    [$fromDate, $toDate]
                );
        }]);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'unit_id' => $this->unit_id,
            'period_id' => $this->period_id,
            'name' => $this->name,
            'description' => $this->description ?? '',
            'icon' => $this->icon,
            'target_value' => $this->target_value,
            'default_value' => $this->default_value,
            'allow_multiple_times' => $this->allow_multiple_times,
            'notes' => $this->notes ?? '',
            'order_by' => $this->order_by,
        ];
    }

    protected static function booted(): void
    {
        self::saved(static function (): void {
            Cache::tags('habits')->flush();
            Cache::tags('trackers')->flush();
        });
    }

    protected function casts(): array
    {
        return [
            'category_id' => 'integer',
            'unit_id' => 'integer',
            'period_id' => 'integer',
            'target_value' => 'integer',
            'default_value' => 'integer',
            'allow_multiple_times' => 'boolean',
            'order_by' => 'integer',
        ];
    }

    protected function targetValue(): Attribute
    {
        return Attribute::make(
            get: static fn (int $val): float => $val / 1000,
            set: static fn (float $val): int => (int) round($val * 1000),
        );
    }

    protected function defaultValue(): Attribute
    {
        return Attribute::make(
            get: static fn (int $val): float => $val / 1000,
            set: static fn (float $val): int => (int) round($val * 1000),
        );
    }

    protected function icon(): Attribute
    {
        return Attribute::make(
            get: static fn (?string $val): ?string => is_null($val)
                ? Config::string('constants.default_icon')
                : $val,
        );
    }

    protected function createdAt(): Attribute
    {
        return $this->localizedDate();
    }

    protected function updatedAt(): Attribute
    {
        return $this->localizedDate();
    }
}
