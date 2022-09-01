<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Amenities;
use Illuminate\Http\Request;
use App\Traits\ApiTrait;

class AmenitiesController extends Controller
{
    use ApiTrait;

    /**
     *  @OA\Get(
     *     path="/api/amenities",
     *     tags={"Amenities"},
     *     summary="Get Amenities List",
     *     operationId="List-amenities",
     * 
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
    public function amenitiesList()
    {
        $amenities = Amenities::select('id','name')->get();
        return $this->response($amenities, 'Amenities!'); 
    }
}
