<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    /**
     * Create a new schedule.
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'offer_id' => 'required|integer|exists:offers,id',
            'day_of_week' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'from_hour' => 'required|date_format:H:i',
            'to_hour' => 'required|date_format:H:i|after:from_hour',
            'format' => 'nullable|string|max:255',
        ]);

        $schedule = Schedule::create($validated);

        return response()->json([
            'message' => 'Schedule created successfully',
            'schedule' => $schedule,
        ], 201);
    }

    /**
     * Update an existing schedule.
     */
    public function update(Request $request, $id)
    {
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json(['error' => 'Schedule not found'], 404);
        }

        $validated = $request->validate([
            'day_of_week' => 'nullable|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'from_hour' => 'nullable|date_format:H:i',
            'to_hour' => 'nullable|date_format:H:i|after:from_hour',
            'format' => 'nullable|string|max:255',
        ]);

        $schedule->update($validated);

        return response()->json([
            'message' => 'Schedule updated successfully',
            'schedule' => $schedule,
        ]);
    }

    /**
     * Get a single schedule by ID.
     */
    public function get($id)
    {
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json(['error' => 'Schedule not found'], 404);
        }

        return response()->json(['schedule' => $schedule]);
    }

    /**
     * Delete a schedule by ID.
     */
    public function delete($id)
    {
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json(['error' => 'Schedule not found'], 404);
        }

        $schedule->delete();

        return response()->json(['message' => 'Schedule deleted successfully']);
    }

    /**
     * List all schedules for a specific offer_id, ordered by day_of_week and from_hour.
     */
    public function listByOfferId($offerId)
    {
        $schedules = Schedule::where('offer_id', $offerId)
            ->orderByRaw("
            CASE day_of_week
                WHEN 'Monday' THEN 1
                WHEN 'Tuesday' THEN 2
                WHEN 'Wednesday' THEN 3
                WHEN 'Thursday' THEN 4
                WHEN 'Friday' THEN 5
                WHEN 'Saturday' THEN 6
                WHEN 'Sunday' THEN 7
            END
        ")
            ->orderBy('from_hour')
            ->get();

        return response()->json(['schedules' => $schedules]);
    }
}
