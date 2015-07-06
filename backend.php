<?php
defined( 'ABSPATH' ) or die( 'Please return to the main page' );

/*
* Creating the setting sub menu
*/
function HS_sg_map_menu() {
	echo '<div class="wrap">';
	$page_url = admin_url() . "options-general.php?page=HS_sg_map_menu";
	$delete_url = $page_url . "&delete";
	$edit_url = $page_url . "&nsg=1";
	echo '<h2><a href="' . $page_url . '">' . __( 'Smallgroup Map', 'HS_sg_map' ) . '</a> <a href="' . $edit_url . '" class="add-new-h2">' . __( 'New Smallgroup', 'HS_sg_map' ) . '</a></h2>';
	global $wpdb;
	$table_name = $wpdb->prefix . "HS_sg_map_sgs";
	
	if( isset( $_GET['delete'], $_GET['sgid'] ) && is_numeric( $_GET['sgid'] ) && current_user_can( 'publish_posts' ) ){
		$wpdb->delete( $table_name, array( 'id' => esc_sql( $_GET['sgid'] ) ), array( '%d' ) );
	}
	
	if( isset( $_POST['HS_sg_map_save'] ) && wp_verify_nonce( $_POST['HS_sg_map_save'], 'HS_sg_map_save_data' ) && current_user_can( 'publish_posts' ) ){
		if( isset( $_POST['HS_sg_map_change_coords'] ) ){
			if( isset( $_POST['HS_sg_map_lat'] ) && HS_is_lat( $_POST['HS_sg_map_lat'] ) ){
				update_option( 'HS_sg_map_lat', esc_attr( $_POST['HS_sg_map_lat'] ) );
			}
			
			if( isset( $_POST['HS_sg_map_lng'] ) && HS_is_lng( $_POST['HS_sg_map_lng'] ) ){
				update_option( 'HS_sg_map_lng', esc_attr( $_POST['HS_sg_map_lng'] ) );
			}
			
			if( isset( $_POST['HS_sg_map_zoom'] ) && is_numeric( $_POST['HS_sg_map_zoom'] ) && $_POST['HS_sg_map_zoom'] >= 0 && $_POST['HS_sg_map_zoom'] <= 21 ){
				update_option( 'HS_sg_map_zoom', esc_attr( $_POST['HS_sg_map_zoom'] ) );
			}
		}
		if( HS_is_lat( $_POST['HS_sg_map_sg_lat']) && HS_is_lng( $_POST['HS_sg_map_sg_lng'] ) ){
			if( isset( $_POST['HS_sg_map_sgid'] ) && is_numeric( $_POST['HS_sg_map_sgid'] ) ){
				$wpdb->update( $table_name, array( 'new_members' => esc_sql( $_POST['HS_sg_map_new_members'] ), 'about' => esc_sql( $_POST['HS_sg_map_about'] ), 'description' => esc_sql( $_POST['HS_sg_map_description'] ), 'url' => esc_url( $_POST['HS_sg_map_image'] ), 'lat' => esc_sql( $_POST['HS_sg_map_sg_lat'] ), 'lng' => esc_sql( $_POST['HS_sg_map_sg_lng'] ) ), array( 'id' => esc_sql( $_POST['HS_sg_map_sgid'] ) ), array( '%s', '%s', '%s', '%s', '%f', '%f' ), array( '%d' ) );
			}
			else{
				$wpdb->insert( $table_name, array( 'new_members' => esc_sql( $_POST['HS_sg_map_new_members'] ), 'about' => esc_sql( $_POST['HS_sg_map_about'] ), 'description' => esc_sql( $_POST['HS_sg_map_description'] ), 'url' => esc_url( $_POST['HS_sg_map_image'] ), 'lat' => esc_sql( $_POST['HS_sg_map_sg_lat'] ), 'lng' => esc_sql( $_POST['HS_sg_map_sg_lng'] ) ), array( '%s', '%s', '%s', '%s', '%f', '%f' ) );
			}
			echo '<div class="updated notice is-dismissible"><p>' . __( 'The Smallgroup has successfully been saved', 'HS_sg_map' ) . '</p></div>';
		}
		else{
			echo '<div class="error notice is-dismissible"><p>' . __( 'Saving was not successfull, please try again', 'HS_sg_map' ) . '</p></div>';
		}
	}
	
	if( isset( $_GET['nsg'] ) && !isset( $_POST['HS_sg_map_save'] ) ) {
		global $lat, $lng, $sgid;
		@$sgid = esc_attr( $_GET['sgid'] );
		
		if( $sgid && is_numeric( $sgid ) ){
			$query = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE id = " . $sgid, 'ARRAY_A' );
			$array = $query[0];
			
			$id = esc_attr( $array['id'] );
			$new_members = esc_attr( $array['new_members'] );
			$about = esc_attr( stripslashes( $array['about'] ) );
			$description = esc_attr( stripslashes( $array['description'] ) );
			$url = esc_url( $array['url'] );
			$lat = esc_attr( $array['lat'] );
			$lng = esc_attr( $array['lng'] );
		}
		else{
			$id = -1; $new_members = ''; $about = ''; $description = ''; $url = ''; $lat = ''; $lng = '';
		}
		?>
		<form name='HS_sg_map_options' action='' method='post'>
			<?php if( $sgid ){ ?><input type='hidden' name='HS_sg_map_sgid' value="<?php echo esc_attr( $sgid );?>"><?php } ?>
			<table class='form-table'>
				<tbody>
					<tr>
						<!--label for the new_members input field-->
						<th><label for='HS_sg_map_new_members'><h4><?php _e( 'Open for new Members?', 'HS_sg_map' );?></h4></label></th>
						<td>
							<!--is the group open for new members-->
							<input type='radio' name='HS_sg_map_new_members' value="1" required <?php if( $new_members == 1 ){ echo 'checked'; }?>><img src="<?php echo plugins_url( '/img/true.png', __FILE__ );?>" alt="<?php _e( 'Yes', 'HS_sg_map' );?>">
							<input type='radio' name='HS_sg_map_new_members' value="0" required <?php if( $new_members == 0 ){ echo 'checked'; }?>><img src="<?php echo plugins_url( '/img/false.png', __FILE__ );?>" alt="<?php _e( 'No', 'HS_sg_map' );?>">
						</td>
					</tr>
					<tr>
						<!--label for the about input field-->
						<th><label for='HS_sg_map_about'><h4><?php _e( 'About Us', 'HS_sg_map' );?></h4></label></th>
						<td>
							<!--a text about the group-->
							<textarea name='HS_sg_map_about' maxlength='65500' rows="7" cols="30"placeholder="<?php _e( 'About Us', 'HS_sg_map' );?>" required><?php echo esc_attr( $about );?></textarea>
						</td>
					</tr>
					<tr>
						<!--label for the description input field-->
						<th><label for='HS_sg_map_description'><h4><?php _e( 'Description', 'HS_sg_map' );?></h4></label></th>
						<td>
							<!--description of the group-->
							<textarea name='HS_sg_map_description' maxlength='65500' rows="7" cols="30"placeholder="<?php _e( 'Description', 'HS_sg_map' );?>" required><?php echo esc_attr( $description );?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for='HS_sg_map_image'><h4><?php _e( 'Upload Image', 'HS_sg_map' );?></h4></label></th>
						<td>
							<input id="HS_sg_map_image" name="HS_sg_map_image" type="text" size="36" placeholder="<?php _e( 'URL', 'HS_sg_map' );?>" value="<?php echo esc_url( $url );?>" required/>
							<input id="HS_sg_map_image_button" class="button" type="button" value="<?php _e( 'Choose Image', 'HS_sg_map' ); ?>" />
								<br><?php _e( 'Enter a URL or upload an image', 'HS_sg_map' ); ?>
						</td>
					</tr>
					<tr>
						<td colspan="2">
						<h3><?php _e( 'Choose a location on the map, where the smallgroup is located', 'HS_sg_map' ); ?></h3>
						<?php echo do_shortcode( '[HS_sg_map selected_sg="' . esc_attr( $id ) .'"]' ); ?>
						</td>
					</tr>
					<tr>
						<td class='submit'><input type='submit' class='button-primary' value="<?php _e( 'Save' );?>"></td>
						<td><?php wp_nonce_field( 'HS_sg_map_save_data', 'HS_sg_map_save' );?></td>
					</tr>
				</tbody>
			</table>
		</form>
		<?php
	}
	else{
		$array = $wpdb->get_results( "SELECT * FROM ". $table_name, 'ARRAY_A' );
		
		?>
		<form action="" method="post">
		<table class="wp-list-table widefat fixed pages">
				<thead><tr>
				<th class="manage-column column-cb check-column"><!--<input type="checkbox">--></th>
				<th class='manage-column'><?php _e( "Picture", 'HS_sg_map' );?></th>
				<th></th>
				<th class='manage-column'><?php _e( "Open for new Members?", 'HS_sg_map' );?></th>
				<th class='manage-column'><?php _e( "About Us", 'HS_sg_map' );?></th>
				<th class='manage-column' colspan="3"><?php _e( "Description", 'HS_sg_map' );?></th>
				</tr></thead>
				<tbody><?php
		foreach( $array as $i => $row ) {
			?><tr>
				<th class="manage-column column-cb check-column"><?php /*<input type="checkbox" name="HS_sg_map_sgs[]" value="<?php _e( esc_attr( $row['id'] ) );?>">*/ ?></th>
				<td class='manage-column'><img src="<?php echo esc_url( $row['url'] );?>" style="height:100px;width:100px;"></td>
				<td class='manage-column'><div class="row-actions"><?php echo '<span class="edit"><a href="' . esc_url( $edit_url ) . '&sgid=' . esc_attr( $row['id'] ) . '">' . __( 'Edit', 'HS_sg_map' ) . '</a></span>'; ?> | <?php echo '<span class="delete"><a href="' . esc_url( $delete_url ) . '&sgid=' . esc_attr( $row['id'] ) . '">' . __( 'Delete', 'HS_sg_map' ) . '</a></span>'; ?></div>
				<td class='manage-column'><?php if( $row['new_members'] == 1 ){ ?><img src="<?php echo plugins_url( '/img/true.png', __FILE__ ); ?>" alt="<?php _e( 'Yes', 'HS_sg_map' ); ?>"><?php } else{ ?><img src="<?php echo plugins_url( '/img/false.png', __FILE__ );?>" alt="<?php _e( 'No', 'HS_sg_map' );?>"><?php }?></td>
				<td class='manage-column'><?php echo esc_attr( stripslashes( $row['about'] ) );?></td>
				<td class='manage-column' colspan="3"><?php echo esc_attr( stripslashes( $row['description'] ) );?></td>
			</tr><?php
		}
		
		echo "</tbody></table></form>";
	}
		
	echo "</div>";
}