<?php

namespace App\Livewire\Admin\Seo;

use App\Support\SeoAudit;
use Livewire\Component;

class Overview extends Component
{
    public function render(SeoAudit $audit)
    {
        return view('livewire.admin.seo.overview', ['audit' => $audit->run()]);
    }
}
