<?php
/**
 * Plugin Name: PMPro Custom Cancellation Form
 * Description: Replaces the default PMPro cancel action with a multi-question form to reduce churn.
 * Version: 1.1
 * Author: Sheryar Khan
 */

// --- CONFIGURATION ---
/**
 * Define your cancellation questions here.
 * 'id': Used for saving to the database (must be unique).
 * 'label': What the user sees.
 * 'type': 'textarea', 'radio', 'checkbox', or 'select'.
 * 'required': true or false.
 * 'min_length': (For 'textarea' only) The minimum required characters.
 * 'options': (For 'radio', 'checkbox', 'select' only) An array of values/labels.
 */
function my_pmpro_get_cancellation_questions() {
    return [
        'cancel_reason_main' => [
            'id'         => 'cancel_reason_main',
            'label'      => 'We\'re sorry to see you go. What is the primary reason you are cancelling today?',
            'type'       => 'textarea',
            'required'   => true,
            'min_length' => 50,
        ],
        'cancel_reason_missing' => [
            'id'         => 'cancel_reason_missing',
            'label'      => 'What feature or content were you looking for that you couldn\'t find?',
            'type'       => 'textarea',
            'required'   => true,
            'min_length' => 50,
        ],
        'cancel_feature_request' => [
            'id'         => 'cancel_feature_request',
            'label'      => 'If you could suggest one thing to improve our membership, what would it be?',
            'type'       => 'textarea',
            'required'   => false,
        ],
        'cancel_found_alternative' => [
            'id'         => 'cancel_found_alternative',
            'label'      => 'Did you find an alternative product or service?',
            'type'       => 'radio',
            'required'   => true,
            'options'    => [
                'yes' => 'Yes',
                'no'  => 'No',
            ],
        ],
        'cancel_price_perception' => [
            'id'         => 'cancel_price_perception',
            'label'      => 'How did you feel about the price of the membership?',
            'type'       => 'radio',
            'required'   => true,
            'options'    => [
                'too_expensive' => 'Too expensive',
                'about_right'   => 'About right',
                'great_value'   => 'It was a great value',
            ],
        ],
        'cancel_how_to_improve' => [
            'id'         => 'cancel_how_to_improve',
            'label'      => 'What areas could we improve on? (Check all that apply)',
            'type'       => 'checkbox',
            'required'   => true,
            'options'    => [
                'improve_content'   => 'More/Better Content',
                'improve_support'   => 'Customer Support',
                'improve_features'  => 'Site Features',
                'improve_usability' => 'Website Usability',
                'improve_other'     => 'Other',
            ],
        ],
        'cancel_content_frequency' => [
            'id'         => 'cancel_content_frequency',
            'label'      => 'How was the frequency of new content?',
            'type'       => 'select',
            'required'   => true,
            'options'    => [
                ''                => 'Select an option...',
                'too_much'        => 'Too much, I felt overwhelmed',
                'just_right'      => 'Just right',
                'not_enough'      => 'Not enough, I wanted more',
            ],
        ],
        'cancel_product_usage' => [
            'id'         => 'cancel_product_usage',
            'label'      => 'How often did you use the membership?',
            'type'       => 'select',
            'required'   => true,
            'options'    => [
                ''          => 'Select an option...',
                'daily'     => 'Every day',
                'weekly'    => 'A few times a week',
                'monthly'   => 'A few times a month',
                'rarely'    => 'Rarely or never',
            ],
        ],
        'cancel_would_return' => [
            'id'         => 'cancel_would_return',
            'label'      => 'Would you consider returning in the future?',
            'type'       => 'radio',
            'required'   => true,
            'options'    => [
                'yes'     => 'Yes',
                'no'      => 'No',
                'maybe'   => 'Maybe',
            ],
        ],
        'cancel_final_feedback' => [
            'id'         => 'cancel_final_feedback',
            'label'      => 'Any final words of feedback or advice for us?',
            'type'       => 'textarea',
            'required'   => false,
        ],
    ];
}

// --- PLUGIN LOGIC ---

/**
 * 1. Add our custom JS and CSS, but *only* on the cancel page.
 */
function my_pmpro_cancel_enqueue_scripts() {
    if ( ! function_exists( 'pmpro_is_page' ) || ! pmpro_is_page( 'cancel' ) ) {
        return;
    }

    wp_enqueue_style( 'pmpro-custom-cancel', plugin_dir_url( __FILE__ ) . 'pmpro-cancel.css', [], '2.2' );
    wp_enqueue_script( 'pmpro-custom-cancel', plugin_dir_url( __FILE__ ) . 'pmpro-cancel.js', ['jquery'], '2.2', true );
}
add_action( 'wp_enqueue_scripts', 'my_pmpro_cancel_enqueue_scripts' );

/**
 * 2. Add our new question fields to the cancel page.
 */
function my_pmpro_add_custom_cancel_fields() {
    $questions = my_pmpro_get_cancellation_questions();
    ?>
    <div id="pmpro_custom_cancel_form">
        <?php foreach ( $questions as $q ) : ?>
            
            <div class="pmpro_form_field <?php echo 'pmpro_form_field-' . esc_attr( $q['type'] ); ?> <?php echo $q['required'] ? 'pmpro_form_field-required' : ''; ?>">
                
                <?php if ( $q['type'] == 'textarea' ) : ?>
                    <label for="<?php echo esc_attr( $q['id'] ); ?>"><?php echo esc_html( $q['label'] ); ?></label>
                    <textarea 
                        id="<?php echo esc_attr( $q['id'] ); ?>" 
                        name="<?php echo esc_attr( $q['id'] ); ?>" 
                        class="pmpro_form_input pmpro_form_input-textarea"
                        <?php if ( $q['required'] ) echo 'data-required="true"'; ?>
                        <?php if ( $q['required'] && isset( $q['min_length'] ) ) echo 'data-min-length="' . esc_attr( $q['min_length'] ) . '"'; ?>
                    ></textarea>
                
                <?php elseif ( $q['type'] == 'select' ) : ?>
                    <label for="<?php echo esc_attr( $q['id'] ); ?>"><?php echo esc_html( $q['label'] ); ?></label>
                    <select 
                        id="<?php echo esc_attr( $q['id'] ); ?>" 
                        name="<?php echo esc_attr( $q['id'] ); ?>" 
                        class="pmpro_form_input pmpro_form_input-select"
                        <?php if ( $q['required'] ) echo 'data-required="true"'; ?>
                    >
                        <?php foreach ( $q['options'] as $value => $label ) : ?>
                            <option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>

                <?php elseif ( $q['type'] == 'radio' ) : ?>
                    <fieldset class="pmpro_form_fieldset" <?php if ( $q['required'] ) echo 'data-required="true" data-type="radio"'; ?>>
                        <legend class="pmpro_form_legend"><?php echo esc_html( $q['label'] ); ?></legend>
                        <?php foreach ( $q['options'] as $value => $label ) : ?>
                            <div class="pmpro_form_option">
                                <input 
                                    type="radio" 
                                    id="<?php echo esc_attr( $q['id'] . '_' . $value ); ?>" 
                                    name="<?php echo esc_attr( $q['id'] ); ?>" 
                                    value="<?php echo esc_attr( $value ); ?>"
                                >
                                <label for="<?php echo esc_attr( $q['id'] . '_' . $value ); ?>"><?php echo esc_html( $label ); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </fieldset>

                <?php elseif ( $q['type'] == 'checkbox' ) : ?>
                    <fieldset class="pmpro_form_fieldset" <?php if ( $q['required'] ) echo 'data-required="true" data-type="checkbox"'; ?>>
                        <legend class="pmpro_form_legend"><?php echo esc_html( $q['label'] ); ?></legend>
                        <?php foreach ( $q['options'] as $value => $label ) : ?>
                            <div class="pmpro_form_option">
                                <input 
                                    type="checkbox" 
                                    id="<?php echo esc_attr( $q['id'] . '_' . $value ); ?>" 
                                    name="<?php echo esc_attr( $q['id'] ); ?>[]" 
                                    value="<?php echo esc_attr( $value ); ?>"
                                >
                                <label for="<?php echo esc_attr( $q['id'] . '_' . $value ); ?>"><?php echo esc_html( $label ); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </fieldset>
                
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    </div>
    <?php
}
add_action( 'pmpro_cancel_before_submit', 'my_pmpro_add_custom_cancel_fields' );

/**
 * 3. Server-side check. This is a fallback in case JS fails.
 */
function my_pmpro_validate_custom_cancel_fields( $should_process ) {
    if ( ! $should_process ) {
        return $should_process;
    }

    $questions = my_pmpro_get_cancellation_questions();
    $error_message = 'Please complete all required fields to proceed with the cancellation.';

    foreach ( $questions as $q ) {
        if ( ! $q['required'] ) {
            continue;
        }

        $id = $q['id'];
        $request_value = isset( $_REQUEST[$id] ) ? $_REQUEST[$id] : null;

        if ( $q['type'] == 'textarea' ) {
            $min_length = isset( $q['min_length'] ) ? $q['min_length'] : 1;
            if ( empty( $request_value ) || strlen( trim( $request_value ) ) < $min_length ) {
                pmpro_setMessage( 'Error: The field "' . $q['label'] . '" must have at least ' . $min_length . ' characters.', 'pmpro_error' );
                return false;
            }
        } elseif ( $q['type'] == 'radio' || $q['type'] == 'select' ) {
            if ( empty( $request_value ) ) {
                pmpro_setMessage( $error_message, 'pmpro_error' );
                return false;
            }
        } elseif ( $q['type'] == 'checkbox' ) {
            if ( empty( $request_value ) || ! is_array( $request_value ) ) {
                pmpro_setMessage( $error_message, 'pmpro_error' );
                return false;
            }
        }
    }

    return true; // All checks passed!
}
add_filter( 'pmpro_cancel_should_process', 'my_pmpro_validate_custom_cancel_fields' );

/**
 * 4. Save the answers to user meta after cancellation is processed.
 */
function my_pmpro_save_custom_cancel_fields( $user ) {
    $questions = my_pmpro_get_cancellation_questions();
    $levels_cancelled = isset( $_REQUEST['levelstocancel'] ) ? sanitize_text_field( $_REQUEST['levelstocancel'] ) : 'all';

    $saved_data = [
        'timestamp' => time(),
        'levels'    => $levels_cancelled,
        'answers'   => [],
    ];

    foreach ( $questions as $q ) {
        $id = $q['id'];
        if ( ! isset( $_REQUEST[$id] ) ) {
            continue;
        }

        $value = $_REQUEST[$id];

        if ( $q['type'] == 'textarea' ) {
            $saved_data['answers'][$id] = sanitize_textarea_field( $value );
        } elseif ( $q['type'] == 'radio' || $q['type'] == 'select' ) {
            $saved_data['answers'][$id] = sanitize_text_field( $value );
        } elseif ( $q['type'] == 'checkbox' && is_array( $value ) ) {
            $saved_data['answers'][$id] = array_map( 'sanitize_text_field', $value );
        }
    }

    if ( ! empty( $saved_data['answers'] ) ) {
        add_user_meta( $user->ID, 'custom_cancellation_survey', $saved_data );
    }
}
add_action( 'pmpro_cancel_processed', 'my_pmpro_save_custom_cancel_fields' );

/**
 * 5. NEW: Add survey answers to the cancellation emails.
 */
function my_pmpro_add_answers_to_email( $body, $email ) {
    // Only run on the admin and user cancellation emails
    if ( $email->template == 'cancel' || $email->template == 'cancel_admin' ) {

        $questions = my_pmpro_get_cancellation_questions();
        $answers_html = "<h3 style='color: #334155; font-size: 1.125rem; font-weight: 600;'>Cancellation Survey Answers:</h3><table role='presentation' border='0' cellpadding='0' cellspacing='0' style='width: 100%; border-collapse: collapse;'>";

        $has_answers = false;

        foreach ( $questions as $q ) {
            $id = $q['id'];
            $label = $q['label'];

            if ( ! empty( $_REQUEST[$id] ) ) {
                $has_answers = true;
                $answer_text = '';
                $value = $_REQUEST[$id];

                // Sanitize and format the answer
                if ( is_array( $value ) ) {
                    // Checkbox array
                    $clean_values = array_map( 'sanitize_text_field', $value );
                    $answer_text = '<ul style="margin: 5px 0 0 20px; padding: 0;">';
                    foreach( $clean_values as $v ) { 
                        $answer_text .= '<li style="margin-bottom: 5px;">' . esc_html($v) . '</li>'; 
                    }
                    $answer_text .= '</ul>';
                } else {
                    // Textarea, Radio, Select
                    $answer_text = nl2br( esc_html( sanitize_text_field( $value ) ) );
                }

                // Add to table
                $answers_html .= "<tr><td style='padding: 12px 0; border-bottom: 1px solid #e2e8f0;'>";
                $answers_html .= "<strong style='display: block; color: #334155;'>" . esc_html( $label ) . "</strong>";
                $answers_html .= "<div style='padding-top: 5px; color: #475569;'>" . $answer_text . "</div>";
                $answers_html .= "</td></tr>";
            }
        }
        $answers_html .= "</table><br>"; // Close table

        if ( $has_answers ) {
            // Find the first paragraph closing tag and insert answers after it.
            // This is safer than the 'has been cancelled' string.
            $pos = strpos( $body, '</p>' );
            if ( $pos !== false ) {
                $body = substr_replace( $body, '</p>' . $answers_html, $pos, 4 );
            } else {
                // Fallback: just add it in.
                $body .= $answers_html;
            }
        }
    }
    return $body;
}
add_filter( 'pmpro_email_body', 'my_pmpro_add_answers_to_email', 10, 2 );