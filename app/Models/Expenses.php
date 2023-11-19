<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    use HasFactory;

    protected $table = 'expenses';

    protected $fillable = [
        'id',
        'name',
        'account_type',
        'amount',
        'post_date',
        'note',
        'added_by',
        'attachment',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'post_date' => 'date:m-d-Y',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->timezone('Asia/Singapore')->format('m-d-y H:i:s');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
