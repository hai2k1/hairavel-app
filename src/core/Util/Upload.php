<?php

namespace Hairavel\Core\Util;

use Hairavel\Core\Exceptions\ErrorException;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

/**
 * Upload processing
 */
class Upload
{

    /**
     * @param string $hasType
     * @param array $config
     * @param int $dirId
     * @param string $driver
     * @return array
     * @throws ErrorException
     */
    public static function load(string $hasType, array $config = [], int $dirId = 0, string $driver = ''): array
    {
        $thumb = $config['thumb'] ??config('image.thumb');
        $width = $config['width'] ?? config('image.thumb_width');
        $height = $config['height'] ??config('image.thumb_height');
        $water = $config['water'] ?? config('image.water');
        $alpha = $config['alpha'] ??config('image.water_alpha');
        $source = resource_path(config('image.water_image'));

        $files = request()->allFiles();
        $ids = [];
        if (is_array($files)) {
            $filesData = [];
            foreach ($files as $key => $item) {
                if (is_array($item)) {
                    foreach ($item as $vo) {
                        $filesData[] = [
                            'field' => $key,
                            'file' => $vo
                        ];
                    }
                } else {
                    $filesData[] = [
                        'field' => $key,
                        'file' => $item
                    ];
                }
            }
            foreach ($filesData as $item) {
                $file = $item['file'];
                $field = $item['field'];
                $ext = $file->extension();
                if (in_array($ext, ['jpg', 'png', 'bmp', 'jpeg', 'gif']))) {
                    $tmpPath = $file->getRealPath();
                    $image = Image::make($file);
                    if ($thumb) {
                        switch ($thumb) {
                            // center crop zoom
                            case 'center':
                                $image->fit($width, $height, function ($constraint) {
                                    $constraint->upsize();
                                }, 'center');
                                break;
                            // fixed size
                            case 'fixed':
                                $image->resize($width, $height, function ($constraint) {
                                    $constraint->upsize();
                                });
                                break;
                            // equal scaling
                            case 'scale':
                                if ($width > $height) {
                                    $image->resize(null, $height, function ($constraint) {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });
                                } else {
                                    $image->resize($width, null, function ($constraint) {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    });
                                }
                        }
                    }
                    if ($water) {
                        switch ($water) {
                            //watermark in the upper left corner
                            case 1:
                            case 'top-left':
                                $position = 'top-left';
                                break;
                            // Center the watermark
                            case 2:
                            case 'top':
                                $position = 'top';
                                break;
                            //watermark in the upper right corner
                            case 3:
                            case 'top-right':
                                $position = 'top-right';
                                break;
                            // left center watermark
                            case 4:
                            case 'left':
                                $position = 'left';
                                break;
                            // Center the watermark
                            default:
                            case 5:
                            case 'center':
                                $position = 'center';
                                break;
                            //right center watermark
                            case 6:
                            case 'right':
                                $position = 'right';
                                break;
                            //watermark in the lower left corner
                            case 7:
                            case 'bottom-left':
                                $position = 'bottom-left';
                                break;
                            // Bottom center watermark
                            case 8:
                            case 'bottom':
                                $position = 'bottom';
                                break;
                            //watermark in the lower right corner
                            case 9:
                            case 'bottom-right':
                                $position = 'bottom-right';
                                break;
                        }
                        $watermark = Image::make($source)->opacity($alpha);
                        $image->insert($watermark, $position, 10, 10);
                    }
                    \File::put($tmpPath, $image->encode($ext, 100));
                }
                $path = $file->store('upload/' . date('Y-m-d'), $driver);
                if ($path) {
                    $tmp = [
                        'dir_id' => $dirId,
                        'has_type' => $hasType,
                        'driver' => $driver ?: config('filesystems.default'),
                        'url' => Storage::disk($driver)->url($path),
                        'field' => $field,
                        'path' => $path,
                        'title' => $file->getClientOriginalName(),
                        'ext' => $file->extension(),
                        'mime' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    $ids[] = \Hairavel\Core\Model\File::insertGetId($tmp);
                } else {
                    app_error('Upload failed');
                }
            }
        }
        $data = \Hairavel\Core\Model\File::where('has_type', $hasType)->whereIn('file_id', $ids)->get([
            'file_id', 'dir_id', 'url', 'title', 'ext', 'size', 'created_at', 'field'
        ]);

        return $data->map(function ($item) {
            $item->size = app_filesize($item['size']);
            $item->time = $item->created_at->format('Y-m-d H:i:s');
            return $item;
        })->toArray();
    }


}
