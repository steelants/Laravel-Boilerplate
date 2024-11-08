<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChangelogController extends Controller
{
    public function index()
    {
        $content = '';

        if (file_exists('../CHANGELOG.md')) {
            $fileContent = file_get_contents('../CHANGELOG.md');
            $content = Str::markdown($fileContent);
        }

        return view('markdown.index', ['content' => $content]);
    }
}
