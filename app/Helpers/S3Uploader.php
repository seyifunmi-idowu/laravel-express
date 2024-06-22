<?php

namespace App\Helpers;

use Aws\S3\S3Client;
use Illuminate\Support\Str;

class S3Uploader
{
    protected $s3Client;
    protected $bucket;
    protected $folder;

    public function __construct($appendFolder = null)
    {
        $this->bucket = env('AWS_S3_BUCKET');
        $this->folder = 'backend-' . env('ENVIRONMENT');
        if ($appendFolder) {
            $this->folder .= $appendFolder;
        }

        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    protected function buildKey($filename)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        return Str::uuid() . '.' . $extension;
    }

    public function uploadFileObject($fileObject, $fileName, $useRandomKey = true)
    {
        $key = $useRandomKey ? $this->buildKey($fileName) : $fileName;
        $key = $this->folder . '/' . $key;

        $this->putObject($key, $fileObject);

        return "https://{$this->bucket}.s3.amazonaws.com/{$key}";
    }

    protected function putObject($key, $body)
    {
        // TODO: Use a job for async processing if needed
        $this->s3Client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'Body' => $body,
            'ACL' => 'public-read',
        ]);
    }

    public function hardDeleteObject($imageUrl)
    {
        $key = implode('/', array_slice(explode('/', $imageUrl), 3));
        return $this->s3Client->deleteObjects([
            'Bucket' => $this->bucket,
            'Delete' => [
                'Objects' => [
                    ['Key' => $key],
                ],
            ],
        ]);
    }
}
