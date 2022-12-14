<?php

namespace Hairavel\Core\UI\Form;

/**
 * Group picture upload
 * @package Hairavel\Core\UI\Form
 */
class Images extends Element implements Component
{
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

    public function type($type = 'manage')
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
     * @return array
     */
    public function render(): array
    {
        $data = [
            'nodeName' => 'app-images',
        ];
        if ($this->type) {
            $data['type'] = $this->type;
        }
        if ($this->url) {
            $data['upload'] = $this->url;
        }
        if ($this->fileUrl) {
            $data['fileUrl'] = $this->fileUrl;
        }
        if ($this->model) {
            $data['vModel:value'] = $this->getModelField();
        }

        return $data;
    }

}
