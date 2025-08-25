function toggleChatWidgetFlowfunnelPopup() {
    const popup = document.getElementById('chatwidgetflowfunnel-popup');
    if (!popup) return;
    if (popup.classList.contains('hidden')) {
        popup.classList.remove('hidden');
        void popup.offsetWidth;
        popup.classList.add('show');
    } else {
        popup.classList.remove('show');
        setTimeout(() => {
            popup.classList.add('hidden');
        }, 300);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const button = document.querySelector('.chatwidgetflowfunnel-button');
    const popup = document.getElementById('chatwidgetflowfunnel-popup');

    if (button) {
        button.addEventListener('click', function() {
            toggleChatWidgetFlowfunnelPopup();
        });
        // keyboard support
        button.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleChatWidgetFlowfunnelPopup();
            }
        });
    }

    // close when clicking outside
    document.addEventListener('click', function(event) {
        if (!popup) return;
        if (!popup.contains(event.target) && !button.contains(event.target) && !popup.classList.contains('hidden')) {
            toggleChatWidgetFlowfunnelPopup();
        }
    });

    // Delegate tracking clicks for options using data attribute
    if (popup) {
        popup.addEventListener('click', function(e) {
            const link = e.target.closest('.chat-option');
            if (link) {
                const option = link.getAttribute('data-chat-option');
                if (option && window.chatwidgetflowfunnelData && window.chatwidgetflowfunnelData.trackingEnabled === 'yes') {
                    jQuery.ajax({
                        url: window.chatwidgetflowfunnelData.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'chatwidgetflowfunnel_track_chat_click',
                            option: option,
                            nonce: window.chatwidgetflowfunnelData.nonce || ''
                        }
                    });
                }
            }
        });
    }
});
