
<!--card型コンポーネントを使い、フォローしているユーザー、フォローされているユーザー、followボタンの一覧表示をつくる-->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $user->name }}</h3>
    </div>
    <div class="card-body">
        <img class="rounded img-fluid" src="{{ Gravatar::src($user->email, 500) }}" alt="">
    </div>
</div>
<!--user_follow viewのfollow_buttonをcard内にインクルード-->
@include('user_follow.follow_button', ['user' => $user])