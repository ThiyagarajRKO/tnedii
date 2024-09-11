<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsLetterSubscription extends Model
{
    use HasFactory;
    
   protected $table = 'newsletter_subscriptions';
   protected $fillable = ['email_id','status'];
}
