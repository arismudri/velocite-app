<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Lib\ResponseFormatter;
use App\Models\Order\Transaction;
use App\Models\Order\TransactionProduct;
use App\Models\Product\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class TransactionProductController extends Controller
{
    protected $response;

    public function __construct()
    {
        $this->response = new ResponseFormatter();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = Transaction::with("details")->get();
            return $this->response->success("Successfully get all product", $data);
        } catch (Exception $e) {

            return $this->response->fail($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all()["data"] ?? [];

            $rule = [
                "*.master_barang_id" => ["required", "numeric", Rule::exists("master_barang", "id")->whereNull("deleted_at")],
                "*.jumlah" => ["required", "numeric", "min:0"],
            ];

            $validate = Validator::make($data, $rule);

            if ($validate->fails()) {
                return $this->response->fail("Validation fail", $validate->messages(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $products = Product::whereIn("id", collect($data)->map(function ($item) {
                return $item["master_barang_id"];
            }))->get();


            $dataTransactionProduct = collect($data)->map(function ($item) use ($products) {
                $item["harga_satuan"] = $products->where("id", $item["master_barang_id"])->first()->harga_satuan;
                return $item;
            });

            $total_harga = $dataTransactionProduct->reduce(function ($carry, $current) {
                $carry += (int) $current["harga_satuan"] * (int) $current["jumlah"];
                return $carry;
            }, 0);

            $transaction = Transaction::create([
                "total_harga" => $total_harga
            ]);

            $user = Auth::user();

            $transactionProduct = TransactionProduct::insert(
                $dataTransactionProduct->map(function ($item) use ($transaction, $user) {
                    $item["transaksi_pembelian_id"] = $transaction->id;
                    $item["created_at"] = now();
                    $item["updated_at"] = now();
                    return $item;
                })->toArray()
            );

            $transaction->get();

            return $this->response->success("Successfully create new product", $transaction);
        } catch (Exception $e) {

            return $this->response->fail($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $product_id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {

        try {
            $rule = [
                "id" => ["required", "numeric", Rule::exists("master_barang", "id")->whereNull("deleted_at")],
            ];

            $validate = Validator::make(compact("id"), $rule);

            if ($validate->fails()) {
                return $this->response->fail("Validation fail", $validate->messages(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $product = Transaction::findOrFail($id);
            $product->details;

            return $this->response->success("Successfully get product", $product);
        } catch (Exception $e) {

            return $this->response->fail($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $product_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        try {
            $data = $request
                ->put("id", $id)
                ->only("nama_barang", "harga_satuan");

            $rule = [
                "id" => ["required", "numeric", Rule::exists("master_barang", "id")->whereNull("deleted_at")],
                "nama_barang" => ["required", "string", "min:2", "max:100"],
                "harga_satuan" => ["required", "numeric"],
            ];

            $validate = Validator::make($data, $rule);

            if ($validate->fails()) {
                return $this->response->fail("Validation fail", $validate->messages(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $product = TransactionProduct::findOrFail($id);
            $dataTransactionProduct = collect($data)->only("nama_barang", "harga_satuan");
            $product->update($dataTransactionProduct->toArray());

            return $this->response->success("Successfully update product", $product);
        } catch (Exception $e) {

            return $this->response->fail($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $product_id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        try {
            $rule = [
                "id" => ["required", "string", Rule::exists("master_barang", "id")->whereNull("deleted_at")],
            ];

            $validate = Validator::make(compact("id"), $rule);

            if ($validate->fails()) {
                return $this->response->fail("Validation fail", $validate->messages(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $product = TransactionProduct::findOrFail($id);

            return $this->response->success("Successfully delete product", $product);
        } catch (Exception $e) {

            return $this->response->fail($e->getMessage());
        }
    }
}
