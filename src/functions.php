<?php
/**
 * Filter the allowed HTML tags for a specific context.
 *
 * This function extends the list of allowed HTML tags and attributes for specific contexts
 * using the `wp_kses` function. The contexts can be 'sizepsgf_kses' for general HTML content
 * or 'sizepsgf_img' for image-specific tags.
 *
 * @param array  $sizepsgf_tags    The default allowed HTML tags and attributes.
 * @param string $sizepsgf_context The context in which the HTML is being filtered.
 * @return array The modified list of allowed HTML tags and attributes.
 *
 * @since 1.0.0
 */
function sizepsgf_kses_allowed_html($sizepsgf_tags, $sizepsgf_context)
{
    switch ($sizepsgf_context) {
        case 'sizepsgf_kses':
            $sizepsgf_tags = array(
                'div'    => array(
                    'class' => array(),
                ),
                'ul'     => array(
                    'class' => array(),
                ),
                'li'     => array(),
                'span'   => array(
                    'class' => array(),
                ),
                'a'      => array(
                    'href'  => array(),
                    'class' => array(),
                ),
                'i'      => array(
                    'class' => array(),
                ),
                'p'      => array(),
                'em'     => array(),
                'br'     => array(),
                'strong' => array(),
                'h1'     => array(),
                'h2'     => array(),
                'h3'     => array(),
                'h4'     => array(),
                'h5'     => array(),
                'h6'     => array(),
                'del'    => array(),
                'ins'    => array(),
                'option' => array(
                    'value' => array(),
                    'data-item' => array(),
                ),
            );
            return $sizepsgf_tags;
        case 'sizepsgf_img':
            $sizepsgf_tags = array(
                'img' => array(
                    'class'  => array(),
                    'height' => array(),
                    'width'  => array(),
                    'src'    => array(),
                    'alt'    => array(),
                ),
            );
            return $sizepsgf_tags;
        default:
            return $sizepsgf_tags;
    }
}

function sizepsgf_custom_sanitize($input) {
   
    $input = wp_kses_post($input);
    $input = sanitize_text_field($input); 
    $input = str_replace('"', '\\"', $input);
    return $input;
}

/**
 * Sanitizes the custom field items data.
 *
 * This function takes input data, which can be either an array or a string,
 * and sanitizes it by ensuring that keys are safe and values are properly 
 * sanitized as text fields. It returns an associative array of sanitized 
 * values or a single sanitized string.
 *
 * @param mixed $data The input data to be sanitized (array or string).
 * @return array|string The sanitized data.
 *
 * @since 1.0.0
 */
function sizepsgf_sanitize_custom_field_items( $data  )
{
    $sanitized_data = array();

    if ( is_array( $data ) ) {
        foreach ( $data as $key => $value ) {
            $sanitized_key = sanitize_key( $key );

            // Check if $value is an array before using array_map
            if ( is_array( $value ) ) {
                $sanitized_value = array_map('sanitize_text_field', $value );
            } else {
                $sanitized_value = sanitize_text_field( $value );
            }

            $sanitized_data[$sanitized_key] = $sanitized_value;
        }
    } else {
        // Sanitize non-array data
        $sanitized_data = sanitize_text_field( $data );
    }

    return $sanitized_data;
}


// product per page
function sizepsgf_product_per_page( $products ) {
    $sizepsgf_product_per_page  = '';
    if (!empty(get_option('sizepsgf_product_per_page'))) {
        $products = get_option('sizepsgf_product_per_page');
    }
    
    return $products;
}

add_filter( 'loop_shop_per_page', "sizepsgf_product_per_page"  , 30 );

/** search panel initial output and after widget save this output will show  */
function sizepsgf_all_product_panel_output($select_all_product = [] ) {
    
    $product_args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'order' => 'desc',
        'ignore_sticky_posts' => 'true'
    );

    $product_output = '';
    $product_query = new WP_Query($product_args);
    $product_output .=  '<option  data-item="" value="empty">Select Product</option>';
    if ($product_query->have_posts()) :
        $search_item_name = 'album';
        $count = 0;
        while ($product_query->have_posts()) : $product_query->the_post();
            global $post;
            $product_id         = $post->ID;
            $select_product = "";
            
            if( is_array( $select_all_product ) || is_object( $select_all_product ) ) {
                foreach( $select_all_product as $key => $value ) {
                    $get_product =  $select_all_product[$key];
                    if ( $get_product == $product_id  ) {
                        $select_product = "selected='selected'";
                    } 
                }
            }
            
           $product_output .= '<option '.$select_product.' data-item="'.$product_id.'" value="'.$product_id.'">'.get_the_title().'</option>';

            $count++;
        endwhile;

        wp_reset_postdata();
    endif; 
   
    return $product_output;
}

/**
 * All exclude product functions
 */
 function sizepsgf_exclude_product_panel_output( $sizepsgf_exclude_product = [] ) {
    
    $product_args = array(
        'post_type'           => 'product',
        'post_status'         => 'publish',
        'posts_per_page'      => -1,
        'order'               => 'desc',
        'ignore_sticky_posts' => 'true'
    );

    
    $product_output = '';
    $product_query = new WP_Query($product_args);
    $product_output .=  '<option  data-item="" value="empty">Select Product</option>';

    if ( $product_query->have_posts() ) :

        $search_item_name = 'album';
        $count = 0;
        
        while ( $product_query->have_posts() ) : $product_query->the_post();
            global $post;
            $product_id         = $post->ID;
            $select_product = " ";
            if( is_array( $sizepsgf_exclude_product ) || is_object( $sizepsgf_exclude_product ) ) {
                foreach( $sizepsgf_exclude_product as $key => $value ) {
                    $get_product =  $sizepsgf_exclude_product[$key];
                    if ( $get_product == $product_id  ) {
                        $select_product = "selected='selected'";
                    } 
                }
            }
            
           $product_output .= '<option '.$select_product.' data-item="'.$product_id.'" value="'.$product_id.'">'.get_the_title().'</option>';

            $count++;
        endwhile;

        wp_reset_postdata();
    endif; 
   
    return $product_output;
}

/**
 * get all product categories
 */

function sizepsgf_get_product_categories( $product_ids = [] ) {

    $options = array();
    $taxonomy = 'product_cat';
    $category_output = '';

    if (!empty( $taxonomy ) ) {
        $terms = get_terms(
            array(
                'parent' => 0,
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
            )
        );

        $category_output .=  '<option  data-item="" value="empty">Select Categories</option>';

        if ( !empty( $terms ) ) {
            foreach ( $terms as $index => $term ) {
                if ( isset( $term ) ) {
                    $options[''] = 'Select';
                    $select_product = ' ';
                    // Get the option and check if it is an array or object
                   
                    if ( $product_ids ) {
                        if ( is_array($product_ids ) || is_object( $product_ids ) ) {
                            foreach ($product_ids as $key => $value) {
                                $get_category = $value; // Retrieve the value correctly
                                if ( $get_category == $term->term_id ) {
                                    $select_product = "selected='selected'";
                                    break; // Exit the loop once a match is found
                                }
                            }
                        }
                    }
                    if ( isset($term->slug ) && isset( $term->name ) ) {
                        $category_output .= '<option  '.$select_product.' data-item="'.$term->term_id.'" value="'.$term->term_id.'">'.$term->name.'</option>';
                    }
                }
                
            }
        }
    }

    printf("%s", do_shortcode( $category_output  ) );
}

/**
 * Custom function to retrieve an option with a default value.
 *
 * @param string $option_name The name of the option to retrieve.
 * @param mixed $default The default value to return if the option does not exist.
 * @return mixed The value of the option, or the default value if the option does not exist.
 */
function sizepsgf_get_option( $option_name, $default = false ) {
    // Check if the option exists
    $option_value = get_option($option_name, $default);
    
    // If the option does not exist, return the default value
    if ($option_value === false) {
        return $default;
    }

    return $option_value;
}

/**
 * sizepsgf_do_shortcode function
 *
 * @param [type] $shortcode
 * @param array $atts
 * @return void
 */
function sizepsgf_do_shortcode($shortcode, $atts = []) {
    $atts_string = '';
    foreach ($atts as $key => $value) {
        $atts_string .= $key . '="' . esc_attr($value) . '" ';
    }
    $atts_string = trim($atts_string);

    return do_shortcode("[$shortcode $atts_string]");
}
