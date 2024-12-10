<?php

namespace app\lib;

use app\lib\Page;

abstract class Template
{
    protected Model $model;

    public function __construct(private Page $page) {}

    public function view(array $params = [], ?Model $model = null, ...$args)
    {
        $model = is_null($model) ? [] : $model->toArray();
        $this->model->clear();
        $this->model->update($params + $args + $model);
        $this->model->prepare();
        ob_start();
        echo $this->get_html();
        return ob_get_clean();
    }

    abstract protected function get_html(): string;
}