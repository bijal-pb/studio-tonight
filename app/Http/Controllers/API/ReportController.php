<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiTrait;
use App\Models\Report;
use Illuminate\Support\Facades\Validator;
use Exception;
use DB;
use Auth;
use Mail;


class ReportController extends Controller
{
    use ApiTrait;
    /**
     *  @OA\Post(
     *      path="/api/customer/report",
     *     tags={"Customer-Home"},
     *     summary="report",
     *     security={{"bearer_token":{}}},
     *     operationId="customer-report",
     * 
     *     @OA\Parameter(
     *         name="studio_id",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="email",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="subject",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="description",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
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
    public function report(Request $request)
    {
        $validator = Validator::make($request->all(),[
			'studio_id' => 'required|exists:studios,id',
            'email' => 'required',
		]);

		if($validator->fails())
		{
			return $this->response($validator->errors()->first(), false,400);
		}
        try{
            $report = new Report();
            $report->studio_id = $request->studio_id;
            $report->name = $request->name;
            $report->email = $request->email;
            $report->subject = $request->subject;
            $report->description = $request->description;
            $report->save();
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'description' => $request->description,
            ];
            // $setting = Setting::first();
            $admin_email = "noreply.studiotonight@gmail.com";
            Mail::send('mail.reportMail', $data, function ($message) use ($admin_email) {
                $message->to($admin_email, 'Studio-Tonight')->subject
                    ('Report');
            });
            
            return $this->response($report,'Report added successfully!');
        }catch(Exception $e){
			return $this->response([], $e->getMessage(), false, 400);
		}
    }
}
