<!DOCTYPE html>
<html lang="en">
@php
    $page = 'login';
@endphp
@include('include.header', ['page' => $page])
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            background: #fff;
        }
        .login-container {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        /* input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        } */
        
        button:disabled {
            background: #ccc;
        }
        .message {
            margin-top: 10px;
            text-align: center;
            font-size: 14px;
        }
    </style>
<body>
    <!-- <input type="text" id="mobile" placeholder="Enter Mobile Number">
    <button id="send-otp">Send OTP</button>
    <br><br>
    <input type="text" id="otp" placeholder="Enter OTP">
    <button id="verify-otp">Verify OTP</button>

    <p id="response"></p> -->
    <section class="page-container">
        <div class="login-support-container">
            <div class="login-page-support-container">
                <p class="label"> Help </p>
                <p class="icon"> ? </p>
            </div>
        </div>
        <div class="login-body-content get-otp" id="get-otp">
            <div class="login-page-header">
                <img src="../assets/images/dummy-logo.jpg" alt="xspend logo" />
                <h4 class="login-logo">Xspend</h4>
            </div>
            <div class="login-page-input-container">
                <p class="main-text">Enter your phone number</p>
                <p class="main-label">We'll send you a text with a verification code.</p>
                <div class="form-split-container">
                    <input type="text" class="countryCode" placeholder="+ 91" readonly>
                    <input type="text" id="mobile" placeholder="Enter Mobile Number">
                </div>
                <button id="send-otp">Get OTP</button>
            </div>
        </div>
        <div class="login-body-content verify-otp" id="verify-otp" style="display:none;">
            <div class="icon-container">
                <i class="fi fi-tr-password-lock"></i>
            </div>
            <div class="login-page-input-container">
                <p class="main-text">Verify OTP</p>
                <p class="main-label">A 4-digit verification code has been sent to <span class="number">+91 9944522049</span> <span class="number-edit">Edit <i class="fi fi-rr-pencil"></i></span></p>

                <div class="otp-input-container">
                    <div id="Otp-inputs" class="inputs">
                        <input class="input" type="text" 
                            inputmode="numeric" maxlength="1" />
                        <input class="input" type="text" 
                            inputmode="numeric" maxlength="1" />
                        <input class="input" type="text" 
                            inputmode="numeric" maxlength="1" />
                        <input class="input" type="text" 
                            inputmode="numeric" maxlength="1" />
                    </div>
                </div>
                <div class="resend-container">
                    <p id="timer-text">Resend OTP in <span id="timer">30</span>s <span id="resend-btn" disabled></span></p>
                </div>
                <button id="verify-otpbtn">Verify OTP</button>
            </div>

        </div>
        <div class="login-page-footer">
            <p>By logging in, you agree to our <span>Terms & Conditions</span></p>
        </div>
    </section>
</body>
<script>
    
$(document).ready(function() {

    // Send OTP
    $('#send-otp').click(function() {
        let mobile = $('#mobile').val();

        if (mobile === '') {
            // $('#response').text("Please enter mobile number!");
            alert("Please enter mobile number!");
            return;
        }

        // let formattedMobile = '+91' + mobile.replace(/^(\+91)?/, ''); 

        $.ajax({
            url: '/auth/login-with-otp',
            method: 'POST',
            data: {
                phone: mobile,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // $('#response').text(response.message);
                alert(response.message);
                document.getElementById('verify-otp').style.display = "block";
                document.getElementById('get-otp').style.display = "none";
                startTimer();
            },
            error: function(xhr) {
                $('#response').text("Error sending OTP");
            }
        });
    });

    // Verify OTP
    $('#otpbtn').click(function() {
        let mobile = $('#mobile').val();
        let otp = '';
        $('#Otp-inputs .input').each(function() {
            otp += $(this).val();
        });

        $.ajax({
            url: '/auth/verify-otp',
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

function saveTokenToServer(tosken) {
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

const inputs = document.getElementById("Otp-inputs");

inputs.addEventListener("input", function (e) {
    const target = e.target;
    const val = target.value;

    if (isNaN(val)) {
        target.value = "";
        return;
    }

    if (val != "") {
        const next = target.nextElementSibling;
        if (next) {
            next.focus();
        }
    }
});

inputs.addEventListener("keyup", function (e) {
    const target = e.target;
    const key = e.key.toLowerCase();

    if (key == "backspace" || key == "delete") {
        target.value = "";
        const prev = target.previousElementSibling;
        if (prev) {
            prev.focus();
        }
        return;
    }
});
let timerDuration = 10; // seconds
  let interval;

  const timerText = document.getElementById("timer-text");
  const resendBtn = document.getElementById("resend-btn");

  function startTimer() {
    clearInterval(interval);
    let timeLeft = timerDuration;

    resendBtn.classList.remove("enabled");
    resendBtn.classList.add("disabled");
    resendBtn.style.cursor = "not-allowed";
    resendBtn.style.color = "gray";

    // reset timer text
    timerText.innerHTML = `Resend OTP in <span id="timer">${timeLeft}</span>s 
      <span id="resend-btn" class="disabled"></span>`;

    const timerDisplay = document.getElementById("timer");
    const newResendBtn = document.getElementById("resend-btn");

    interval = setInterval(() => {
      timeLeft--;
      timerDisplay.textContent = timeLeft;

      if (timeLeft <= 0) {
        clearInterval(interval);
        newResendBtn.classList.add("enabled");
        newResendBtn.classList.remove("disabled");
        newResendBtn.style.cursor = "pointer";
        newResendBtn.style.color = "#007bff";
        timerText.innerHTML = `Didn’t get the code? <span id="resend-btn" class="enabled">Resend OTP</span>`;

        // Add click handler again after enabling
        document.getElementById("resend-btn").addEventListener("click", resendOTP);
      }
    }, 1000);
  }

  function resendOTP() {
    alert("OTP sent again! ✅");
    startTimer(); // restart countdown
  }

//   // start timer on load
//   startTimer();
</script>
</html>
