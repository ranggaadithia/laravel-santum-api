<?php

namespace App\Http\Controllers;

use App\Models\BookingRoom;
use App\Models\Room;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class BookingRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $bookingRooms = BookingRoom::with('user', 'room')->get();

            if ($bookingRooms->isEmpty())
                return response()->json([
                    'status' => 'success',
                    'message' => 'No booking rooms found',
                    'data' => []
                ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Booking rooms data fetched successfully',
                'data' => $bookingRooms,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $room = Room::find($request->room_id);

            if ($room == null)
                return response()->json([
                    'status' => 'error',
                    'message' => 'Room not found',
                    'data' => []
                ], 404);

            $validator = Validator::make($request->all(), [
                'room_id' => 'required|exists:rooms,id',
                'booking_date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'reason_for_booking' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->all(),
                    'data' => []
                ], 422);
            }

            if (BookingRoom::isBookingConflict($request->room_id, $request->booking_date, $request->start_time, $request->end_time)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking conflict',
                    'data' => []
                ], 409);
            }

            $bookingRoom = BookingRoom::create([
                'user_id' => auth()->user()->id,
                'room_id' => $room->id,
                'booking_date' => $request->booking_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'reason_for_booking' => $request->reason_for_booking
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Booking room created successfully',
                'data' => $bookingRoom->with('user', 'room')->first()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $bookingRoom = BookingRoom::with('user', 'room')->find($id);

            if ($bookingRoom == null)
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking room not found',
                    'data' => []
                ], 404);

            return response()->json([
                'status' => 'success',
                'message' => 'Booking room data fetched successfully',
                'data' => $bookingRoom
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $bookingRoom = BookingRoom::find($id);

            if ($bookingRoom == null)
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking room not found',
                    'data' => []
                ], 404);

            $validator = Validator::make($request->all(), [
                'booking_date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'reason_for_booking' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->all(),
                    'data' => []
                ], 422);
            }

            if (BookingRoom::isBookingConflict($bookingRoom->room_id, $request->booking_date, $request->start_time, $request->end_time)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking conflict',
                    'data' => []
                ], 409);
            }

            $bookingRoom->update([
                'booking_date' => $request->booking_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'reason_for_booking' => $request->reason_for_booking
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Booking room updated successfully',
                'data' => $bookingRoom->with('user', 'room')->first()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $bookingRoom = BookingRoom::find($id);

            if ($bookingRoom == null)
                return response()->json([
                    'status' => 'error',
                    'message' => 'Booking room not found',
                    'data' => []
                ], 404);

            $bookingRoom->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Booking room deleted successfully',
                'data' => []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
    }
}
