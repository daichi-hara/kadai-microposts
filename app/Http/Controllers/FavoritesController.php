<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    //favoriteの保存
    public function store(Request $request, $id)
    {
        //Auth::userで現在ログインしているユーザ情報を取得。そして、ユーザがお気に入りしているmicropostsを獲得
        \Auth::user()->favorite($id);
        return back();
    }
    //unfavorite
    public function destroy($id)
    {
        \Auth::user()->unfavorite($id);
        return back();
    }
}
