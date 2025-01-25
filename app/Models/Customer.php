<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    const UPDATED_AT = null;
    const CREATED_AT = 'creation_date';

    protected $table = 'customer';

    protected $casts = [
        'dob' => 'datetime:Y-m-d',
        'creation_date' => 'datetime:Y-m-d H:i:s',
    ];
    

    protected $fillable = [
        'first_name',
        'last_name',
        'age',
        'dob',
        'email',
        
    ];
}
