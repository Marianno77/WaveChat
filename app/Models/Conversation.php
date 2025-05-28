<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['first_user_id', 'second_user_id'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function firstUser()
    {
        return $this->belongsTo(User::class, 'first_user_id');
    }

    public function secondUser()
    {
        return $this->belongsTo(User::class, 'second_user_id');
    }

    public function getUsers()
    {
        return collect([$this->firstUser(), $this->secondUser()]);
    }

}
