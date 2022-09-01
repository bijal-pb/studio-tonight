<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenities;
use App\Models\Studio;
use App\Models\Setting;
use App\Models\StudioAmenities;
use App\Models\StudioPhoto;
use App\Models\StudioTiming;
use Illuminate\Http\Request;


class StudioController extends Controller
{
    public function index(Request $request)
    {

        $studios = Studio::select('studios.*', 'users.name as user_name', 'users.email as user_email')
            ->leftJoin('users', 'studios.user_id', '=', 'users.id')
            ->get();
        if ($request->search != null) {
            $studios = $studios->where('title', 'LIKE', '%' . $request->search . '%')
                ->orWhere('price', 'LIKE', '%' . $request->search . '%');
        }
        if ($request->perPage != null) {
            $studios = $studios->paginate($request->perPage);
        } else {
            $studios = Studio::paginate(10);
        }
        if ($request->sortby != null && $request->sorttype) {
            $studios = $studios->orderBy($request->sortby, $request->sorttype);
        } else {
            $studios = Studio::orderBy('id','desc');
        }

        if ($request->ajax()) {
            return response()->json(view('admin.studios.studio_data', compact('studios'))->render());
        }

        return view('admin.studios.list', compact(['studios']));
    }

    public function studio(Request $request)
    {

        $columns = array(
            0 => 'id',
            1 => 'title',
            2 => 'address',
            3 => 'user_name',
            4 => 'created_at',
            5 => 'rating',
            6 => 'action',
            7 => 'active',
        );

        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $studios = Studio::select('studios.*', 'users.name as user_name')
            ->leftJoin('users', 'studios.user_id', '=', 'users.id');
        if ($request->search['value'] != null) {
            $studios = $studios->where('title', 'LIKE', '%' . $request->search['value'] . '%')
            ->orWhere('users.name', 'LIKE', '%' . $request->search['value'] . '%')
            ->orWhere('studios.address', 'LIKE', '%' . $request->search['value'] . '%')
            ->orWhere('studios.created_at','LIKE','%'.$request->search['value'].'%')
            ->orWhere('studios.id','LIKE','%'.$request->search['value'].'%');
        }

        if ($request->length != '-1') {
            $studios = $studios->take($request->length);
        } else {
            $studios = $studios->take(Studio::count());
        }
        $studios = $studios->skip($request->start)
            ->orderBy($order, $dir)
            ->get();
        $data = array();
        if (!empty($studios)) {
            foreach ($studios as $studio) {
                $url = route('admin.studio.get', ['id' => $studio->id]);
                $statusUrl = route('admin.studio.status.change', ['studio_id' => $studio->id]);
                $checked = $studio->is_active == 1 ? 'checked' : '';
                $nestedData['id'] = $studio->id;
                $nestedData['title'] = $studio->title;
                $nestedData['address'] = $studio->address;
                $nestedData['user_id'] = $studio->user_name;
                $nestedData['created_at'] = date("d-m-Y", strtotime($studio->created_at));
                if($studio->rating != null){
                    $ratingRem = 5 - (int)$studio->rating;
                }
                $nestedData['rating'] = $studio->rating == null ? str_repeat('<i style="color:orange;" class="fal fa-star" aria-hidden="true"></i>',5) : str_repeat('<i style="color:orange;" class="fa fa-star checked" aria-hidden="true"></i>',$studio->rating).str_repeat('<i style="color:orange;" class="fal fa-star" aria-hidden="true"></i>',$ratingRem)  ;
                $nestedData['action'] = "<a href='$url' class='detail-booking btn btn-outline-warning btn-sm btn-icon'>
                                         <i class='fal fa-eye'></i>
                                          </a>";
                $nestedData['active'] = "<div class='custom-control custom-switch'><input type='radio' class='custom-control-input active' data-url='$statusUrl' id='active$studio->id' name='active$studio->id' $checked><label class='custom-control-label' for='active$studio->id'></label>
                                      </div>";                          
                $data[] = $nestedData;
            }
        }
        return response()->json([
            'draw' => $request->draw,
            'data' => $data,
            'recordsTotal' => Studio::all()
                ->count(),
            'recordsFiltered' => $request->search['value'] != null ? $studio = Studio::all()->count() : $studio = Studio::all()
                ->count(),
        ]);
    }
    public function getStudio($id)
    {
       
        $studio = Studio::with(['studio_type', 'studio_amenities', 'amenities', 'timing', 'studio_photos', 'studioUser', 'verification_image','studio_booking'])->find($id);
        //  return $studio;
        return view('admin.studios.detail', compact('studio'));
    }

    public function changeStatus(Request $request)
    {
        $studio = Studio::find($request->studio_id);
        if ($studio->is_active == 1) {
            $message = "Studio is deactivated successfully!";
            $studio->is_active = 0;
        } else {
           $studio->is_active = 1;
           $message = "Studio is activated successfully!";
        }
        $studio->save();
        return response()->json(['status' => 'success', 'message' => $message]);
    }
}
