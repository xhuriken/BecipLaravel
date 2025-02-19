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
     * Relationship with Files
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
     * Get all clients for a given project
     */
    public static function getAllClients(int $id)
    {
        return self::findOrFail($id)->clients()->get();
    }

    /**
     * Get all files for a given project
     */
    public static function getAllFiles(int $id)
    {
        return self::findOrFail($id)->files()->get();
    }

    /**
     * Get referent's name of a project
     */
    public static function getReferentName(int $id)
    {
        return User::where('id', $id)->value('name') ?? 'Inconnu';
    }
    public static function getCompanyName(int $id)
    {
        return Company::where('id', $id)->value('name') ?? 'Inconnu';
    }

}
