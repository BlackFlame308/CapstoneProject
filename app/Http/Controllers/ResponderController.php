<?php

namespace App\Http\Controllers;

use App\Models\Responder;
use Illuminate\Http\Request;

class ResponderController extends Controller
{
    public function index()
    {
        $responders = Responder::paginate(10);
        return view('responders.index', compact('responders'));
    }

    public function create()
    {
        return view('responders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'responder_id' => 'required|unique:responders',
            'contact_number' => 'required',
            'assigned_area' => 'required',
            'address' => 'nullable',
        ]);

        Responder::create($request->all());
        return redirect()->route('responders.index')->with('success', 'Responder created successfully.');
    }

    public function show(Responder $responder)
    {
        return view('responders.show', compact('responder'));
    }

    public function edit(Responder $responder)
    {
        return view('responders.edit', compact('responder'));
    }

    public function update(Request $request, Responder $responder)
    {
        $request->validate([
            'name' => 'required',
            'responder_id' => 'required|unique:responders,responder_id,' . $responder->id,
            'contact_number' => 'required',
            'assigned_area' => 'required',
            'address' => 'nullable',
        ]);

        $responder->update($request->all());
        return redirect()->route('responders.index')->with('success', 'Responder updated successfully.');
    }

    public function destroy(Responder $responder)
    {
        $responder->delete();
        return redirect()->route('responders.index')->with('success', 'Responder deleted successfully.');
    }
}