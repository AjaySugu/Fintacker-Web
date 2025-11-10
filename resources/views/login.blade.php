<html>
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <input type="text" id="mobile" placeholder="Enter Mobile Number">
    <button id="send-otp">Send OTP</button>
    <br><br>
    <input type="text" id="otp" placeholder="Enter OTP">
    <button id="verify-otp">Verify OTP</button>

    <p id="response"></p>
</body>

<script>
    
$(document).ready(function() {

    // Send OTP
    $('#send-otp').click(function() {
        let mobile = $('#mobile').val();

        if (mobile === '') {
            $('#response').text("Please enter mobile number!");
            return;
        }

        $.ajax({
            url: '/send-otp',
            method: 'POST',
            data: {
                phone: mobile,
                _token: '{{ csrf_token() }}' // If using Laravel
            },
            success: function(response) {
                $('#response').text(response.message);
            },
            error: function(xhr) {
                $('#response').text("Error sending OTP");
            }
        });
    });

    // Verify OTP
    $('#verify-otp').click(function() {
        let mobile = $('#mobile').val();
        let otp = $('#otp').val();

        if (otp === '') {
            $('#response').text("Please enter OTP!");
            return;
        }

        $.ajax({
            url: '/verify-otp',
            method: 'POST',
            data: {
                phone: mobile,
                otp: otp,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#response').text("OTP Verified Successfully!");
                } else {
                    saveTokenToServer("jkdfh");
                    $('#response').text("Invalid OTP. Please try again.");
                }
            },
            error: function(xhr) {
                $('#response').text("Error verifying OTP");
            }
        });
    });

});

document.addEventListener("message", function(event) {
    const data = JSON.parse(event.data);
    if (data.type === 'FCM_TOKEN') {
        console.log("Received Token:", data.token);
        // If user already logged in, send to backend
        saveTokenToServer(data.token);
    }
});

function saveTokenToServer(token) {
    fetch('/save-fcm-token', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ token: token })
    }).then(res => res.json()).then(response => {
        console.log("Token saved", response);
    });
}
</script>
</html>
