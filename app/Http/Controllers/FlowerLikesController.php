<?php

namespace App\Http\Controllers;

use App\Models\FlowerLike;
use Illuminate\Http\Request;

class FlowerLikesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // TODO: join and display the info (flower)????
        $user_id = auth()->user()->id;
        return FlowerLike::where('user_id', $user_id)->orderBy('created_at', 'desc')->paginate(20);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $flowerLike = new FlowerLike;

        $flowerLike->flower_id = $request->string('flower_id');
        $flowerLike->user_id = auth()->user()->id;
        // TODO: how do we handle unique constraint errors?
        $flowerLike->save();

        return $flowerLike;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        FlowerLike::where([
            ['id', '=', $id],
            ['user_id', '=', auth()->user()->id],
        ])->delete();

        return response()->noContent();;
    }
}
