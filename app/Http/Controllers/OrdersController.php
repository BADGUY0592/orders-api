<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helper;

class OrdersController extends Controller
{
    /**
    * Display a listing of the orders.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        $orders = OrderResource::collection(Order::all());
        return response()->json([
            "success" => true,
            "message" => "Order List",
            "data" => $orders
        ]);
    }

    /**
    * Store a newly created order.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer|min:1|exists:users,id',
        ]);
        if($validator->fails()){
            return response()->json([
                "success" => false,
                "message" => 'Validation Error.', 
                "error" => $validator->errors()
            ]);    
        }

        $order = Order::firstOrCreate([
            'customer_id' => $request->customer_id,
            'status' => 1,
        ], [
            'total' => 0.00
        ]);

        return response()->json([
            "success" => true,
            "message" => "Order created successfully.",
            "data" => new OrderResource($order)
        ]);
    } 

    /**
    * Display the specified order.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id)
    {
        $order = Order::where('id', $id)->first();
        $order->status = $order->full_status;
        if($order) {
            return response()->json([
                "success" => true,
                "message" => "Order retrieved successfully.",
                "data" => new OrderResource($order)
            ]);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Order not found.",
            ]); 
        }
    }

    /**
    * Update the specified customer in order.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer|min:1|exists:users,id',
        ]);
        if($validator->fails()){
            return response()->json([
                "success" => false,
                "message" => 'Validation Error.', 
                "error" => $validator->errors()
            ]);    
        }
        $order = Order::where('id', $id)->first();
        if($order) {
            $order->customer_id = $request->customer_id;
            $order->save();
    
            return response()->json([
                "success" => true,
                "message" => "Order updated successfully.",
                "data" => new OrderResource($order)
            ]);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Order not found.",
            ]);
        }
    }

    /**
    * Remove the specified order.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        $order = Order::where('id', $id)->first();
        if($order) {
            $order->delete();
    
            return response()->json([
                "success" => true,
                "message" => "Order deleted successfully.",
                "data" => new OrderResource($order)
            ]);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Order not found.",
            ]);
        }
    }
    
    /**
    * Pay an order.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function pay($id) {
        $order = Order::where('id', $id)
            ->with('user')
            ->first();
        $message = "Order Paid Successfully.";

        if($order) {
            if($order->status == 1) {
                $data = [
                    'order_id' => $order->id,
                    'customer_email' => $order->user->email,
                    'value' => $order->total
                ];

                $response = Helper::payOrder($data);

                if($response->message == "Payment Successful") {
                    $order->update([
                        'status' => 2
                    ]);

                    return response()->json([
                        "success" => true,
                        "message" => "Order paid successfully.",
                        "data" => new OrderResource($order)
                    ]);
                } else if($response->message == "Insufficient Funds") {
                    $order->update([
                        'status' => 3
                    ]);
                    
                    return response()->json([
                        "success" => true,
                        "message" => "Order payment failed due to insufficient funds.",
                        "data" => new OrderResource($order)
                    ]);
                } else {
                    $message = "Order payment failed as something went wrong with payment gateway(". $response->message .").";
                }
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
