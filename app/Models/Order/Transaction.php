<?php

namespace App\Models\Order;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'total_harga',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaksi_pembelian';

    /**
     * The "boot" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();


        self::creating(function ($model) {
            $user = Auth::user();
            $model->created_by = $user->id ?? null;
            $model->updated_by = $user->id ?? null;
        });

        static::updating(function ($model) {
            $user = Auth::user();
            $model->updated_by = $user->id ?? null;
        });

        static::deleting(function ($model) {
            $user = Auth::user();
            $model->deleted_by = $user->id ?? null;
        });
    }

    /**
     * the details of transaction product.
     */
    public function details()
    {
        return $this->hasMany(TransactionProduct::class, "transaksi_pembelian_id");
    }
}
