<?php

namespace Hairavel\Core\Manage;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait Upload
{

    /**
     * file association type
     * @var string
     */
    public string $hasType = '';

    /**
     * Get the association type
     * @return mixed
     */
    public function getHasType()
    {
        if ($this->hasType) {
            return $this->hasType;
        }
        $parsing = app_parsing();
        $this->hasType = strtolower($parsing['layer']);
        return $this->hasType;
    }

    /**
     * File Upload
     * @param Request $request
     * @return array|void
     */
    public function ajax(Request $request)
    {
        $id = (int)$request->get('id') ?: 0;
        $dirId = \Hairavel\Core\Model\FileDir::where('dir_id', $id)->value('dir_id');
        if (empty($dirId)) {
            $dirId = \Hairavel\Core\Model\FileDir::where('has_type', $this->getHasType())->orderBy('dir_id',
                'desc')->value('dir_id');
        }
        if (empty($dirId)) {
            $dirId = \Hairavel\Core\Model\FileDir::insertGetId([
                'name' => 'default',
                'has_type' => $this->getHasType()
            ]);
        }

        $data = \Hairavel\Core\Util\Upload::load($this->getHasType(), [
            'thumb' => $request->get('thumb'),
            'width' => $request->get('width'),
            'height' => $request->get('height'),
            'water' => $request->get('water'),
            'alpha' => $request->get('alpha'),
            'source' => resource_path(config('image.water_image'))
        ], $dirId, $this->driver);
        if (empty($data)) {
            app_error('Failed to upload file');
        }
        return app_success('Upload successful', $data);
    }

    /**
     * Remote image storage
     */
    public function remote()
    {
        $data = \request()->input('files');
        $files = [];
        $domain = env('APP_URL');
        foreach ($data as $key => $file) {
            if (stripos($file, $domain, 0) === false && !preg_match("/(^\.)|(^\/)/", $file)) {
                $files[$key] = $file;
            }
        }
        if (empty($files)) {
            app_error('No remote image added');
        }

        $dirId = \Hairavel\Core\Model\FileDir::where('has_type', $this->getHasType())->orderBy('dir_id',
            'desc')->value('dir_id');
        if (empty($dirId)) {
            $dirId = \Hairavel\Core\Model\FileDir::insertGetId([
                'name' => 'default',
                'has_type' => $this->getHasType()
            ]);
        }

        foreach ($data as $key => $vo) {
            if (!$files[$key]) {
                continue;
            }
            try {
                $client = new \GuzzleHttp\Client();
                $imgTmp = $client->request('get', $vo)->getBody()->getContents();
                $tmpFile = tempnam(sys_get_temp_dir(), 'upload_');
                $tmp = fopen($tmpFile, 'w');
                fwrite($tmp, $imgTmp);
                fclose($tmp);
                $size = filesize($tmpFile);
                $mime = mime_content_type($tmpFile);
                $path = Storage::disk($this->driver)->putFile('upload/' . date('Y-m-d'), $tmpFile);
                @unlink($tmpFile);
            } catch (GuzzleException $exception) {
                app_error($exception->getMessage());
            }
            $url = Storage::disk($this->driver)->url($path);
            $ext = pathinfo($url, PATHINFO_EXTENSION);
            $data[$key] = $url;

            $upload = [
                'dir_id' => $dirId,
                'has_type' => $this->getHasType(),
                'driver' => $this->driver ?: config('filesystems.default'),
                'url' => $url,
                'path' => $path,
                'title' => pathinfo($vo, PATHINFO_FILENAME) . '.' . $ext,
                'ext' => $ext,
                'mime' => $mime,
                'size' => $size,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            \Hairavel\Core\Model\File::insert($upload);
        }
        return app_success('Picture obtained successfully', $data);
    }
}
