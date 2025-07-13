<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InputForm extends Component
{
    public string $name, $label, $type;
    public ?string $value;

    /**
     * Create a new component instance.
     */
    public function __construct(string $name, $label, $type = 'text', ?string $value = '')
    {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|string|Closure
    {
        return view('components.input-form');
    }
}
