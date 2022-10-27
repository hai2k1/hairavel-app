<?php

namespace Hairavel\Core\UI\Form;

/**
 * Multiple file upload
 * @package Hairavel\Core\UI\Form
 */
class Files extends Element implements Component
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

    /**
     * upload method
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
     * @return string[]
     */
    public function render(): array
    {
        $data = [
            'nodeName' => 'app-files',
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
