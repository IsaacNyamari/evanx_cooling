<?php

namespace App\Livewire;

use App\Models\ContactMessages;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class AdminMessagesIndex extends Component
{
    use WithPagination,WithoutUrlPagination;
    protected $paginationTheme = 'bootstrap';
    public function mount()
    {
        $this->getMessages();
    }

    public function render()
    {
        return view('livewire.admin-messages-index');
    }

    public function markMessageRead(ContactMessages $message)
    {
        $message->is_read = true;
        $message->update();
        $this->dispatch('LoadMessages');
    }

    #[On('LoadMessages')]
    public function getMessages()
    {
        return ContactMessages::paginate(5);
    }
    #[On('LoadMessages')]
    public function totalMessages()
    {
        return ContactMessages::all();
    }
}
