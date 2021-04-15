<?php

namespace App\Models;

use App\Events\CoopCanceled;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Coop extends Model
{
    use HasFactory;

    // Constants
    const CANCELED = 'canceled';

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'creating' => \App\Events\CoopCreating::class,
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function owner()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function hasBeenFullyFunded()
    {
        return $this->purchases->sum->amount >= $this->goal;
    }

    public function cancel(){
        DB::transaction(function () {
            $this->purchases->map(function ($purchase) {
                $purchase->cancel();
            });

            $this->status = Coop::CANCELED;
            $this->save();

            // The following is not intended to be triggered if none of previous cancelation succeeds
            event(new CoopCanceled($this));
        });
    }
}
