function toggleChatWidgetFlowfunnelPopup() {
    const popup = document.getElementById('chatwidgetflowfunnel-popup');
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

document.addEventListener('click', function(event) {
    const popup = document.getElementById('chatwidgetflowfunnel-popup');
    const button = document.querySelector('.chatwidgetflowfunnel-button');
    if (!popup.contains(event.target) && !button.contains(event.target) && !popup.classList.contains('hidden')) {
        toggleChatWidgetFlowfunnelPopup();
    }
});

function trackChatWidgetFlowfunnelClick(option) {
    // Send AJAX request to track click
    if (window.chatwidgetflowfunnelData && window.chatwidgetflowfunnelData.trackingEnabled === 'yes') {
        jQuery.ajax({
            url: window.chatwidgetflowfunnelData.ajaxurl,
            type: 'POST',
            data: {
                action: 'track_chat_click',
                option: option,
                nonce: window.chatwidgetflowfunnelData.nonce || ''
            },
            success: function(response) {
                // Optionally handle response
                // console.log(response);
            }
        });
    }
}
