A Journey To The Center of WP-CLI
=================================

Daniel Bachhuber - @danielbachhuber - WCNYC 2014

***

Starting At The Surface
-----------------------

***

### What The Client Wants

> For our upcoming site rebrand, we need to migrate all of the

***

### History Of CLI On VIP

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

***

### Thorsten The Enlightened, December 2011

> I played with WP_CLI yesterday a bit and will likely build all my further developments on top of it. It’s utilizing php-cli-tools and gives you easy access to all kind of parameter parsing, progress bars, menus, user input, tables and the like. With minor effort I was able to integrate my CLI exporter into it and now am trying to get commit access to the repository. I’ll likely implement new scripts on top of it from now on and if possible will port over some of the old stuff."

***

Digging In By Contributing
------------

***

### First Pull Request

@todo whatever

***

### Design Decisions: Composability

* The output from one command should be easily pipe-able to another command.
* Similarly, there should be no overlapping functionality between commands.

    // Bad
    $ wp post delete --post_type=banana --force

    // Good
    $ wp post delete $(wp post list --format=ids --post_type=banana) --force

***

### Design Decisions: Run In Admin

1. Added `define( 'WP_ADMIN', true )` in v0.8.0 (#164) to fix WP Super Cache.
2. Removed in v0.9.0 because we no longer loaded advanced-cache.php (#351).
3. Final verdict in v0.10.0 (#385): WP-CLI is an alternative to wp-admin.

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

### Behat: Easy BDD

    Scenario: Disabled commands
      Given a WP install
      And a config.yml file:
        """
        disabled_commands:
        - eval-file
        """

      When I try `WP_CLI_CONFIG_PATH=config.yml wp help eval-file`
      Then STDERR should be:
        """
        Error: The 'eval-file' command has been disabled from the config file.
        """

***

### Behat: Easy BDD

    Scenario: Impose Site Users
      Given a WP install
      And a site-users.yml file:
        """
        state: site
          users:
            editorone:
              display_name: Editor One
              email: editorone@example.com
              role: editor
        """

      When I run `wp dictator impose site-users.yml`
      Then STDOUT should not be empty

      When I run `wp dictator compare site-users.yml`
      Then STDOUT should be empty

***

### BDD For Your Commands

Surprise! Let's ship some code.

***

Internals You Should Use
------------------------

***

### `\WP_CLI\Formatter`

* Output your results as a table, CSV, JSON or just IDs.

***

### php-cli-tools

* Heavily incorporated into WP-CLI — and we now maintain!

***

### `\WP_CLI\Iterator`

* Query and CSV iterators.
* Iterator == helps you process large data sets.
* Prevent performance issues from reading all data at once.

    $iterator = new \WP_CLI\Iterators\Query( "SELECT * FROM node" );
    foreach( $iterator as $i => $row ) {
    	if ( $post = Post::get_by_original_id( $row->nid ) ) {
    		WP_CLI::warning( "Post already exists" ) );
    		continue;
    	}
    	$post = Post::create_from_original_row( $row );
    }

***

### `\WP_CLI\Process` (coming in 0.17.0)

* Create and run a system process.
* Returns `\WP_CLI\ProcessRun` so you can inspect results.

    wp> $process = \WP_CLI\Process::create( "rm -rf /" );
    wp> $process->run()

***

    object(WP_CLI\ProcessRun)#116 (6) {
      ["stdout"] => string(0) ""
      ["stderr"] => string(103) "rm: it is dangerous to operate recursively on '/' rm: use --no-preserve-root to override this failsafe"
      ["return_code"] => int(1)
      ["command"]=> string(8) "rm -rf /"
      ["cwd"] => NULL
      ["env"] =>
      array(0) {}
    }

***

Get hacking!
-------

@danielbachhuber
https://github.com/danielbachhuber/wcnyc2014
