<?php
phpinfo();
?>
/* Professional emoji styling - makes them look like real icons */
.emoji, .icon {
    font-family: 'Segoe UI Emoji', 'Apple Color Emoji', 'Noto Color Emoji', 'Android Emoji', 'EmojiOne Color', sans-serif;
    font-style: normal;
    font-weight: normal;
    display: inline-block;
    vertical-align: middle;
    margin-right: 5px;
}

/* Icon buttons with emoji */
.icon-btn {
    background: linear-gradient(135deg, #f5f5f5, #e0e0e0);
    border: none;
    padding: 10px 20px;
    border-radius: 50px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s ease;
}

.icon-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Colored icon backgrounds */
.icon-primary { background: linear-gradient(135deg, #3498db, #2980b9); color: white; }
.icon-success { background: linear-gradient(135deg, #27ae60, #219a52); color: white; }
.icon-warning { background: linear-gradient(135deg, #f39c12, #e67e22); color: white; }
.icon-danger { background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; }

/* Floating animation for QR codes */
@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0px); }
}

.qr-float {
    animation: float 3s ease-in-out infinite;
}