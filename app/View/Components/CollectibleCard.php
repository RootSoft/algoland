<?php

namespace App\View\Components;

use App\Domain\Collectible;
use Illuminate\View\Component;

class CollectibleCard extends Component
{

    public Collectible $collectible;

    /**
     * Create a new component instance.
     *
     * @param Collectible $collectible
     */
    public function __construct(Collectible $collectible)
    {
        $this->collectible = $collectible;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.collectible-card');
    }
}
