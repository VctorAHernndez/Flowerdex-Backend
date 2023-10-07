<?php

use App\Http\Controllers\FlowersController;
use App\Http\Controllers\FlowerLikesController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->string('email'))->first();

    if (!$user || !Hash::check($request->string('password'), $user->password)) {
        throw ValidationException::withMessages([
            'error' => ['The provided credentials are incorrect'],
        ]);
    }

    // TODO: revoke depending on device type (i.e. mobile, web, native, etc.)
    $user->tokens()->delete();

    // TODO; somehow remove the prefix of the plaintextoken
    return $user->createToken('universal-login-token')->plainTextToken;
});

Route::post('/signup', function (Request $request) {
    $request->validate([
        'name' => 'required',
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $existingUser = User::where('email', $request->string('email'))->first();

    if ($existingUser) {
        throw ValidationException::withMessages([
            'error' => ['User with provided credentials already exists'],
        ]);
    }

    $newUser = new User;
    $newUser->name = $request->string('name');
    $newUser->email = $request->string('email');
    $newUser->password = Hash::make($request->string('password'));
    $newUser->save();

    // TODO; somehow remove the prefix of the plaintextoken
    return $newUser->createToken('universal-login-token')->plainTextToken;
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/flowers', [FlowersController::class, 'index'])->middleware('auth:sanctum');
Route::get('/flowers/{id}', [FlowersController::class, 'show'])->middleware('auth:sanctum');

Route::get('/likes', [FlowerLikesController::class, 'index'])->middleware('auth:sanctum');
Route::post('/likes', [FlowerLikesController::class, 'store'])->middleware('auth:sanctum');
Route::delete('/likes/{id}', [FlowerLikesController::class, 'destroy'])->middleware('auth:sanctum');
