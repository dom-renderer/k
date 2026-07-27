<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('inventory.index')->with('success', 'Product created successfully!');
    }
}
