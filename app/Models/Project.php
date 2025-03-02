<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\File;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';
    protected $fillable = [
        'name', 'company_id', 'referent_id', 'address', 'is_mask_valided',
        'is_mask_distributed', 'comment', 'created_at', 'updated_at'
    ];

    /**
     * Relationship with Users (Clients)
     */
    public function clients()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id');
    }

    /**
     * Relationship with files
     */
    public function files()
    {
        return $this->hasMany(File::class, 'project_id');
    }

    /**
     * Relationship with Referent (User)
     */
    public function referent()
    {
        return $this->belongsTo(User::class, 'referent_id');
    }

    /**
     * Get referent's name of a project
     */
    public static function getReferentName($id)
    {
        $user = User::where('id', $id)->first();

        return $user ? $user->name : 'Aucun';
    }

    public static function getCompanyName($id)
    {
        $company = Company::where('id', $id)->first();

        return $company ? $company->name : 'Aucune';
    }


}
