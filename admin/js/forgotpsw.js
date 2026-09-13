 function sendOtp() {
            const emailInput = document.getElementById('email');
            const sendBtn = document.getElementById('send-btn');
            const otpWrapper = document.getElementById('otp-wrapper');
            if (emailInput.value.trim() === '') {
                alert('Please enter your email address first.');
                return;
            }
            sendBtn.textContent = 'Sent';
            sendBtn.disabled = true;
            otpWrapper.style.display = 'block';
        }