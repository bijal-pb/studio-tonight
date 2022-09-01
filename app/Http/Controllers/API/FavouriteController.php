<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Studio;
use App\Models\StudioFavourite;
use App\Traits\ApiTrait;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavouriteController extends Controller
{
    use ApiTrait;

    /**
     *  @OA\Get(
     *      path="/api/customer/favourite/list",
     *     tags={"Favourite"},
     *     summary="favourite studio list",
     *     security={{"bearer_token":{}}},
     *     operationId="favourite-list",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
     **/
    public function favouriteStudioList(Request $request)
    {
        try {
            $studFavs = StudioFavourite::with('studio')->where('customer_id', Auth::id())->get();
            return $this->response($studFavs, 'Favourite  Studio List!');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }
    }

    /**
     *  @OA\Post(
     *      path="/api/studio/favourite/add",
     *     tags={"Favourite"},
     *     summary="add remove studio favourite list",
     *     security={{"bearer_token":{}}},
     *     operationId="studio-favourite-add",
     *
     *     @OA\Parameter(
     *         name="studio_id",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="1-Add favourite | 2- Remove favourite",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
     **/
    public function favouriteAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'studio_id' => 'required|exists:studios,id',
            'type' => 'required|in:1,2',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 404);
        }
        try {
            $studFav = StudioFavourite::where('studio_id', $request->studio_id)->where('customer_id', Auth::id())->first();
            if ($request->type == 1) {
                if (!isset($studFav)) {
                    $studFav = new StudioFavourite;
                    $studFav->customer_id = Auth::id();
                    $studFav->studio_id = $request->studio_id;
                    $studFav->save();
                    return $this->response([], 'Favourite added successfully!');
                }
            }
            if ($request->type == 2) {
                if (isset($studFav)) {
                    $studFav->delete();
                }
                return $this->response('', 'Favourite removed successfully!');
            }
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }
    }
}
