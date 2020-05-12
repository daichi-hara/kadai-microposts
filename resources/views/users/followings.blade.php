
<!--共通のレイアウトページのlayouts.sppを継承する-->
@extends('layouts.app')

@section('content')
    <div class="row">
        <!--aside内に、card.bladeとuserの変数$userの代入を行う。asideをつくる-->
        <aside class="col-sm-4">
            @include('users.card', ['user' => $user])
        </aside>
        <div class="col-sm-8">
            <!--ナビタブとユーザー情報の代入-->
            @include('users.navtabs', ['user' => $user])
            @include('users.users', ['users' => $users])
        </div>
    </div>
@endsection