<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Form extends Component
{
    public $action;
    public $method;
    public $title;
    public $text;

    public function __construct($action = '#', $method = 'POST', $title = '', $text='')
    {
        $this->action = $action;
        $this->method = strtoupper($method);
        $this->title = $title;
        $this->text = $text;
    }

    public function render(): View|Closure|string
    {
        return view('components.form');
    }
}
