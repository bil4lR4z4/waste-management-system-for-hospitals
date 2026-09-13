<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PasswordField extends Component
{
    public $name;
    public $labelClass;
    public $label;
    public $placeholder;
    public $class;
    public $size;

    public function __construct($name, $label = '', $labelClass = '', $placeholder = '' , $class = '' , $size='')
    {
        $this->name = $name;
        $this->label = $label;
        $this->labelClass = $labelClass;
        $this->placeholder = $placeholder;
        $this->class = $class;
        $this->size = $size;
    }
    
    public function render(): View|Closure|string
    {
        return view('components.password-field');
    }
}
