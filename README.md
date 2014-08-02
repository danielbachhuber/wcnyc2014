A Journey To The Center of WP-CLI
=================================

Daniel Bachhuber - @danielbachhuber - WCNYC 2014

***

Starting At The Surface
-----------------------

***

### What The Client Wants

    @todo this is an example client request

***

### History Of CLI On VIP
```
<?php
/*
 * Moves posts from one category (and all its children) to another category.
 */

die( "Please edit the config settings before running this script\n" );

// for the audit trail - put your user id in here
global $current_user; $current_user = '12345';

// Set blog id
$_blog_id = 12345;
$_blog_url = 'http://testblog.wordpress.com';
switch_to_blog( $_blog_id );
do_it_to_it_remove_term();
```

***

### Thorsten, The Enlightened

   @todo quote from Thorsten

***

### First Pull Request

***

Digging In By Contributing
------------

***

#

***

### Behat: Easy BDD

* Behat is Behavior-Driven Development.
* Tests break into "Context-Action-Outcome".
* Why it's important: it's easy!

    Scenario: Usermeta CRUD
      Given a WP install

      When I run `wp user meta add 1 foo 'bar'`
      And I run `wp user meta get 1 foo`
      Then STDOUT should be:
        """
        bar
        """

***

### BDD For Your Commands

Surprise! Let's ship some code.

***

Internals You Should Use
------------------------

***

### `\WP_CLI\Formatter`

***

### php-cli-tools

* Heavily incorporated into WP-CLI — and we now maintain!

***

### `\WP_CLI\Iterator`

* Query and CSV iterators.
* Iterator == helps you process large data sets.
* Prevent performance issues from reading all data at once.

```
$iterator = new \WP_CLI\Iterators\Query( "SELECT * FROM node" );
foreach( $iterator as $i => $row ) {
	if ( $post = Post::get_by_original_id( $row->nid ) ) {
		WP_CLI::warning( "Post already exists" ) );
		continue;
	}
	$post = Post::create_from_original_row( $row );
}
```

***

### `\WP_CLI\Process` (coming in 0.17.0)

* Create and run a system process.
* Returns `\WP_CLI\ProcessRun` so you can inspect results.

```
wp> $process = \WP_CLI\Process::create( "rm -rf /" );
wp> $process->run()
```

***

```
object(WP_CLI\ProcessRun)#116 (6) {
  ["stdout"]=>
  string(0) ""
  ["stderr"]=>
  string(103) "rm: it is dangerous to operate recursively on '/'
rm: use --no-preserve-root to override this failsafe
"
  ["return_code"]=>
  int(1)
  ["command"]=>
  string(8) "rm -rf /"
  ["cwd"]=>
  NULL
  ["env"]=>
  array(0) {
  }
}
```

***

Get hacking!
-------

@danielbachhuber
https://github.com/danielbachhuber/wcnyc2014
