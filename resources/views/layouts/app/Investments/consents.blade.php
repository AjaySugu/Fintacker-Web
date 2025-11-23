<!DOCTYPE html>
<html lang="en">
@php
    $page = 'transactions';
    $common_files = 'main';
@endphp
@include('include.header', ['page' => $page], ['common_files' => $common_files])
<body>
    <button id='consent-btn'>Create consents</button>
</body>
<script>
    
$(document).ready(function() {
    $('#consent-btn').click(function(e) {
        e.preventDefault();
        let csrfToken = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: "/constent/setu/consent", // Laravel route
            type: "POST",
            data: {
                _token: csrfToken,
            },
            success: function(response) {
                console.log(response);
                if (response.status === 200) {
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

</script>
</html>