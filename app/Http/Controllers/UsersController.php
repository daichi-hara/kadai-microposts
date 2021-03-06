<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Micropost;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(10);

        return view('users.index', [
            'users' => $users,
        ]);
    }
    
    public function show($id)
    {
        $user = User::find($id);
        $microposts = $user->microposts()->orderBy('created_at', 'desc')->paginate(10);

        $data = [
            'user' => $user,
            'microposts' => $microposts,
        ];

        $data += $this->counts($user);

        return view('users.show', $data);
    }
    
    //followしているユーザー情報の取得。ここはControllerだから、modelからレコードを獲得する！！
    public function followings($id)
    {
        $user = User::find($id);
        $followings = $user->followings()->paginate(10);

        $data = [
            'user' => $user,
            'users' => $followings,
        ];

        $data += $this->counts($user);

        return view('users.followings', $data);
    }

    //follower自分をフォローしているユーザー情報の取得
    public function followers($id)
    {
        $user = User::find($id);
        $followers = $user->followers()->paginate(10);

        $data = [
            'user' => $user,
            'users' => $followers,
        ];

        $data += $this->counts($user);

        return view('users.followers', $data);
    }
    
    public function favorites($id)
    {
        //お気に入りをしようとしているユーザーをID
        $user = User::find($id);
        $favorites = $user->favorites()->paginate(10);

        //保存する情報。中間テーブルの2つのカラム
        $data = [
            'user' => $user,
            'microposts' => $favorites,
        ];

        //ユーザーが何件お気に入りしたのか＝count($user)
        $data += $this->counts($user);

        //お気に入りタブから飛ぶとそこに表示するためのデータを(このページに,このデータを)returnしてあげる
        return view('users.favorites', $data);
    }
}
