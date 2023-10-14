<?php

namespace App\Http\Controllers;

use App\Models\FlowerLike;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FlowerLikesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->user()->id;
        return FlowerLike::where('user_id', $userId)->orderBy('created_at', 'desc')->paginate(20);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $userId = auth()->user()->id;
        $flowerId = $request->string('flower_id');

        if (FlowerLike::where(['flower_id' => $flowerId, 'user_id' => $userId])->exists()) {
            return response(null, Response::HTTP_CONFLICT);
        }

        $flowerLike = new FlowerLike;
        $flowerLike->flower_id = $flowerId;
        $flowerLike->user_id = $userId;
        $flowerLike->save();

        return $flowerLike;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        $like = FlowerLike::findOrFail($id);

        if ($like->user_id !== auth()->user()->id) {
            return response(null, Response::HTTP_FORBIDDEN);
        }

        $like->delete();

        return response()->noContent();
    }
}
