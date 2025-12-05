<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ShowCreatives extends Component
{
    public $creatives;
    public $thumbnails;
    public $tags;
    public $comments;
    public $likes;

    public function __construct($creatives, $thumbnails, $tags, $comments, $likes)
    {
        $this->creatives = $creatives;
        $this->thumbnails = $thumbnails;
        $this->tags = $tags;
        $this->comments = $comments;
        $this->likes = $likes;
    }

    public function render(): View|Closure|string
    {
        return view('components.show.creatives');
    }
}
