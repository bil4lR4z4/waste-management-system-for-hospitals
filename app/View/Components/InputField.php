<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InputField extends Component
{ public $name;
    public $label;
    public $labelClass;
    public $type;
    public $value;
    public $placeholder;
    public $class;
    public $size;
    public $step;
    public $readonly;
    public $required;
    public $divClass;


    public function __construct($name, $label = '', $labelClass = '', $type = 'text', $value = '', $placeholder = '' , $class = '', $size='' , $step='' , $readonly = false , $required = false, $divClass = '')
    {
        $this->name = $name;
        $this->label = $label;
        $this->labelClass = $labelClass;
        $this->type = $type;
        $this->value = $value;
        $this->placeholder = $placeholder;
        $this->class = $class;
        $this->divClass = $divClass;
        $this->size = $size;
        $this->step = $step;
        $this->readonly = filter_var($readonly, FILTER_VALIDATE_BOOLEAN);
        $this->required = filter_var($required, FILTER_VALIDATE_BOOLEAN);
    }

    public function render(): View|Closure|string
    {
        return view('components.input-field');
    }
}
