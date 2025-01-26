<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes; 

    protected $table = 'leads'; 

    protected $fillable = [
        'name', 'email', 'whats', 'origin', 'password', 'lead_type_id', 'lead_thermometer_id',
    ];

    protected $dates = ['deleted_at'];

    public function leadType()
    {
        return $this->belongsTo(LeadType::class);
    }

    public function leadThermometer()
    {
        return $this->belongsTo(LeadThermometer::class);
    }
}
