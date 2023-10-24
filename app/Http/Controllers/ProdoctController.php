<?php

namespace App\Http\Controllers;

use App\Models\Prodoct;
use Illuminate\Http\Request;

class ProdoctController extends Controller
{
    public function index(Request $request)
    {

        // start seatch button
        $keywords = $request->get('search');
        $perPage = 5;
        if (!empty($keywords)) {
            $products = Prodoct::where('name', 'LIKE', "%$keywords%")
                ->orWhere('category', 'LIKE', "%$keywords")
                ->latest()->paginate($perPage);
        }else {
            $products = Prodoct::latest()->paginate($perPage);
        }
        return view('products.index', ['products' => $products])->with('i',(request()->input('page',1) - 1) *5);
       // end search button

    }
    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'required|image|mimes:jpg,png,gif,svg|max:2028',
        ]);

        $product = new Prodoct();

        $file_name = time() . '.' . request()->image->getClientOriginalExtension();
        request()->image->move(public_path('image'), $file_name);

        $product->name = $request->name;
        $product->description = $request->description;
        $product->image = $file_name;
        $product->category = $request->category;
        $product->quantity = $request->quantity;
        $product->price = $request->price;

        $product->save();
        return redirect()->route('products.index')->with('success', 'product successfully saved');
    }

    public function edit($id){
        $product = Prodoct::findOrFail($id);
        return view('products.edit', ['product' => $product]);
    }
    public function update(Request $request, Prodoct $product){
        $request->validate([
            'name' => 'required',
        ]);
        $file_name = $request->hidden_product_image;

        if ($request->image != ''){
            $file_name = time() . '.' . request()->image->getClientOriginalExtension();
            request()->image->move(public_path('image'), $file_name);
        }

        $product = Prodoct::find($request->hidden_id);

        $product->name = $request->name;
        $product->description = $request->description;
        $product->image = $file_name;
        $product->category = $request->category;
        $product->quantity = $request->quantity;
        $product->price = $request->price;

        $product->save();
        return redirect()->route('products.index')->with('success', 'product successfully updated');
    }

    public function destroy($id){
        $product = Prodoct::findOrFail($id);
        $image_path = public_path()."/image/";
        $image = $image_path. $product->image;
        if (file_exists($image)) {
            @unlink($image);
        }
        $product->delete();
        return redirect('products')->with('success', 'product successfully deleted');
    }
}
