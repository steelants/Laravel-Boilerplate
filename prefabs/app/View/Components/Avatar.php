<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\User;

class Avatar extends Component
{
    public $short;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?User $user = null,
        public $name = null,
        public $image = null,
        public $color = null,
        public $size = 'md',
    ) {
        if (isset($user)) {
            $this->name ??= $user->name;
            // $this->image ??= $user->image;
        }

        $hash = substr(sha1($this->name), 0, 10);
        $this->color ??= 1 + hexdec($hash) % 6;

        preg_match_all('/(?<=\s|^)[a-z]/i', $this->name, $matches);
        $this->short = strtoupper(implode('', array_slice($matches[0], 0, 2)));
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.avatar');
    }
}
