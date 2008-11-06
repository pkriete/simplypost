<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* Named Paths
 *
 * Used for automatic redirects
 * and to make linking more robust inside
 * templates.
 *
 * Required: home, 404, login
 */
$paths['home']			= 'home';
$paths['404']			= '404';
$paths['login']			= 'member/login';

/* Force a slug on some uris
 *
 * Structure:
 * $force_slug['leading'] = 'slug';
 *
 * Both are regular expressions that are internally
 * wrapped in capture groups, so a pipe (|) <=> OR
 */
$force_slug['category|forum|thread|post|member/profile'] = '\d+';

/* End of file _paths.php */
/* Location: ./templates/default/_paths.php */