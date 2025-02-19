<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';
    protected $fillable =  ['name', 'company_id', 'referent_id', 'address', 'is_mask_valided',
                            'is_mask_distributed', 'comment', 'created_at', 'updated_at'];

    public static function getAllClients(int $id) {
        $clients = User::from('users as C')
                     ->leftJoin('project_user as PU', 'C.id', '=', 'PU.project_id');

        $clients->where('PU.user_id', $id);

        return $clients->select('C.*')->get();
    }
    public static function getAllFiles(int $id) {
        $files = File::from('files as f')
            ->where('f.project_id', $id);
        return $files->select('f.*')->get();
    }

    public static function getReferentName(int $id) {
        $name = User::from('users as C')
                    ->select('C.name')
                    ->where('id', $id);
        return $name->first()->name;
    }
}
