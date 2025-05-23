<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

class ProductController extends Controller
{
    # filter view
    public function index(Request $request) {
        $query = Product::query();

        # filter with status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        # filter with minimum price
        if ($request->has('price_min')) {
            $query->where('price', '>=', $request->input('price_min'));
        }

        # filter with maximum price
        if ($request->has('price_max')) {
            $query->where('price', '<=', $request->input('price_max'));
        }

        if ($request->has('in_stock')) {
            $query->where('stock', '>', 0);
        }

        return response()->json($query->paginate(10));
    }

    # detail view
    public function show($id) {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        return response()->json($product);
    }
    
    # create 
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        // Set status based on stock
        $validated['status'] = ($validated['stock'] > 0) ? 'active' : 'inactive';

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    # Reduce stock for a product (called by order service when order is approved)
    public function reduceStock(Request $request, $id) {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($id);

        if ($product->stock < $validated['quantity']) {
            return response()->json(['error' => 'Insufficient stock'], 400);
        }

        $product->stock -= $validated['quantity'];
        $product->status = ($product->stock > 0) ? 'active' : 'inactive';
        $product->save();

        return response()->json($product);
    }

    # Add stock for a product (e.g., restock)
    public function addStock(Request $request, $id) {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($id);

        $product->stock += $validated['quantity'];
        $product->status = ($product->stock > 0) ? 'active' : 'inactive';
        $product->save();

        return response()->json($product);
    }

    # update
    public function update(Request $request, $id) {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:products,name,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
        ]);

        # If stock is being updated, set status accordingly
        if (array_key_exists('stock', $validated)) {
            $validated['status'] = ($validated['stock'] > 0) ? 'active' : 'inactive';
        }
        
        $product->update($validated);

        return response()->json($product);
    }

    # delete
    public function destroy($id) {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(null, 204);
    }
}
