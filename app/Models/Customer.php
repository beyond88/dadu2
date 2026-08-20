<?php

namespace App\Models;

use App\Traits\ModelBoot;
use App\Traits\Scopes\ScopeActive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;

/**
 * Customer
 */
class Customer extends Authenticatable implements JWTSubject
{
    protected $guard = 'customer';

    use HasFactory, ScopeActive, ModelBoot, Notifiable;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        "first_name",
        "last_name",
        "code",
        "email",
        'password',
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
        "billing_same",
        "b_first_name",
        "b_last_name",
        "b_email",
        "b_phone",
        "b_address_line_1",
        "b_address_line_2",
        "b_country",
        "b_state",
        "b_city",
        "b_zipcode",
        "b_short_address",
        "avatar",
        "status",
        "is_verified",
        "created_by",
        "updated_by",
        "type",
        "customer_id",
        "total_wallet_amount",
        "is_active",
        "opening_balance",
    ];
    protected $hidden = [
        'password',
    ];

    protected $appends = ['text', 'avatar_url', 'full_name'];

    // CONST
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_UNVERIFIED = 'unverified';
    public const TYPE_CUSTOMER = 'customer';
    public const TYPER_EMPLOYEE = 'employee';

    public const FILE_STORE_PATH = 'customers';

    // MUTATORS & ACCESSORS
    /**
     * getFullNameAttribute
     *
     * @return void
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    /**
     * getTextAttribute
     *
     * @return void
     */
    public function getTextAttribute()
    {
        return trim(($this->code ? '[' . $this->code . '] ' : '') . $this->first_name . ' ' . $this->last_name);
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

    // Relations

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


    /**
     * b_country_data
     *
     * @return void
     */
    public function b_country_data()
    {
        return $this->belongsTo(SystemCountry::class, 'b_country');
    }
    /**
     * b_state_data
     *
     * @return void
     */
    public function b_state_data()
    {
        return $this->belongsTo(SystemState::class, 'b_state');
    }
    /**
     * b_city_data
     *
     * @return void
     */
    public function b_city_data()
    {
        return $this->belongsTo(SystemCity::class, 'b_city');
    }
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function platforms()
    {
        return $this->hasMany(CustomerPlatform::class)->where('is_active', true);
    }

    /**
     * invoices
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'customer_id');
    }

    /**
     * Invoices raised for this customer, optionally bounded by date.
     */
    public function invoicesQuery($from = null, $to = null)
    {
        // invoices.date is a real DATE column, so it is compared directly rather
        // than through DATE() — that keeps an index on (customer_id, date) usable.
        return Invoice::where('customer_id', $this->id)
            ->when($from, fn ($q) => $q->where('date', '>=', $from))
            ->when($to, fn ($q) => $q->where('date', '<=', $to));
    }

    /**
     * Payments recorded against this customer's invoices, optionally bounded by
     * date. Bulk payments do not always stamp customer_id, so a payment is
     * treated as this customer's when it belongs to one of their invoices.
     */
    public function invoicePaymentsQuery($from = null, $to = null)
    {
        return InvoicePayment::whereIn('invoice_id', $this->invoices()->select('id'))
            ->dateFrom($from)
            ->dateTo($to);
    }

    /**
     * Money received from this customer that belongs to no invoice — surplus that
     * was turned into credit.
     */
    public function creditPaymentsQuery($from = null, $to = null)
    {
        return InvoicePayment::whereNull('invoice_id')
            ->where('customer_id', $this->id)
            ->dateFrom($from)
            ->dateTo($to);
    }

    /**
     * Everything this customer still owes: the sum of each invoice's own unpaid
     * remainder (see Invoice::dueAmount()).
     *
     * The opening balance is prepaid credit, not a due, so it is deliberately not
     * part of this sum. It only affects a due once it has been applied as a
     * balance payment on a specific invoice — that payment row is already counted
     * below. Payments not tied to an invoice (surplus credit) are excluded too.
     *
     * Passing $asOf gives the due at the end of that day instead of today's.
     */
    public function totalDue($asOf = null): float
    {
        return round($this->totalInvoiced(null, $asOf) - $this->totalPaid(null, $asOf), 2);
    }

    /**
     * Where this customer stands overall: prepaid credit minus what they owe.
     *
     * Positive means we hold credit for them, negative means they owe us. This
     * is a derived figure — opening_balance keeps holding the credit alone, so
     * an unpaid invoice shows up here immediately without being written into
     * the credit field (which would double count it against totalDue()).
     */
    public function netBalance($asOf = null): float
    {
        return round((float) $this->opening_balance - $this->totalDue($asOf), 2);
    }

    /**
     * Total value invoiced to this customer, regardless of payment.
     */
    public function totalInvoiced($from = null, $to = null): float
    {
        return round((float) $this->invoicesQuery($from, $to)->sum('total'), 2);
    }

    /**
     * Everything received from this customer against their invoices.
     */
    public function totalPaid($from = null, $to = null): float
    {
        return round((float) $this->invoicePaymentsQuery($from, $to)->sum('amount'), 2);
    }

    /**
     * The prepaid credit this customer held at the end of the given day.
     *
     * The stored opening_balance is today's figure, so the movements recorded
     * after that day are unwound: credit spent on invoices is added back, credit
     * received is taken off. Every automatic movement leaves a dated payment row,
     * so this is exact unless the balance field itself was edited by hand — those
     * edits are not recorded anywhere and cannot be dated.
     */
    public function balanceAsOf($date): float
    {
        if (!$date) {
            return round((float) $this->opening_balance, 2);
        }

        $spentAfter = (float) InvoicePayment::where('payment_type', 'balance')
            ->where(function ($q) {
                $q->whereIn('invoice_id', $this->invoices()->select('id'))
                  ->orWhere('customer_id', $this->id);
            })
            ->dateAfter($date)
            ->sum('amount');

        $creditedAfter = (float) $this->creditPaymentsQuery()->dateAfter($date)->sum('amount');

        return round((float) $this->opening_balance + $spentAfter - $creditedAfter, 2);
    }
}
