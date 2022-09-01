<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class PageController extends Controller
{
    public function index(Request $request)
    {

        $pages = Page::select('id', 'name', 'slug', 'page')->get();
        if ($request->search != null) {
            $pages = $pages->where('name', 'LIKE', '%' . $request->search . '%');
        }
        if ($request->perPage != null) {
            $pages = $pages->paginate($request->perPage);
        } else {
            $pages = Page::paginate(10);
        }
        if ($request->sortby != null && $request->sorttype) {
            $pages = $pages->orderBy($request->sortby, $request->sorttype);
        } else {
            $pages = Page::orderBy('id', 'desc');
        }

        if ($request->ajax()) {
            return response()->json(view('admin.pages.page_data', compact('$pages'))->render());
        }

        return view('admin.pages.index', compact(['pages']));
    }

    public function page(Request $request)
    {

        $columns = array(
            0 => 'id',
            1 => 'name',
            2 => 'page',
            3 => 'action',
        );

        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $pages = Page::select('id', 'name', 'page', 'slug');
        if ($request->search['value'] != null) {
            $pages = $pages->where('name', 'LIKE', '%' . $request->search['value'] . '%')
                            ->orWhere('id','LIKE','%'.$request->search['value'].'%');
        }

        if ($request->length != '-1') {
            $pages = $pages->take($request->length);
        } else {
            $pages = $pages->take(Page::count());
        }
        $pages = $pages->skip($request->start)
            ->orderBy($order, $dir)
            ->get();

        $data = array();
        
        if (!empty($pages)) {
            foreach ($pages as $page) {
                $url = route('admin.page.edit', ['page_id' => $page->id]);
                $pageUrl = route('page.show',['slug' => $page->slug]);
                $nestedData['id'] = $page->id;
                $nestedData['name'] = $page->name;
                $nestedData['page'] = "<a href='$pageUrl' class='btn btn-outline-success btn-sm btn-icon waves-effect waves-themed'>
                                         <i class='fal fa-eye'></i>
                                          </a>";
                $nestedData['action'] = "<a href='$url' class='btn btn-outline-warning btn-sm btn-icon waves-effect waves-themed'>
                                          <i class='fal fa-edit'></i>
                                           </a>";
                $data[] = $nestedData;
            }
        }
        return response()->json([
            'draw' => $request->draw,
            'data' => $data,
            'recordsTotal' => Page::all()
                ->count(),
            'recordsFiltered' => $request->search['value'] != null ? $page = Page::all()->count() : $page = Page::all()
                ->count(),
        ]);
    }

    public function show($slug)
    {
        $page = Page::whereSlug($slug)->first();
        if($page){
            return view('admin.pages.show',compact('page'));
        }
        
    }
    public function edit(Request $request, $page_id){
        $pages = Page::find($request->page_id);
        return view('admin.pages.edit',compact('pages'));
    }

    public function save(Request $request, $page_id){
        $pages = Page::find($request->page_id);
        $pages->name = $request->name;
        $pages->page = $request->description;
        $pages->save();  
         return response()->json('success' );

    }

}
