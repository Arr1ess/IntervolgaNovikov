<?php

namespace app\router;

use app\lib\Page;
use Exception;

class Route
{
    // private $responseFunction;
    private $middlewares = [];
    private array $data = [];
    private Page $page;

    public function __construct(?callable $responseFunction = null)
    {
        if ($responseFunction !== null) $this->middlewares[] = $responseFunction;
    }

    public function middleware(callable $middleware): Route
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function execute()
    {
        foreach (array_reverse($this->middlewares) as $middleware) {
            $response = call_user_func($middleware);
            if ($response instanceof Response) {
                $response->send();
                return;
            }
        }
        if(isset($this->page)){
            
        }
        (Response::error("Не найден обработчик запроса", ResponseCode::NOT_FOUND))->send();
    }
    public function addData(callable $middleware): Route{
        $this->data[] = $middleware();
        return $this;
    }

    public function setView(Page $view): Route{
        $this->page = $view;
        return $this;
    }
}
