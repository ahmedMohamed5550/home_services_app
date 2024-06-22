<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SponsorController extends Controller
{

    /**
     * @OA\Get(
     * path="/api/sponsor",
     * summary="show all sponsors",
     * tags={"Sponsors"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(response="200", description="Success"),
     * )
     */

    public function index()
    {
        $sponsor = Sponsor::all();
        if($sponsor->count() != 0){
            return response()->json([
                'status' => true,
                'sponsor' => $sponsor
            ],200);
        }
        else{
            return response()->json([
                'status' => false,
                'message' => "not sponsor found to show it"
            ],401);
        }

    }

    /**
     * @OA\Post(
     *     path="/api/sponsor/",
     *     summary="create a new sponsor",
     *     tags={"Sponsors"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     description="title of sponsors"
     *                 ),
     *                 @OA\Property(
     *                     property="desc",
     *                     type="string",
     *                     description="sponsor description"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="service image"
     *                 ),
     *                 @OA\Property(
     *                     property="expired_at",
     *                     type="date",
     *                     description="what is the sponsor expired at"
     *                 ),
    *     @OA\Property(
    *         property="comment",
    *         type="string",
    *         description="to access image use https://mahllola.online/public/image  example : https://mahllola.online/public/storage/services_folder/ttyVNuauz67kqXX40jyewMwh3DWpdFjjyJ0pIiPd.jpg"
    *     )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="add new sponsor successfully"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors"
     *     )
     * )
     */

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'desc' => 'required|string',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
            'expired_at' => 'required|date',
        ]);

        // Store the image in the 'services_images' directory on the 'public' disk
        $newImage = $request->file('image')->store('sponsor_folder','public');

        // Get the URL to the stored image
        $imageUrl = Storage::url($newImage);

        // store copy of image in public
        Artisan::call('storage:link');

        $sponsor=Sponsor::create([
            'title' => $request->title,
            'desc' => $request->desc,
            'image' =>$imageUrl,
            'expired_at' => $request->expired_at,
        ]);

        return response()->json([
            'status' => true,
            'sponsor' => $sponsor
        ],200);
    }


    /**
     * @OA\Get(
     *     path="/api/sponsor/{id}",
     *     summary="Get sponsor by id",
     *     tags={"Sponsors"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="sponsor id",
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *     ),
     *
     * )
     */

    public function show($id)
    {
        $sponsor = Sponsor::where('id',$id)->get();
        if($sponsor->count() != 0){
            return response()->json([
                'status' => true,
                'sponsor' => $sponsor
            ],200);
        }
        else{
            return response()->json([
                'status' => false,
                'message' => "not found"
            ],401);
        }
    }


    /**
     * @OA\Delete(
     *     path="/api/sponsor/{id}",
     *     summary="Delete an sponsor",
     *     description="Delete service by ID",
     *     tags={"Sponsors"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the sponsor to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="sponsor deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="sponsor not found"
     *     )
     * )
     */

    public function destroy($id)
    {
        $sponsor = Sponsor::find($id);
        if($sponsor){
            $sponsor->delete();
            return response()->json([
                'status' => true,
                'message' => "deleted succesfully"
            ],200);
        }
        else{
            return response()->json([
                'status' => false,
                'message' => "not found"
            ],401);
        }

    }
}
