<?php

namespace App\Http\Controllers;

use App\Models\Flower;

class FlowersController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    // TODO: filter depending on other properties
    public function index() {
        return Flower::orderBy('created_at', 'desc')->paginate(20);
    }

    /**
     * Display the specified resource.
     * 
     * @param string  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id) {
        return Flower::findOrFail($id);
    }
}
