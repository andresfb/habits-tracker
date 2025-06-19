<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvitationStatus;
use App\Events\InvitationApprovedEvent;
use App\Events\InvitationRejectedEvent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $email
 * @property string $token
 * @property Carbon $registered_at
 * @property Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class Invitation extends Model
{
    use SoftDeletes;

    public static function generateToken(): string
    {
        return mb_substr(
            string: hash(
                'sha256',
                sprintf('%s|%s', Str::random(20), time())
            ),
            start: 0,
            length: 40
        );
    }

    public static function getPendingList(): array
    {
        return self::select('id', 'email')
            ->where('status', InvitationStatus::CREATED)
            ->latest()
            ->get()
            ->pluck('email', 'id')
            ->toArray();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approve(): void
    {
        $approved = self::where('id', $this->id)
            ->update([
                'status' => InvitationStatus::APPROVED,
            ]);

        if ($approved === false) {
            throw new RuntimeException('Invitation could not be approved');
        }

        InvitationApprovedEvent::dispatch($this->id);
    }

    public function reject(string $reason): void
    {
        $rejected = self::where('id', $this->id)
            ->update([
                'status' => InvitationStatus::REJECTED,
            ]);

        if ($rejected === false) {
            throw new RuntimeException('Invitation could not be rejected');
        }

        InvitationRejectedEvent::dispatch($this->id, $reason);
    }

    public function complete(int $userId, string $token): void
    {
        $updated = self::where('token', $token)
            ->update([
                'user_id' => $userId,
                'registered_at' => now(),
                'status' => InvitationStatus::REGISTERED,
            ]);

        if ($updated === false) {
            throw new RuntimeException('Invitation could not be completed.');
        }
    }

    protected function casts(): array
    {
        return [
            'status' => InvitationStatus::class,
        ];
    }
}
