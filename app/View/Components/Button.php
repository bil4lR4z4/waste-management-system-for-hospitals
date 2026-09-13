<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public $type;
    public $variant;
    public $size;
    public $label;

    public function __construct($label, $type = 'submit', $variant = 'primary', $size = '')
    {
        $this->label = $label;
        $this->type = $type;
        $this->variant = $variant;
        $this->size = $size;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.button');
    }
}
