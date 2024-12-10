<?php


namespace app\Interfaces;

use app\lib\Model;

interface SavedModel
{
    public function get_key(): string|int;
    public function get_model(): Model;
}
