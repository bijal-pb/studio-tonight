<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Studio;
use App\Models\Setting;
use App\Models\AppVersion;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Session;
use Hash;
use Mail;
use Laravel\Socialite\Facades\Socialite;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['user']);
        });

        if ($request->search != null) {
            $users = $users->where('first_name', 'LIKE', '%' . $request->search . '%')
                ->orWhere('last_name', 'LIKE', '%' . $request->search . '%')
                ->orWhere('email', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->sortby != null && $request->sorttype) {
            $users = $users->orderBy($request->sortby, $request->sorttype);
        } else {
            $users = $users->orderBy('id', 'desc');
        }
        if ($request->perPage != null) {
            $users = $users->paginate($request->perPage);
        } else {
            $users = $users->paginate(10);
        }
        if ($request->ajax()) {
            return response()->json(view('admin.users.user_data', compact('users'))->render());
        }
        return view('admin.users.list', compact(['users']));
    }

    public function users(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'profile',
            2 => 'name',
            3 => 'email',
            4 => 'status',
            5 => 'active',
        );
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $totalUser = User::whereHas('roles', function ($q) {
                        $q->whereIn('name', ['user']);
                    });
        $users = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['user']);
        });
        if($request->type != null){
            $users = $users->where('type',$request->type);
            $totalUser = $totalUser->where('type',$request->type);
        }
        if ($request->search['value'] != null) {
            $users = $users->where('name', 'LIKE', '%' . $request->search['value'] . '%')
                ->orWhere('email', 'LIKE', '%' . $request->search['value'] . '%')
                ->orWhere('id', 'LIKE', '%' . $request->search['value'] . '%');

            $totalUser = $totalUser->where('name', 'LIKE', '%' . $request->search['value'] . '%')
                ->orWhere('email', 'LIKE', '%' . $request->search['value'] . '%')
                ->orWhere('id', 'LIKE', '%' . $request->search['value'] . '%');
        }
        if ($request->length != '-1') {
            $users = $users->take($request->length);
        } else {
            $users = $users->take(User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['user']);
            })->count());
        }
        $users = $users->skip($request->start)
            ->orderBy($order, $dir)
            ->get();
        $data = array();
        if (!empty($users)) {
            foreach ($users as $user) {
                $url = route('admin.user.get', ['user_id' => $user->id]);
                $statusUrl = route('admin.user.status.change', ['user_id' => $user->id]);
                $checked = $user->status == 1 ? 'checked' : '';
                $nestedData['id'] = $user->id;
                $nestedData['name'] = $user->name;
                $nestedData['email'] = $user->email;
                if(!empty($user->avatar)){
                    $nestedData['avatar'] = "<img class='profile-image rounded-circle d-inline-block' src='$user->avatar' width='100'>";    
                }else{
                    $nestedData['avatar'] = "<img class='profile-image rounded-circle d-inline-block' src='".asset('user/logo.png')."' width='100'>";
                }
                $nestedData['status'] = $user->type == 1 ?  "<span class='badge badge-primary'>Studio</span>" : "<span class='badge badge-danger'>Customer</span>";
                $nestedData['active'] = "<div class='custom-control custom-switch'>
                                            <input type='radio' class='custom-control-input active' data-url='$statusUrl' data-status='$user->status' id='active$user->id' name='active$user->id' $checked>
                                            <label class='custom-control-label' for='active$user->id'></label>
                                        </div>";
                $data[] = $nestedData;
            }
        }
        return response()->json([
            'draw' => $request->draw,
            'data' => $data,
            'recordsTotal' => User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['user']);
            })->count(),
            'recordsFiltered' => $request->search['value'] != null || $request->type != null ? $totalUser->count() : $users = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['user']);
            })->count(),
        ]);
    }
    public function getUser(Request $request)
    {
        $user = User::find($request->user_id);
        return response()->json(['data' => $user]);
    }
    public function changeStatus(Request $request)
    {
        $user = User::find($request->user_id);
        if ($user->status == 1) {
            $user->status = 2;
            $message = "User deactivated successfully!";
        } else {
            $user->status = 1;
            $message = "User activated successfully!";
        }
        $user->save();

        if($user->status == 2){
            $studios = Studio::where('user_id', $user->id)->get();
            foreach($studios as $studio){
                $studio->is_active = false;
                $studio->save();
            }
        }

        if($user->status == 1){
            $studios = Studio::where('user_id', $user->id)->get();
            foreach($studios as $studio){
                $studio->is_active = true;
                $studio->save();
            }
        }
        
        return response()->json(['status' => 'success', 'message' => $message]);
    }
    public function store(Request $request)
    {
        if ($request->user_id != null) {
            $user = User::find($request->user_id);
        } else {
            $user = new User;
        }
        $user->name = $request->first_name;
        $user->status = $request->status;
        $user->save();
        return response()->json(['status' => 'success']);
    }
    public function profile()
    {
        $user = User::find(Auth::id());
        return view('admin.users.profile', compact('user'));
    }
    public function update_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'avatar' => 'nullable|image|mimes:png,jpeg,jpg,svg,ico|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }
        try {
            $user = User::find(Auth::id());
            $user->name = $request->name;
            $user->email = $request->email;
            if ($request->hasfile('avatar')) {
                $file = $request->file('avatar');
                $filename = time() . $file->getClientOriginalName();
                $file->move(public_path() . '/user/', $filename);
                $user->avatar = asset('/user/' . $filename);
            }
            $user->save();
            return response()->json(['status' => 'success', 'data' => $user]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function password()
    {
        return view('admin.users.password');
    }

    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current' => 'required|max:255',
            'password' => 'required|max:255|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        try {
            $user = User::find(Auth::id());
            if ($user) {
                if (Hash::check($request->current, $user->password)) {
                    $user->password =  bcrypt($request->password);
                    $user->save();
                    return response()->json(['status' => 'success']);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Enter valid current password!']);
                }
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function admin_login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if ($user->roles[0]->name != 'Admin' && $user->roles[0]->name != 'Developer') {
                return response()->json(['status' => 'error', 'message' => 'Enter valid email or password!']);
            }
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                return response()->json(['status' => 'success']);
            }
        }

        return response()->json(['status' => 'error', 'message' => 'Enter valid email or password!']);
    }

    public function app_setting()
    {
        $setting = Setting::latest()->first();
        return view('admin.setting', compact('setting'));
    }

    public function setting_update(Request $request)
    {
        try {
            $setting = Setting::latest()->first();
            $setting->name = $request->name;
            $setting->url = $request->url;
            $setting->push_token = $request->push_token;
            $setting->api_log = $request->api_log;
            $setting->host = $request->host;
            $setting->port = $request->port;
            $setting->email = $request->email;
            $setting->password = $request->password;
            $setting->from_address = $request->from_address;
            $setting->from_name = $request->from_name;
            $setting->encryption = $request->encryption;
            $setting->save();
            return response()->json(['status' => 'success', 'data' => $setting]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function app_version(){
        $AppVersion = AppVersion::latest()->first();
        return view('admin.appversion',compact('AppVersion'));
    }
    public function version_update(Request $request)
    {
        try {
            $AppVersion = AppVersion::latest()->first();
            $AppVersion->ios = $request->ios;
            $AppVersion->android = $request->android;
            $AppVersion->forcefully = $request->forcefully;
            $AppVersion->save();
            return response()->json(['status' => 'success', 'data' => $AppVersion]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    

    public function forgot_password()
    {
        return view('auth.forgot');
    }

    public function password_mail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        $user = User::where('email', $request->email)->first();
        if (empty($user)) {
            return response()->json(['status' => 'error', 'message' => 'This email not registered']);
        }

        try {
            $newPass = substr(md5(time()), 0, 10);
            $user->password = bcrypt($newPass);
            $user->save();
            $data = [
                'username' => $user->user_name,
                'password' => $newPass
            ];
            $email = $user->email;
            Mail::send('mail.forgot', $data, function ($message) use ($email) {
                $message->to($email, 'test')->subject('Forgot Password');
            });
            return response()->json(['status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function googleLogin()
    {
        return Socialite::driver('google')->redirect();
    }

    public function facebookLogin()
    {
        return Socialite::driver('facebook')->redirect();
    }
}
