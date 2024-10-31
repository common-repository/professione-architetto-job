<?php
/**
 * Plugin Name: professioneArchitetto Job
 * Plugin URI: https://www.professionearchitetto.it/banner/
 * Description: Le opportunità di lavoro pubblicate su professione Architetto.
 * Version: 0.3
 * Author: redazione professione Architetto
 * Author URI: https://www.professionearchitetto.it/
 * License: GPLv2 or later
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 * Text Domain: professionearchitetto-job
 * Domain Path: /languages

*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( !class_exists( 'professioneArchitetto_job_RSS' ) ) :
define('PROF_ARCH_JOB', 'professionearchitetto-job');

class professioneArchitetto_job_RSS extends WP_Widget {
	public $plugin_basename;
	public $plugin_url;
	public $plugin_path;
	public $feedurl = 'https://www.professionearchitetto.it/rss/pajob/';
	public $defaultTitle = 'professioneArchitetto Job';
	public $version = '0.2';

	function __construct() {		
		$widget_ops = array( 'description' => __("Le opportunità di lavoro pubblicate su professione Architetto", PROF_ARCH_JOB) );
		parent::__construct('professioneArchitetto_job', 'professioneArchitetto Job', $widget_ops);
	}

	public function widget( $args, $instance ) {
		if ( isset($instance['error']) && $instance['error'] ) return;
	
		$url = 'https://www.professionearchitetto.it/rss/pajob/';
		if(isset($instance['regione']) && $instance['regione'] != 0) $url .= "?rg=" . $instance['regione'];
		$rss = fetch_feed($url);
		$title = $instance['title'];
		$desc = '';
		$link = '';
 
		if ( ! is_wp_error($rss) ) {
			$desc = esc_attr(strip_tags(@html_entity_decode($rss->get_description(), ENT_QUOTES, get_option('blog_charset'))));
			if ( empty($title) ) $title = strip_tags( $rss->get_title() );
			$link = strip_tags( $rss->get_permalink() );
			while ( stristr($link, 'http') != $link ) $link = substr($link, 1);
		}
 
		if(empty($title)) $title = $desc;
 
		$title = '<a class="rsswidget" href="' . esc_url( $link ) . '">'. esc_html( $title ) . '</a>'; 
		echo $args['before_widget'];
		echo $args['before_title'] . $title . $args['after_title'];
//		echo $url;
		$this->rss_output( $rss, $instance );
		echo $args['after_widget'];
 
		if ( ! is_wp_error($rss) ) $rss->__destruct();
		unset($rss);
	}
 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args((array) $new_instance, array( 'title' => $this->defaultTitle, 'regione' => '', 'items' => 5, 'error' => false, 'show_summary' => 0, 'show_date' => 0, 'nonews' => "Non ci sono offerte" ));
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['regione'] = strip_tags($new_instance['regione']);
		$instance['items'] = strip_tags($new_instance['items']);
		$instance['error'] = strip_tags($new_instance['error']);
		$instance['show_summary'] = strip_tags($new_instance['show_summary']);
		$instance['show_date'] = strip_tags($new_instance['show_date']);
		$instance['nonews'] = strip_tags($new_instance['nonews']);
		return $instance;
	}

	function form( $args ) {
		$args = wp_parse_args( (array) $args, array( 'title' => $this->defaultTitle, 'regione' => 0, 'items' => 5, 'error' => false, 'show_summary' => 0, 'show_date' => 0 ) );
		$title = strip_tags($args['title']);
		$args['number'] = $this->number;
		$args['url'] = $this->feedurl;

    $esc_number = esc_attr( $args['number'] );
?>
    <p><label for="rss-title-<?php echo $esc_number; ?>"><?php _e( 'Inserisci un titolo per il widget (opzionale):', PROF_ARCH_JOB ); ?></label>
    <input class="widefat" id="rss-title-<?php echo $esc_number; ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $args['title'] ); ?>" /></p>
    <p><label for="rss-regione-<?php echo $esc_number; ?>"><?php _e( 'Indica la regione di interesse:', PROF_ARCH_JOB ); ?></label>
		<select class="widefat" id="rss-regione-<?php echo $esc_number; ?>" name="<?php echo $this->get_field_name('regione'); ?>">
    <?php
			$NomiRegioni = array(0=> "Italia", -3 => "Sud Italia", -2 => "Centro Italia", -1 => "Nord Italia", 1=> "Abruzzo", 2=> "Basilicata", 3=> "Calabria", 4=> "Campania", 5=> "Emilia Romagna", 6=> "Friuli Venezia Giulia", 7=> "Lazio", 8=> "Liguria", 9=> "Lombardia", 10=> "Marche", 11=> "Molise", 12=>"Piemonte", 13=> "Puglia", 14=> "Sardegna", 15=> "Sicilia", 16=> "Toscana", 17=> "Trentino Alto-Adige", 18=> "Umbria", 19=> "Valle d'Aosta", 20=> "Veneto");
			foreach($NomiRegioni as $key => $value) {
				echo "<option value='$key' " . selected( $args['regione'], $key, false ) . ">$value</option>";
			}
    ?>
    </select></p>
    <p><label for="rss-items-<?php echo $esc_number; ?>"><?php _e( 'Quanti annunci mostrare?', PROF_ARCH_JOB ); ?></label>
    <select id="rss-items-<?php echo $esc_number; ?>" name="<?php echo $this->get_field_name('items'); ?>">
    <?php
    for ( $i = 1; $i <= 20; ++$i ) {
        echo "<option value='$i' " . selected( $args['items'], $i, false ) . ">$i</option>";
    }
    ?>
    </select></p>
    <p><input id="rss-show-summary-<?php echo $esc_number; ?>" name="<?php echo $this->get_field_name('show_summary'); ?>" type="checkbox" value="1" <?php checked( $args['show_summary'] ); ?> />
    <label for="rss-show-summary-<?php echo $esc_number; ?>"><?php _e( 'Mostrare il dettaglio?', PROF_ARCH_JOB ); ?></label></p>
    <p><input id="rss-show-date-<?php echo $esc_number; ?>" name="<?php echo $this->get_field_name('show_date'); ?>" type="checkbox" value="1" <?php checked( $args['show_date'] ); ?>/>
    <label for="rss-show-date-<?php echo $esc_number; ?>"><?php _e( 'Mostrare la data di pubblicazione?', PROF_ARCH_JOB ); ?></label></p>
    <p><label for="rss-nonews-<?php echo $esc_number; ?>"><?php _e( 'Il testo da mostrare se non ci sono articoli:', PROF_ARCH_JOB ); ?></label>
    <input class="widefat" id="rss-nonews-<?php echo $esc_number; ?>" name="<?php echo $this->get_field_name('nonews'); ?>" type="text" value="<?php echo esc_attr( $args['nonews'] ); ?>" /></p>
<?php
	}

	private function rss_output( $rss, $args = array() ) {
    if ( is_string( $rss ) ) {
        $rss = fetch_feed($rss);
    } elseif ( is_array($rss) && isset($rss['url']) ) {
        $args = $rss;
        $rss = fetch_feed($rss['url']);
    } elseif ( !is_object($rss) ) {
        return;
    }
 
    $default_args = array( 'show_author' => 0, 'show_date' => 0, 'show_summary' => 0, 'items' => 0, 'nonews' => "Non ci sono offerte"  );
    $args = wp_parse_args( $args, $default_args );

    if ( is_wp_error($rss) ) {
			echo '<ul><li>' . $args['nonews'] . '</li></ul>';
/*
			if ( is_admin() || current_user_can('manage_options') )
				echo '<p>' . sprintf( __('<strong>RSS Error</strong>: %s', PROF_ARCH_JOB), $rss->get_error_message() ) . '</p>';
*/
			return;
    }
  
    $items = (int) $args['items'];
    if ( $items < 1 || 20 < $items )
        $items = 10;
    $show_summary  = (int) $args['show_summary'];
    $show_author   = (int) $args['show_author'];
    $show_date     = (int) $args['show_date'];
 
    if ( !$rss->get_item_quantity() ) {
        echo '<ul><li>' . __( 'An error has occurred, which probably means the feed is down. Try again later.', PROF_ARCH_JOB ) . '</li></ul>';
        $rss->__destruct();
        unset($rss);
        return;
    }
    echo '<ul>';
    foreach ( $rss->get_items( 0, $items ) as $item ) {
        $link = $item->get_link();
        while ( stristr( $link, 'http' ) != $link ) {
            $link = substr( $link, 1 );
        }
        $link = esc_url( strip_tags( $link ) );
 
        $title = esc_html( trim( strip_tags( $item->get_title() ) ) );
        if ( empty( $title ) ) {
            $title = __( 'Untitled', PROF_ARCH_JOB );
        }
 
        $desc = @html_entity_decode( $item->get_description(), ENT_QUOTES, get_option( 'blog_charset' ) );
        //$desc = esc_attr( wp_trim_words( $desc, 55, ' [&hellip;]' ) );
 
        $summary = '';
        if ( $show_summary ) {
            $summary = $desc;
 
            // Change existing [...] to [&hellip;].
            if ( '[...]' == substr( $summary, -5 ) ) {
                $summary = substr( $summary, 0, -5 ) . '[&hellip;]';
            }
 
            //$summary = '<div class="rssSummary">' . esc_html( $summary ) . '</div>';
            $summary = '<div class="rssSummary">' . $summary . '</div>';
        }
 
        $date = '';
        if ( $show_date ) {
            $date = $item->get_date( 'U' );
 
            if ( $date ) {
                $date = ' <div class="rss-date">' . date_i18n( get_option( 'date_format' ), $date ) . '</div>';
            }
        }
 
        $author = '';
        if ( $show_author ) {
            $author = $item->get_author();
            if ( is_object($author) ) {
                $author = $author->get_name();
                $author = ' <cite>' . esc_html( strip_tags( $author ) ) . '</cite>';
            }
        }
 
        if ( $link == '' ) {
            echo "<li>$title{$date}{$summary}{$author}</li>";
        } elseif ( $show_summary ) {
            echo "<li><a class='rsswidget' href='$link'>$title</a>{$date}{$summary}{$author}</li>";
        } else {
            echo "<li><a class='rsswidget' href='$link'>$title</a>{$date}{$author}</li>";
        }
    }
    echo '</ul>';
    $rss->__destruct();
    unset($rss);
	}

}
endif;

function professioneArchitetto_job_RSS_Register()
{
	$locale = apply_filters( 'plugin_locale', get_locale(), PROF_ARCH_JOB );
	load_plugin_textdomain( PROF_ARCH_JOB, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	return register_widget( "professioneArchitetto_job_RSS" );
}
add_action( 'widgets_init', 'professioneArchitetto_job_RSS_Register' );
