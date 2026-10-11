<?php
session_start();
include 'db.php';

// Fetch users with unread support messages
$users_query =$conn->query("
    SELECT u.id, u.firstname, u.lastname, u.email, 
           COUNT(c.id) AS unread_count,
           MAX(c.created_at) AS last_activity
    FROM users u
    LEFT JOIN support_chats c ON u.id = c.user_id AND c.is_read = 0 AND c.sender = 'user'
    GROUP BY u.id
    ORDER BY last_activity DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Live Support Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chat-history { height: 400px; overflow-y: auto; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; }
        .user-item.active { background-color: #2c5364; color: white; }
    </style>
</head>
<body class="bg-light p-4">

<div class="container-fluid bg-white p-4 rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0">Admin Support Center</h3>
        <a href="admin_users.php" class="btn btn-secondary btn-sm">User Balances</a>
    </div>

    <div class="row">
        <!-- User List Column -->
        <div class="col-md-4 border-end">
            <h5 class="fw-bold mb-3">User Conversations</h5>
            <div class="list-group">
                <?php if ($users_query &&$users_query->num_rows > 0): ?>
                    <?php while ($u =$users_query->fetch_assoc()): ?>
                        <button onclick="loadUserChat(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['firstname'] . ' ' .$u['lastname']); ?>')" 
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center user-item">
                            <div>
                                <strong><?php echo htmlspecialchars(($u['firstname'] ?? 'User') . ' ' . ($u['lastname'] ?? '')); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($u['email']); ?></small>
                            </div>
                            <?php if ($u['unread_count'] > 0): ?>
                                <span class="badge bg-danger rounded-pill"><?php echo $u['unread_count']; ?> New</span>
                            <?php endif; ?>
                        </button>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted">No active support conversations.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Live Chat Box Column -->
        <div class="col-md-8">
            <h5 class="fw-bold mb-3" id="activeChatTitle">Select a user to start chatting</h5>
            <div class="chat-history mb-3" id="adminChatWindow">
                <p class="text-muted text-center mt-5">Select a conversation from the left menu.</p>
            </div>
            
            <form id="adminReplyForm" onsubmit="sendAdminReply(event)" style="display:none;">
                <div class="input-group">
                    <input type="text" id="adminInput" class="form-control" placeholder="Type reply as Admin..." required>
                    <button type="submit" class="btn btn-primary fw-bold">Send Reply</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let selectedUserId = null;

    function loadUserChat(userId, userName) {
        selectedUserId = userId;
        document.getElementById('activeChatTitle').innerText = 'Chat with ' + userName + ' (User #' + userId + ')';
        document.getElementById('adminReplyForm').style.display = 'block';
        fetchMessages();
    }

    function fetchMessages() {
        if (!selectedUserId) return;
        fetch('process_chat.php?fetch_user_id=' + selectedUserId)
            .then(res => res.json())
            .then(data => {
                const win = document.getElementById('adminChatWindow');
                win.innerHTML = '';
                data.forEach(msg => {
                    const isUser = msg.sender === 'user';
                    win.innerHTML += `
                        <div class="mb-2 ${isUser ? 'text-start' : 'text-end'}">
                            <span class="badge ${isUser ? 'bg-secondary' : 'bg-primary'}">${isUser ? 'User' : 'Admin'}</span>
                            <div class="p-2 rounded mt-1 d-inline-block ${isUser ? 'bg-light border' : 'bg-primary text-white'}">
                                ${msg.message}
                            </div>
                            <br><small class="text-muted">${msg.created_at}</small>
                        </div>
                    `;
                });
                win.scrollTop = win.scrollHeight;
            });
    }

    function sendAdminReply(e) {
        e.preventDefault();
        const input = document.getElementById('adminInput');
        const msg = input.value;
        if (!msg || !selectedUserId) return;

        const formData = new FormData();
        formData.append('message', msg);
        formData.append('sender', 'admin');
        formData.append('user_id', selectedUserId);

        fetch('process_chat.php', { method: 'POST', body: formData })
            .then(() => {
                input.value = '';
                fetchMessages();
            });
    }

    // Auto-refresh chat every 5 seconds for real-time notifications
    setInterval(fetchMessages, 5000);
</script>

</body>
</html>
