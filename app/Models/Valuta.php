<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Valuta extends Model
{
    use HasFactory;
    protected $fillable = ['valuta', 'code', 'procent'];
    protected $table = 'valutas';
    protected $guarded = ['id'];
}
