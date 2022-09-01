<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Type;
use Illuminate\Http\Request;
use App\Traits\ApiTrait;


class TypeController extends Controller
{
    use ApiTrait;

    /**
     *  @OA\Get(
     *     path="/api/types",
     *     tags={"Types"},
     *     summary="Get Types List",
     *     operationId="List-types",
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
    public function typeList()
    {
        $types = Type::select('id','name','image')->get();
        return $this->response($types, 'Types!'); 
    }
}
