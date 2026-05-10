<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table = 'leaves';

    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'status',
        'reason',
        'attachment',
        'leave_type'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
