<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\DiagramRequest;
use App\Models\Diagram;
use App\Services\DiagramService;
use Illuminate\Http\Request;

class DiagramController extends Controller  //TODO add a policy for this thing
{
    public function index(Request $request)
    {
        $diagrams = $request->user()->diagrams()->get();
        return view('diagrams.index', ['diagrams' => $diagrams]);
    }
    public function show($id)
    {
        $diagram = DiagramService::getDiagramById($id);
        return view('diagrams.show', ['diagram' => $diagram]);
    }
}
