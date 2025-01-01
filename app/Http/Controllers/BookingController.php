<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Schedule;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Create a new booking.
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'offer_id' => 'required|integer|exists:offers,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'format' => 'nullable|string|max:255',
        ]);

        $booking = Booking::create($validated);

        return response()->json([
            'message' => 'Booking created successfully',
            'booking' => $booking,
        ], 201);
    }

    /**
     * Update an existing booking.
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        $validated = $request->validate([
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'format' => 'nullable|string|max:255',
        ]);

        $booking->update($validated);

        return response()->json([
            'message' => 'Booking updated successfully',
            'booking' => $booking,
        ]);
    }

    /**
     * Delete a booking by ID.
     */
    public function delete($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully']);
    }

    /**
     * Get available bookings for a specific offer (with counting schedules and bookings).
     */
    public function getAvailableBookingsByOffer($offerId)
    {
        // Get schedules for the offer
        $schedules = Schedule::where('offer_id', $offerId)
            ->get();

        // Get existing bookings for the offer
        $bookings = Booking::where('offer_id', $offerId)
            ->get();

        // Calculate available slots based on schedules and existing bookings
        $availableSlots = [];
        foreach ($schedules as $schedule) {
            $fromHour = Carbon::createFromFormat('H:i', $schedule->from_hour);
            $toHour = Carbon::createFromFormat('H:i', $schedule->to_hour);

            // Generate hourly slots
            $currentHour = $fromHour->copy();
            while ($currentHour->lt($toHour)) {
                $nextHour = $currentHour->copy()->addHour();
                $slotStart = $currentHour->format('H:i');
                $slotEnd = $nextHour->format('H:i');
                $slotRange = $slotStart . ' - ' . $slotEnd;

                // Check if there's a booking that conflicts with this slot
                $isBooked = $bookings->filter(function ($booking) use ($currentHour, $nextHour, $schedule) {
                    $startDate = Carbon::parse($booking->start_date);
                    $endDate = Carbon::parse($booking->end_date);

                    // Check if the booking overlaps with the time slot
                    return $startDate->isSameDay(Carbon::parse($schedule->day_of_week)) &&
                        $startDate->between($currentHour, $nextHour) ||
                        $endDate->between($currentHour, $nextHour);
                })->isNotEmpty();

                // If no bookings exist, this slot is available
                if (!$isBooked) {
                    $availableSlots[] = [
                        'schedule_id' => $schedule->id,
                        'day_of_week' => $schedule->day_of_week,
                        'slot' => $slotRange,
                        'available' => true,
                    ];
                }

                // Move to the next hour
                $currentHour = $nextHour;
            }
        }

        return response()->json([
            'available_slots' => $availableSlots,
        ]);
    }


    /**
     * Get all bookings for the authenticated user.
     */
    public function getMyBookings(Request $request)
    {
        $userId = $request->user()->id;

        $bookings = Booking::where('user_id', $userId)
            ->get();

        return response()->json([
            'my_bookings' => $bookings,
        ]);
    }
}
