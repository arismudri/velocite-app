<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Order\Transaction;
use App\Models\Product\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $trasactions = Transaction::get();
        $products = Product::get();
        return view("transaction", compact("products", "trasactions"));
    }
}
