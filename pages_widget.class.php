<?php

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class Pages_Widget extends WP_Widget_Pages {

	function widget( $args, $instance ) {
		extract( $args );
        
        global $_wp_additional_image_sizes;

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Pages' ) : $instance['title'], $instance, $this->id_base);
		$sortby = empty( $instance['sortby'] ) ? 'menu_order' : $instance['sortby'];
		$exclude = empty( $instance['exclude'] ) ? '' : $instance['exclude'];
        
		if ( $sortby == 'menu_order' )
			$sortby = 'menu_order, post_title';
        
        $show_title = isset( $instance['show_title'] ) ? $instance['show_title'] : true;
        $show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;
        $show_content = isset( $instance['show_content'] ) ? $instance['show_content'] : false;
        $show_thumb = isset( $instance['show_thumb'] ) ? $instance['show_thumb'] : false;
        $thumb_size = empty( $instance['thumb_size'] ) ? (isset($_wp_additional_image_sizes['post-thumbnail']) ? 'post-thumbnail' : 'thumbnail') : $instance['thumb_size'];
        $post_type = empty( $instance['post_type'] ) ? 'post' : $instance['post_type'];
        
        // temporary set hierarchical
        global $wp_post_types;
        $hierarchical = $wp_post_types[$post_type]->hierarchical;
        $wp_post_types[$post_type]->hierarchical = true;
        
		$out = wp_list_pages( apply_filters('widget_pages_args', array(
            'title_li' => '',
            'echo' => 0,
            'sort_column' => $sortby,
            'exclude' => $exclude,
            'show_title' => $show_title,
            'show_date' => ($show_date ? 'modified' : ''),
            'show_content' => $show_content,
            'show_thumb' => $show_thumb,
            'thumb_size' => $thumb_size,
            'walker' => new Walker_Pages_Widget(),
            'post_type' => $post_type
        ) ) );
        
        // restore hierarchical
        $wp_post_types[$post_type]->hierarchical = $hierarchical;

		if ( !empty( $out ) ) {
			echo $before_widget;
			if ( $title)
				echo $before_title . $title . $after_title;
		?>
		<ul>
			<?php echo $out; ?>
		</ul>
		<?php
			echo $after_widget;
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = parent::update($new_instance, $old_instance);
        
        $instance['show_title'] = (bool) $new_instance['show_title'];
        $instance['show_date'] = (bool) $new_instance['show_date'];
        $instance['show_content'] = (bool) $new_instance['show_content'];
        $instance['show_thumb'] = (bool) $new_instance['show_thumb'];
        $instance['thumb_size'] = strip_tags($new_instance['thumb_size']);
        $instance['post_type'] = strip_tags($new_instance['post_type']);

		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'sortby' => 'post_title', 'title' => '', 'exclude' => '') );
		$title = esc_attr( $instance['title'] );
		$exclude = esc_attr( $instance['exclude'] );
        $show_title = isset( $instance['show_title'] ) ? (bool) $instance['show_title'] : true;
        $show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
        $show_content = isset( $instance['show_content'] ) ? (bool) $instance['show_content'] : false;
        $show_thumb = isset( $instance['show_thumb'] ) ? (bool) $instance['show_thumb'] : false;
        $thumb_size = isset( $instance['thumb_size'] ) ? $instance['thumb_size'] : '';
        $post_type	= esc_attr($instance['post_type']);
        $post_types = get_post_types('', 'objects');
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p>
			<label for="<?php echo $this->get_field_id('sortby'); ?>"><?php _e( 'Sort by:' ); ?></label>
			<select name="<?php echo $this->get_field_name('sortby'); ?>" id="<?php echo $this->get_field_id('sortby'); ?>" class="widefat">
				<option value="post_title"<?php selected( $instance['sortby'], 'post_title' ); ?>><?php _e('Page title'); ?></option>
				<option value="menu_order"<?php selected( $instance['sortby'], 'menu_order' ); ?>><?php _e('Page order'); ?></option>
				<option value="ID"<?php selected( $instance['sortby'], 'ID' ); ?>><?php _e( 'Page ID' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e( 'Exclude:' ); ?></label> <input type="text" value="<?php echo $exclude; ?>" name="<?php echo $this->get_field_name('exclude'); ?>" id="<?php echo $this->get_field_id('exclude'); ?>" class="widefat" />
			<br />
			<small><?php _e( 'Page IDs, separated by commas.' ); ?></small>
		</p>
        
        <p><input class="checkbox" type="checkbox" <?php checked( $show_title ); ?> id="<?php echo $this->get_field_id( 'show_title' ); ?>" name="<?php echo $this->get_field_name( 'show_title' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_title' ); ?>"><?php _e( 'Display page title?' ); ?></label></p>
        
        <p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display page date?' ); ?></label></p>
        
        <p><input class="checkbox" type="checkbox" <?php checked( $show_content ); ?> id="<?php echo $this->get_field_id( 'show_content' ); ?>" name="<?php echo $this->get_field_name( 'show_content' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_content' ); ?>"><?php _e( 'Display page content?' ); ?></label></p>
        
        <p><input class="checkbox" type="checkbox" <?php checked( $show_thumb ); ?> id="<?php echo $this->get_field_id( 'show_thumb' ); ?>" name="<?php echo $this->get_field_name( 'show_thumb' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_thumb' ); ?>"><?php _e( 'Display thumbnail?' ); ?></label></p>
        
        <p><label for="<?php echo $this->get_field_id( 'thumb_size' ); ?>"><?php _e( 'Thumbnail size:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'thumb_size' ); ?>" name="<?php echo $this->get_field_name( 'thumb_size' ); ?>" type="text" value="<?php echo $thumb_size; ?>" /></p>
        
        <p><label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e('Choose the Post Type to display:'); ?></label> 
        <select name="<?php echo $this->get_field_name('post_type'); ?>" id="<?php echo $this->get_field_id('post_type'); ?>" class="widefat">
            <?php foreach ($post_types as $option) : ?>
                <option value="<?=$option->name?>" id="<?=$option->name?>" <?=$post_type == $option->name ? ' selected="selected"' : ''?>><?=$option->name?></option>
            <?php endforeach; ?>
        </select></p>
<?php
	}
}

class Walker_Pages_Widget extends Walker_Page {
	function start_el( &$output, $page, $depth, $args, $current_page = 0 ) {
		if ( $depth )
			$indent = str_repeat("\t", $depth);
		else
			$indent = '';

		extract($args, EXTR_SKIP);
		$css_class = array('page_item', 'page-item-'.$page->ID);
		if ( !empty($current_page) ) {
			$_current_page = get_post( $current_page );
			if ( in_array( $page->ID, $_current_page->ancestors ) )
				$css_class[] = 'current_page_ancestor';
			if ( $page->ID == $current_page )
				$css_class[] = 'current_page_item';
			elseif ( $_current_page && $page->ID == $_current_page->post_parent )
				$css_class[] = 'current_page_parent';
		} elseif ( $page->ID == get_option('page_for_posts') ) {
			$css_class[] = 'current_page_parent';
		}

		$css_class = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );

		$output .= $indent . '<li class="' . $css_class . '">';
        if ($show_thumb && ($thumb = get_the_post_thumbnail($page->ID, (is_numeric($thumb_size) ? array($thumb_size) : $thumb_size))))
            $output .= '<div class="page-thumbnail">'.$thumb.'</div>';
        $output .= '<div class="page-wrapper">';
        if ($show_title) {
            $permalink = get_permalink($page->ID);
            if ($permalink)
                $output .= '<a href="' . $permalink . '">' . $link_before;
            $output .= '<span class="page-title">'.apply_filters( 'the_title', $page->post_title, $page->ID ).'</span>';
            if ($permalink)
                $output .= $link_after . '</a>';
        }
        if ($show_content)
            $output .= '<div class="page-content">'.apply_filters('the_content', $page->post_content).'</div>';

		if ( !empty($show_date) ) {
			if ( 'modified' == $show_date )
				$time = $page->post_modified;
			else
				$time = $page->post_date;

			$output .= " " . mysql2date($date_format, $time);
		}
        
        $output .= '</div>';
	}
}

?>
