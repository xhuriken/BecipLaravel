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
}
