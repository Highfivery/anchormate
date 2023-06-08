<?php
/**
 * Plugin Name:       AnchorMate
 * Plugin URI:        https://github.com/Highfivery/anchormate
 * Description:       With 'AnchorMate', transform headlines into clickable adventures in a snap!
 * Version:           1.0.0
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Author:            Highfivery
 * Author URI:        https://highfivery.com
 * License:           GPLv2
 * License URI:       https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain:       anchormate
 *
 * @package anchormate
 */

/**
 * Filters the post content and adds anchor tags to all headline tags.
 *
 * This function applies to the content of the current post. It adds anchor tags
 * to all headline tags with the class `anchormate-anchor`. This allows users to
 * easily create links to specific sections of the post.
 *
 * @see https://developer.wordpress.org/reference/hooks/the_content/
 *
 * @param string $content Content of the current post.
 * @return string Updated content of the current post.
 * @package anchormate
 */

add_filter(
	'the_content',
	function( $content ) {
		// Regex to find headline tags.
		$pattern = '#(?P<full_tag><(?P<tag_name>h\d)(?P<tag_extra>[^>]*)>(?P<tag_contents>[^<]*)</h\d>)#i';

		if ( preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER ) ) {
			$find    = array();
			$replace = array();

			foreach ( $matches as $match ) {
				if ( strlen( $match['tag_extra'] ) && false !== stripos( $match['tag_extra'], 'id=' ) ) {
					continue;
				}

				$find[]     = $match['full_tag'];
				$id         = sanitize_title( $match['tag_contents'] );
				$id_attr    = sprintf( ' id="%s"', $id );
				$link_attr  = sprintf( ' href="#%s"', $id );
				$aria_label = sprintf(
					' aria-label="#%s"',
					/* translators: %s: Content of the headline */
					sprintf( __( 'Link to %s', 'anchormate' ), $match['tag_contents'] )
				);

				$replace[] = sprintf(
					'<%1$s%2$s%3$s>%4$s <a class="anchormate-anchor"%5$s>#</a></%1$s>',
					$match['tag_name'],
					$match['tag_extra'],
					$id_attr,
					$match['tag_contents'],
					$link_attr
				);
			}

					$content = str_replace( $find, $replace, $content );
		}

					return $content;
	}
);
