<div class="container mt-4">
    <div class="chat-box border rounded p-3" style="height: 600px; overflow-y: scroll;">
        <!-- User's Messages -->
        <div class="message mb-3 text-end">
            <div class="message-content bg-primary text-white rounded p-2 d-inline-block">
                This is my message!
            </div>
            <div class="message-info text-muted small font-italic">
                <span><strong class="font-italic">You</strong></span> <span class="message-time">10:00 AM</span>
            </div>
        </div>

        <!-- Other User's Messages -->
        <div class="message mb-3">
            <div class="message-content bg-secondary rounded p-2 d-inline-block text-light">
                This is a message from another user.
            </div>
            <div class="message-info text-muted small font-italic">
                <span><strong>Other User</strong></span> <span class="message-time">10:01 AM</span>
            </div>
        </div>

        <!-- Add more messages here -->
    </div>

    <div class="input-group mt-3">
        <input type="text" class="form-control" placeholder="Type a message..." aria-label="Type a message">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="button">
                <i class="fas fa-paperclip"></i> <!-- Attachment icon -->
            </button>
            <button class="btn btn-primary" type="button">
                <i class="fas fa-paper-plane"></i> <!-- Send icon -->
            </button>
        </div>
    </div>
</div>
