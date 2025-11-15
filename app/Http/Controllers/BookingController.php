<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Carbon\CarbonImmutable;
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
            $date = CarbonImmutable::parse($request->week);
            $startOfWeek = $date->startOfWeek(CarbonImmutable::MONDAY);
            $endOfWeek = $date->endOfWeek(CarbonImmutable::SUNDAY);

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
                'bookings' => BookingResource::collection($bookings),
                'week_start' => $startOfWeek->toISOString(),
                'week_end' => $endOfWeek->toISOString(),
            ]);
        }

        // Default: return all bookings
        $bookings = $query->orderBy('start_time', 'desc')->get();
        return BookingResource::collection($bookings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request)
    {
        $booking = Booking::create($request->validated());
        $booking->load(['client', 'user']);

        return (new BookingResource($booking))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        // Allow viewing - in a real app, you might want role-based permissions
        $booking->load(['client', 'user']);
        return new BookingResource($booking);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $booking->update($request->validated());
        $booking->load(['client', 'user']);

        return new BookingResource($booking);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        // Allow deletion - in a real app, you might want role-based permissions
        // For now, any authenticated user can delete any booking
        $booking->delete();

        return response()->json(null, 204);
    }
}
