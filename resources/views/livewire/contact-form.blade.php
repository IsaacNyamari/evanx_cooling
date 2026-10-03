<form wire:submit.prevent="sendMessage" method="POST" class="text-dark">
    @csrf
    <div class="row g-3">
        <div class="col-12 mb-3">
            @session('success')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <strong>Success!</strong> {{ session('success') }}
                </div>
            @endsession
            @session('error')
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <strong>Error!</strong> {{ session('error') }}
                </div>
            @endsession
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" wire:model="name" id="name"
                    placeholder="Your Name">
                <label for="name">Your Name</label>
                @error('name')
                    <p class="form-text text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="email" class="form-control" wire:model="email" id="email"
                    placeholder="Your Email">
                <label for="email">Your Email</label>
                @error('email')
                    <p class="form-text text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="col-12">
            <div class="form-floating">
                <input type="text" class="form-control" wire:model="phone" id="phone"
                    placeholder="Your Phone">
                <label for="phone">Your Phone</label>
                @error('phone')
                    <p class="form-text text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="col-12">
            <div class="form-floating">
                <input type="text" class="form-control" wire:model="subject" id="subject"
                    placeholder="Subject">
                <label for="subject">Subject</label>
                @error('subject')
                    <p class="form-text text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="col-12">
            <div class="form-floating">
                <textarea class="form-control" wire:model="message" placeholder="Leave a message here" id="message"
                    style="height: 100px"></textarea>
                <label for="message">Message</label>
                
            </div>@error('message')
                    <p class="form-text text-danger">{{ $message }}</p>
                @enderror
        </div>
        <div class="col-12">
            <button class="btn btn-primary py-3 px-5" type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove>Send Message</span>
                <span wire:loading>
                    <i class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></i>
                    Sending...
                </span>
            </button>
        </div>
    </div>
</form>
