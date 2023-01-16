<?php

namespace App\Http\Controllers;

use App\FullServiceProduct;
use App\Product;
use Illuminate\Http\Request;

class FullServiceProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'quantity' => 'required|numeric',
            'price' => 'numeric',
        ];

        if($request->validate($rules)) {

            if($product = Product::where('name', $request->name)->first()) {
                $productItem = FullServiceProduct::create([
                    'full_service_id' => $request->fullServiceId,
                    'product_id' => $product->id,
                    'name' => $request->name,
                    'quantity' => $request->quantity,
                    'price' => $request->price,
                ]);
                $productItem = $productItem->fresh('product');
            } else {
                return response()->json([
                    'errors' => [
                        'name' => ['Product name was not found']
                    ]
                ], 422);
            }

            $this->dispatch($productItem->queSynch());

            return response()->json([
                'productItem' => $productItem,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required',
            'quantity' => 'required|numeric',
            'price' => 'numeric',
        ];

        if($request->validate($rules)) {

            if($product = Product::where('name', $request->name)->first()) {
                $productItem = FullServiceProduct::findOrFail($id);
                $productItem->update([
                    'full_service_id' => $request->fullServiceId,
                    'product_id' => $product->id,
                    'name' => $request->name,
                    'quantity' => $request->quantity,
                    'price' => $request->price,
                ]);
                $productItem = $productItem->fresh('product');
            } else {
                return response()->json([
                    'errors' => [
                        'name' => ['Product name was not found']
                    ]
                ], 422);
            }

            $this->dispatch($productItem->queSynch());

            return response()->json([
                'productItem' => $productItem,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteProduct($id)
    {
        $fullServiceProduct = FullServiceProduct::findOrFail($id);
        if($fullServiceProduct->delete()) {
            $this->dispatch($fullServiceProduct->queSynch());
            return response()->json([
                'fullServiceItem' => $fullServiceProduct,
            ]);
        }
    }
}
