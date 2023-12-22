<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property Merchant $merchant
 * @property Affiliate $affiliate
 * @property float $subtotal
 * @property float $commission_owed
 * @property string $payout_status
 */
class Order extends Model
{
    use HasFactory;

    const STATUS_UNPAID = 'unpaid';
    const STATUS_PAID = 'paid';

    protected $fillable = [
        'merchant_id',
        'affiliate_id',
        'subtotal',
        'commission_owed',
        'payout_status',
        'customer_email',
        'created_at',
        'external_order_id'
    ];


    protected $appends=['order_id'];

    public function getOrderIdAttribute()
    {
        return $this->external_order_id;
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function unPaid()
    {
        return $this->where('payout_status', self::STATUS_UNPAID);
    }

    public function paid()
    {
        return $this->where('payout_status', self::STATUS_PAID);
    }
}
