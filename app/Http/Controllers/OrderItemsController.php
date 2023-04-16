<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Validator;
use App\Models\OrderItem;
use App\Models\Products;

class OrderItemsController extends Controller
{
    /**
    * Store an item in a order.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function addItem(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|min:1|exists:products,id',
        ]);
        if($validator->fails()){
            return response()->json([
                "success" => false,
                "message" => 'Validation Error.', 
                "error" => $validator->errors()
            ]);    
        }

        $message = "Product added to order successfully.";
        //fetch product
        $product = Products::where('id', $request->product_id)->first();
        if($product == NULL) {
            $message = "Product not found.";
        }

        //fetch order
        $order = Order::where('id', $id)->first();
        if($order) {
            
            //we will add item in order only if order is not paid or failed
            if($order->status == 1) {
                //fetch item order
                $item = OrderItem::where('order_id', $id)
                    ->where('product_id', $request->product_id)
                    ->first();
    
                if($item) {
                    $item->quantity = $item->quantity + 1;
                    $item->price = $product->price;
                    $item->save();
                } else {
                    OrderItem::create([
                        'order_id' => $id,
                        'product_id' => $request->product_id,
                        'quantity' => 1,
                        'price' => $product->price
                    ]);
                }
    
                $order->total = $order->total + $product->price;
                $order->save();
    
                return response()->json([
                    "success" => true,
                    "message" => $message,
                    "data" => new OrderResource($order)
                ]);
            } else if($order->status == 2) {
                $message = "Order already paid.";
            } else {
                $message = "Order already failed.";
            }
        } else {
            $message = "Order not found.";
        }

        return response()->json([
            "success" => false,
            "message" => $message,
        ]);
    } 
}
