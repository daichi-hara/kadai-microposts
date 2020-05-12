<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserFollowController extends Controller
{
    //User.php(Model)で定義したfollowメソッドを使って、ユーザーをフォローできるようにする
    public function store(Request $requset, $id)
    {
        //ログインしているユーザーのfollowメソッドを動かす
        \Auth::user()->follow($id);
        return back();
    }
    
    //unfollowメソッドを使ってユーザーをアンフォローできるようにする
    public function destroy(Request $requset, $id)
    {
        \Auth::user()->unfollow($id);
        return back();
    }
    
}
