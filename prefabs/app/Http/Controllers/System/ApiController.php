<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

class ApiController extends Controller
{

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $routes = [];
        $routesCollection = Route::getRoutes();

        foreach ($routesCollection as $route) {
            if (!str_starts_with($route->uri(), "api"))
                continue;

            if (!str_starts_with($route->getActionName(), "@"))
                continue;

            [$class, $method] = explode("@", $route->getActionName());
            $reflectionClass = new ReflectionClass($class);
            $reflectionMethod = $reflectionClass->getMethod($method);

            $routes[] = [
                "Method" => $route->methods()[0],
                "Uri" => $route->uri(),
                "Description" => $this->phpDocsDescription($reflectionMethod),
                "Parameters" => $this->phpDocsParameters($reflectionMethod),
                "Returns" => $reflectionMethod->getReturnType() ? $reflectionMethod->getReturnType()->getName() : "NULL",
            ];
        }

        return view('system.api.index', [
            'routes' => $routes,
        ]);
    }

    private function phpDocsParameters(ReflectionMethod $method): array
    {
        // Retrieve the full PhpDoc comment block
        $doc = $method->getDocComment();

        // Trim each line from space and star chars
        $lines = array_map(function ($line) {
            return trim($line, " *");
        }, explode("\n", $doc));

        // Retain lines that start with an @
        $lines = array_filter($lines, function ($line) {
            return strpos($line, "@param") === 0;
        });

        $args = [];

        // Push each value in the corresponding @param array
        foreach ($lines as $line) {
            [$null, $type, $name, $comment] = explode(' ', $line, 4);

            $args[] = [
                'type' => $type,
                'name' => $name,
                'comment' => $comment
            ];
        }

        return $args;
    }

    private function phpDocsDescription(ReflectionMethod $method): string
    {
        $doc = $method->getDocComment();
        $lines = [];

        foreach (explode("\n", $doc) as $i =>$line) {
            $trimedLine =trim(trim($line, " *"), "/");

            if (str_starts_with($trimedLine, "@")) {
                break;
            }

            $lines[$i] = $trimedLine;
        }

        return implode("\n", $lines);
    }
}
