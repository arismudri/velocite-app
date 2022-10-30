<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'nama_barang', 'harga_satuan',
    ];


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master_barang';

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
     * the variants of product.
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
