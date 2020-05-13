<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
//以下は中間テーブルのModel設計

    public function followings()
    {  
        // belongsToManyの第一引数にModelクラス, 第二引数に中間テーブル, 第三、四引数に中間テーブル内のカラム名を指定する。左からの流れなので、user_id, follow_idの順
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    public function followers()
    {   
        //第三、四引数の順番がfollowingsと異なる。左からの流れであることが要因
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    //フォローの定義
    public function follow($userId)
    {
        //following($userId)がfollowしているuser_idと同じか確認
        $exit = $this->is_following($userId);
        
        //its_meとフォローしたid($usrId)が自分以外の人であるか確認
        $its_me = $this->id == $userId;
        
        //上二つの条件のいずれかが満たされているか否かで条件分岐する
        if ($exit || $its_me) {
            //条件が満たされていた場合、何もしない
            return false;
        }else {
            //条件が見たされていない場合、フォローするように分岐
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    //アンフォローの定義
    public function unfollow($userId)
    {
        //既にフォローしているかの確認
        $exist = $this->is_following($userId);
        
        //相手が自分自身かどうか確認
        $its_me = $this->id == $userId;
        
        //既にフォローしていて、相手が自分自身ではない　のか否かで条件分岐
        if ($exist && !$its_me) {
            
            //既にフォローしていればフォローを外す
            $this->followings()->detach($userId); //detachは削除
            return true;
        } else {
            
            //未フォローであればなんもしない
            return false;
        }
    }
    
    public function is_following($userId)
    {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    //タイムラインにフォロワーの投稿を追加させるためのModel
    public function feed_microposts()
    {
        //Array＝配列、フォローしているユーザーのuser_idを配列で取得
        $follow_user_ids = $this->followings()->pluck('users.id')->toArray();
        //自分のid(=$this->id)も配列に追加しますよ
        $follow_user_ids[] = $this->id;
        //micropostテーブルに含みます（whereIn）
        //user_idカラムで$follow_user_idsの配列の中にあるユーザーidを含むものすべて取得します（user_idを含むmicropostが取得できる）
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
    
    
    
    //課題２の中間テーブルの設定。ユーザーがお気に入りしたmicropostsの一覧を表示するメソッドを定義
    public function favorites()
    {   
        //第一引数に取得したい情報のモデルを、第2引数に中間テーブルを、第3引数に自分のカラム名を、第4引数に中間テーブルに保存されている相手のカラムを入れる
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    public function favorite($micropostId)
    {
        //お気に入り済みかどうか確認
        $exist = $this->is_favoriting($micropostId);
        
        //お気に入り済みかどうかで条件分岐し、負ならお気に入りする
        if ($exist) {
            //お気に入り済みであれば何もしない
            return false;
        } else {
            //お気に入りにしていなければお気に入りにする
            $this->favorites()->attach($micropostId);
            return true;
        }
    }
    
    public function unfavorite($micropostId)
    {
        //お気に入り済みか確認
        $exist = $this->is_favoriting($micropostId);
        
        //お気に入り済みかどうかで条件分岐し、正ならお気に入り解除する
        if ($exist) {
            //お気に入りしてたら解除（detach）
            $this->favorites()->detach($micropostId);
            return true;
        } else {
            //お気に入りしてなかったらなんもしない
            return false;
        }
    }
    
    //is_favoritingの定義
    public function is_favoriting($micropostId) 
    {
        return $this->favorites()->where('micropost_id', $micropostId)->exists();
    }
}
