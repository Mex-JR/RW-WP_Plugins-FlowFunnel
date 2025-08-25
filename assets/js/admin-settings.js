function addInquiryField() {
    const container = document.getElementById('inquiry-options');
    const div = document.createElement('div');
    div.className = 'flex items-center space-x-2 p-2 rounded-lg bg-gray-50';
    div.innerHTML = `
        <input type="text"
               name="chatwidgetflowfunnel_chat_options[]"
               class="flex-1 rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
               placeholder="Enter inquiry option"
               required>
        <button type="button"
                class="chatwidgetflowfunnel-remove-btn inline-flex items-center p-2 border-transparent rounded-md text-red-600 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>
    `;
    container.appendChild(div);
}

function removeInquiryField(button) {
    if (button && button.parentElement) {
        button.parentElement.remove();
    }
}

// Style options handler
document.addEventListener('DOMContentLoaded', function() {
    const styleOptions = document.querySelectorAll('.style-option');
    const hiddenInput = document.getElementById('selected_style');

    // Set initial selected style
    styleOptions.forEach(option => {
        if (option.dataset.style === hiddenInput.value) {
            option.classList.remove('border-gray-200');
            option.classList.add('border-indigo-500', 'bg-indigo-50');
        }
    });

    // Handle style selection
    styleOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove active class from all options
            styleOptions.forEach(opt => {
                opt.classList.remove('border-indigo-500', 'bg-indigo-50');
                opt.classList.add('border-gray-200');
            });

            // Add active class to clicked option
            this.classList.remove('border-gray-200');
            this.classList.add('border-indigo-500', 'bg-indigo-50');

            // Update hidden input value
            hiddenInput.value = this.dataset.style;
        });
    });

    // Add inquiry field button
    const addBtn = document.getElementById('chatwidgetflowfunnel-add-inquiry');
    if (addBtn) {
        addBtn.addEventListener('click', function() {
            addInquiryField();
        });
    }

    // Delegate remove button clicks for dynamic elements
    const container = document.getElementById('inquiry-options');
    if (container) {
        container.addEventListener('click', function(e) {
            const target = e.target.closest('.chatwidgetflowfunnel-remove-btn');
            if (target) {
                removeInquiryField(target);
            }
        });
    }
});

// Comment out all verification related functions
/*
function verifyWhatsAppNumber() {
    // ... existing verification code ...
}

function updateVerificationStatus() {
    // ... existing status code ...
}

function changeWhatsAppNumber() {
    // ... existing change number code ...
}

function resendVerificationCode() {
    // ... existing resend code ...
}

function verifyCode() {
    // ... existing verify code ...
}
*/
