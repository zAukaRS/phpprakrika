<?php
namespace Src;
use Error;

class Route
{
    private static array $routes = [];
    private static string $prefix = '';

    // Конструктор
    public function __construct(string $prefix = '')
    {
        self::setPrefix($prefix);
    }

    // Метод для установки префикса
    public static function setPrefix($value)
    {
        self::$prefix = $value;
    }

    // Метод для добавления маршрута
    public static function add(string $route, array $action): void
    {
        if (!array_key_exists($route, self::$routes)) {
            self::$routes[$route] = $action;
        }
    }

    // Метод для редиректа
    public function redirect(string $url): void
    {
        header('Location: ' . $this->getUrl($url));
        exit();
    }

    // Метод для получения полного URL
    public function getUrl(string $url): string
    {
        return self::$prefix . $url;
    }

    // Метод для запуска маршрута
    public function start(): void
    {
        $path = explode('?', $_SERVER['REQUEST_URI'])[0];
        $path = substr($path, strlen(self::$prefix) + 1);

        if (!array_key_exists($path, self::$routes)) {
            throw new Error('This path does not exist');
        }

        $class = self::$routes[$path][0];
        $action = self::$routes[$path][1];

        if (!class_exists($class)) {
            throw new Error('This class does not exist');
        }

        if (!method_exists($class, $action)) {
            throw new Error('This method does not exist');
        }

        // Вызов метода класса
        call_user_func([new $class, $action], new Request());
    }
}
