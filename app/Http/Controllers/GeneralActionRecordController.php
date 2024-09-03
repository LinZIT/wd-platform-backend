<?php

namespace App\Http\Controllers;

use App\Models\GeneralActionRecord;
use Illuminate\Http\Request;

class GeneralActionRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $high_importance = GeneralActionRecord::where('importance', 'Alta')->all();
            $regular_importance = GeneralActionRecord::where('importance', 'Normal')->all();
            $low_importance = GeneralActionRecord::where('importance', 'Leve')->all();
            return response()->json(['data' => ['high' => $high_importance, 'regular' => $regular_importance, 'low' => $low_importance]]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(GeneralActionRecord $generalActionRecord)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GeneralActionRecord $generalActionRecord)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GeneralActionRecord $generalActionRecord)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GeneralActionRecord $generalActionRecord)
    {
        //
    }
}
