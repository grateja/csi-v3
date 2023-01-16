<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Product;
use App\ProductPurchase;

class ProductsController extends Controller
{
    public function index(Request $request) {
        $products = Product::where('name', 'like', "%$request->keyword%")->orderBy('name')->get();

        return response()->json([
            'result' => $products
        ], 200);
    }

    public function store(Request $request) {
        $rules = [
            'name' => 'required|unique:products,name,NULL,id,deleted_at,NULL',
            'sellingPrice' => 'numeric',
            'currentStock' => 'numeric',
        ];

        if($request->validate($rules)) {



            return DB::transaction(function () use ($request) {

                $product = Product::withTrashed()->where('name', $request->name)->first();
                if($product) {
                    $product->update([
                        'name' => $request->name,
                        'description' => $request->description,
                        'selling_price' => $request->sellingPrice,
                        'current_stock' => $request->currentStock,
                        'img_path' => $request->imgPath,
                        'deleted_at' => null,
                    ]);
                } else {
                    $product = Product::create([
                        'name' => $request->name,
                        'description' => $request->description,
                        'selling_price' => $request->sellingPrice,
                        'current_stock' => $request->currentStock,
                        'img_path' => $request->imgPath,
                    ]);
                }

                $this->dispatch($product->queSynch());

                return response()->json([
                    'product' => $product,
                ], 200);
            });
        }
    }

    public function setPicture($id, Request $request) {
        if($request->hasFile('file')) {

            $product = Product::findOrFail($id);
            File::delete(public_path() . $product->img_path);

            $extension = $request->file('file')->getClientOriginalExtension();
            $name = str_random() . '.' . $extension;

            $path = '/img/products/';
            $source = $path . $name;
            $request->file('file')->move(public_path() . $path, $name);

            $product->update([
                'img_path' => $path . $name
            ]);

            return response()->json([
                'img_path' => $path . $name
            ]);
        }
        return response()->json([
            'errors' => [
                'message' => ['No File selected']
            ]
        ], 422);
    }

    public function removePicture($id) {
        $product = Product::findOrFail($id);
        File::delete(public_path() . $product->img_path);
        $product->update([
            'img_path' => ''
        ]);
        return response()->json([
            'message' => ['Picture removed']
        ]);
    }

    public function deleteProduct($id) {
        $product = Product::findOrFail($id);
        if($product->delete()) {
            File::delete(public_path() . $product->img_path);

            $this->dispatch($product->queSynch());

            return response()->json([
                'product' => $product,
                'success' => 'Product saved successfully',
            ]);
        }
    }

    public function update($id, Request $request) {
        $rules = [
            'name' => 'required',
            'sellingPrice' => 'numeric',
            'currentStock' => 'numeric',
        ];
        $product = Product::findOrFail($id);
        if($product->name != $request->name) {
            // name was changed
            $rules['name'] = 'required|unique:products,name,NULL,id,deleted_at,NULL';
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $product) {
                $product->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'selling_price' => $request->sellingPrice,
                    'current_stock' => $request->currentStock,
                    'img_path' => $request->imgPath,
                ]);

                $this->dispatch($product->queSynch());

                return response()->json([
                    'product' => $product,
                    'success' => 'Product saved successfully',
                ], 200);
            });
        }
    }

    public function show($id) {
        $product = Product::find($id);

        return response()->json([
            'product' => $product,
        ], 200);
    }

    public function addStock($productId, Request $request) {
        $product = Product::findOrFail($productId);

        $rules = [
            'date' => 'required|date',
            'quantity' => 'required|numeric|min:1',
            'unitCost' => 'numeric|nullable',
        ];

        if($request->validate($rules)) {
            $productPurchase = ProductPurchase::create([
                'date' => $request->date,
                'product_id' => $product->id,
                'receipt' => $request->receipt,
                'quantity' => $request->quantity,
                'unit_cost' => $request->unitCost,
                'remarks' => $request->remarks,
                'user_id' => auth('api')->id(),
            ]);

            $this->dispatch($product->queSynch());
            $this->dispatch($productPurchase->queSynch());

            return response()->json([
                'branchProduct' => $product->fresh(),
            ], 200);
        }
    }
}
