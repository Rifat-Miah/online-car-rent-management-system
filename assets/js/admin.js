document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.delete-member-btn');
    const messageBox = document.getElementById('memberMessage');

    deleteButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const memberId = this.getAttribute('data-id');
            const csrfToken = this.getAttribute('data-token');

            const confirmed = confirm('Are you sure you want to delete this member?');

            if (!confirmed) {
                return;
            }

            const formData = new FormData();
            formData.append('id', memberId);
            formData.append('csrf_token', csrfToken);

            fetch('index.php?controller=adminMembers&action=deleteMember', {
                method: 'POST',
                body: formData
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    if (data.success) {
                        const row = document.getElementById('member-row-' + memberId);

                        if (row) {
                            row.remove();
                        }

                        showMemberMessage(data.message, 'success');
                    } else {
                        showMemberMessage(data.message, 'error');
                    }
                })
                .catch(function () {
                    showMemberMessage('Something went wrong. Please try again.', 'error');
                });
        });
    });

    function showMemberMessage(message, type) {
        if (!messageBox) {
            return;
        }

        const className = type === 'success' ? 'alert-success' : 'alert-error';

        messageBox.innerHTML = '<div class="' + className + '">' + escapeHtml(message) + '</div>';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});