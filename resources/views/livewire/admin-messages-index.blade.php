<div x-data="messagesManager()" x-init="initMessages(@js($this->getMessages()))" class="card shadow-sm border-0 rounded-3 overflow-hidden">

    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-semibold">
            <i class="fa fa-envelope me-2"></i> Customer Messages
        </h5>
        <span class="badge bg-white text-primary rounded-pill px-3 py-2">
            <i class="fa fa-comments me-1"></i> {{ $this->totalMessages()->count() ?? 0 }} Total Messages
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr class="border-0">
                        <th style="width: 20%" class="ps-3">Name</th>
                        <th style="width: 25%">Email</th>
                        <th style="width: 30%">Message</th>
                        <th style="width: 15%">Date</th>
                        <th style="width: 10%" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->getMessages() ?? [] as $message)
                        <tr class="message-row {{ $message->is_read ? '' : 'fw-bold bg-light-warning' }}"
                            data-message-id="{{ $message->id }}">
                            <td class="ps-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2">
                                        <span class="avatar-initials">{{ substr($message->name, 0, 2) }}</span>
                                    </div>
                                    <span>{{ $message->name }}</span>
                                </div>
                            </td>
                            <td>
                                <a href="mailto:{{ $message->email }}" class="text-decoration-none text-primary">
                                    <i class="fa fa-envelope me-1 fa-sm"></i> {{ Str::limit($message->email, 25) }}
                                </a>
                            </td>
                            <td>
                                <div class="message-preview">
                                    <i class="fa fa-quote-left text-muted me-1 fa-xs"></i>
                                    <span class="text-truncate d-inline-block" style="max-width: 250px;"
                                        title="{{ $message->message }}">
                                        {{ Str::limit($message->message, 60) }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <small class="text-muted">{{ $message->created_at->format('M d, Y') }}</small>
                                    <small class="text-muted-50">{{ $message->created_at->format('h:i A') }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group gap-1">
                                    <button class="btn btn-sm btn-outline-primary view-message"
                                        @click="viewMessage(@js($message))" title="View Message">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-message"
                                        @click="confirmDelete(@js($message))" title="Delete Message">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fa fa-inbox fa-4x text-muted mb-3 d-block"></i>
                                <h5 class="text-muted mb-2">No messages yet</h5>
                                <p class="text-muted small mb-0">When customers send messages from the contact
                                    form,<br>they will appear here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper p-3 bg-light border-top">
            {{ $this->getMessages()->links() }}
        </div>
    </div>

    <!-- Single View Message Modal -->
    <div x-cloak x-show="viewModalOpen" class="modal-overlay" x-transition.opacity @click.away="viewModalOpen = false">
        <div class="bg-light p-4 modal-container modal-lg" @click.stop>
            <div class="modal-content shadow-lg p-4">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title">
                        <i class="fa fa-envelope-open-text me-2"></i> Message Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" @click="viewModalOpen = false"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-primary mb-1">
                                <i class="fa fa-user me-1"></i> From:
                            </label>
                            <p class="mb-0" x-text="selectedMessage.name"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-primary mb-1">
                                <i class="fa fa-calendar me-1"></i> Date:
                            </label>
                            <p class="mb-0" x-text="selectedMessage.formatted_date"></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-primary mb-1">
                                <i class="fa fa-envelope me-1"></i> Email:
                            </label>
                            <p class="mb-0">
                                <a :href="'mailto:' + selectedMessage.email" class="text-decoration-none"
                                    x-text="selectedMessage.email"></a>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-primary mb-1">
                                <i class="fa fa-phone me-1"></i> Phone:
                            </label>
                            <p class="mb-0">
                                <template x-if="selectedMessage.phone">
                                    <a :href="'tel:' + selectedMessage.phone" class="text-decoration-none"
                                        x-text="selectedMessage.phone"></a>
                                </template>
                                <span x-show="!selectedMessage.phone" class="text-muted">Not provided</span>
                            </p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-primary mb-1">
                            <i class="fa fa-tag me-1"></i> Subject:
                        </label>
                        <p class="mb-0">
                            <span class="badge bg-info" x-text="selectedMessage.subject"></span>
                        </p>
                    </div>
                    <div class="mb-0">
                        <label class="fw-bold text-primary mb-2">
                            <i class="fa fa-comment me-1"></i> Message:
                        </label>
                        <div class="bg-light p-3 rounded-3 border-start border-primary border-4"
                            x-html="selectedMessage.formatted_message"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-between">
                    <button type="button" class="btn btn-secondary" @click="viewModalOpen = false">
                        <i class="fa fa-times me-1"></i> Close
                    </button>
                    <a :href="'mailto:' + selectedMessage.email + '?subject=Re: ' + selectedMessage.subject"
                        class="btn btn-primary">
                        <i class="fa fa-reply me-1"></i> Reply
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Single Delete Confirmation Modal -->
    <div x-cloak x-show="deleteModalOpen" class="modal-overlay" x-transition.opacity
        @click.away="deleteModalOpen = false">
        <div class="modal-container modal-sm" @click.stop>
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title">
                        <i class="fa fa-trash me-2"></i> Delete Message
                    </h5>
                    <button type="button" class="btn-close btn-close-white"
                        @click="deleteModalOpen = false"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fa fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <p class="mb-2">Are you sure you want to delete this message?</p>
                    <p class="text-muted small mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary px-4" @click="deleteModalOpen = false">
                        <i class="fa fa-times me-1"></i> Cancel
                    </button>
                    <form :action="deleteUrl" method="POST" class="d-inline">
                        @csrf
                        @method('post')
                        <button type="submit" class="btn btn-danger px-4">
                            <i class="fa fa-trash me-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <style>
        /* Avatar Styles */
        .avatar-circle {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #0d6efd, #0099ff);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-initials {
            color: white;
            font-size: 12px;
            font-weight: 600;
        }

        /* Message Preview */
        .message-preview {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Table Row Styles */
        .bg-light-warning {
            background-color: #fff3cd !important;
        }

        .message-row {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .message-row:hover {
            background-color: #e7f1ff !important;
            transform: translateX(2px);
        }

        /* Table Styles */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 8px;
        }

        .table tbody td {
            vertical-align: middle;
            padding: 14px 8px;
        }

        /* Button Styles */
        .btn-group {
            display: flex;
            gap: 5px;
        }

        .btn-outline-primary,
        .btn-outline-danger {
            border-radius: 6px;
            transition: all 0.2s ease;
            padding: 5px 10px;
            font-size: 13px;
        }

        .btn-outline-primary:hover {
            background-color: #0d6efd;
            border-color: #0d6efd;
            transform: translateY(-1px);
        }

        .btn-outline-danger:hover {
            background-color: #dc3545;
            border-color: #dc3545;
            transform: translateY(-1px);
        }

        /* Modal Overlay and Container */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1050;
        }

        .modal-container {
            background: transparent;
            max-width: 90%;
            width: 100%;
        }

        .modal-container.modal-sm {
            max-width: 400px;
        }

        .modal-container.modal-lg {
            max-width: 800px;
        }

        .modal-content {
            border: none;
            border-radius: 12px;
        }

        .modal-header {
            border-radius: 12px 12px 0 0;
        }

        /* Pagination Styles */
        .pagination-wrapper {
            border-top: 1px solid #dee2e6;
        }

        .pagination-wrapper nav {
            display: flex;
            justify-content: center;
        }

        .pagination-wrapper .pagination {
            margin-bottom: 0;
            gap: 5px;
        }

        .pagination-wrapper .page-item .page-link {
            border-radius: 8px;
            color: #0d6efd;
            border: 1px solid #dee2e6;
            padding: 8px 14px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .pagination-wrapper .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
        }

        .pagination-wrapper .page-item:not(.active) .page-link:hover {
            background-color: #e7f1ff;
            border-color: #0d6efd;
            transform: translateY(-1px);
        }

        /* Alpine.js x-cloak */
        [x-cloak] {
            display: none !important;
        }

        /* Transitions */
        .modal-overlay {
            transition: opacity 0.2s ease;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .table thead th {
                font-size: 11px;
                padding: 10px 5px;
            }

            .table tbody td {
                padding: 10px 5px;
                font-size: 13px;
            }

            .avatar-circle {
                width: 28px;
                height: 28px;
            }

            .avatar-initials {
                font-size: 10px;
            }

            .btn-outline-primary,
            .btn-outline-danger {
                padding: 4px 8px;
                font-size: 11px;
            }

            .modal-container.modal-lg {
                max-width: 95%;
            }

            .modal-body {
                padding: 20px;
            }
        }

        @media (max-width: 576px) {
            .table thead th {
                font-size: 10px;
            }

            .table tbody td {
                font-size: 12px;
            }

            .text-truncate {
                max-width: 120px !important;
            }

            .btn-group {
                flex-direction: column;
                gap: 3px;
            }

            .modal-footer {
                flex-direction: column;
                gap: 10px;
            }

            .modal-footer .btn {
                width: 100%;
            }

            .modal-container.modal-sm {
                max-width: 90%;
            }
        }
    </style>

    <script>
        function messagesManager() {
            return {
                viewModalOpen: false,
                deleteModalOpen: false,
                selectedMessage: {
                    id: null,
                    name: '',
                    email: '',
                    phone: '',
                    subject: '',
                    message: '',
                    formatted_date: '',
                    formatted_message: ''
                },
                deleteUrl: '',

                initMessages(messages) {
                    this.messages = messages;
                },

                viewMessage(message) {
                    this.selectedMessage = {
                        id: message.id,
                        name: message.name,
                        email: message.email,
                        phone: message.phone || null,
                        subject: message.subject,
                        message: message.message,
                        formatted_date: new Date(message.created_at).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }),
                        formatted_message: message.message.replace(/\n/g, '<br>')
                    };

                    this.viewModalOpen = true;

                    // Mark as read via Livewire
                    if (!message.is_read) {
                        @this.call('markMessageRead', message.id);
                        // Update row style locally
                        const row = document.querySelector(`tr[data-message-id="${message.id}"]`);
                        if (row) {
                            row.classList.remove('fw-bold', 'bg-light-warning');
                        }
                    }
                },

                confirmDelete(message) {
                    this.selectedMessage = message;
                    this.deleteUrl = '{{ url('/admin/messages') }}/' + message.id;
                    this.deleteModalOpen = true;
                },

                closeModals() {
                    this.viewModalOpen = false;
                    this.deleteModalOpen = false;
                }
            }
        }

        // Make entire row clickable
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.message-row').forEach(row => {
                row.addEventListener('click', function(e) {
                    if (!e.target.closest('.btn-group') && !e.target.closest('.btn')) {
                        const viewBtn = this.querySelector('.view-message');
                        if (viewBtn && viewBtn.__x) {
                            // Trigger Alpine click handler
                            viewBtn.click();
                        }
                    }
                });
            });
        });
    </script>
</div>
