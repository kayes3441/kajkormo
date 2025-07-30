<?php

namespace App\Trait;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use function App\Traits\cacheRemoveByType;
use function App\Traits\getWebConfig;
use function App\Traits\translate;

trait FileManagerTrait
{
    /**
     * upload method working for image
     * @param string $dir
     * @param $image
     * @return string
     */
    protected function upload(string $dir,  $image = null): string
    {
        $storage = config('filesystems.disks.default') ?? 'public';
        if (!is_null($image)) {
            if (!$this->checkFileExists($dir)['status']) {
                Storage::disk($storage)->makeDirectory($dir);
            }
            $imageName = Carbon::now()->toDateString() . "-" . uniqid() . "." . $image->getClientOriginalExtension();
            $image->storeAs($dir, $imageName);
        } else {
            $imageName = 'def.png';
        }

        return $imageName;
    }

    /**
     * @param string $dir
     * @param $oldImage
     * @param $image
     * @param string $fileType image/file
     * @return string
     */
    public function update(string $dir, $oldImage, $image, string $fileType = 'image'): string
    {
        if ($this->checkFileExists(filePath: $dir . $oldImage)['status']) {
            Storage::disk($this->checkFileExists(filePath: $dir . $oldImage)['disk'])->delete($dir . $oldImage);
        }
        return $this->upload($dir, $image);
    }

    /**
     * @param string $filePath
     * @return array
     */
    protected function  delete(string $filePath): array
    {
        if ($this->checkFileExists(filePath: $filePath)['status']) {
            Storage::disk($this->checkFileExists(filePath: $filePath)['disk'])->delete($filePath);
        }
        return [
            'success' => 1,
            'message' => 'Removed successfully'
        ];
    }



    private function checkFileExists(string $filePath): array
    {
        if (Storage::disk('public')->exists($filePath)) {
            return [
                'status' => true,
                'disk' => 'public'
            ];
        }else {
            return [
                'status' => false,
                'disk' => config('filesystems.disks.default') ?? 'public'
            ];
        }
    }
}
