<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Navbar extends Component
{

    public $type;

    public $message;

    /**
     * Navbar constructor.
     * @param $type
     * @param $message
     */
    public function __construct($type = '', $message = '') {
        $this->type = $type;
        $this->message = $message;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render()
    {
        return view('components.navbar');
    }
}
