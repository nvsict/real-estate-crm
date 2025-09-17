jQuery(document).ready(function($) {
    // --- Lead List Page ---

    // Handle the inline status change dropdown
    $('body').on('change', '.c360-change-status-dropdown', function() {
        var dropdown = $(this);
        var leadId = dropdown.data('lead-id');
        var statusId = dropdown.val();
        var nonce = $('#c360_change_status_nonce_field_' + leadId).val();

        if (!statusId) return;

        dropdown.prop('disabled', true);

        $.post(ajaxurl, {
            action: 'c360_change_lead_status',
            post_id: leadId,
            status_id: statusId,
            nonce: nonce
        }).done(function(response) {
            if (response.success) {
                var statusText = dropdown.find('option:selected').text();
                dropdown.closest('tr').find('.c360-status-badge').text(statusText);
                dropdown.closest('tr').css('background-color', '#dff0d8').animate({
                    backgroundColor: 'transparent'
                }, 1500);
            } else {
                alert('Failed to update status.');
            }
        }).fail(function() {
            alert('An error occurred.');
        }).always(function() {
            dropdown.prop('disabled', false);
        });
    });

    // Handle the action menu dropdown
    $(document).on('click', function() {
        $('.c360-action-dropdown').hide();
    });

    $('body').on('click', '.c360-action-button', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.c360-action-dropdown').not($(this).next('.c360-action-dropdown')).hide();
        $(this).next('.c360-action-dropdown').toggle();
    });

    // --- Bulk Edit Functionality ---
    $('#bulk-edit').on('click', function() {
        var bulk_row = $('#bulk-edit-row');
        // Clear any existing custom fields
        bulk_row.find('.c360-bulk-field').remove();

        // Clone and add our custom fields
        var status_select = $('#c360-bulk-status').clone().addClass('c360-bulk-field');
        var agent_select = $('#c360-bulk-agent').clone().addClass('c360-bulk-field');
        
        var status_field = $('<div class="inline-edit-col c360-bulk-field"><label class="inline-edit-group"><span class="title">Status</span></label></div>').append(status_select);
        var agent_field = $('<div class="inline-edit-col c360-bulk-field"><label class="inline-edit-group"><span class="title">Agent</span></label></div>').append(agent_select);

        bulk_row.find('.inline-edit-status').after(agent_field);
        bulk_row.find('.inline-edit-status').after(status_field);
    });
});

