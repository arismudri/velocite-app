<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksi_pembelian_barang', function (Blueprint $table) {
            $table->id();
            $table->integer("jumlah")->default(0);
            $table->integer("harga_satuan")->default(0);
            $table->unsignedBigInteger("transaksi_pembelian_id")->nullable();
            $table->foreign("transaksi_pembelian_id")->references("id")->on("transaksi_pembelian")->onUpdate("cascade")->onDelete("restrict");
            $table->unsignedBigInteger("master_barang_id")->nullable();
            $table->foreign("master_barang_id")->references("id")->on("master_barang")->onUpdate("cascade")->onDelete("restrict");

            $table->unsignedBigInteger("created_by")->nullable();
            $table->foreign("created_by")->references("id")->on("users")->onUpdate("cascade")->onDelete("restrict");
            $table->unsignedBigInteger("updated_by")->nullable();
            $table->foreign("updated_by")->references("id")->on("users")->onUpdate("cascade")->onDelete("restrict");
            $table->unsignedBigInteger("deleted_by")->nullable();
            $table->foreign("deleted_by")->references("id")->on("users")->onUpdate("cascade")->onDelete("restrict");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksi_pembelian_barang');
    }
}
