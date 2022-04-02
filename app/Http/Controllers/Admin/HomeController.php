<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Visitor;
use App\User;
use App\Page;

class HomeController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {
        $visitsCount = 0;
        $onlineCount = 0;
        $pageCount = 0;
        $userCount = 0;

        //Visitor's Count
        $visitsCount = Visitor::count();

        //Online Users Count
        $dateLimit = date('Y-m-d H:i:s', strtotime('-5 minutes'));
        $onlineList = Visitor::select('ip')->where('date_access', '>=', $dateLimit)->groupBy('ip')->get();
        $onlineCount = count($onlineList);

        //Page Count
        $pageCount = Page::count();

        //User Count
        $userCount = User::count();

        return view('Admin.home', [
            'visitsCount' => $visitsCount,
            'onlineCount' => $onlineCount,
            'pageCount' => $pageCount,
            'userCount' => $userCount
        ]);
    }
}
