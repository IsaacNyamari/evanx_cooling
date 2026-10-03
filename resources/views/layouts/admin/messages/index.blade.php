@extends('layouts.app')

@section('title', 'Customer Messages - ' . config('app.name'))

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <livewire:admin-messages-index />
            </div>
        </div>
    </div>



    <!-- Delete Confirmation Modal -->


    <style>
        .message-row {
            cursor: pointer;
            transition: background 0.2s;
        }

        .border-l {
            border-left: 5px solid red;
        }

        .message-row:hover {
            background: #f8f9fa;
        }

        .table-responsive {
            min-height: 400px;
        }

        .btn-sm {
            margin: 0 2px;
        }

        /* .badge {
                                font-size: 0.8rem;
                                padding: 5px 10px;
                            } */

        #modal-message {
            white-space: pre-wrap;
            word-wrap: break-word;
            max-height: 200px;
            overflow-y: auto;
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.85rem;
            }

            .btn-sm {
                padding: 0.25rem 0.5rem;
            }

            .card-header h5 {
                font-size: 1rem;
            }
        }
    </style>

@endsection
