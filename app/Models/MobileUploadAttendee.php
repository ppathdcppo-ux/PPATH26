<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileUploadAttendee extends Model
{
    protected $fillable = [
        'mobile_upload_id',
        'name',
        'gender'
    ];


    public function upload()
    {
        return $this->belongsTo(
            MobileUpload::class,
            'mobile_upload_id',
            'id'
        );
    }
}
