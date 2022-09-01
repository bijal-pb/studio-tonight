<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SocialPlatform;
use App\Models\AppVersion;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;
use Auth;
use Hash;
use DB;
use Mail;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Services\StripeService;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Customer;
use Stripe\Token;
use Stripe\Payout;

/**
 * @OA\Info(
 *      description="",
 *     version="1.0.0",
 *      title="Studio Tonight",
 * )
 **/

/**
 *  @OA\SecurityScheme(
 *     securityScheme="bearer_token",
 *         type="http",
 *         scheme="bearer",
 *     ),
 **/
class UserController extends Controller
{

    use ApiTrait;


    public function handleGoogle(Request $request)
    {
        try {
            // $user = Socialite::with('google')->stateless()->user();
            // $user = Socialite::with('google')->userFromToken($request->token);
            $user = Socialite::with('google')->stateless()->userFromToken($request->token);
            if(!$user){
                return $this->response([], "Unauthorized user!", false);
            }
            return $this->handleUser($user, "google", $request);
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false);
        }
    }

    public function handleFacebook(Request $request)
    {
        try {
            $user = Socialite::with('facebook')->stateless()->userFromToken($request->token);
            if(!$user){
                return $this->response([], "Unauthorized user!", false);
            }
            // return $this->response($user);
            return $this->handleUser($user, "facebook", $request);
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false);
        }
    }

    public function handleApple(Request $request)
    {
        try {
            $user = Socialite::with('apple')->stateless()->userFromToken($request->token);
            if(!$user){
                return $this->response([], "Unauthorized user!", false);
            }
            // return $this->response($user);
            return $this->handleUser($user, "mac", $request);
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false);
        }
    }

    private function handleUser($ssoUser, $ssoPlatform,$request)
    {
        try{
            $user = User::where('social_id', $request->social_id)->first();
            $checkUser = User::where('social_id', $request->social_id)->first();
            if (!$user) {
                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->avatar = $request->avatar;
                $user->type = $request->type;
                $user->social_type = $ssoPlatform;
                $user->social_id = $request->social_id;
            }
            $user->lat = $request->lat;
            $user->lang = $request->lang;
            $user->address = $request->address;
            $user->city = $request->city;
            $user->state = $request->state;
            $user->country = $request->country;
            $user->device_type = $request->device_type;
            $user->device_token = $request->device_token;
            $user->firebase_id = $request->firebase_id;
            $user->save();
            if(!$checkUser){
                $user->assignRole([2]);
            }
            $platform = SocialPlatform::where('platform', $ssoPlatform)->where('platform_id', $ssoUser->id)->first();

            if ($platform && $platform->user_id !== $user->id) {
                $platform->delete();
                $platform = false;
            }

            if (!$platform) {
                $platform = new SocialPlatform();
            }

            $platform->user_id = $user->id;
            $platform->platform_id = $ssoUser->id;
            $platform->platform  = $ssoPlatform;
            $platform->avatar =  $ssoUser->avatar;
            $platform->save();

            $user = User::find($user->id);
            /* Common response for login */
            return $this->login($user);
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
    }

    private function login(User $user)
    {
        if($user->status == 2){
            return $this->response([], 'Your account is blocked, please contact administrator!', false, 401);
        }
        $token = $user->createToken('API')->accessToken;

        return $this->response(['token' => $token, "user" => $user]);
    }

    /**
     *  @OA\Get(
     *     path="/api/logout",
     *     tags={"User"},
     *     security={{"bearer_token":{}}},  
     *     summary="Logout",
     *     security={{"bearer_token":{}}},
     *     operationId="Logout",
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
    public function logout()
    {
        try {
            $user = User::find(Auth::id());
            $user->tokens()->delete();
            $user->device_type = null;
            $user->device_token = null;
            $user->save();
            return $this->response('', 'Logout Successfully!');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false,404);
        }
    }

    /**
     *  @OA\Get(
     *     path="/api/profile",
     *     tags={"User"},
     *     security={{"bearer_token":{}}},  
     *     summary="Get Login User Profile",
     *     operationId="profile",
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
    public function me()
    {
        try{
            $user = User::find(Auth::id());
            return $this->response($user, 'Profile!'); 
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false);
        }

    }


    public function interceptLogin(Request $request)
    {

        if (env('APP_ENV') == 'development' || env('APP_ENV') == 'local') {
            $user = User::find($request->id);
            $token = $user->createToken('API')->accessToken;
            return $this->response(['token' => $token, "user" => $user->only(["name", "avatar"])]);
        }
    }

    /**
     *  @OA\Get(
     *     path="/api/notification/enable",
     *     tags={"User"},
     *     security={{"bearer_token":{}}},  
     *     summary="Notification Enable or Disable",
     *     operationId="notification-enable",
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
    public function notificationEnable()
    {
        $user = User::find(Auth::id());
        if($user->is_notification == 1){
            $user->is_notification = 0;
            $msg = "Notification is disabled successfully!";
        }else{
            $user->is_notification = 1;
            $msg = "Notification is enabled successfully!";
        }
        $user->save();
        return $this->response([], $msg); 
    }

    /**
     *  @OA\Get(
     *     path="/api/user/switch",
     *     tags={"User"},
     *     security={{"bearer_token":{}}},  
     *     summary="Switch User",
     *     operationId="switch-user",
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
    public function switchUser()
    {
        $user = User::find(Auth::id());
        if($user->type == 1){
            $user->type = 2;
            $msg = "Switched Customer!";
        }else{
            $user->type = 1;
            $msg = "Switched Studio!";
        }
        $user->save();
        return $this->response([], $msg); 
    }

     /**
     *  @OA\Post(
     *     path="/api/profile/edit/image",
     *     tags={"User"},
     *     security={{"bearer_token":{}}},  
     *     summary="profile-edit",
     *     operationId="profile-edit",
     * 
     *     @OA\Parameter(
     *         name="image",
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
    public function edit_profile_image(Request $request)
    {
        $user = User::find(Auth::id());
        $user->avatar = $request->image;
        $user->save();

        return $this->response($user, 'Avatar updated!'); 
    }

     /**
     *  @OA\Post(
     *     path="/api/change/password",
     *     tags={"User"},
     *     summary="Change Password",
     *     security={{"bearer_token":{}}},
     *     operationId="change-password",
     *
     *     @OA\Parameter(
     *         name="current_password",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="password",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
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
    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false);
        }

        try {
            $user = User::find(Auth::id());
            if ($user) {
                if (Hash::check($request->current_password, $user->password)) {
                    $user->password = bcrypt($request->password);
                    $user->save();
                    return $this->response('', 'Password is updated succesfully.');
                } else {
                    return $this->response([], 'Old password is incorrect.', false, 401);
                }
            }
            return $this->response([], 'Enter Valid user name', false);

        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false);
        }
    }

   /**
     *  @OA\Get(
     *     path="/api/version",
     *     tags={"User"},
     *     summary="Get App Version",
     *     operationId="app-version",
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
    public function appVersion()
    {
        try {
            $appVer = AppVersion::first();
            return $this->response($appVer,'App version detail!');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
    }



    /**
     * @OA\Get(
     *     path="/api/stripe/express/url",
     *     tags={"Stripe"},
     *     security={{"bearer_token":{}}},
     *     summary="get express url",
     *     operationId="stripe-express-url",
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
    public function stripe_express_url(Request $request)
    {

        Stripe::setApiKey(env('STRIPE_SECRATE_KEY'));
        $user = User::find(Auth::id());
        if ($user->stripe_acc_id == null) {
            $account = Account::create([
                'type' => 'express',
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
                'business_type' => 'individual',
            ]);
            $user->stripe_acc_id = $account->id;
            $user->save();
        }

        $link_url = AccountLink::create([
            'account' => $user->stripe_acc_id,
            'refresh_url' => 'http://ingeniousmindslab.com/studio-tonight/public/api/stripe/refresh/'.Auth::id(),
            'return_url' => 'http://ingeniousmindslab.com/studio-tonight/public/api/stripe?status=success',
            'type' => 'account_onboarding',
        ]);

        return $this->response(['url' => $link_url->url], 'Stripe link');
    }

    /**
     * @OA\Get(
     *     path="/api/stripe/account/status",
     *     tags={"Stripe"},
     *     security={{"bearer_token":{}}},
     *     summary="Stripe account status",
     *     operationId="stripe-account-status",
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
    public function retrive_acc(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRATE_KEY'));

        $stripe_acc = Account::retrieve(Auth::user()->stripe_acc_id, []);

        if ($stripe_acc->capabilities->transfers == 'active') {
            $user = User::find(Auth::id());
            $user->stripe_acc_status = 1;
            $user->save();
            return $this->response($user, 'Stripe Active');
        }
        return $this->response($user, 'Pending verfication retry connect account!', false, 404);
    }

    public function stripe_refresh($id)
    {
        Stripe::setApiKey(env('STRIPE_SECRATE_KEY'));
        $user = User::find($id);

        $link_url = AccountLink::create([
            'account' => $user->stripe_acc_id,
            'refresh_url' => 'http://ingeniousmindslab.com/studio-tonight/public/api/stripe/refresh/'.$id,
            'return_url' => 'http://ingeniousmindslab.com/studio-tonight/public/api/stripe?status=success',
            'type' => 'account_onboarding',
        ]);

        return $link_url;
    }

}
