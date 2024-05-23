<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class Messages extends Component
{
    public function render()
    {
        return view('livewire.messages');
    }
    #[On('msg')]
    public function msgs($msg)
    {
        session()->flash('msg',$msg);
    }
}
