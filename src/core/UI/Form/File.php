<?php

namespace Hairavel\Core\UI\Form;

/**
 * File Upload
 * @package Hairavel\Core\UI\Form
 */
class File extends Element implements Component
{
    protected string $type = 'upload';
    protected string $url = '';
    protected string $fileUrl = '';
    protected string $callback = '';

    /**
     * File constructor.
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
    public function type(string $type = 'upload'): self
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
     * callback
     * @param string $callback
     * @return $this
     */
    public function callback(string $callback): self
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @return array
     */
    public function render(): array
    {
        $data = [
            'nodeName' => 'app-file',
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
        if($this->callback){
            $data['vOn:upload'] = $this->callback;
        }
        return $data;
    }

}
