<?php

namespace P4BKS\Controllers\Blocks;

if ( ! class_exists( 'SplitTwoColumns_Controller' ) ) {

	/**
	 * Class SplitTwoColumns_Controller
	 *
	 * @package P4BKS\Controllers\Blocks
	 */
	class SplitTwoColumns_Controller extends Controller {

		/** @const string BLOCK_NAME */
		const BLOCK_NAME = 'split_two_columns';

		/**
		 * Shortcode UI setup for the shortcode.
		 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
		 */
		public function prepare_fields() {

			$focus_options = [
				[ 'value' => 'left top',      'label' => __( '1 - Top Left', 'planet4-blocks' ) ],
				[ 'value' => 'center top',    'label' => __( '2 - Top Center', 'planet4-blocks' ) ],
				[ 'value' => 'right top',     'label' => __( '3 - Top Right', 'planet4-blocks' ) ],
				[ 'value' => 'left center',   'label' => __( '4 - Middle Left', 'planet4-blocks' ) ],
				[ 'value' => 'center center', 'label' => __( '5 - Middle Center', 'planet4-blocks' ) ],
				[ 'value' => 'right center',  'label' => __( '6 - Middle Right', 'planet4-blocks' ) ],
				[ 'value' => 'left bottom',   'label' => __( '7 - Bottom Left', 'planet4-blocks' ) ],
				[ 'value' => 'center bottom', 'label' => __( '8 - Bottom Center', 'planet4-blocks' ) ],
				[ 'value' => 'right bottom',  'label' => __( '9 - Bottom Right', 'planet4-blocks' ) ],
			];

			$option_values     = get_option( 'planet4_options' );
			$issue_category_id = $option_values['issues_parent_category'] ?? '';
			$categories        = [];
			if ( 0 !== absint( $issue_category_id ) ) {
				$categories   = get_categories( [
					'parent'    => $issue_category_id,                  // Get the dynamic id of the 'Issue' category.
					'orderby'   => 'name',
					'order'     => 'ASC',
				] );
			}

			$options = [];
			if ( $categories ) {
				foreach ( $categories as $category ) {
					$issue = get_page_by_title( html_entity_decode( $category->name ) );      // Category and Issue need to have the same name.
					if ( $issue ) {
						$options[] = [
							'value' => (string) $issue->ID,
							'label' => html_entity_decode( $category->name ),
						];
					}
				}
			}

			$all_tags           = get_tags( [ 'orderby' => 'name' ] );
			$select_tag_options = [ '0' => '--Select Tag--' ];
			foreach ( $all_tags as $tag ) {
				$select_tag_options[] = [
					'value' => (string) $tag->term_id,
					'label' => $tag->name,
				];
			}

			$select_issue_label = sprintf( '<h3>%1$s</h3>%2$s',
										__( 'Issue fields (Column 1 - Left side)', 'planet4-blocks' ),
										__( 'Select an issue', 'planet4-blocks' )
			);
			$select_tag_label   = sprintf(  '<br><hr/><br><h3>%1$s</h3>%2$s',
										__( 'Campaign fields (Column 2 - Right side)', 'planet4-blocks' ),
										__( 'Select a tag', 'planet4-blocks' )
			);

			$fields = [
				[
					'attr'        => 'select_issue',
					'label'       => $select_issue_label,
					'description' => __( 'Associate this block to the Issue that it will talk about', 'planet4-blocks' ),
					'type'        => 'select',
					'options'     => $options,
				],
				[
					'label' => __( 'Issue Title', 'planet4-blocks' ),
					'attr'  => 'title',
					'type'  => 'text',
					'meta'  => [
						'placeholder' => __( 'Enter title', 'planet4-blocks' ),
						'data-plugin' => 'planet4-blocks',
					],
					'description' => __( '(Optional) Fill this only if you need to override issue title.', 'planet4-blocks' ),
				],
				[
					'label' => __( 'Issue Description', 'planet4-blocks' ),
					'attr'  => 'issue_description',
					'type'  => 'textarea',
					'meta'  => [
						'placeholder' => __( 'Enter description', 'planet4-blocks' ),
					],
					'description' => __( '(Optional) Fill this only if you need to override issue description.', 'planet4-blocks' ),
				],
				[
					'label' => __( 'Issue link text', 'planet4-blocks' ),
					'attr'  => 'issue_link_text',
					'type'  => 'text',
					'meta'  => [
						'placeholder' => __( 'Enter link text', 'planet4-blocks' ),
					],
					'description' => __( '(Optional)', 'planet4-blocks' ),
				],
				[
					'label' => __( 'Issue link path', 'planet4-blocks' ),
					'attr'  => 'issue_link_path',
					'type'  => 'url',
					'meta'  => [
						'placeholder' => __( 'Enter link path', 'planet4-blocks' ),
					],
					'description' => __( '(Optional)', 'planet4-blocks' ),
				],
				[
					'label'       => __( 'Issue Image', 'planet4-blocks' ),
					'attr'        => 'issue_image',
					'type'        => 'attachment',
					'libraryType' => [ 'image' ],
					'addButton'   => __( 'Select Image for Issue', 'planet4-blocks' ),
					'frameTitle'  => __( 'Select Image for Issue', 'planet4-blocks' ),
					'description' => __( '(Optional)', 'planet4-blocks' ),
				],
				[
					'label'       => __( 'Select focus point for issue image', 'planet4-blocks' ) . '<img src="' .
					                 esc_url( plugins_url( '/planet4-plugin-blocks/admin/images/grid_9.png' ) ) . '" />',
					'attr'        => 'focus_issue_image',
					'type'        => 'select',
					'options'     => $focus_options,
					'description' => __( '(Optional)', 'planet4-blocks' ),
				],
				[
					'attr'        => 'select_tag',
					'label'       => $select_tag_label,
					'description' => __( 'Associate the selected Issue with a Tag', 'planet4-blocks' ),
					'type'        => 'select',
					'options'     => $select_tag_options,
					'multiple'    => false,
				],
				[
					'label' => __( 'Campaign Description', 'planet4-blocks' ),
					'attr'  => 'tag_description',
					'type'  => 'textarea',
					'meta'  => [
						'placeholder' => __( 'Enter description', 'planet4-blocks' ),
					],
					'description' => __( '(Optional)', 'planet4-blocks' ),
				],
				[
					'label' => __( 'Campaign button text', 'planet4-blocks' ),
					'attr'  => 'button_text',
					'type'  => 'text',
					'meta'  => [
						'placeholder' => __( 'Enter button text', 'planet4-blocks' ),
					],
					'description' => __( '(Optional)', 'planet4-blocks' ),
				],
				[
					'label' => __( 'Campaign button link', 'planet4-blocks' ),
					'attr'  => 'button_link',
					'type'  => 'url',
					'meta'  => [
						'placeholder' => __( 'Enter button link', 'planet4-blocks' ),
					],
					'description' => __( '(Optional)', 'planet4-blocks' ),
				],
				[
					'label'       => __( 'Campaign Image', 'planet4-blocks' ),
					'attr'        => 'tag_image',
					'type'        => 'attachment',
					'libraryType' => [ 'image' ],
					'addButton'   => __( 'Select Image for Campaign', 'planet4-blocks' ),
					'frameTitle'  => __( 'Select Image for Campaign', 'planet4-blocks' ),
					'description' => __( '(Optional)', 'planet4-blocks' ),
				],
				[
					'label'       => __( 'Select focus point for campaign image', 'planet4-blocks' ) . '<img src="' .
					                 esc_url( plugins_url( '/planet4-plugin-blocks/admin/images/grid_9.png' ) ) . '" />',
					'attr'        => 'focus_tag_image',
					'type'        => 'select',
					'options'     => $focus_options,
				],

			];

			/*
			 * Define the Shortcode UI arguments.
			 */
			$shortcode_ui_args = [

				/*
				 * How the shortcode should be labeled in the UI. Required argument.
				 */
				'label' => __( 'Split Two Columns', 'planet4-blocks' ),

				/*
				 * Include an icon with your shortcode. Optional.
				 * Use a dashicon, or full HTML (e.g. <img src="/path/to/your/icon" />).
				 */
				'listItemImage' => '<img src="' . esc_url( plugins_url() . '/planet4-plugin-blocks/admin/images/' . self::BLOCK_NAME . '.png' ) . '" />',

				/*
				 * Define the UI for attributes of the shortcode. Optional.
				 * See above, to where the the assignment to the $fields variable was made.
				 */
				'attrs' => $fields,
			];

			shortcode_ui_register_for_shortcode( 'shortcake_' . self::BLOCK_NAME, $shortcode_ui_args );
		}

		/**
		 * Callback for the shortcode.
		 * It renders the shortcode based on supplied attributes.
		 *
		 * @param array  $fields Array of fields that are to be used in the template.
		 * @param string $content The content of the post.
		 * @param string $shortcode_tag The shortcode tag.
		 *
		 * @return string
		 */
		public function prepare_template( $fields, $content, $shortcode_tag ) : string {
			$issue_id        = absint( $fields['select_issue'] );
			$issue_meta_data = get_post_meta( $issue_id );

			$tag_id            = absint( $fields['select_tag'] );
			$tag               = get_term( $tag_id );
			$campaign_image_id = $fields['tag_image'] ?? get_term_meta( $tag_id, 'tag_attachment_id', true );
			$issue_image_id    = $fields['issue_image'] ?? get_post_thumbnail_id( $issue_id );

			$issue_title       = $fields['title'] ?? ( $issue_meta_data['p4_title'][0] ?? get_the_title( $issue_id ) );
			$issue_description = $fields['issue_description'] ?? ( $issue_meta_data['p4_description'][0] ?? '' );
			$issue_link_text   = $fields['issue_link_text'] ?? __( 'Learn more about this issue', 'planet4-blocks' );
			$issue_link_path   = $fields['issue_link_path'] ?? get_permalink( $issue_id );

			$data = $issue_meta_data ? [
				'issue' => [
					'title'       => $issue_title,
					'description' => $issue_description,
					'image'       => get_the_post_thumbnail_url( $issue_id ),
					'srcset'      => wp_get_attachment_image_srcset( $issue_image_id ),
					'image_alt'   => get_post_meta( $issue_image_id, '_wp_attachment_image_alt', true ),
					'link_text'   => $issue_link_text,
					'link_url'    => $issue_link_path,
					'focus'       => $fields['focus_issue_image'] ?? '',
				],
				'campaign' => [
					'image'       => wp_get_attachment_url( $campaign_image_id ),
					'srcset'      => wp_calculate_image_srcset( [ '1118', '746' ],
										wp_get_attachment_image_src( $campaign_image_id, 'full' )[0],
										wp_get_attachment_metadata( $campaign_image_id ) ),
					'image_alt'   => get_post_meta( $campaign_image_id, '_wp_attachment_image_alt', true ),
					'name'        => $tag->name,
					'link'        => get_tag_link( $tag ),
					'description' => $fields['tag_description'] ?? $tag->description,
					'button_text' => $fields['button_text'] ?? __( 'Get Involved', 'planet4-blocks' ),
					'button_link' => $fields['button_link'] ?? get_tag_link( $tag ),
					'focus'       => $fields['focus_tag_image'] ?? '',
				],
			] : [];

			// Shortcode callbacks must return content, hence, output buffering here.
			ob_start();
			$this->view->block( self::BLOCK_NAME, $data );

			return ob_get_clean();
		}
	}
}
