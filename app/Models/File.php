<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'files';
    protected $fillable =  ['project_id', 'user_id', 'name', 'extension', 'comment',
                            'is_validated', 'validated_time', 'type', 'is_last_index',
                            'distribution_count', 'created_at', 'updated_at'];

    protected $appends = ['uploaded_recently', 'uploader_name'];
    public function getUploadedRecentlyAttribute()
    {
        return $this->created_at->diffInHours(Carbon::now()) <= 24;
    }

    public function getUploaderNameAttribute()
    {
        return $this->uploadedBy ? $this->uploadedBy->name : 'Utilisateur inconnu';
    }
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
