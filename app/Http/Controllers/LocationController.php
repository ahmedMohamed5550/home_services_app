<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Exception;
use Illuminate\Http\Request;

class LocationController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/location/store",
     *     summary="Add location to user",
     *     tags={"location"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="city",
     *                     type="string",
     *                     description="City name"
     *                 ),
     *                 @OA\Property(
     *                     property="bitTitle",
     *                     type="string",
     *                     description="Title or nickname for the location"
     *                 ),
     *                 @OA\Property(
     *                     property="street",
     *                     type="string",
     *                     description="Street name"
     *                 ),
     *                 @OA\Property(
     *                     property="specialMarque",
     *                     type="string",
     *                     description="Special landmark near the location"
     *                 ),
     *                 @OA\Property(
     *                     property="lat",
     *                     type="number",
     *                     format="float",
     *                     description="Latitude coordinate"
     *                 ),
     *                 @OA\Property(
     *                     property="long",
     *                     type="number",
     *                     format="float",
     *                     description="Longitude coordinate"
     *                 ),
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer",
     *                     description="User ID"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Location added successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Added Location successfully"),
     *             @OA\Property(property="location", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */


     public function store(Request $request){
        try{
            $request->validate([
                'city' => 'nullable|string|max:255',
                'bitTitle' => 'nullable|string|max:255',
                'street' => 'nullable|string|max:255',
                'specialMarque' => 'required|string|max:255',
                'lat' => 'nullable|numeric',
                'long' => 'nullable|numeric',
                'user_id' => 'required|exists:users,id',
            ]);

            $location = new Location();
            $location->city = $request->city;
            $location->bitTitle = $request->bitTitle;
            $location->street = $request->street;
            $location->specialMarque = $request->specialMarque;
            $location->lat = $request->lat;
            $location->long = $request->long;
            $location->user_id = $request->user_id;
            $location->save();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Added Location successfully',
                    'location' => $location,
                ],
                201
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'An error occurred',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }



    /**
     * @OA\Get(
     *     path="/api/location/showUsersLocation/{user_id}",
     *     summary="Show all locations",
     *     description="Show all locations for a user by user ID",
     *     tags={"location"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="ID of the user to show all locations",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Show locations successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="locations", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No locations found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No locations found")
     *         )
     *     )
     * )
     */

    public function showUsersLocation($id){
        $location = Location::where('user_id',$id)->get();
        if($location ->count() != 0){
            foreach($location as $locations){
                $locations->user;
            }
            return response()->json([
                'status' => 'true',
                'locations' => $location,
            ],200);
        }

        else{
            return response()->json([
                'status' => false,
                'message' => 'no location found',
            ],401);
        }

    }


    /**
     * @OA\Post(
     *     path="/api/location/update/{id}",
     *     summary="Update user location",
     *     tags={"location"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the location",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="city",
     *                     type="string",
     *                     description="City name"
     *                 ),
     *                 @OA\Property(
     *                     property="bitTitle",
     *                     type="string",
     *                     description="Title or nickname for the location"
     *                 ),
     *                 @OA\Property(
     *                     property="street",
     *                     type="string",
     *                     description="Street name"
     *                 ),
     *                 @OA\Property(
     *                     property="specialMarque",
     *                     type="string",
     *                     description="Special landmark near the location"
     *                 ),
     *                 @OA\Property(
     *                     property="lat",
     *                     type="number",
     *                     format="float",
     *                     description="Latitude coordinate"
     *                 ),
     *                 @OA\Property(
     *                     property="long",
     *                     type="number",
     *                     format="float",
     *                     description="Longitude coordinate"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Location updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Updated Location successfully"),
     *             @OA\Property(property="location", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Location not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Location not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation errors"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */


 public function update(Request $request, $id){
    $request->validate([
        'city' => 'required|string|max:255',
        'bitTitle' => 'required|string|max:255',
        'street' => 'required|string|max:255',
        'specialMarque' => 'nullable|string|max:255',
        'lat' => 'nullable|numeric',
        'long' => 'nullable|numeric',
    ]);

    try{
        $location = Location::find($id);

        if ($location) {
            $location->city = $request->city;
            $location->bitTitle = $request->bitTitle;
            $location->street = $request->street;
            $location->specialMarque = $request->specialMarque;
            $location->lat = $request->lat;
            $location->long = $request->long;
            $location->update();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Updated Location successfully',
                    'location' => $location,
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Location not found',
                ],
                404
            );
        }
    } catch (Exception $e) {
        return response()->json(
            [
                'status' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ],
            500
        );
    }
}




    /**
     * @OA\Delete(
     *     path="/api/location/destroy/{id}",
     *     summary="Delete a location",
     *     description="Delete location for a user by location ID",
     *     tags={"location"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the location to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Delete Location successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Location not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Location not found")
     *         )
     *     )
     * )
     */

    public function destroy($id){
        $location=Location::find($id);

        if($location){
            $location->delete();
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Delete Location successfully',
                ],
                200
            );
        }
        else{
            return response()->json(
                [
                    'status' => false,
                    'message' => 'location not found',
                ],
                401
            );
        }
    }
}
