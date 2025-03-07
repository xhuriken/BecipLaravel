<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Grosv\LaravelPasswordlessLogin\Traits\PasswordlessLogin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use PasswordlessLogin;

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\MailResetPasswordToken($token));
    }
    public function getGuardNameAttribute(): string
    {
        return config('laravel-passwordless-login.user_guard');
    }

    public function getShouldRememberLoginAttribute(): bool
    {
        return config('laravel-passwordless-login.remember_login');
    }

    public function getLoginRouteExpiresInAttribute(): int
    {
        return config('laravel-passwordless-login.login_route_expires');
    }

    public function getRedirectUrlAttribute(): string
    {
        return config('laravel-passwordless-login.redirect_on_success');
    }
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'company_id',
        'email',
        'password',
        'role',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     *
     * Get Company Name with Company ID
     *
     * @param $id
     * @return void
     */
    public static function getCompanyName($id){
        $name = Company::from("companies")->where("id", "=", $id)->first();
        if($name!=null){
            return $name->name;
        }else{
            return "Aucune";
        }

    }

    public function isBecip() {
        return in_array(auth()->user()->role, ['engineer', 'drawer', 'secretary']);
    }

    /**
     *
     * @return mixed
     */
    public function projects() {
        if ($this->isBecip()) {
            return Project::all();
        } else {
            return Project::from('projects as P')
                ->join('project_user as PU', 'P.id', '=', 'PU.project_id')
                ->where('PU.user_id', $this->id)
                ->select('P.*')
                ->get();
        }
    }

    public function projectsRelation() //honnêtement j'ai pas compris
    {
        return $this->belongsToMany(Project::class, 'project_user', 'user_id', 'project_id');
    }
}
