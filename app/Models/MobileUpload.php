<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileUpload extends Model
{
    protected $fillable = [
        'table_name',
        'author'
    ];


    public function attendees()
    {
        return $this->hasMany(
            MobileUploadAttendee::class,
            'mobile_upload_id',
            'id'
        );
    }
}
