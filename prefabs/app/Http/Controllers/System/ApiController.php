<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;

class ApiController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $routes = [];
        $routesCollection = Route::getRoutes();

        foreach ($routesCollection as $route) {
            if (!str_starts_with($route->uri(), 'api')) {
                continue;
            }

            $reflectionMethod = null;
            if (str_contains($route->getActionName(), '@')) {
                [
                    $class,
                    $method,
                ] = explode('@', $route->getActionName());
                $reflectionClass = new ReflectionClass($class);
                $reflectionMethod = $reflectionClass->getMethod($method);
            }

            $routes[] = [
                'Method'      => $route->methods()[0],
                'Uri'         => $route->uri(),
                'Description' => ($reflectionMethod != null ? $this->phpDocsDescription($reflectionMethod) : ''),
                'Parameters'  => ($reflectionMethod != null ? $this->phpDocsParameters($reflectionMethod) : []),
                'Returns'     => ($reflectionMethod != null ? $this->returnTypeName($reflectionMethod) : 'NULL'),
            ];
        }

        return view('system.api.index', [
            'layout' => config('boilerplate.layouts.system'),
            'routes' => $routes,
        ]);
    }

    /**
     * Vrati nazev navratoveho typu metody jako citelny string.
     * Union a intersection typy nemaji getName(), proto je skladame z dilcich typu.
     */
    private function returnTypeName(ReflectionMethod $method): string
    {
        $type = $method->getReturnType();

        return match (true) {
            $type instanceof ReflectionNamedType        => $type->getName(),
            $type instanceof ReflectionUnionType        => $this->joinTypeNames($type->getTypes(), '|'),
            $type instanceof ReflectionIntersectionType => $this->joinTypeNames($type->getTypes(), '&'),
            default                                     => 'NULL',
        };
    }

    /**
     * Spoji nazvy dilcich typu. Nelze pouzit pluck('name') - ReflectionNamedType
     * nema verejnou property $name, nazev vraci pouze metoda getName().
     *
     * @param  array<int, \ReflectionType>  $types
     */
    private function joinTypeNames(array $types, string $separator): string
    {
        $names = array_map(
            fn ($type) => $type instanceof ReflectionNamedType ? $type->getName() : 'UNKNOWN',
            $types
        );

        return implode($separator, $names);
    }

    private function phpDocsParameters(ReflectionMethod $method): array
    {
        // Retrieve the full PhpDoc comment block
        $doc = $method->getDocComment();

        // Metoda bez PHPDoc bloku vraci false, coz nelze predat do explode()
        if ($doc === false) {
            return [];
        }

        // Trim each line from space and star chars
        $lines = array_map(function ($line) {
            return trim($line, ' *');
        }, explode("\n", $doc));

        // Retain lines that start with an @
        $lines = array_filter($lines, function ($line) {
            return strpos($line, '@param') === 0;
        });

        $args = [];

        // Push each value in the corresponding @param array
        foreach ($lines as $line) {
            [
                $null,
                $type,
                $name,
                $comment,
            ] = explode(' ', $line, 4);

            $args[] = [
                'type'    => $type,
                'name'    => $name,
                'comment' => $comment,
            ];
        }

        return $args;
    }

    private function phpDocsDescription(ReflectionMethod $method): string
    {
        $doc = $method->getDocComment();

        // Metoda bez PHPDoc bloku vraci false, coz nelze predat do explode()
        if ($doc === false) {
            return '';
        }

        $lines = [];

        foreach (explode("\n", $doc) as $i => $line) {
            $trimedLine = trim(trim($line, ' *'), '/');

            if (str_starts_with($trimedLine, '@')) {
                break;
            }

            $lines[$i] = $trimedLine;
        }

        return implode("\n", $lines);
    }
}
