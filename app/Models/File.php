<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'files';
    protected $fillable =  ['project_id', 'user_id', 'name', 'extension', 'comment',
                            'is_validated', 'validate_time', 'type', 'is_last_index',
                            'distribution_count', 'created_at', 'updated_at'];

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
