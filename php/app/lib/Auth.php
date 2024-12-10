<?php

namespace app\lib;

class Auth
{
    private static $instance = null;
    public function __construct(private Model $user)
    {
        self::$instance = $this;
    }
    public static function authorize(): bool
    {
        return (self::$instance !== null);
    }
}
