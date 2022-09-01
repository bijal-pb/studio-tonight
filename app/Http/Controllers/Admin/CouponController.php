<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $coupons = Coupon::query();
        if($request->search != null){
            $coupons = $coupons->where('code','LIKE','%'.$request->search.'%');
        }
        if($request->sortby!= null && $request->sorttype)
        {
            $coupons = $coupons->orderBy($request->sortby,$request->sorttype);
        }else{
            $coupons= $coupons->orderBy('id','desc');
        }
        
        if($request->perPage != null){
            $coupons= $coupons->paginate($request->perPage);
        }else{
            $coupons= $coupons->paginate(10);
        }
        if($request->ajax())
        {
            return response()->json( view('admin.coupon.coupon_data', compact('coupons'))->render());
        }
        return view('admin.coupon.index' , compact('coupons'));
    }

    public function coupons(Request $request)
    {
        $columns = array( 
            0 =>'id', 
            1 =>'code',
            2 =>'total_users',
            3 =>'apply_users',
            4 =>'expiry_date',
            5 =>'type',
            6 =>'type_value',
            7 =>'action',
        );  
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $coupons = Coupon::query();
        if($request->search['value'] != null){
            $coupons = $coupons->where('code','LIKE','%'.$request->search['value'].'%')
                               ->orWhere('total_users','LIKE','%'.$request->search['value'].'%')
                               ->orWhere('apply_users','LIKE','%'.$request->search['value'].'%')
                               ->orWhere('expiry_date','LIKE','%'.$request->search['value'].'%')
                               ->orWhere('type','LIKE','%'.$request->search['value'].'%')
                               ->orWhere('type_value','LIKE','%'.$request->search['value'].'%')
                               ->orWhere('id','LIKE','%'.$request->search['value'].'%');
                           
        }
        if($request->length != '-1')
        {
            $coupons = $coupons->take($request->length);
        }else{
            $coupons = $coupons->take(Coupon::count());
        }
        $coupons = $coupons->skip($request->start)
                        ->orderBy($order,$dir)
                        ->get();
       
        $data = array();
        if(!empty($coupons))
        {
            foreach ($coupons as $coupon)
            {
                $url = route('admin.coupon.get', ['coupon_id' => $coupon->id]);
                $urls = route('admin.coupon.delete', ['coupon_id' => $coupon->id]);
                $nestedData['id'] = $coupon->id;
                $nestedData['code'] = $coupon->code;
                $nestedData['total_users'] = $coupon->total_users;
                $nestedData['apply_users'] = $coupon->apply_users;
                $nestedData['expiry_date'] = $coupon->expiry_date;
                $nestedData['type'] = $coupon->type;
                $nestedData['type_value'] = $coupon->type == 'flat' ? '$ '.$coupon->type_value : $coupon->type_value.' %';
                $nestedData['action'] =  "<td>
                                             <button class='edit-cat btn btn-outline-warning btn-sm btn-icon' data-url=' $url '><i class='fal fa-pencil'></i></button>
                                             <button class='delete-cat btn btn-outline-danger btn-sm btn-icon'  data-url=' $urls '><i class='fal fa-trash'></i></button>
                                         </td>";
                $data[] = $nestedData;

            }
        }
        return response()->json([
            'draw' => $request->draw,
            'data' =>$data,
            'recordsTotal' => Coupon::count(),
            'recordsFiltered' => $request->search['value'] != null ? $coupons->count() : Coupon::count(),
        ]);
    }

    public function getCoupon(Request $request){
        $cou = Coupon::find($request->coupon_id);
        return response()->json(['data'=>$cou]);
    }
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'code' => ['required',
            Rule::unique('coupons')->ignore($request->coupon_id), ],
            'total_users' => 'required|numeric|min:0|not_in:0',
            'expiry_date' => 'required',
            'type' => 'required',
            'type_value' => 'required'
		]);

		if($validator->fails())
		{
            return response()->json(['status'=>'error','message' => $validator->errors()->first()]);
        }

        if($request->coupon_id != null)
        {
            $coupons = Coupon::find($request->coupon_id);
        }else{
            $coupons = new Coupon;
        }
        $coupons->code = $request->code;
        $coupons->total_users = $request->total_users;
        $coupons->expiry_date = date("Y-m-d", strtotime($request->expiry_date));
        $coupons->type = $request->type;
        $coupons->type_value = $request->type_value;
        $coupons->save();
        return response()->json(['status'=>'success']);
        
    }

    public function delete(Request $request)
    {
        $coupons = Coupon::find($request->coupon_id);
        $coupons->delete();
        return response()->json(['status'=>'success']);
    }

}
