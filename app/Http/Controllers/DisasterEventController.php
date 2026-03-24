<?php

namespace App\Http\Controllers;

use App\Models\DisasterEvent;
use Illuminate\Http\Request;

class DisasterEventController extends Controller
{
    public function index()
    {
        $events = DisasterEvent::paginate(10);
        return view('disaster_events.index', compact('events'));
    }

    public function create()
    {
        return view('disaster_events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'disaster_type' => 'required',
            'date' => 'required|date',
            'description' => 'required',
        ]);

        DisasterEvent::create($request->all());
        return redirect()->route('disaster_events.index')->with('success', 'Disaster event created successfully.');
    }

    public function show(DisasterEvent $disasterEvent)
    {
        return view('disaster_events.show', compact('disasterEvent'));
    }

    public function edit(DisasterEvent $disasterEvent)
    {
        return view('disaster_events.edit', compact('disasterEvent'));
    }

    public function update(Request $request, DisasterEvent $disasterEvent)
    {
        $request->validate([
            'disaster_type' => 'required',
            'date' => 'required|date',
            'description' => 'required',
        ]);

        $disasterEvent->update($request->all());
        return redirect()->route('disaster_events.index')->with('success', 'Disaster event updated successfully.');
    }

    public function destroy(DisasterEvent $disasterEvent)
    {
        $disasterEvent->delete();
        return redirect()->route('disaster_events.index')->with('success', 'Disaster event deleted successfully.');
    }
}