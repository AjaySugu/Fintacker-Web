
$(document).ready(function() {
    $('#loginBtn').click(function(e) {
        e.preventDefault();

        let email = $('#email').val();
        let password = $('#password').val();
        let csrfToken = $('meta[name="csrf-token"]').attr('content');

        $('#loginBtn').prop('disabled', true).text('Logging in...');

        $.ajax({
            url: "/check", // Laravel route
            type: "POST",
            data: {
                _token: csrfToken,
                email: email,
                password: password
            },
            success: function(response) {
                $('#message').text(response.message).css('color', 'green');
                $('#loginBtn').prop('disabled', false).text('Login');

                // Redirect if login success
                if (response.status === 'success') {
                    window.location.href = response.redirect_url;
                }
            },
            error: function(xhr) {
                $('#message').text(xhr.responseJSON?.message || 'Login failed').css('color', 'red');
                $('#loginBtn').prop('disabled', false).text('Login');
            }
        });
    });
});
