<?php

namespace App\Models;

use App\Actions\Stripe\CancelCharge;
use App\Actions\Stripe\RefundCharge;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'type',
        'amount',
        'status',
        'source',
        'memo',
        'is_canceled',
        'is_pending',
    ];

    public static function sources()
    {
        return [
            'Check',
            'CreditCard',
            'KickfurtherCredits',
            'KickfurtherFunds',
            'Wire',
        ];
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function cancel() {
        if(!$this->is_canceled){
            $type_transaction = 'refund';
            if(Str::contains($this->source, ['Credits', 'Funds'])){
                $source = $this->source;
            } elseif($this->source === 'CreditCard'){
                if($this->is_pending){
                    $this->is_pending = false;
                    $stripe = new CancelCharge;
                    $stripe->refund(Str::random(10));
                } else {
                    $user_refund_prefs = Buyer::find($this->buyer_id)->refund_prefs;
                    if($user_refund_prefs === 'cc') {
                        $source = 'CreditCard';
                        $stripe = new RefundCharge;
                        $stripe->refund(Str::random(10), $this->amount);
                    } else {
                        $type_transaction = 'credit';
                        $source = 'KickfurtherCredits';
                    }
                }
            }

            if(!empty($source)){
                self::create([
                    'buyer_id' => $this->buyer_id,
                    'type' => $type_transaction,
                    'amount' => $this->amount,
                    'status' => 'completed',
                    'source' => $source,
                    'memo' => 'Canceled by expiration date',
                    'is_canceled' => false,
                    'is_pending' => false,
                ]);
            }

            $this->is_canceled = true;
            $this->save();
        }
    }
}
