<?php

namespace App\Models;

use App\Traits\CreatedByRelationship;
use App\Traits\CreatedUpdatedBy;
use App\Traits\ProductRelationship;
use App\Traits\UpdatedByRelationship;
use App\Traits\WarehouseRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ProductStockHistory extends Model
{
    use HasFactory, CreatedUpdatedBy, ProductRelationship, WarehouseRelationship, CreatedByRelationship, UpdatedByRelationship;

    protected $guarded = ['id'];

    const TYPE_IN   = 'in';
    const TYPE_OUT  = 'out';

    const ACTION_FROM_STOCK_UPDATE      = 'stock_update';
    const ACTION_FROM_PURCHASE_RECEIVE  = 'purchase_receive';
    const ACTION_FROM_PURCHASE_RETURN   = 'purchase_return';
    const ACTION_FROM_INVOICE           = 'invoice';
    const ACTION_FROM_INVOICE_RETURN    = 'invoice_return';
    const ACTION_FROM_FREE_ITEM         = 'free_item';
    const ACTION_FROM_TRANSFER_OUT      = 'transfer_out';
    const ACTION_FROM_TRANSFER_IN       = 'transfer_in';
    const ACTION_FROM_DAMAGE            = 'damage';
    const ACTION_FROM_LOSS              = 'loss';

    public function product_stock()
    {
        return $this->belongsTo(ProductStock::class);
    }
    public function historyFrom() : MorphTo
    {
        return $this->morphTo('history_from');
    }
}
