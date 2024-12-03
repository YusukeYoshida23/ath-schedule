<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return auth()->user()->schedules()
        ->orderBy('start_time')
        ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $schedule = auth()->user()->schedules()->create($validated);

        return response()->json($schedule, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        if ($schedule->user_id !== auth()->id()) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        return response()->json($schedule);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        if ($schedule->user_id !== auth()->id()) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $schedule->update($validated);

        return response()->json($schedule);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        if ($schedule->user_id !== auth()->id()) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        $schedule->delete();

        return response()->json(['message' => '削除しました']);
    }
}
