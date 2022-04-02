<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class PageController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $pages = Page::paginate(10);

        return view('Admin.pages.index', [
            'pages' => $pages,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('Admin.pages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->only([
            'title',
            'body'
        ]);

        $data['slug'] = Str::slug($data['title'], '-');

        $validator = Validator::make($data, [
            'title' => ['required', 'string', 'max:100'],
            'body' => ['string'],
            'slug' => ['required', 'string', 'max:100', 'unique:pages']
        ]);

        if ($validator->fails()) {
            return \redirect()->route('pages.create')
                ->withErrors($validator)
                ->withInput();
        }

        $page = new Page;
        $page->title = $data['title'];
        $page->slug = $data['slug'];
        $page->body = $data['body'];
        $page->save();


        return redirect()->route('pages.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $page = Page::find($id);

        if ($page) {
            return view('Admin/pages/edit', [
                'page' => $page
            ]);
        }

        return redirect()->route('pages.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @param $user
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $page = Page::find($id);

        if ($page) {
            $data = $request->only([
                'title',
                'body',
            ]);

            if($page['title'] !== $data['title']) {
                $data['slug'] = Str::slug($data['title'], '-');

                $validator = Validator::make($data, [
                    'title' => ['required', 'string', 'max:100'],
                    'body' => ['string'],
                    'slug' => ['required', 'string', 'max:100', 'unique:pages']
                ]);
            } else {
                $validator = Validator::make($data, [
                    'title' => ['required', 'string', 'max:100'],
                    'body' => ['string']
                ]);
            }


            if ($validator->fails()) {
                return redirect()->route('pages.edit', [
                    'page' => $id
                ])
                    ->withErrors($validator)
                    ->withInput();
            }

            $page->title = $data['title'];
            $page->body = $data['body'];

            if(!empty($data['slug'])) {
                $page->slug = $data['slug'];
            }

            $page->save();
        }

        return redirect()->route('pages.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {

            $page = Page::find($id);
            $page->delete();

         return redirect()->route('pages.index');
    }
}
