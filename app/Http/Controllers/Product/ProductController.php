<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Lib\ResponseFormatter;
use App\Models\Product\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
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
            $data = Product::get();
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
            $data = $request->only("nama_barang", "harga_satuan");

            $rule = [
                "nama_barang" => ["required", "string", "min:2", "max:100"],
                "harga_satuan" => ["required", "numeric", "min:0"],
            ];

            $validate = Validator::make($data, $rule);

            if ($validate->fails()) {
                return $this->response->fail("Validation fail", $validate->messages(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $dataProduct = collect($data)->only("nama_barang", "harga_satuan");
            $product = Product::create($dataProduct->toArray());

            return $this->response->success("Successfully create new product", $product);
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

            $product = Product::findOrFail($id);

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
            $data = $request->only("nama_barang", "harga_satuan");

            $data["id"] = $id;

            $rule = [
                "id" => ["required", "numeric", Rule::exists("master_barang", "id")->whereNull("deleted_at")],
                "nama_barang" => ["required", "string", "min:2", "max:100"],
                "harga_satuan" => ["required", "numeric", "min:0"],
            ];

            $validate = Validator::make($data, $rule);

            if ($validate->fails()) {
                return $this->response->fail("Validation fail", $validate->messages(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $dataProduct = collect($data)->only("nama_barang", "harga_satuan");
            $product = Product::where("id", $id)->update($dataProduct->toArray());
            $product = Product::findOrFail($id);

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
                "id" => ["required", "numeric", Rule::exists("master_barang", "id")->whereNull("deleted_at")],
            ];

            $validate = Validator::make(compact("id"), $rule);

            if ($validate->fails()) {
                return $this->response->fail("Validation fail", $validate->messages(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $product = Product::findOrFail($id);

            $product->delete();

            return $this->response->success("Successfully delete product", $product);
        } catch (Exception $e) {

            return $this->response->fail($e->getMessage());
        }
    }
}
