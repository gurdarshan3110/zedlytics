<div class="container mt-4">
    <div class="chat-box border rounded p-3 chatarea-fixed-height">
        <!-- Display existing notes -->
        @foreach($client->logs as $log)
            <div class="message mb-3 {{ $log->user_id == Auth::id() ? 'text-end' : '' }}">
                <div class="message-content {{ $log->user_id == Auth::id() ? 'bg-primary text-white' : 'bg-secondary text-light' }} rounded p-2 d-inline-block">
                    {{ $log->note }}
                </div>
                <div class="message-info text-muted small font-italic">
                    <span><strong class="font-italic">{{ $log->user_id == Auth::id() ? 'You' : $log->user->name }}</strong></span> <span class="message-time">| {{ $log->created_at->format('d/m/y h:i A') }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <form action="{{ route('clients.notes') }}" method="POST" class="input-group mt-3 d-flex align-items-center chat-input-area" id="chatForm">
        @csrf
        <input type="hidden" name="client_id" value="{{ $client->id }}">
        <textarea class="form-control w-85 input-chat-box" name="note" placeholder="Type a message..." aria-label="Type a message"></textarea>
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="button">
                <i class="fas fa-paperclip"></i> <!-- Attachment icon -->
            </button>
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-paper-plane"></i> <!-- Send icon -->
            </button>
        </div>
    </form>
</div>
@push('jsscript')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('#chatForm');
    const inputBox = document.querySelector('.input-chat-box');
    const attachButton = document.getElementById('attachButton');
    const attachmentInput = document.getElementById('attachment');

    inputBox.focus();

    inputBox.addEventListener('keypress', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.submit();
        }
    });

    attachButton.addEventListener('click', function () {
        attachmentInput.click();
    });
});
</script>
@endpush