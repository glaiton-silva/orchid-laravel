<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadType extends Model
{
    use SoftDeletes; 

    protected $table = 'leads_types';

    protected $fillable = ['name'];

    protected $dates = ['deleted_at'];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}
