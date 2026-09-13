<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TextArea extends Component
{
    public $name;
    public $label;
    public $labelClass;
    public $placeholder;
    public $rows;
    public $value;
    public $class;
    public $size;

    public function __construct($name, $label = '', $labelClass = '', $placeholder = '', $value = '', $rows = 3 , $class = '', $size='')
    {
        $this->name = $name;
        $this->label = $label;
        $this->labelClass = $labelClass;
        $this->placeholder = $placeholder;
        $this->value = $value;
        $this->rows = $rows;
        $this->class = $class;
        $this->size = $size;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.text-area');
    }
}
