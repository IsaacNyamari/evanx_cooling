<?php

namespace App\Livewire;

use App\Mail\ContactPageForm;
use App\Models\ContactMessages;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;
use SweetAlert2\Laravel\Traits\WithSweetAlert;

class ContactForm extends Component
{
    use WithSweetAlert;

    #[Validate('string|required')]
    public string $name;

    #[Validate('email|required')]
    public string $email;

    #[Validate('string|required')]
    public string $phone;

    #[Validate('string|required')]
    public string $subject;

    #[Validate('string|required')]
    public string $message;

    public function render()
    {
        return view('livewire.contact-form');
    }

    public function sendMessage()
    {
        $data = $this->validate();
        ContactMessages::create($data);
        try {
            Mail::to('jablessions76@gmail.com')->send(new ContactPageForm($data));

            $message = 'Your message was sent succcessfully!';
            $this->swalToastSuccess([
                'position' => 'top-end',
                'text' => $message,
                'icon' => 'success',
                'showConfirmButton' => false,
                'timer' => 3000,
                'timerProgressBar' => true,
                'didOpen' => '(toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }',
            ]);
            $this->reset();
        } catch (\Throwable $error) {
            return back()->with('error', 'message not sent succcessfully!');
        }

    }
}
