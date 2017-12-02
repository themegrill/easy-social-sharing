<?php
/**
 * EasySocialSharing Sharing
 *
 * Functions for sharing data.
 *
 * @author   ThemeGrill
 * @category Core
 * @package  EasySocialSharing/Functions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cached share networks counts.
 *
 * @param  int    $post_id
 * @param  string $network
 * @param  string $last_share_time
 * @param  string $last_share_ip
 *
 * @return bool
 */
function ess_check_cached_counts( $last_share_time = null ) {

	$expiration = 1;

	$is_cached = false;
	if ( $last_share_time == null || $last_share_time == - 1 ) {

		return $is_cached;
	}
	if ( 0 < $expiration ) {

		if ( strtotime( sprintf( '+ %d hours', $expiration ), strtotime( $last_share_time ) ) > strtotime( 'now' ) ) {
			$is_cached = true;
		}
	}

	return $is_cached;
}

function ess_handle_cache() {

	$option = get_option( 'ess-social-network-cache-date', - 1 );

	if ( $option === - 1 ) {

		update_option( 'ess-social-network-cache-date', date( 'Y-m-d H:i:s' ) );

	} else {

		if ( ! ess_check_cached_counts( $option ) ) {

			update_option( 'ess-social-network-cache-date', date( 'Y-m-d H:i:s' ) );


		}
	}

	return $option;
}

/**
 * Social share networks with Link.
 *
 * @param  string $network
 * @param  string $media_url
 * @param  int    $i
 * @param  string $post_link
 * @param  string $post_title
 *
 * @return string
 */
function ess_share_link( $network, $media_url = '', $i = 0, $post_link = '', $post_title = '' ) {
	if ( ! $network ) {
		return;
	}

	$link = '';

	if ( '' !== $post_link ) {
		$permalink = $post_link;
	} else {
		$permalink = ( class_exists( 'WooCommerce' ) && is_checkout() || is_front_page() ) ? get_bloginfo( 'url' ) : get_permalink();

		if ( class_exists( 'BuddyPress' ) && is_buddypress() ) {
			$permalink = bp_get_requested_url();
		}
	}

	$permalink = rawurlencode( $permalink );

	if ( '' !== $post_title ) {
		$title = $post_title;
	} else {
		$title = class_exists( 'WooCommerce' ) && is_checkout() || is_front_page() ? get_bloginfo( 'name' ) : get_the_title();
	}

	$title = rawurlencode( wp_strip_all_tags( html_entity_decode( $title, ENT_QUOTES, 'UTF-8' ) ) );

	$twitter_username = get_option( 'easy_social_sharing_twitter_username' );

	switch ( $network ) {
		case 'facebook' :
			$link = sprintf( 'http://www.facebook.com/sharer.php?u=%1$s&t=%2$s', esc_attr( $permalink ), esc_attr( $title ) );
			break;
		case 'twitter' :
			$link = sprintf( 'http://twitter.com/share?text=%2$s&url=%1$s&via=%3$s', esc_attr( $permalink ), esc_attr( $title ), ! empty( $twitter_username ) ? esc_attr( $twitter_username ) : get_bloginfo( 'name' ) );
			break;
		case 'googleplus' :
			$link = sprintf( 'https://plus.google.com/share?url=%1$s&t=%2$s', esc_attr( $permalink ), esc_attr( $title ) );
			break;
		case 'pinterest' :
			$link = $media_url ? sprintf( 'http://www.pinterest.com/pin/create/button/?url=%1$s&media=%2$s&description=%3$s', esc_attr( $permalink ), esc_attr( urlencode( $media_url ) ), esc_attr( $title ) ) : '#';
			break;
		case 'stumbleupon' :
			$link = sprintf( 'http://www.stumbleupon.com/badge?url=%1$s&title=%2$s', esc_attr( $permalink ), esc_attr( $title ) );
			break;
		case 'tumblr' :
			$link = sprintf( 'https://www.tumblr.com/share?v=3&u=%1$s&t=%2$s', esc_attr( $permalink ), esc_attr( $title ) );
			break;
		case 'blogger' :
			$link = sprintf( 'https://www.blogger.com/blog_this.pyra?t&u=%1$s&n=%2$s', esc_attr( $permalink ), esc_attr( $title ) );
			break;
		case 'myspace' :
			$link = sprintf( 'https://myspace.com/post?u=%1$s', esc_attr( $permalink ) );
			break;
		case 'delicious' :
			$link = sprintf( 'https://delicious.com/post?url=%1$s&title=%2$s', esc_attr( $permalink ), esc_attr( $title ) );
			break;
		case 'amazon' :
			$link = sprintf( 'http://www.amazon.com/gp/wishlist/static-add?u=%1$s&t=%2$s', esc_attr( $permalink ), esc_attr( $title ) );
			break;
		case 'printfriendly' :
			$link = sprintf( 'http://www.printfriendly.com/print?url=%1$s&title=%2$s', esc_attr( $permalink ), esc_attr( $title ) );
			break;
		case 'yahoomail' :
			$link = sprintf( 'http://compose.mail.yahoo.com/?body=%1$s', esc_attr( $permalink ) );
			break;
		case 'gmail' :
			$link = sprintf( 'https://mail.google.com/mail/u/0/?view=cm&fs=1&su=%2$s&body=%1$s&ui=2&tf=1', esc_attr( $permalink ), esc_attr( $title ) );
			break;
		case 'aol' :
			$link = sprintf( 'http://webmail.aol.com/Mail/ComposeMessage.aspx?subject=%2$s&body=%1$s', esc_attr( $permalink ), esc_attr( $title ) );
			break;
		case 'newsvine' :
			$link = sprintf( 'http://www.newsvine.com/_tools/seed&save?u=%1$s&h=%2$s', esc_attr( $permalink ), esc_attr( $title ) );
			break;
		case 'hackernews' :
			$link = sprintf( 'https://news.ycombinator.com/submitlink?u=%1$s&t=%2$s', esc_attr( $permalink ), esc_attr( $title ) );
			break;
		case 'evernote' :
			$link = sprintf( 'http://www.evernote.com/clip.action?url=%1$s&title=%2$s', esc_attr( $permalink ), esc_attr( $title ) );
			break;
		case 'digg' :
			$link = sprintf( 'http://digg.com/submit?url=%1$s&title=%2$s', esc_attr( $permalink ), esc_attr( $title ) );
			break;
		case 'livejournal' :
			$link = sprintf( 'http://www.livejournal.com/update.bml?subject=%2$s&event=%1$s', esc_attr( $permalink ), esc_attr( $title ) );
			break;
		case 'friendfeed' :
			$link = sprintf( 'http://friendfeed.com/?url=%1$s&title=%2$s', esc_attr( $permalink ), esc_attr( $title ) );
			break;
		case 'buffer' :
			$link = sprintf( 'https://bufferapp.com/add?url=%1$s&title=%2$s', esc_attr( $permalink ), esc_attr( $title ) );
			break;
		case 'reddit' :
			$link = sprintf( 'http://www.reddit.com/submit?url=%1$s&title=%2$s', esc_attr( $permalink ), esc_attr( $title ) );
			break;
		case 'vkontakte' :
			$link = sprintf( 'http://vk.com/share.php?url=%1$s', esc_attr( $permalink ) );
			break;
		case 'linkedin' :
			$link = sprintf( 'http://www.linkedin.com/shareArticle?mini=true&url=%1$s&title=%2$s', esc_attr( $permalink ), esc_attr( $title ) );
			break;
	}

	return $link;
}

/**
 * Get shares number.
 */
function ess_get_shares_number( $social_network, $url, $post_id = '' ) {
	$result = false;

	$raw_url = rawurlencode( $url );

	if ( in_array( $social_network, ess_get_share_networks_with_api_support() ) ) {
		$request_url = '';

		switch ( $social_network ) {
			case 'facebook' :
				if ( $facebook_access_tokens = get_option( 'easy_social_sharing_facebook_access_token' ) ) {
					$request_url = sprintf( 'https://graph.facebook.com/v2.4/?access_token=%1$s&id=', esc_attr( $facebook_access_tokens ) );
				}
				break;
			case 'linkedin' :
				$request_url = 'http://www.linkedin.com/countserv/count/share?format=json&url=';
				break;
			case 'pinterest' :
				$request_url = 'http://widgets.pinterest.com/v1/urls/count.json?url=';
				break;
			case 'googleplus' :
				$request_url = 'https://plusone.google.com/_/+1/fastbutton?url=';
				break;
			case 'stumbleupon' :
				$request_url = 'http://www.stumbleupon.com/services/1.01/badge.getinfo?url=';
				break;
			case 'vkontakte' :
				$request_url = 'https://vk.com/share.php?act=count&index=1&format=json&url=';
				break;
			case 'reddit' :
				$request_url = 'http://www.reddit.com/api/info.json?url=';
				break;
			case 'buffer' :
				$request_url = 'https://api.bufferapp.com/1/links/shares.json?url=';
				break;
		}

		$request_url .= $url;

		$theme_request = wp_remote_get( $request_url, array( 'timeout' => 30 ) );

		if ( ! is_wp_error( $theme_request ) && wp_remote_retrieve_response_code( $theme_request ) == 200 ) {
			$theme_response = wp_remote_retrieve_body( $theme_request );

			if ( 'pinterest' === $social_network ) {
				$theme_response = preg_replace( '/^receiveCount\((.*)\)$/', "\\1", $theme_response );
			}

			if ( 'googleplus' === $social_network ) {
				preg_match( '/window.__SSR = {c:(.*),a:\'/', $theme_response, $matches );

				if ( is_array( $matches ) && isset( $matches[1] ) ) {
					$result = (float) $matches[1];
				}
			} else if ( 'vkontakte' === $social_network ) {
				preg_match( '/VK.Share.count\(1, ([0-9]+)\);/', $theme_response, $matches );

				if ( is_array( $matches ) && isset( $matches[1]) ) {
					$result = (int) $matches[1];
				}
			} else {
				$count_object = json_decode( $theme_response );
			}

			switch ( $social_network ) {
				case 'buffer' :
					$result = isset( $count_object->shares ) ? (int) $count_object->shares : false;

					break;
				case 'facebook' :
					$result = isset( $count_object->share->share_count ) ? (int) $count_object->share->share_count : false;

					break;
				case 'linkedin' :
				case 'pinterest' :
					$result = $count_object->count;

					break;
				case 'stumbleupon' :
					$result = isset( $count_object->result->views ) ? (int) $count_object->result->views : false;

					if ( false === $result && isset( $count_object->success ) && true === $count_object->success ) {
						$result = 0;
					}

					break;
				case 'reddit' :
					$score = 0;

					if ( isset( $count_object->data->children ) ) {
						foreach ( $count_object->data->children as $child ) {
							$score += (int) $child->data->score;
						}
					}

					$result = $score;

					break;
				case 'facebook' :
					$result = $count_object->share->share_count;

					break;
			}
		}
	}

	return $result;
}
