<?php

namespace App\Http\Controllers\v1_0;

// Illuminate
use App\Branch;
use App\Tree;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;

class StateController extends Controller
{   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        return [
            'trees' => Tree::allWithRelations(),
            'branches' => Branch::allWithRelations()
        ];
    }
}
