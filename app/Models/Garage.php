<?php

namespace App\Models;

use App\Support\Settings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Garage extends Model
{
    protected $fillable = [
        'user_id', 'name', 'logo', 'description',
        'status', 'admin_notes', 'reviewed_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(GaragePhoto::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public static function hideContactEnabled(): bool
    {
        return Settings::bool('hide_contact_until_accepted', true);
    }

    public static function acceptedGarageIdsFor(?User $user): array
    {
        if (! $user) {
            return [];
        }

        return DB::table('quotes')
            ->join('quote_request_garages', 'quotes.quote_request_garage_id', '=', 'quote_request_garages.id')
            ->join('branches', 'quote_request_garages.branch_id', '=', 'branches.id')
            ->join('quote_requests', 'quote_request_garages.quote_request_id', '=', 'quote_requests.id')
            ->where('quotes.status', 'accepted')
            ->where('quote_requests.user_id', $user->id)
            ->pluck('branches.garage_id')
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values()
            ->all();
    }
}
