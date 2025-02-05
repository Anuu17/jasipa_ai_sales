<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;

trait FileUpload {
    public function uploadFile(UploadedFile $file, string $directory = 'uploads') : string
        {
            $filename = 'jasipa_ai_sales'.uniqid().'.'.$file->getClientOriginalExtension();

            //move file to storage

            $file->move(public_path($directory), $filename);

            return $directory. '/'.$filename;
        }
}
