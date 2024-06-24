<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $rooms = Room::all();

            if ($rooms->isEmpty())
                return response()->json([
                    'status' => 'success',
                    'message' => 'No rooms found',
                    'data' => []
                ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Rooms data fetched successfully',
                'data' => $rooms,
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
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'size' => 'required',
                'capacity' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->all(),
                    'data' => []
                ], 422);
            }

            $room = Room::create($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Room created successfully',
                'data' => $room
            ], 201);
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
            $room = Room::find($id);

            if (!$room)
                return response()->json([
                    'status' => 'error',
                    'message' => 'Room not found',
                    'data' => []
                ], 404);

            return response()->json([
                'status' => 'success',
                'message' => 'Room data fetched successfully',
                'data' => $room
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
            $room = Room::find($id);

            if (!$room)
                return response()->json([
                    'status' => 'error',
                    'message' => 'Room not found',
                    'data' => []
                ], 404);

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'size' => 'required',
                'capacity' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->all(),
                    'data' => []
                ], 422);
            }

            $room->update($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Room updated successfully',
                'data' => $room
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
            $room = Room::find($id);

            if (!$room)
                return response()->json([
                    'status' => 'error',
                    'message' => 'Room not found',
                    'data' => []
                ], 404);

            $room->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Room deleted successfully',
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
