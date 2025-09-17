<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// --- META BOX REGISTRATION HOOK ---
function c360_add_all_metaboxes() {
    // Lead Details
    add_meta_box( 'c360_lead_basic_details', __( 'Basic Lead Information', 'client360-crm' ), 'c360_lead_basic_details_metabox_html', 'lead', 'normal', 'high' );
    add_meta_box( 'c360_lead_source_details', __( 'Lead Source and Details', 'client360-crm' ), 'c360_lead_source_details_metabox_html', 'lead', 'normal', 'default' );
    add_meta_box( 'c360_lead_assignment_details', __( 'Lead Assignment and Ownership', 'client360-crm' ), 'c360_lead_assignment_details_metabox_html', 'lead', 'side', 'default' );
    add_meta_box( 'c360_lead_dates_details', __( 'Lead Dates and Follow-up', 'client360-crm' ), 'c360_lead_dates_details_metabox_html', 'lead', 'side', 'default' );
    add_meta_box( 'c360_lead_scoring_details', __( 'Lead Scoring and Nurturing', 'client360-crm' ), 'c360_lead_scoring_details_metabox_html', 'lead', 'side', 'low' );

    // Contact Details
    add_meta_box( 'c360_contact_main_details', __( 'Contact Information', 'client360-crm' ), 'c360_contact_main_details_metabox_html', 'contact', 'normal', 'high' );
    add_meta_box( 'c360_contact_address_details', __( 'Address Information', 'client360-crm' ), 'c360_contact_address_details_metabox_html', 'contact', 'normal', 'default' );
    add_meta_box( 'c360_contact_source_details', __( 'Lead Source Information', 'client360-crm' ), 'c360_contact_source_details_metabox_html', 'contact', 'normal', 'default' );
    add_meta_box( 'c360_contact_classification_details', __( 'Status and Classifications', 'client360-crm' ), 'c360_contact_classification_details_metabox_html', 'contact', 'normal', 'default' );
    add_meta_box( 'c360_contact_additional_info', __( 'Additional Information', 'client360-crm' ), 'c360_contact_additional_info_metabox_html', 'contact', 'normal', 'low' );
    add_meta_box( 'c360_contact_social_media', __( 'Social Media', 'client360-crm' ), 'c360_contact_social_media_metabox_html', 'contact', 'side', 'default' );
    add_meta_box( 'c360_contact_assignment', __( 'Assignment & Notes', 'client360-crm' ), 'c360_contact_assignment_metabox_html', 'contact', 'side', 'default' );
    
    // Campaign Details
    add_meta_box( 'c360_campaign_details', __( 'Campaign Details', 'client360-crm' ), 'c360_campaign_details_metabox_html', 'campaign', 'normal', 'high' );
    
    // Property Details
    add_meta_box( 'c360_property_basic_info', __( 'Basic Property Information', 'client360-crm' ), 'c360_property_basic_info_metabox_html', 'property', 'normal', 'high' );
    add_meta_box( 'c360_property_details', __( 'Property Details', 'client360-crm' ), 'c360_property_details_metabox_html', 'property', 'normal', 'high' );
    add_meta_box( 'c360_property_amenities', __( 'Amenities', 'client360-crm' ), 'c360_property_amenities_metabox_html', 'property', 'normal', 'default' );
    add_meta_box( 'c360_property_availability', __( 'Available For', 'client360-crm' ), 'c360_property_availability_metabox_html', 'property', 'normal', 'default' );
    add_meta_box( 'c360_property_listing_details', __( 'Listing Details', 'client360-crm' ), 'c360_property_listing_details_metabox_html', 'property', 'side', 'default' );
    add_meta_box( 'c360_property_dealer_details', __( 'Dealer Details', 'client360-crm' ), 'c360_property_dealer_details_metabox_html', 'property', 'side', 'default' );
    add_meta_box( 'c360_property_media', __( 'Media & Metadata', 'client360-crm' ), 'c360_property_media_metabox_html', 'property', 'side', 'low' );

    // Task Details
    add_meta_box( 'c360_task_details', __( 'Task Details', 'client360-crm' ), 'c360_task_details_metabox_html', 'task', 'normal', 'high' );
    
    // Log Details
    add_meta_box( 'c360_meeting_details', __( 'Meeting Details', 'client360-crm' ), 'c360_meeting_details_metabox_html', 'meeting_log', 'normal', 'high' );
    add_meta_box( 'c360_call_details', __( 'Call Details', 'client360-crm' ), 'c360_call_details_metabox_html', 'call_log', 'normal', 'high' );
    add_meta_box( 'c360_email_details', __( 'Email Details', 'client360-crm' ), 'c360_email_details_metabox_html', 'email_log', 'normal', 'high' );

    // Payment Details
    add_meta_box( 'c360_payment_details', __( 'Payment Details', 'client360-crm' ), 'c360_payment_details_metabox_html', 'payment', 'normal', 'high' );
    // Document Details
    add_meta_box( 'c360_document_details', __( 'Document Details', 'client360-crm' ), 'c360_document_details_metabox_html', 'document', 'normal', 'high' );
    
    // Related Activity History
    add_meta_box( 'c360_related_activity', __( 'Activity History', 'client360-crm' ), 'c360_related_activity_metabox_html', array('lead', 'contact'), 'normal', 'low' );

}
add_action( 'add_meta_boxes', 'c360_add_all_metaboxes' );


// --- HTML RENDERER FUNCTIONS ---

function c360_lead_basic_details_metabox_html( $post ) {
    wp_nonce_field( 'c360_save_lead_details', 'c360_lead_details_nonce' );
    $email = get_post_meta( $post->ID, '_lead_email', true );
    $phone = get_post_meta( $post->ID, '_lead_phone', true );
    $address = get_post_meta( $post->ID, '_lead_address', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="post_title"><?php _e( 'Lead Name', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="post_title" name="post_title" class="regular-text" value="<?php echo esc_attr( $post->post_title ); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_lead_email"><?php _e( 'Lead Email', 'client360-crm' ); ?></label></th>
            <td><input type="email" id="c360_lead_email" name="c360_lead_email" class="regular-text" value="<?php echo esc_attr( $email ); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_lead_phone"><?php _e( 'Lead Phone Number', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_lead_phone" name="c360_lead_phone" class="regular-text" value="<?php echo esc_attr( $phone ); ?>"></td>
        </tr>
         <tr>
            <th><label for="c360_lead_address"><?php _e( 'Lead Address', 'client360-crm' ); ?></label></th>
            <td><textarea id="c360_lead_address" name="c360_lead_address" class="large-text"><?php echo esc_textarea( $address ); ?></textarea></td>
        </tr>
    </table>
    <?php
}

function c360_lead_source_details_metabox_html( $post ) {
    $source = get_post_meta( $post->ID, '_lead_source', true );
    $source_details = get_post_meta( $post->ID, '_lead_source_details', true );
    $campaign = get_post_meta( $post->ID, '_lead_campaign', true );
    $channel = get_post_meta( $post->ID, '_lead_source_channel', true );
    $medium = get_post_meta( $post->ID, '_lead_source_medium', true );
    $source_campaign = get_post_meta( $post->ID, '_lead_source_campaign', true );
    $referral = get_post_meta( $post->ID, '_lead_source_referral', true );

    $current_status_terms = wp_get_object_terms( $post->ID, 'lead_status', array('fields' => 'ids') );
    $current_status = !empty($current_status_terms) ? $current_status_terms[0] : '';
    ?>
    <table class="form-table">
         <tr>
            <th><label for="c360_lead_status"><?php _e( 'Lead Status', 'client360-crm' ); ?></label></th>
            <td>
                <?php
                wp_dropdown_categories( array(
                    'taxonomy' => 'lead_status',
                    'name' => 'c360_lead_status',
                    'selected' => $current_status,
                    'show_option_none' => __('Select Status', 'client360-crm'),
                    'hierarchical' => true,
                    'hide_empty' => false,
                ) );
                ?>
            </td>
        </tr>
        <tr>
            <th><label for="c360_lead_source"><?php _e( 'Lead Source', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_lead_source" name="c360_lead_source" class="regular-text" value="<?php echo esc_attr( $source ); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_lead_source_details"><?php _e( 'Lead Source Details', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_lead_source_details" name="c360_lead_source_details" class="regular-text" value="<?php echo esc_attr( $source_details ); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_lead_campaign"><?php _e( 'Lead Campaign', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_lead_campaign" name="c360_lead_campaign" class="regular-text" value="<?php echo esc_attr( $campaign ); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_lead_source_channel"><?php _e( 'Lead Source Channel', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_lead_source_channel" name="c360_lead_source_channel" class="regular-text" value="<?php echo esc_attr( $channel ); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_lead_source_medium"><?php _e( 'Lead Source Medium', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_lead_source_medium" name="c360_lead_source_medium" class="regular-text" value="<?php echo esc_attr( $medium ); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_lead_source_campaign"><?php _e( 'Lead Source Campaign', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_lead_source_campaign" name="c360_lead_source_campaign" class="regular-text" value="<?php echo esc_attr( $source_campaign ); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_lead_source_referral"><?php _e( 'Lead Source Referral', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_lead_source_referral" name="c360_lead_source_referral" class="regular-text" value="<?php echo esc_attr( $referral ); ?>"></td>
        </tr>
    </table>
    <?php
}

function c360_lead_assignment_details_metabox_html($post) {
    $assigned_agent = get_post_meta( $post->ID, '_lead_assigned_agent', true );
    ?>
    <p>
        <label for="c360_lead_assigned_agent"><strong><?php _e( 'Lead Assigned Agent', 'client360-crm' ); ?></strong></label>
        <br>
        <?php
        wp_dropdown_users(array(
            'name' => 'c360_lead_assigned_agent',
            'selected' => $assigned_agent,
            'show_option_none' => __('Select Agent', 'client360-crm'),
            'role__in' => array('administrator', 'c360_employee'),
            'style' => 'width: 95%;'
        ));
        ?>
    </p>
     <p>
        <strong><?php _e( 'Lead Owner', 'client360-crm' ); ?>:</strong>
        <?php
        $author = get_the_author_meta('display_name', $post->post_author);
        echo esc_html($author);
        ?>
    </p>
    <?php
}

function c360_lead_dates_details_metabox_html($post) {
    $conversion_date = get_post_meta( $post->ID, '_lead_conversion_date', true );
    $followup_date = get_post_meta( $post->ID, '_lead_followup_date', true );
    $followup_status = get_post_meta( $post->ID, '_lead_followup_status', true );
    ?>
    <p><strong><?php _e('Creation Date:', 'client360-crm'); ?></strong> <?php echo esc_html($post->post_date); ?></p>
    <p><label for="c360_lead_conversion_date"><?php _e( 'Conversion Date', 'client360-crm' ); ?></label></p>
    <p><input type="date" id="c360_lead_conversion_date" name="c360_lead_conversion_date" value="<?php echo esc_attr($conversion_date); ?>" style="width: 95%;"></p>
    <p><label for="c360_lead_followup_date"><?php _e( 'Follow-up Date', 'client360-crm' ); ?></label></p>
    <p><input type="date" id="c360_lead_followup_date" name="c360_lead_followup_date" value="<?php echo esc_attr($followup_date); ?>" style="width: 95%;"></p>
    <p><label for="c360_lead_followup_status"><?php _e( 'Follow-up Status', 'client360-crm' ); ?></label></p>
    <p><input type="text" id="c360_lead_followup_status" name="c360_lead_followup_status" value="<?php echo esc_attr($followup_status); ?>" style="width: 95%;"></p>
    <?php
}

function c360_lead_scoring_details_metabox_html($post) {
    $score = get_post_meta( $post->ID, '_lead_score', true );
    $nurturing_workflow = get_post_meta( $post->ID, '_lead_nurturing_workflow', true );
    $engagement_level = get_post_meta( $post->ID, '_lead_engagement_level', true );
    $conversion_rate = get_post_meta( $post->ID, '_lead_conversion_rate', true );
    $nurturing_stage = get_post_meta( $post->ID, '_lead_nurturing_stage', true );
    $next_action = get_post_meta( $post->ID, '_lead_next_action', true );
    ?>
    <p><label for="c360_lead_score"><?php _e( 'Lead Score', 'client360-crm' ); ?></label></p>
    <p><input type="number" id="c360_lead_score" name="c360_lead_score" value="<?php echo esc_attr($score); ?>" style="width: 95%;"></p>
    <p><label for="c360_lead_nurturing_workflow"><?php _e( 'Nurturing Workflow', 'client360-crm' ); ?></label></p>
    <p><input type="text" id="c360_lead_nurturing_workflow" name="c360_lead_nurturing_workflow" value="<?php echo esc_attr($nurturing_workflow); ?>" style="width: 95%;"></p>
     <p><label for="c360_lead_engagement_level"><?php _e( 'Engagement Level', 'client360-crm' ); ?></label></p>
    <p><input type="text" id="c360_lead_engagement_level" name="c360_lead_engagement_level" value="<?php echo esc_attr($engagement_level); ?>" style="width: 95%;"></p>
    <p><label for="c360_lead_conversion_rate"><?php _e( 'Conversion Rate (%)', 'client360-crm' ); ?></label></p>
    <p><input type="number" id="c360_lead_conversion_rate" name="c360_lead_conversion_rate" value="<?php echo esc_attr($conversion_rate); ?>" style="width: 95%;"></p>
    <p><label for="c360_lead_nurturing_stage"><?php _e( 'Nurturing Stage', 'client360-crm' ); ?></label></p>
    <p><input type="text" id="c360_lead_nurturing_stage" name="c360_lead_nurturing_stage" value="<?php echo esc_attr($nurturing_stage); ?>" style="width: 95%;"></p>
    <p><label for="c360_lead_next_action"><?php _e( 'Next Action', 'client360-crm' ); ?></label></p>
    <p><input type="text" id="c360_lead_next_action" name="c360_lead_next_action" value="<?php echo esc_attr($next_action); ?>" style="width: 95%;"></p>
    <?php
}

function c360_contact_main_details_metabox_html( $post ) {
    wp_nonce_field( 'c360_save_contact_details', 'c360_contact_details_nonce' );
    $first_name = get_post_meta( $post->ID, '_contact_first_name', true );
    $last_name = get_post_meta( $post->ID, '_contact_last_name', true );
    $title = get_post_meta( $post->ID, '_contact_title', true );
    $email = get_post_meta( $post->ID, '_contact_email', true );
    $phone = get_post_meta( $post->ID, '_contact_phone', true );
    $mobile = get_post_meta( $post->ID, '_contact_mobile', true );
    $contact_method = get_post_meta( $post->ID, '_contact_preferred_method', true );
    ?>
     <table class="form-table">
        <tr>
            <th><label for="c360_contact_first_name"><?php _e( 'First Name', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_contact_first_name" name="_contact_first_name" class="regular-text" value="<?php echo esc_attr( $first_name ); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_contact_last_name"><?php _e( 'Last Name', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_contact_last_name" name="_contact_last_name" class="regular-text" value="<?php echo esc_attr( $last_name ); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_contact_title"><?php _e( 'Title', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_contact_title" name="_contact_title" class="regular-text" value="<?php echo esc_attr( $title ); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_contact_email"><?php _e( 'Email', 'client360-crm' ); ?></label></th>
            <td><input type="email" id="c360_contact_email" name="_contact_email" class="regular-text" value="<?php echo esc_attr( $email ); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_contact_phone"><?php _e( 'Phone Number', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_contact_phone" name="_contact_phone" class="regular-text" value="<?php echo esc_attr( $phone ); ?>"></td>
        </tr>
         <tr>
            <th><label for="c360_contact_mobile"><?php _e( 'Mobile Number', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_contact_mobile" name="_contact_mobile" class="regular-text" value="<?php echo esc_attr( $mobile ); ?>"></td>
        </tr>
         <tr>
            <th><label for="c360_contact_preferred_method"><?php _e( 'Preferred Contact Method', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_contact_preferred_method" name="_contact_preferred_method" class="regular-text" value="<?php echo esc_attr( $contact_method ); ?>"></td>
        </tr>
    </table>
    <?php
}

function c360_contact_address_details_metabox_html($post) {
    $physical = get_post_meta( $post->ID, '_contact_physical_address', true );
    $mailing = get_post_meta( $post->ID, '_contact_mailing_address', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="c360_contact_physical_address"><?php _e( 'Physical Address', 'client360-crm' ); ?></label></th>
            <td><textarea id="c360_contact_physical_address" name="_contact_physical_address" class="large-text"><?php echo esc_textarea( $physical ); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="c360_contact_mailing_address"><?php _e( 'Mailing Address', 'client360-crm' ); ?></label></th>
            <td><textarea id="c360_contact_mailing_address" name="_contact_mailing_address" class="large-text"><?php echo esc_textarea( $mailing ); ?></textarea></td>
        </tr>
    </table>
    <?php
}

function c360_contact_source_details_metabox_html($post) {
    $source = get_post_meta( $post->ID, '_contact_lead_source', true );
    $referral = get_post_meta( $post->ID, '_contact_referral_source', true );
    $campaign = get_post_meta( $post->ID, '_contact_campaign_source', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="c360_contact_lead_source"><?php _e( 'Lead Source', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_contact_lead_source" name="_contact_lead_source" class="regular-text" value="<?php echo esc_attr( $source ); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_contact_referral_source"><?php _e( 'Referral Source', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_contact_referral_source" name="_contact_referral_source" class="regular-text" value="<?php echo esc_attr( $referral ); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_contact_campaign_source"><?php _e( 'Campaign Source', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_contact_campaign_source" name="_contact_campaign_source" class="regular-text" value="<?php echo esc_attr( $campaign ); ?>"></td>
        </tr>
    </table>
    <?php
}

function c360_contact_classification_details_metabox_html($post) {
    $status = get_post_meta( $post->ID, '_contact_lead_status', true );
    $rating = get_post_meta( $post->ID, '_contact_lead_rating', true );
    $probability = get_post_meta( $post->ID, '_contact_conversion_probability', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="c360_contact_lead_status"><?php _e( 'Lead Status (if applicable)', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_contact_lead_status" name="_contact_lead_status" class="regular-text" value="<?php echo esc_attr( $status ); ?>"></td>
        </tr>
         <tr>
            <th><label for="c360_contact_lead_rating"><?php _e( 'Lead Rating', 'client360-crm' ); ?></label></th>
            <td><input type="number" id="c360_contact_lead_rating" name="_contact_lead_rating" class="small-text" value="<?php echo esc_attr( $rating ); ?>"></td>
        </tr>
         <tr>
            <th><label for="c360_contact_conversion_probability"><?php _e( 'Lead Conversion Probability (%)', 'client360-crm' ); ?></label></th>
            <td><input type="number" id="c360_contact_conversion_probability" name="_contact_conversion_probability" class="small-text" value="<?php echo esc_attr( $probability ); ?>"></td>
        </tr>
    </table>
    <?php
}

function c360_contact_additional_info_metabox_html($post) {
    $birthday = get_post_meta( $post->ID, '_contact_birthday', true );
    $anniversary = get_post_meta( $post->ID, '_contact_anniversary', true );
    $milestones = get_post_meta( $post->ID, '_contact_key_milestones', true );
    $occupation = get_post_meta( $post->ID, '_contact_occupation', true );
    $hobbies = get_post_meta( $post->ID, '_contact_hobbies', true );
    $gender = get_post_meta( $post->ID, '_contact_gender', true );
    $dob = get_post_meta( $post->ID, '_contact_dob', true );
    $frequency = get_post_meta( $post->ID, '_contact_communication_frequency', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="c360_contact_birthday"><?php _e( 'Birthday', 'client360-crm' ); ?></label></th>
            <td><input type="date" id="c360_contact_birthday" name="_contact_birthday" value="<?php echo esc_attr( $birthday ); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_contact_anniversary"><?php _e( 'Anniversary', 'client360-crm' ); ?></label></th>
            <td><input type="date" id="c360_contact_anniversary" name="_contact_anniversary" value="<?php echo esc_attr( $anniversary ); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_contact_key_milestones"><?php _e( 'Key Milestones', 'client360-crm' ); ?></label></th>
            <td><textarea id="c360_contact_key_milestones" name="_contact_key_milestones" class="large-text"><?php echo esc_textarea( $milestones ); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="c360_contact_occupation"><?php _e( 'Occupation', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_contact_occupation" name="_contact_occupation" class="regular-text" value="<?php echo esc_attr( $occupation ); ?>"></td>
        </tr>
         <tr>
            <th><label for="c360_contact_hobbies"><?php _e( 'Interests or Hobbies', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_contact_hobbies" name="_contact_hobbies" class="regular-text" value="<?php echo esc_attr( $hobbies ); ?>"></td>
        </tr>
        <tr>
            <th><label><?php _e( 'Gender', 'client360-crm' ); ?></label></th>
            <td>
                <label><input type="radio" name="_contact_gender" value="male" <?php checked($gender, 'male'); ?>> Male</label><br>
                <label><input type="radio" name="_contact_gender" value="female" <?php checked($gender, 'female'); ?>> Female</label><br>
                <label><input type="radio" name="_contact_gender" value="other" <?php checked($gender, 'other'); ?>> Other</label>
            </td>
        </tr>
         <tr>
            <th><label for="c360_contact_dob"><?php _e( 'Date of Birth', 'client360-crm' ); ?></label></th>
            <td><input type="date" id="c360_contact_dob" name="_contact_dob" value="<?php echo esc_attr( $dob ); ?>"></td>
        </tr>
         <tr>
            <th><label for="c360_contact_communication_frequency"><?php _e( 'Communication Frequency', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_contact_communication_frequency" name="_contact_communication_frequency" class="regular-text" value="<?php echo esc_attr( $frequency ); ?>"></td>
        </tr>
    </table>
    <?php
}

function c360_contact_social_media_metabox_html($post) {
    $linkedin = get_post_meta( $post->ID, '_contact_linkedin', true );
    $facebook = get_post_meta( $post->ID, '_contact_facebook', true );
    $twitter = get_post_meta( $post->ID, '_contact_twitter', true );
    $other = get_post_meta( $post->ID, '_contact_other_social', true );
    ?>
    <p><label for="c360_contact_linkedin"><?php _e( 'LinkedIn Profile URL', 'client360-crm' ); ?></label></p>
    <p><input type="url" id="c360_contact_linkedin" name="_contact_linkedin" value="<?php echo esc_attr($linkedin); ?>" style="width: 95%;"></p>
     <p><label for="c360_contact_facebook"><?php _e( 'Facebook Profile URL', 'client360-crm' ); ?></label></p>
    <p><input type="url" id="c360_contact_facebook" name="_contact_facebook" value="<?php echo esc_attr($facebook); ?>" style="width: 95%;"></p>
     <p><label for="c360_contact_twitter"><?php _e( 'Twitter Handle', 'client360-crm' ); ?></label></p>
    <p><input type="text" id="c360_contact_twitter" name="_contact_twitter" value="<?php echo esc_attr($twitter); ?>" style="width: 95%;"></p>
     <p><label for="c360_contact_other_social"><?php _e( 'Other Social Media URL', 'client360-crm' ); ?></label></p>
    <p><input type="url" id="c360_contact_other_social" name="_contact_other_social" value="<?php echo esc_attr($other); ?>" style="width: 95%;"></p>
    <?php
}


function c360_contact_assignment_metabox_html($post) {
     $assigned_agent = get_post_meta( $post->ID, '_contact_assigned_agent', true );
     $notes = get_post_meta( $post->ID, '_contact_internal_notes', true );
    ?>
    <p>
        <label for="c360_contact_assigned_agent"><strong><?php _e( 'Assigned Agent', 'client360-crm' ); ?></strong></label>
        <br>
        <?php
        wp_dropdown_users(array(
            'name' => '_contact_assigned_agent',
            'selected' => $assigned_agent,
            'show_option_none' => __('Select Agent', 'client360-crm'),
            'role__in' => array('administrator', 'c360_employee'),
            'style' => 'width: 95%;'
        ));
        ?>
    </p>
    <hr>
    <p><label for="c360_contact_internal_notes"><strong><?php _e( 'Internal Notes', 'client360-crm' ); ?></strong></label></p>
    <textarea name="_contact_internal_notes" rows="5" style="width:100%;"><?php echo esc_textarea($notes); ?></textarea>
    <?php
}

function c360_campaign_details_metabox_html( $post ) {
    wp_nonce_field( 'c360_save_campaign_details', 'c360_campaign_details_nonce' );
    $type = get_post_meta( $post->ID, '_campaign_type', true );
    $status = get_post_meta( $post->ID, '_campaign_status', true );
    $start_date = get_post_meta( $post->ID, '_campaign_start_date', true );
    $end_date = get_post_meta( $post->ID, '_campaign_end_date', true );
    $budget = get_post_meta( $post->ID, '_campaign_budget', true );
    $expected_revenue = get_post_meta( $post->ID, '_campaign_expected_revenue', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="c360_campaign_type"><?php _e( 'Campaign Type', 'client360-crm' ); ?></label></th>
            <td><input type="text" id="c360_campaign_type" name="_campaign_type" class="regular-text" value="<?php echo esc_attr( $type ); ?>"></td>
        </tr>
         <tr>
            <th><label for="c360_campaign_status"><?php _e( 'Status', 'client360-crm' ); ?></label></th>
            <td>
                <select id="c360_campaign_status" name="_campaign_status">
                    <option value="planning" <?php selected($status, 'planning'); ?>><?php _e('Planning', 'client360-crm'); ?></option>
                    <option value="active" <?php selected($status, 'active'); ?>><?php _e('Active', 'client360-crm'); ?></option>
                    <option value="completed" <?php selected($status, 'completed'); ?>><?php _e('Completed', 'client360-crm'); ?></option>
                    <option value="cancelled" <?php selected($status, 'cancelled'); ?>><?php _e('Cancelled', 'client360-crm'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="c360_campaign_start_date"><?php _e( 'Start Date', 'client360-crm' ); ?></label></th>
            <td><input type="date" id="c360_campaign_start_date" name="_campaign_start_date" value="<?php echo esc_attr( $start_date ); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_campaign_end_date"><?php _e( 'End Date', 'client360-crm' ); ?></label></th>
            <td><input type="date" id="c360_campaign_end_date" name="_campaign_end_date" value="<?php echo esc_attr( $end_date ); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_campaign_budget"><?php _e( 'Budget ($)', 'client360-crm' ); ?></label></th>
            <td><input type="number" id="c360_campaign_budget" name="_campaign_budget" class="regular-text" value="<?php echo esc_attr( $budget ); ?>" step="0.01"></td>
        </tr>
        <tr>
            <th><label for="c360_campaign_expected_revenue"><?php _e( 'Expected Revenue ($)', 'client360-crm' ); ?></label></th>
            <td><input type="number" id="c360_campaign_expected_revenue" name="_campaign_expected_revenue" class="regular-text" value="<?php echo esc_attr( $expected_revenue ); ?>" step="0.01"></td>
        </tr>
    </table>
    <?php
}

function c360_property_basic_info_metabox_html( $post ) {
    wp_nonce_field( 'c360_save_property_details', 'c360_property_details_nonce' );
    $fields = [
        '_property_type' => 'Property Type', '_buying_type' => 'Buying Type', '_locality' => 'Locality',
        '_property_address' => 'Property Address', '_city' => 'City', '_state' => 'State', '_pin_code' => 'Pin Code'
    ];
    ?>
    <table class="form-table">
        <tr><th><label for="title"><?php _e('Society Name', 'client360-crm'); ?></label></th><td><p class="description"><?php _e('Please enter the Society Name in the main title field above.', 'client360-crm'); ?></p></td></tr>
        <?php c360_render_property_fields($post->ID, $fields); ?>
    </table>
    <?php
}

function c360_property_details_metabox_html( $post ) {
    $fields = [
        '_area_sqft' => 'Area (sqft)', '_bedrooms' => 'Bedrooms', '_bathrooms' => 'Bathrooms',
        '_year_built' => 'Year Built', '_property_age' => 'Property Age', '_furnishing' => 'Furnishing'
    ];
    ?>
    <table class="form-table">
        <?php c360_render_property_fields($post->ID, $fields); ?>
    </table>
    <?php
}

function c360_property_amenities_metabox_html( $post ) {
    c360_render_property_checkboxes($post->ID, '_amenities', c360_get_amenities_options());
}

function c360_property_availability_metabox_html( $post ) {
    c360_render_property_checkboxes($post->ID, '_available_for', c360_get_availability_options());
}

function c360_property_listing_details_metabox_html( $post ) {
    c360_render_property_side_fields($post->ID, ['_listing_price' => 'Listing Price ($)', '_available_from' => 'Available From']);
}

function c360_property_dealer_details_metabox_html( $post ) {
    c360_render_property_side_fields($post->ID, ['_dealer_name' => 'Dealer Name', '_dealer_phone' => 'Dealer Phone', '_dealer_email' => 'Dealer Email']);
}

function c360_property_media_metabox_html( $post ) {
    c360_render_property_side_fields($post->ID, [
        '_posted_by' => 'Posted By', '_is_verified' => 'Is Verified?', '_has_photos' => 'Has Photos?',
        '_property_photos' => 'Property Photos (URLs)', '_description' => 'Description', '_internal_notes' => 'Internal Notes'
    ]);
}

function c360_task_details_metabox_html( $post ) {
    wp_nonce_field( 'c360_save_task_details', 'c360_task_details_nonce' );
    
    $related_to_type = get_post_meta($post->ID, '_task_related_to_type', true);
    $related_id = get_post_meta( $post->ID, '_task_related_to', true );
    $start_date = get_post_meta( $post->ID, '_task_start_date', true );
    $end_date = get_post_meta( $post->ID, '_task_end_date', true );
    $url = get_post_meta( $post->ID, '_task_url', true );
    $notes = get_post_meta( $post->ID, '_task_notes', true );

    // Get all contacts and leads for the dropdowns
    $contacts = get_posts(array('post_type' => 'contact', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'));
    $leads = get_posts(array('post_type' => 'lead', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'));
    ?>
    <table class="form-table">
        <tr>
            <th><label><?php _e('Related To', 'client360-crm'); ?></label></th>
            <td>
                <label><input type="radio" name="_task_related_to_type" value="contact" <?php checked($related_to_type, 'contact'); ?>> <?php _e('Contact', 'client360-crm'); ?></label>
                <label style="margin-left: 10px;"><input type="radio" name="_task_related_to_type" value="lead" <?php checked($related_to_type, 'lead'); ?>> <?php _e('Lead', 'client360-crm'); ?></label>
            </td>
        </tr>
        <tr>
            <th><label for="c360_task_assignment"><?php _e('Assignment To', 'client360-crm'); ?></label></th>
            <td>
                <select name="_task_related_to" id="c360_task_assignment_contact" style="<?php echo ($related_to_type !== 'contact') ? 'display:none;' : ''; ?>">
                    <option value=""><?php _e('-- Select a Contact --', 'client360-crm'); ?></option>
                    <?php foreach ($contacts as $contact): ?>
                        <option value="<?php echo esc_attr($contact->ID); ?>" <?php selected($related_id, $contact->ID); ?>><?php echo esc_html($contact->post_title); ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="_task_related_to_lead" id="c360_task_assignment_lead" style="<?php echo ($related_to_type !== 'lead') ? 'display:none;' : ''; ?>">
                     <option value=""><?php _e('-- Select a Lead --', 'client360-crm'); ?></option>
                    <?php foreach ($leads as $lead): ?>
                        <option value="<?php echo esc_attr($lead->ID); ?>" <?php selected($related_id, $lead->ID); ?>><?php echo esc_html($lead->post_title); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
             <th><label for="c360_task_start_date"><?php _e( 'Start Date', 'client360-crm' ); ?></label></th>
             <td><input type="date" id="c360_task_start_date" name="_task_start_date" value="<?php echo esc_attr( $start_date ); ?>"></td>
        </tr>
         <tr>
             <th><label for="c360_task_end_date"><?php _e( 'End Date', 'client360-crm' ); ?></label></th>
             <td><input type="date" id="c360_task_end_date" name="_task_end_date" value="<?php echo esc_attr( $end_date ); ?>"></td>
        </tr>
         <tr>
             <th><label for="c360_task_url"><?php _e( 'URL', 'client360-crm' ); ?></label></th>
             <td><input type="url" id="c360_task_url" name="_task_url" class="regular-text" value="<?php echo esc_attr( $url ); ?>"></td>
        </tr>
         <tr>
             <th><label for="c360_task_notes"><?php _e( 'Notes', 'client360-crm' ); ?></label></th>
             <td><textarea id="c360_task_notes" name="_task_notes" class="large-text"><?php echo esc_textarea( $notes ); ?></textarea></td>
        </tr>
    </table>
     <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('input[name="_task_related_to_type"]').on('change', function() {
                if ($(this).val() === 'contact') {
                    $('#c360_task_assignment_contact').show();
                    $('#c360_task_assignment_lead').hide();
                    $('#c360_task_assignment_lead').val(''); 
                } else {
                    $('#c360_task_assignment_contact').hide();
                    $('#c360_task_assignment_lead').show();
                     $('#c360_task_assignment_contact').val('');
                }
            });
            $('input[name="_task_related_to_type"]:checked').trigger('change');
        });
    </script>
    <?php
}

function c360_log_details_metabox_html( $post ) {
    // This function now only handles Email Logs.
    if ( 'email_log' !== $post->post_type ) {
        return;
    }

    wp_nonce_field( 'c360_save_email_details', 'c360_email_details_nonce' );

    $related_to_type = get_post_meta($post->ID, '_email_related_to_type', true);
    $related_id = get_post_meta( $post->ID, '_email_related_to', true );
    $start_date = get_post_meta( $post->ID, '_email_start_date', true );

    $contacts = get_posts(array('post_type' => 'contact', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'));
    $leads = get_posts(array('post_type' => 'lead', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'));
    ?>
    <table class="form-table">
         <tr>
            <th><label for="post_title"><?php _e('Subject', 'client360-crm'); ?></label></th>
            <td><input type="text" name="post_title" class="large-text" value="<?php echo esc_attr($post->post_title); ?>"></td>
        </tr>
        <tr>
            <th><label><?php _e('Related To', 'client360-crm'); ?></label></th>
            <td>
                <label><input type="radio" name="_email_related_to_type" value="contact" <?php checked($related_to_type, 'contact'); ?>> <?php _e('Contact', 'client360-crm'); ?></label>
                <label style="margin-left: 10px;"><input type="radio" name="_email_related_to_type" value="lead" <?php checked($related_to_type, 'lead'); ?>> <?php _e('Lead', 'client360-crm'); ?></label>
            </td>
        </tr>
        <tr>
            <th><label for="c360_email_recipient"><?php _e('Recipient', 'client360-crm'); ?></label></th>
            <td>
                <select name="_email_related_to" id="c360_email_recipient">
                    <option value=""><?php _e('-- Select --', 'client360-crm'); ?></option>
                    <optgroup label="Contacts">
                    <?php foreach ($contacts as $contact): ?>
                        <option value="<?php echo esc_attr($contact->ID); ?>" <?php selected($related_id, $contact->ID); ?>><?php echo esc_html($contact->post_title); ?></option>
                    <?php endforeach; ?>
                    </optgroup>
                    <optgroup label="Leads">
                    <?php foreach ($leads as $lead): ?>
                        <option value="<?php echo esc_attr($lead->ID); ?>" <?php selected($related_id, $lead->ID); ?>><?php echo esc_html($lead->post_title); ?></option>
                    <?php endforeach; ?>
                    </optgroup>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="c360_email_start_date"><?php _e('Start Date', 'client360-crm'); ?></label></th>
            <td><input type="date" name="_email_start_date" id="c360_email_start_date" value="<?php echo esc_attr($start_date); ?>"></td>
        </tr>
    </table>
    <p class="description"><?php _e('Enter the email message in the main content editor above.', 'client360-crm'); ?></p>
    <?php
}

function c360_meeting_details_metabox_html( $post ) {
    wp_nonce_field( 'c360_save_meeting_details', 'c360_meeting_details_nonce' );

    $related_to_type = get_post_meta($post->ID, '_meeting_related_to_type', true);
    $related_id = get_post_meta( $post->ID, '_meeting_related_to', true );
    $location = get_post_meta( $post->ID, '_meeting_location', true );
    $datetime = get_post_meta( $post->ID, '_meeting_datetime', true );

    $contacts = get_posts(array('post_type' => 'contact', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'));
    $leads = get_posts(array('post_type' => 'lead', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'));
    ?>
    <table class="form-table">
         <tr>
            <th><label for="post_title"><?php _e('Agenda', 'client360-crm'); ?></label></th>
            <td><input type="text" name="post_title" class="large-text" value="<?php echo esc_attr($post->post_title); ?>">
            <p class="description"><?php _e('Enter the meeting agenda in the title field above.', 'client360-crm'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label><?php _e('Related To', 'client360-crm'); ?></label></th>
            <td>
                <label><input type="radio" name="_meeting_related_to_type" value="contact" <?php checked($related_to_type, 'contact'); ?>> <?php _e('Contact', 'client360-crm'); ?></label>
                <label style="margin-left: 10px;"><input type="radio" name="_meeting_related_to_type" value="lead" <?php checked($related_to_type, 'lead'); ?>> <?php _e('Lead', 'client360-crm'); ?></label>
            </td>
        </tr>
        <tr>
            <th><label for="c360_meeting_attendees"><?php _e('Choose Preferred Attendees', 'client360-crm'); ?></label></th>
            <td>
                <select name="_meeting_related_to" id="c360_meeting_attendees">
                    <option value=""><?php _e('-- Select --', 'client360-crm'); ?></option>
                    <optgroup label="Contacts">
                    <?php foreach ($contacts as $contact): ?>
                        <option value="<?php echo esc_attr($contact->ID); ?>" <?php selected($related_id, $contact->ID); ?>><?php echo esc_html($contact->post_title); ?></option>
                    <?php endforeach; ?>
                    </optgroup>
                    <optgroup label="Leads">
                    <?php foreach ($leads as $lead): ?>
                        <option value="<?php echo esc_attr($lead->ID); ?>" <?php selected($related_id, $lead->ID); ?>><?php echo esc_html($lead->post_title); ?></option>
                    <?php endforeach; ?>
                    </optgroup>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="c360_meeting_location"><?php _e('Location', 'client360-crm'); ?></label></th>
            <td><input type="text" name="_meeting_location" id="c360_meeting_location" class="regular-text" value="<?php echo esc_attr($location); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_meeting_datetime"><?php _e('Date Time', 'client360-crm'); ?></label></th>
            <td><input type="datetime-local" name="_meeting_datetime" id="c360_meeting_datetime" value="<?php echo esc_attr($datetime); ?>"></td>
        </tr>
    </table>
    <p class="description"><?php _e('Enter any additional meeting notes in the main content editor below.', 'client360-crm'); ?></p>
    <?php
}


function c360_payment_details_metabox_html( $post ) {
    wp_nonce_field( 'c360_save_payment_details', 'c360_payment_details_nonce' );
    $amount = get_post_meta( $post->ID, '_payment_amount', true );
    $date = get_post_meta( $post->ID, '_payment_date', true );
    $method = get_post_meta( $post->ID, '_payment_method', true );
    $related_id = get_post_meta( $post->ID, '_payment_related_contact', true );
    $methods = c360_get_options_for('payment_methods');
    ?>
     <table class="form-table">
        <tr>
            <th><label for="c360_payment_amount"><?php _e( 'Amount ($)', 'client360-crm' ); ?></label></th>
            <td><input type="number" id="c360_payment_amount" name="c360_payment_amount" class="regular-text" value="<?php echo esc_attr( $amount ); ?>" step="0.01"></td>
        </tr>
         <tr>
            <th><label for="c360_payment_date"><?php _e( 'Payment Date', 'client360-crm' ); ?></label></th>
            <td><input type="date" id="c360_payment_date" name="c360_payment_date" class="regular-text" value="<?php echo esc_attr( $date ); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_payment_method"><?php _e( 'Payment Method', 'client360-crm' ); ?></label></th>
            <td>
                <select id="c360_payment_method" name="c360_payment_method">
                    <?php foreach ($methods as $method_key => $method_label): ?>
                        <option value="<?php echo esc_attr($method_key); ?>" <?php selected( $method, $method_key ); ?>><?php echo esc_html($method_label); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="c360_payment_related_contact"><?php _e( 'Related Contact', 'client360-crm' ); ?></label></th>
            <td>
                <select id="c360_payment_related_contact" name="c360_payment_related_contact" style="width: 50%;">
                     <option value=""><?php _e( '-- Select a Contact --', 'client360-crm' ); ?></option>
                    <?php c360_render_related_posts_dropdown( array('contact'), $related_id ); ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

function c360_document_details_metabox_html( $post ) {
    wp_nonce_field( 'c360_save_document_details', 'c360_document_details_nonce' );
    wp_enqueue_media(); 

    $file_url = get_post_meta( $post->ID, '_c360_file_url', true );
    ?>
    <table class="form-table">
         <tr>
            <th><label for="document_folder"><?php _e( 'Folder Name', 'client360-crm' ); ?></label></th>
            <td>
                <?php
                wp_dropdown_categories(array(
                    'taxonomy' => 'document_folder',
                    'name' => 'document_folder',
                    'selected' => wp_get_object_terms($post->ID, 'document_folder', array('fields' => 'ids'))[0] ?? 0,
                    'hierarchical' => true,
                    'show_option_none' => __('Select Folder', 'client360-crm'),
                    'hide_empty' => false,
                    'id' => 'document_folder_select'
                ));
                ?>
                <a href="#" id="create_new_folder_link" style="margin-left: 10px;"><?php _e('or Create New', 'client360-crm'); ?></a>
                <div id="new_folder_name_wrapper" style="display: none; margin-top: 10px;">
                     <input type="text" name="new_folder_name" id="new_folder_name" class="regular-text" placeholder="Enter new folder name...">
                </div>
            </td>
        </tr>
        <tr>
            <th><label for="document_file"><?php _e( 'Choose File', 'client360-crm' ); ?></label></th>
            <td>
                <input type="file" name="document_file" id="document_file" required>
                 <div id="document_preview">
                    <?php if ( $file_url ): ?>
                        <p><strong><?php _e('Current File:', 'client360-crm'); ?></strong> <a href="<?php echo esc_url($file_url); ?>" target="_blank"><?php echo basename($file_url); ?></a></p>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
         <tr>
            <th><label for="post_title"><?php _e( 'File Name', 'client360-crm' ); ?></label></th>
            <td>
                <input type="text" name="post_title" id="post_title" class="regular-text" value="<?php echo esc_attr($post->post_title); ?>">
                <p class="description"><?php _e('(Optional) If left blank, the original filename will be used.', 'client360-crm'); ?></p>
            </td>
        </tr>
    </table>
     <script type="text/javascript">
        jQuery(document).ready(function($){
            $('#create_new_folder_link').on('click', function(e){
                e.preventDefault();
                $('#document_folder_select').val('');
                $('#new_folder_name_wrapper').show();
                // Set a special value to indicate a new folder is being created
                $('#document_folder_select').append($('<option>', { value: 'new', text: 'Creating New...' }).attr('selected', true)).hide();
            });
        });
    </script>
    <?php
}

function c360_related_activity_metabox_html( $post ) {
    $related_items = array();
    $post_types_to_query = array( 'task', 'meeting_log', 'call_log', 'email_log', 'payment', 'document' );

    $args = array(
        'post_type' => $post_types_to_query, 
        'posts_per_page' => -1,
        'meta_query' => array( 
            'relation' => 'OR',
            array( 'key' => '_task_related_to', 'value' => $post->ID, 'compare' => '=' ),
            array( 'key' => '_meeting_related_to', 'value' => $post->ID, 'compare' => '=' ),
            array( 'key' => '_call_related_to', 'value' => $post->ID, 'compare' => '=' ),
            array( 'key' => '_email_related_to', 'value' => $post->ID, 'compare' => '=' ),
            array( 'key' => '_payment_related_contact', 'value' => $post->ID, 'compare' => '=' ),
            array( 'key' => '_document_related_to', 'value' => $post->ID, 'compare' => '=' ),
        )
    );

    $activity_query = new WP_Query( $args );

    if ( $activity_query->have_posts() ) {
        echo '<ul>';
        while ( $activity_query->have_posts() ) {
            $activity_query->the_post();
            $post_type_obj = get_post_type_object( get_post_type() );
            $label = $post_type_obj->labels->singular_name;
            echo '<li>';
            echo '<strong>' . esc_html( $label ) . ':</strong> ';
            echo '<a href="' . esc_url( get_edit_post_link( get_the_ID() ) ) . '">' . get_the_title() . '</a>';
            echo ' - <small>' . get_the_date() . '</small>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>' . __( 'No related activity found.', 'client360-crm' ) . '</p>';
    }
    wp_reset_postdata();
}

function c360_render_related_posts_dropdown( $post_types, $selected_id ) {
    foreach( $post_types as $pt ) {
        $posts = get_posts(array('post_type' => $pt, 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'));
        if ( ! empty($posts) ) {
            echo '<optgroup label="' . esc_attr( get_post_type_object( $pt )->labels->name ) . '">';
            foreach ( $posts as $p ) {
                echo '<option value="' . esc_attr( $p->ID ) . '" ' . selected( $selected_id, $p->ID, false ) . '>' . esc_html( $p->post_title ) . '</option>';
            }
            echo '</optgroup>';
        }
    }
}

/**
 * Renders the metabox for Call Logs.
 */
function c360_call_details_metabox_html( $post ) {
    wp_nonce_field( 'c360_save_call_details', 'c360_call_details_nonce' );

    $related_to_type = get_post_meta($post->ID, '_call_related_to_type', true);
    $related_id = get_post_meta( $post->ID, '_call_related_to', true );
    $start_date = get_post_meta( $post->ID, '_call_start_date', true );
    $end_date = get_post_meta( $post->ID, '_call_end_date', true );
    $duration = get_post_meta( $post->ID, '_call_duration', true );

    $contacts = get_posts(array('post_type' => 'contact', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'));
    $leads = get_posts(array('post_type' => 'lead', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'));
    ?>
    <table class="form-table">
         <tr>
            <th><label for="post_title"><?php _e('Call Subject / Title', 'client360-crm'); ?></label></th>
            <td><input type="text" name="post_title" class="large-text" value="<?php echo esc_attr($post->post_title); ?>"></td>
        </tr>
        <tr>
            <th><label><?php _e('Related To', 'client360-crm'); ?></label></th>
            <td>
                <label><input type="radio" name="_call_related_to_type" value="contact" <?php checked($related_to_type, 'contact'); ?>> <?php _e('Contact', 'client360-crm'); ?></label>
                <label style="margin-left: 10px;"><input type="radio" name="_call_related_to_type" value="lead" <?php checked($related_to_type, 'lead'); ?>> <?php _e('Lead', 'client360-crm'); ?></label>
            </td>
        </tr>
        <tr>
            <th><label for="c360_call_recipient"><?php _e('Recipient', 'client360-crm'); ?></label></th>
            <td>
                <select name="_call_related_to" id="c360_call_recipient">
                    <option value=""><?php _e('-- Select --', 'client360-crm'); ?></option>
                    <optgroup label="Contacts">
                    <?php foreach ($contacts as $contact): ?>
                        <option value="<?php echo esc_attr($contact->ID); ?>" <?php selected($related_id, $contact->ID); ?>><?php echo esc_html($contact->post_title); ?></option>
                    <?php endforeach; ?>
                    </optgroup>
                    <optgroup label="Leads">
                    <?php foreach ($leads as $lead): ?>
                        <option value="<?php echo esc_attr($lead->ID); ?>" <?php selected($related_id, $lead->ID); ?>><?php echo esc_html($lead->post_title); ?></option>
                    <?php endforeach; ?>
                    </optgroup>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="c360_call_start_date"><?php _e('Start Date', 'client360-crm'); ?></label></th>
            <td><input type="date" name="_call_start_date" id="c360_call_start_date" value="<?php echo esc_attr($start_date); ?>"></td>
        </tr>
         <tr>
            <th><label for="c360_call_end_date"><?php _e('End Date', 'client360-crm'); ?></label></th>
            <td><input type="date" name="_call_end_date" id="c360_call_end_date" value="<?php echo esc_attr($end_date); ?>"></td>
        </tr>
        <tr>
            <th><label for="c360_call_duration"><?php _e('Call Duration (e.g., 5 mins)', 'client360-crm'); ?></label></th>
            <td><input type="text" name="_call_duration" id="c360_call_duration" class="regular-text" value="<?php echo esc_attr($duration); ?>"></td>
        </tr>
    </table>
    <p class="description"><?php _e('Enter any additional call notes in the main content editor below.', 'client360-crm'); ?></p>
    <?php
}
/**
 * Renders the metabox for Email Logs.
 */
function c360_email_details_metabox_html( $post ) {
    wp_nonce_field( 'c360_save_email_details', 'c360_email_details_nonce' );

    $related_to_type = get_post_meta($post->ID, '_email_related_to_type', true);
    $related_id = get_post_meta( $post->ID, '_email_related_to', true );
    $start_date = get_post_meta( $post->ID, '_email_start_date', true );
    // The subject will now be the main post title.

    $contacts = get_posts(array('post_type' => 'contact', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'));
    $leads = get_posts(array('post_type' => 'lead', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'));
    ?>
    <table class="form-table">
         <tr>
            <th><label for="post_title"><?php _e('Subject', 'client360-crm'); ?></label></th>
            <td><input type="text" name="post_title" class="large-text" value="<?php echo esc_attr($post->post_title); ?>"></td>
        </tr>
        <tr>
            <th><label><?php _e('Related To', 'client360-crm'); ?></label></th>
            <td>
                <label><input type="radio" name="_email_related_to_type" value="contact" <?php checked($related_to_type, 'contact'); ?>> <?php _e('Contact', 'client360-crm'); ?></label>
                <label style="margin-left: 10px;"><input type="radio" name="_email_related_to_type" value="lead" <?php checked($related_to_type, 'lead'); ?>> <?php _e('Lead', 'client360-crm'); ?></label>
            </td>
        </tr>
        <tr>
            <th><label for="c360_email_recipient"><?php _e('Recipient', 'client360-crm'); ?></label></th>
            <td>
                <select name="_email_related_to" id="c360_email_recipient">
                    <option value=""><?php _e('-- Select --', 'client360-crm'); ?></option>
                    <optgroup label="Contacts">
                    <?php foreach ($contacts as $contact): ?>
                        <option value="<?php echo esc_attr($contact->ID); ?>" <?php selected($related_id, $contact->ID); ?>><?php echo esc_html($contact->post_title); ?></option>
                    <?php endforeach; ?>
                    </optgroup>
                    <optgroup label="Leads">
                    <?php foreach ($leads as $lead): ?>
                        <option value="<?php echo esc_attr($lead->ID); ?>" <?php selected($related_id, $lead->ID); ?>><?php echo esc_html($lead->post_title); ?></option>
                    <?php endforeach; ?>
                    </optgroup>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="c360_email_start_date"><?php _e('Start Date', 'client360-crm'); ?></label></th>
            <td><input type="date" name="_email_start_date" id="c360_email_start_date" value="<?php echo esc_attr($start_date); ?>"></td>
        </tr>
    </table>
    <p class="description"><?php _e('Enter the email message in the main content editor below.', 'client360-crm'); ?></p>
    <?php
}
// --- SAVE META DATA FUNCTIONS ---

function c360_save_all_meta( $post_id, $post ) {
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

    // --- SAVE LEAD ---
    if ( isset($_POST['c360_lead_details_nonce']) && wp_verify_nonce( $_POST['c360_lead_details_nonce'], 'c360_save_lead_details' ) ) {
         if ( isset( $_POST['c360_lead_email'] ) ) { update_post_meta( $post_id, '_lead_email', sanitize_email( $_POST['c360_lead_email'] ) ); }
        if ( isset( $_POST['c360_lead_phone'] ) ) { update_post_meta( $post_id, '_lead_phone', sanitize_text_field( $_POST['c360_lead_phone'] ) ); }
        if ( isset( $_POST['c360_lead_address'] ) ) { update_post_meta( $post_id, '_lead_address', sanitize_textarea_field( $_POST['c360_lead_address'] ) ); }
        if ( isset( $_POST['c360_lead_status'] ) ) { wp_set_object_terms( $post_id, (int)$_POST['c360_lead_status'], 'lead_status', false ); }
        if ( isset( $_POST['c360_lead_source'] ) ) { update_post_meta( $post_id, '_lead_source', sanitize_text_field( $_POST['c360_lead_source'] ) ); }
        if ( isset( $_POST['c360_lead_source_details'] ) ) { update_post_meta( $post_id, '_lead_source_details', sanitize_text_field( $_POST['c360_lead_source_details'] ) ); }
        if ( isset( $_POST['c360_lead_campaign'] ) ) { update_post_meta( $post_id, '_lead_campaign', sanitize_text_field( $_POST['c360_lead_campaign'] ) ); }
        if ( isset( $_POST['c360_lead_source_channel'] ) ) { update_post_meta( $post_id, '_lead_source_channel', sanitize_text_field( $_POST['c360_lead_source_channel'] ) ); }
        if ( isset( $_POST['c360_lead_source_medium'] ) ) { update_post_meta( $post_id, '_lead_source_medium', sanitize_text_field( $_POST['c360_lead_source_medium'] ) ); }
        if ( isset( $_POST['c360_lead_source_campaign'] ) ) { update_post_meta( $post_id, '_lead_source_campaign', sanitize_text_field( $_POST['c360_lead_source_campaign'] ) ); }
        if ( isset( $_POST['c360_lead_source_referral'] ) ) { update_post_meta( $post_id, '_lead_source_referral', sanitize_text_field( $_POST['c360_lead_source_referral'] ) ); }
        if ( isset( $_POST['c360_lead_assigned_agent'] ) ) { update_post_meta( $post_id, '_lead_assigned_agent', absint( $_POST['c360_lead_assigned_agent'] ) ); }
        if ( isset( $_POST['c360_lead_conversion_date'] ) ) { update_post_meta( $post_id, '_lead_conversion_date', sanitize_text_field( $_POST['c360_lead_conversion_date'] ) ); }
        if ( isset( $_POST['c360_lead_followup_date'] ) ) { update_post_meta( $post_id, '_lead_followup_date', sanitize_text_field( $_POST['c360_lead_followup_date'] ) ); }
        if ( isset( $_POST['c360_lead_followup_status'] ) ) { update_post_meta( $post_id, '_lead_followup_status', sanitize_text_field( $_POST['c360_lead_followup_status'] ) ); }
        if ( isset( $_POST['c360_lead_score'] ) ) { update_post_meta( $post_id, '_lead_score', absint( $_POST['c360_lead_score'] ) ); }
        if ( isset( $_POST['c360_lead_nurturing_workflow'] ) ) { update_post_meta( $post_id, '_lead_nurturing_workflow', sanitize_text_field( $_POST['c360_lead_nurturing_workflow'] ) ); }
        if ( isset( $_POST['c360_lead_engagement_level'] ) ) { update_post_meta( $post_id, '_lead_engagement_level', sanitize_text_field( $_POST['c360_lead_engagement_level'] ) ); }
        if ( isset( $_POST['c360_lead_conversion_rate'] ) ) { update_post_meta( $post_id, '_lead_conversion_rate', absint( $_POST['c360_lead_conversion_rate'] ) ); }
        if ( isset( $_POST['c360_lead_nurturing_stage'] ) ) { update_post_meta( $post_id, '_lead_nurturing_stage', sanitize_text_field( $_POST['c360_lead_nurturing_stage'] ) ); }
        if ( isset( $_POST['c360_lead_next_action'] ) ) { update_post_meta( $post_id, '_lead_next_action', sanitize_text_field( $_POST['c360_lead_next_action'] ) ); }
    }

    // --- SAVE CONTACT ---
    if ( isset($_POST['c360_contact_details_nonce']) && wp_verify_nonce( $_POST['c360_contact_details_nonce'], 'c360_save_contact_details' ) ) {
       $fields_to_save = array(
            '_contact_first_name', '_contact_last_name', '_contact_title', '_contact_email', '_contact_phone', 
            '_contact_mobile', '_contact_preferred_method', '_contact_physical_address', '_contact_mailing_address',
            '_contact_lead_source', '_contact_referral_source', '_contact_campaign_source', '_contact_lead_status',
            '_contact_lead_rating', '_contact_conversion_probability', '_contact_birthday', '_contact_anniversary',
            '_contact_key_milestones', '_contact_occupation', '_contact_hobbies', '_contact_gender', '_contact_dob',
            '_contact_communication_frequency', '_contact_linkedin', '_contact_facebook', '_contact_twitter',
            '_contact_other_social', '_contact_assigned_agent', '_contact_internal_notes'
        );

        foreach ($fields_to_save as $field_key) {
            if (isset($_POST[$field_key])) {
                if ( strpos($field_key, 'email') !== false ) {
                     update_post_meta($post_id, $field_key, sanitize_email($_POST[$field_key]));
                } elseif ( strpos($field_key, 'url') !== false || strpos($field_key, 'linkedin') !== false || strpos($field_key, 'facebook') !== false ) {
                     update_post_meta($post_id, $field_key, esc_url_raw($_POST[$field_key]));
                } elseif (strpos($field_key, 'address') !== false || strpos($field_key, 'notes') !== false || strpos($field_key, 'milestones') !== false) {
                    update_post_meta($post_id, $field_key, sanitize_textarea_field($_POST[$field_key]));
                }
                else {
                    update_post_meta($post_id, $field_key, sanitize_text_field($_POST[$field_key]));
                }
            }
        }
        
        $first_name = isset($_POST['_contact_first_name']) ? sanitize_text_field($_POST['_contact_first_name']) : '';
        $last_name = isset($_POST['_contact_last_name']) ? sanitize_text_field($_POST['_contact_last_name']) : '';
        
        if ( ($first_name || $last_name) && $post->post_title !== trim($first_name . ' ' . $last_name)) {
            remove_action('save_post', 'c360_save_all_meta', 10);
            wp_update_post(array('ID' => $post_id, 'post_title' => trim($first_name . ' ' . $last_name)));
            add_action('save_post', 'c360_save_all_meta', 10, 2);
        }
    }

    // --- SAVE CAMPAIGN ---
    if ( isset($_POST['c360_campaign_details_nonce']) && wp_verify_nonce( $_POST['c360_campaign_details_nonce'], 'c360_save_campaign_details' ) ) {
        $fields_to_save = array(
            '_campaign_type', '_campaign_status', '_campaign_start_date', '_campaign_end_date',
            '_campaign_budget', '_campaign_expected_revenue'
        );
        foreach ($fields_to_save as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
    
    // --- SAVE PROPERTY ---
    if ( isset($_POST['c360_property_details_nonce']) && wp_verify_nonce( $_POST['c360_property_details_nonce'], 'c360_save_property_details' ) ) {
        $fields_to_save = [
            '_property_type', '_buying_type', '_locality', '_property_address', '_city', '_state', '_pin_code',
            '_area_sqft', '_bedrooms', '_bathrooms', '_year_built', '_property_age', '_furnishing',
            '_dealer_name', '_dealer_phone', '_dealer_email', '_listing_price', '_available_from',
            '_posted_by', '_property_photos', '_description', '_internal_notes'
        ];

        foreach ($fields_to_save as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
        
        update_post_meta($post_id, '_is_verified', isset($_POST['_is_verified']) ? 'yes' : 'no');
        update_post_meta($post_id, '_has_photos', isset($_POST['_has_photos']) ? 'yes' : 'no');

        $amenities = isset($_POST['_amenities']) && is_array($_POST['_amenities']) ? array_map('sanitize_text_field', $_POST['_amenities']) : array();
        update_post_meta($post_id, '_amenities', $amenities);

        $available_for = isset($_POST['_available_for']) && is_array($_POST['_available_for']) ? array_map('sanitize_text_field', $_POST['_available_for']) : array();
        update_post_meta($post_id, '_available_for', $available_for);
    }

// --- SAVE TASK ---
if ( isset($_POST['c360_task_details_nonce']) && wp_verify_nonce( $_POST['c360_task_details_nonce'], 'c360_save_task_details' ) ) {
    
    if (isset($_POST['_task_related_to_type'])) {
        update_post_meta($post_id, '_task_related_to_type', sanitize_key($_POST['_task_related_to_type']));
    }

    $related_id = 0;
    if (isset($_POST['_task_related_to_type']) && $_POST['_task_related_to_type'] === 'contact' && isset($_POST['_task_related_to'])) {
        $related_id = absint($_POST['_task_related_to']);
    } elseif (isset($_POST['_task_related_to_type']) && $_POST['_task_related_to_type'] === 'lead' && isset($_POST['_task_related_to_lead'])) {
        $related_id = absint($_POST['_task_related_to_lead']);
    }
    update_post_meta($post_id, '_task_related_to', $related_id);

    if (isset($_POST['_task_start_date'])) { update_post_meta($post_id, '_task_start_date', sanitize_text_field($_POST['_task_start_date'])); }
    if (isset($_POST['_task_end_date'])) { update_post_meta($post_id, '_task_end_date', sanitize_text_field($_POST['_task_end_date'])); }
    if (isset($_POST['_task_url'])) { update_post_meta($post_id, '_task_url', esc_url_raw($_POST['_task_url'])); }
    if (isset($_POST['_task_notes'])) { update_post_meta($post_id, '_task_notes', sanitize_textarea_field($_POST['_task_notes'])); }
}
// --- SAVE MEETING LOG DETAILS ---
if ( isset($_POST['c360_meeting_details_nonce']) && wp_verify_nonce( $_POST['c360_meeting_details_nonce'], 'c360_save_meeting_details' ) ) {
    if (isset($_POST['_meeting_related_to_type'])) { update_post_meta($post_id, '_meeting_related_to_type', sanitize_key($_POST['_meeting_related_to_type'])); }
    if (isset($_POST['_meeting_related_to'])) { update_post_meta($post_id, '_meeting_related_to', absint($_POST['_meeting_related_to'])); }
    if (isset($_POST['_meeting_location'])) { update_post_meta($post_id, '_meeting_location', sanitize_text_field($_POST['_meeting_location'])); }
    if (isset($_POST['_meeting_datetime'])) { update_post_meta($post_id, '_meeting_datetime', sanitize_text_field($_POST['_meeting_datetime'])); }
}

// --- SAVE CALL LOG DETAILS ---
if ( isset($_POST['c360_call_details_nonce']) && wp_verify_nonce( $_POST['c360_call_details_nonce'], 'c360_save_call_details' ) ) {
    if (isset($_POST['_call_related_to_type'])) { update_post_meta($post_id, '_call_related_to_type', sanitize_key($_POST['_call_related_to_type'])); }
    if (isset($_POST['_call_related_to'])) { update_post_meta($post_id, '_call_related_to', absint($_POST['_call_related_to'])); }
    if (isset($_POST['_call_start_date'])) { update_post_meta($post_id, '_call_start_date', sanitize_text_field($_POST['_call_start_date'])); }
    if (isset($_POST['_call_end_date'])) { update_post_meta($post_id, '_call_end_date', sanitize_text_field($_POST['_call_end_date'])); }
    if (isset($_POST['_call_duration'])) { update_post_meta($post_id, '_call_duration', sanitize_text_field($_POST['_call_duration'])); }
}

// --- SAVE EMAIL LOG DETAILS ---
if ( isset($_POST['c360_email_details_nonce']) && wp_verify_nonce( $_POST['c360_email_details_nonce'], 'c360_save_email_details' ) ) {
    if (isset($_POST['_email_related_to_type'])) {
        update_post_meta($post_id, '_email_related_to_type', sanitize_key($_POST['_email_related_to_type']));
    }
    if (isset($_POST['_email_related_to'])) {
        update_post_meta($post_id, '_email_related_to', absint($_POST['_email_related_to']));
    }
    if (isset($_POST['_email_start_date'])) {
        update_post_meta($post_id, '_email_start_date', sanitize_text_field($_POST['_email_start_date']));
    }
}
    // --- SAVE PAYMENT ---
    if ( isset($_POST['c360_payment_details_nonce']) && wp_verify_nonce( $_POST['c360_payment_details_nonce'], 'c360_save_payment_details' ) ) {
        if ( isset( $_POST['c360_payment_amount'] ) ) { update_post_meta( $post_id, '_payment_amount', sanitize_text_field( $_POST['c360_payment_amount'] ) ); }
        if ( ! empty( $_POST['c360_payment_date'] ) ) {
            $payment_date = sanitize_text_field( $_POST['c360_payment_date'] );
            $d = DateTime::createFromFormat('Y-m-d', $payment_date);
            if ( $d && $d->format('Y-m-d') === $payment_date ) { update_post_meta( $post_id, '_payment_date', $payment_date ); } 
            else { delete_post_meta( $post_id, '_payment_date' ); }
        } else { delete_post_meta( $post_id, '_payment_date' ); }
        if ( isset( $_POST['c360_payment_method'] ) ) { update_post_meta( $post_id, '_payment_method', sanitize_key( $_POST['c360_payment_method'] ) ); }
        if ( isset( $_POST['c360_payment_related_contact'] ) ) { update_post_meta( $post_id, '_payment_related_contact', absint( $_POST['c360_payment_related_contact'] ) ); }
    }
    // --- SAVE DOCUMENT ---
if ( isset($_POST['c360_document_details_nonce']) && wp_verify_nonce( $_POST['c360_document_details_nonce'], 'c360_save_document_details' ) ) {

    $folder_id = 0;
    // Check if creating a new folder
    if ( isset($_POST['document_folder']) && $_POST['document_folder'] === 'new' && !empty($_POST['new_folder_name']) ) {
        $new_folder = wp_insert_term( sanitize_text_field($_POST['new_folder_name']), 'document_folder' );
        if ( ! is_wp_error($new_folder) ) {
            $folder_id = $new_folder['term_id'];
        }
    } elseif ( isset($_POST['document_folder']) ) {
        $folder_id = absint($_POST['document_folder']);
    }

    // Set the folder for the document
    if ($folder_id > 0) {
        wp_set_object_terms($post_id, $folder_id, 'document_folder');
    }

    // Handle the file upload
    if ( ! empty($_FILES['document_file']['name']) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        $uploadedfile = $_FILES['document_file'];
        $upload_overrides = array( 'test_form' => false );
        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

        if ( $movefile && ! isset( $movefile['error'] ) ) {
            // If the post title was left blank, use the original filename.
            if (empty($post->post_title)) {
                remove_action('save_post', 'c360_save_all_meta', 10);
                wp_update_post(array('ID' => $post_id, 'post_title' => sanitize_file_name($uploadedfile['name'])));
                add_action('save_post', 'c360_save_all_meta', 10, 2);
            }
            update_post_meta($post_id, '_c360_file_url', $movefile['url']);
        }
    }
}
}
add_action( 'save_post', 'c360_save_all_meta', 10, 2 );

