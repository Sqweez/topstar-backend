<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\ProductBatch
 *
 * @property int $id
 * @property int $quantity
 * @property int $product_id
 * @property int $store_id
 * @property int $purchase_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch wherePurchasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereStoreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBatch whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\Club|null $club
 */
class ProductBatch extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function product(): BelongsTo {
        return $this->belongsTo(Product::class);
    }

    public function club(): BelongsTo {
        return $this->belongsTo(Club::class, 'store_id');
    }
}
