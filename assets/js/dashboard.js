document.getElementById('messageDropdown')
    .addEventListener('show.bs.dropdown', function () {

        //mark read
    });

document.querySelectorAll('.message-item').forEach(item => {
    item.addEventListener('click', function () {
        const messageId = this.dataset.messageId;

        fetch('mark-read.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'message_id=' + messageId
        });
    });
});

function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('active');
}

// Tutup menu jika pengguna mengklik area luar menu-content
window.onclick = function(event) {
    const menu = document.getElementById('mobileMenu');
    if (event.target == menu) {
        menu.classList.remove('active');
    }
}