<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'week' => 'nullable|date',
        ]);

        $query = Booking::with(['client', 'user']);

        // If week parameter is provided, filter by week
        if ($request->has('week')) {
            $date = Carbon::parse($request->week);
            $startOfWeek = $date->copy()->startOfWeek(Carbon::MONDAY);
            $endOfWeek = $date->copy()->endOfWeek(Carbon::SUNDAY);

            $query->where(function ($q) use ($startOfWeek, $endOfWeek) {
                $q->whereBetween('start_time', [$startOfWeek, $endOfWeek])
                    ->orWhereBetween('end_time', [$startOfWeek, $endOfWeek])
                    ->orWhere(function ($subQuery) use ($startOfWeek, $endOfWeek) {
                        $subQuery->where('start_time', '<=', $startOfWeek)
                            ->where('end_time', '>=', $endOfWeek);
                    });
            });

            $bookings = $query->orderBy('start_time')->get();

            return response()->json([
                'bookings' => $bookings,
                'week_start' => $startOfWeek->toISOString(),
                'week_end' => $endOfWeek->toISOString(),
            ]);
        }

        // Default: return all bookings
        $bookings = $query->orderBy('start_time', 'desc')->get();
        return response()->json($bookings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request)
    {
        $booking = Booking::create($request->validated());
        $booking->load(['client', 'user']);

        return response()->json($booking, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $booking->load(['client', 'user']);
        return response()->json($booking);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $booking->update($request->validated());
        $booking->load(['client', 'user']);

        return response()->json($booking);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $booking->delete();

        return response()->json(null, 204);
    }
}
