<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function index(Request $request) {
        $query = Order::with('product')->query();

        # filter based on status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        # filter based on product
        if ($request->has('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        return response()->json($query->paginate(10));
    }

    # detail view
    public function show($id) {
        $order = Order::with('product')->findOrFail($id);
        return response()->json($order);
    }

    # create    
    public function store(Request $request) {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $order = Order::create([
            'product_id' => $request->input('product_id'),
            'quantity' => $request->input('quantity'),
            'status' => $request->input('status'),
        ]);

        $order = Order::create($validated);

        return response()->json($order, 201);
    }

    # Update order status (e.g., approve, reject, cancel)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->input('status');
        $order->save();

        return response()->json($order);
    }

    # update order
    public function update(Request $request, $id) {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'product_id' => 'sometimes|required|exists:products,id',
            'quantity' => 'sometimes|required|integer|min:1',
            'status' => 'sometimes|required|in:pending,approved,rejected,cancelled',
        ]);

        $order->update($validated);

        return response()->json($order);
    }

    # delete
    public function destroy($id) {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
