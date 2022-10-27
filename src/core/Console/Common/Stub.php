<?php

namespace Hairavel\Core\Console\Common;

use Illuminate\Console\Command;

class Stub extends Command
{
    public function generatorDir($path)
    {
        $path = base_path('/modules/' . $path);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    public function generatorFile($file, $tpl = '', $data = [])
    {
        $file = base_path('/modules/' . $file);
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($path, 0777, true);
        }
        $content = file_get_contents($tpl);
        foreach ($data as $key => $vo) {
            $content = str_replace('{{' . $key . '}}', $vo, $content);
        }
        file_put_contents($file, $content);
    }

    public function getAppName($name)
    {
        $name = $this->ask('please enter'.$name.'(English)');
        if (!preg_match('/[a-zA-Z]/', $name, $match)) {
            $this->error($name.'Only English characters are supported!');
            return $this->getAppName($name);
        }
        return $name;
    }
}
