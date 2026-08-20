<?php

namespace App\Models;

use App\Traits\ModelBoot;
use App\Traits\Scopes\ScopeActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Supplier
 */
class Supplier extends Model
{
    use HasFactory, ScopeActive, ModelBoot;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        "first_name",
        "last_name",
        "email",
        "phone",
        "company",
        "designation",
        "address_line_1",
        "address_line_2",
        "country",
        "state",
        "city",
        "zipcode",
        "short_address",
        "avatar",
        "status",
        "created_by",
        "updated_by",
        "opening_balance"
    ];

    /**
     * appends
     *
     * @var array
     */
    protected $appends = ['text', 'avatar_url', 'full_name'];

    // CONST
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';


    public const FILE_STORE_PATH = 'suppliers';

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * What the opening balance contributes to the amount owed to this supplier.
     *
     * The stored figure follows the ledger convention the supplier form uses:
     * negative means we already owed them that much before any purchase was
     * recorded. Purchases count the other way round — a bill raises the due as
     * a positive number — so the opening balance has to be flipped before the
     * two can be added together.
     */
    public function openingDue(): float
    {
        return round(-1 * (float) $this->opening_balance, 2);
    }

    /**
     * The two halves of the opening balance, split so neither shows up as a
     * negative amount on screen: money already handed to the supplier before
     * any purchase (an advance) versus money already owed to them (a debt).
     * Exactly one of these is non-zero for a given supplier.
     */
    public function openingAdvance(): float
    {
        return round(max(0, (float) $this->opening_balance), 2);
    }

    public function openingDebt(): float
    {
        return round(max(0, -1 * (float) $this->opening_balance), 2);
    }

    /**
     * Everything billed to us by this supplier: purchases plus what was already
     * owed when the supplier was created.
     */
    public function totalBilled($purchaseTotal = null): float
    {
        $purchases = $purchaseTotal !== null
            ? (float) $purchaseTotal
            : (float) $this->purchases()->sum('total');

        return round($purchases + $this->openingDue(), 2);
    }

    /**
     * Still outstanding to this supplier: everything billed minus everything
     * paid. Positive means we owe them.
     */
    public function totalDue($purchaseTotal = null, $paidTotal = null): float
    {
        $paid = $paidTotal !== null
            ? (float) $paidTotal
            : (float) \App\Models\PurchasePayment::whereIn('purchase_id', $this->purchases()->select('id'))->sum('amount');

        return round($this->totalBilled($purchaseTotal) - $paid, 2);
    }

    // MUTATORS & ACCESSORS
    /**
     * getTextAttribute
     *
     * @return void
     */
    public function getTextAttribute()
    {
        return $this->name;
    }
    /**
     * getAvatarUrlAttribute
     *
     * @return void
     */
    public function getAvatarUrlAttribute()
    {
        return getStorageImage(self::FILE_STORE_PATH, $this->avatar);
    }

    /**
     * getFullNameAttribute
     *
     * @return void
     */
    public function getFullNameAttribute()
    {
        return $this->last_name ? $this->first_name . ' ' . $this->last_name : $this->first_name;
    }

    /**
     * systemCountry
     *
     * @return void
     */
    public function systemCountry()
    {
        return $this->belongsTo(SystemCountry::class, 'country');
    }

    /**
     * systemState
     *
     * @return void
     */
    public function systemState()
    {
        return $this->belongsTo(SystemState::class, 'state');
    }

    /**
     * systemCity
     *
     * @return void
     */
    public function systemCity()
    {
        return $this->belongsTo(SystemCity::class, 'city');
    }

}
