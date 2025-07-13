<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Events\OrderPlaced;
use App\Models\Product;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        info('OrderController@store called with request: ' . json_encode($request->all()));
        $order = Order::create($request->all());
        event(new OrderPlaced($order));
        return response()->json(['message' => 'Order placed successfully']);
    }

    public function index(){
        $orders = Product::all();
        return response()->json($orders);
    }
}

