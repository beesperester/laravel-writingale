<?php

namespace App\Http\Controllers\v1_0;

// Illuminate
use App\Branch;
use App\Tree;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json([
            'trees' => Tree::allWithRelations(),
            'branches' => Branch::allWithRelations(),
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
}
