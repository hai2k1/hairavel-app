<?php

namespace Hairavel\Core\UI\Form;

/**
 *Class Image
 * upload picture
 * @package Hairavel\Core\UI\Form
 */
class Image extends Element implements Component
{

    private array $thumb = [];
    private array $water = [];
    protected string $type = 'manage';
    protected string $url = '';
    protected string $fileUrl = '';

    /**
     * Text constructor.
     * @param string $name
     * @param string $field
     * @param string $has
     */
    public function __construct(string $name, string $field, string $has = '')
    {
        $this->name = $name;
        $this->field = $field;
        $this->has = $has;
    }

    /**
     * Thumbnail
     * @param int $width
     * @param int $height
     * @param string $type
     * @return $this
     */
    public function thumb(int $width, int $height, string $type = 'scale'): self
    {
        $this->thumb = [
            'width' => $width,
            'height' => $height,
            'thumb' => $type
        ];
        return $this;
    }

    /**
     * Watermark
     * @param string $position
     * @param int $alpha
     * @return $this
     */
    public function water(string $position = 'center', int $alpha = 80): self
    {
        $this->water = [
            'alpha' => $alpha,
            'water' => $position
        ];
        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function type(string $type = 'manage'): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * upload address
     * @param string $url
     * @return $this
     */
    public function url(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * file address
     * @param string $url
     * @return $this
     */
    public function fileUrl(string $url): self
    {
        $this->fileUrl = $url;
        return $this;
    }


    /**
     * render component
     * @return array
     */
    public function render(): array
    {
        $data = [
            'nodeName' => 'app-file',
            'format' => 'image',
            'image' => true,
            'size' => 125
        ];
        if ($this->url) {
            $data['upload'] = $this->url;
        }
        if ($this->fileUrl) {
            $data['fileUrl'] = $this->fileUrl;
        }
        if ($this->type) {
            $data['type'] = $this->type;
        }
        if ($this->model) {
            $data['vModel:value'] = $this->getModelField();
        }
        return $data;
    }

}
