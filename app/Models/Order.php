<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_READY_FOR_COLLECTION = 'ready_for_collection';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'total',
        'status',
        'received_at',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'received_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * The statuses accepted by the order workflow.
     *
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_READY_FOR_COLLECTION,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * Return only the next actions an administrator may take from this status.
     * Completed orders are confirmed by the customer, never by staff.
     *
     * @return array<string, string>
     */
    public function availableAdminTransitions(): array
    {
        return match ($this->status) {
            self::STATUS_PENDING => [
                self::STATUS_PROCESSING => 'Start processing',
                self::STATUS_CANCELLED => 'Cancel order',
            ],
            self::STATUS_PROCESSING => [
                self::STATUS_READY_FOR_COLLECTION => 'Mark ready for collection',
                self::STATUS_CANCELLED => 'Cancel order',
            ],
            default => [],
        };
    }

    public function canBeUpdatedByAdminTo(string $status): bool
    {
        return array_key_exists($status, $this->availableAdminTransitions());
    }

    public function canBeCancelledByCustomer(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeMarkedReceivedByCustomer(): bool
    {
        return $this->status === self::STATUS_READY_FOR_COLLECTION;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_READY_FOR_COLLECTION => 'Ready for collection',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    /**
     * Put reserved inventory back when this order is cancelled. Call only
     * inside a database transaction while the order row is locked.
     */
    public function restoreReservedStock(): void
    {
        $items = $this->items()
            ->whereNotNull('product_id')
            ->selectRaw('product_id, SUM(quantity) as quantity')
            ->groupBy('product_id')
            ->orderBy('product_id')
            ->get();

        foreach ($items as $item) {
            $product = Product::query()
                ->lockForUpdate()
                ->find($item->product_id);

            if ($product) {
                $product->increment('stock', $item->quantity);
            }
        }
    }
}
