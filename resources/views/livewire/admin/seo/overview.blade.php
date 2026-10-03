@php
    $score = $audit['score'];
    $tone = $score >= 85 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
    $toneHex = ['success' => '#198754', 'warning' => '#ffc107', 'danger' => '#dc3545'][$tone];
    $verdict = $score >= 85 ? 'Great shape' : ($score >= 60 ? 'Good, with room to improve' : 'Needs attention');
    $icons = ['good' => ['fa-circle-check', 'text-success'], 'warn' => ['fa-triangle-exclamation', 'text-warning'], 'bad' => ['fa-circle-xmark', 'text-danger'], 'info' => ['fa-circle-info', 'text-primary']];
@endphp
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center py-4">
                <div class="mx-auto mb-3 position-relative" style="width: 150px; height: 150px; border-radius: 50%; background: conic-gradient({{ $toneHex }} {{ $score * 3.6 }}deg, #e9ecef 0)">
                    <div class="position-absolute top-50 start-50 translate-middle bg-white d-flex flex-column align-items-center justify-content-center" style="width: 116px; height: 116px; border-radius: 50%">
                        <span class="fw-bold" style="font-size: 2.4rem; line-height: 1">{{ $score }}</span>
                        <small class="text-muted">out of 100</small>
                    </div>
                </div>
                <h5 class="mb-1 text-{{ $tone === 'warning' ? 'warning-emphasis' : $tone }}">{{ $verdict }}</h5>
                <p class="text-muted small mb-3">Based on your page titles and descriptions, product listings and the technical checks.</p>
                <div class="row g-2 text-start small">
                    <div class="col-6"><div class="border rounded p-2"><div class="fw-bold fs-5">{{ $audit['products']['total'] }}</div>Products listed</div></div>
                    <div class="col-6"><div class="border rounded p-2"><div class="fw-bold fs-5">{{ $audit['products']['custom'] }}</div>Custom product SEO</div></div>
                    <div class="col-6"><div class="border rounded p-2"><div class="fw-bold fs-5">{{ $audit['products']['problem_count'] }}</div>Products to improve</div></div>
                    <div class="col-6"><div class="border rounded p-2"><div class="fw-bold fs-5">{{ $audit['pages']['issues'] }}</div>Pages to improve</div></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header"><strong><i class="fa fa-list-check me-1"></i> Checklist</strong></div>
            <ul class="list-group list-group-flush">
                @foreach ($audit['checks'] as $check)
                    <li class="list-group-item d-flex gap-3 align-items-start py-3">
                        <i class="fa {{ $icons[$check['status']][0] }} {{ $icons[$check['status']][1] }} mt-1 fs-5"></i>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $check['label'] }}</div>
                            <div class="small text-muted">{{ $check['detail'] }}</div>
                        </div>
                        @if (! empty($check['link']) && ! empty($check['action']))
                            <a href="{{ $check['link'] }}" @if (str_starts_with($check['link'], 'http') && ! str_starts_with($check['link'], url('/'))) target="_blank" rel="noopener" @else wire:navigate @endif
                                class="btn btn-sm btn-outline-primary text-nowrap">{{ $check['action'] }}</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong><i class="fa fa-file-lines me-1"></i> How your main pages look on Google</strong>
                <a href="{{ route('admin.seo.pages') }}" wire:navigate class="btn btn-sm btn-outline-primary">Edit pages</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light"><tr><th class="ps-3">Page</th><th>Title</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach ($audit['pages']['rows'] as $row)
                            <tr>
                                <td class="ps-3 text-nowrap">{{ $row['label'] }} @if ($row['custom'])<span class="badge text-bg-light border">Custom</span>@endif @if ($row['noindex'])<span class="badge text-bg-danger">noindex</span>@endif</td>
                                <td class="small text-muted">{{ \Illuminate\Support\Str::limit($row['title'], 60) }}</td>
                                <td><span class="badge text-bg-{{ ['good' => 'success', 'warn' => 'warning', 'bad' => 'danger'][$row['status']] }}">{{ ['good' => 'Good', 'warn' => 'Improve', 'bad' => 'Fix'][$row['status']] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong><i class="fa fa-box me-1"></i> Products to improve</strong>
                <a href="{{ route('admin.seo.products', ['filter' => 'issues']) }}" wire:navigate class="btn btn-sm btn-outline-primary">See all {{ $audit['products']['problem_count'] }}</a>
            </div>
            <ul class="list-group list-group-flush">
                @forelse ($audit['products']['problems'] as $problem)
                    <li class="list-group-item small">
                        <div class="fw-semibold">{{ $problem['name'] }}</div>
                        <div class="text-muted">
                            @if (! $problem['has_image'])No photo. @endif
                            @if ($problem['title_rating']['status'] !== 'good')Title: {{ strtolower($problem['title_rating']['message']) }}. @endif
                            @if ($problem['description_rating']['status'] !== 'good')Description: {{ strtolower($problem['description_rating']['message']) }}. @endif
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-success small"><i class="fa fa-circle-check me-1"></i> Every product listing looks good.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
