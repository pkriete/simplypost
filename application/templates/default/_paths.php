<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Named paths for automatic redirects
$paths['login']			= 'member/login';
$paths['register']		= 'member/register';


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