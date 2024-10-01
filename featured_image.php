<?php
/*** Example of stored as featured image **/
function Generate_Featured_Image( $image_url, $post_id  ){
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($image_url);
    $filename = basename($image_url);
    if(wp_mkdir_p($upload_dir['path']))
      $file = $upload_dir['path'] . '/' . $filename;
    else
      $file = $upload_dir['basedir'] . '/' . $filename;
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
    $res2= set_post_thumbnail( $post_id, $attach_id );
}
?>

<!-- Example - 2 = Form -->
<form method="post" enctype="multipart/form-data">
    <p>
        <label>Select Image:</label>
        <input type="file" name="image" required />
    </p>
    <input type="hidden" name="image_nonce" value="<?php echo wp_create_nonce('image_nonce'); ?>" />
    <input type="submit" name="upload_file" value="Submit" />
</form>

<?php
function fn_set_featured_image() {
    if ( isset( $_POST['upload_file'] ) && wp_verify_nonce( $_REQUEST['image_nonce'], 'image_nonce' ) ) {

        $upload = wp_upload_bits( $_FILES["image"]["name"], null, file_get_contents( $_FILES["image"]["tmp_name"] ) );

        if ( ! $upload['error'] ) {
            $post_id = 'POST_ID_HERE'; //set post id to which you need to add featured image
            $filename = $upload['file'];
            $wp_filetype = wp_check_filetype( $filename, null );
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name( $filename ),
                'post_content' => '',
                'post_status' => 'inherit'
            );

            $attachment_id = wp_insert_attachment( $attachment, $filename, $post_id );

            if ( ! is_wp_error( $attachment_id ) ) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');

                $attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
                wp_update_attachment_metadata( $attachment_id, $attachment_data );
                set_post_thumbnail( $post_id, $attachment_id );
            }
        }
    }
}
add_action('init', 'fn_set_featured_image');

//https://artisansweb.net/set-featured-image-programmatically-wordpress/
