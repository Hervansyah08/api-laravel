<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Support\Facades\Validator;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $person =  Person::all();
        return response()->json([
            'status' => true,
            'message' => 'Semua Data Person ',
            'data' => $person,
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $person = Person::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Data Person berhasil disimpan',
            'data' => $person,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Person $person)
    {
        return response()->json([
            'status' => true,
            'message' => 'Data Person Berhasil Ditemukan',
            'data' => $person,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Person $person)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $person->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Data Person berhasil diupdate',
            'data' => $person,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Person $person)
    {
        $person->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data Person berhasil dihapus',
        ], 204);
    }
}
