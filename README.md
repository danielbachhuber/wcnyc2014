A Journey To The Center of WP-CLI
=================================

[Daniel Bachhuber](http://danielbachhuber.com) - [@danielbachhuber](https://twitter.com/danielbachhuber) - WCNYC 2014

***

Starting At The Surface
-----------------------

***

### What The Client Needs

> For our upcoming site rebrand, we need to move all of the 'Entrepreneurship' posts (and children) to the "Businessology" category. Also, because it's tied to the theme launch, we need this to happen as close to the launch as possible.

> Can you be around on Sunday night to make this happen?

***

### Bin Directory Full Of Scripts

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

> I played with WP_CLI yesterday a bit and will likely build all my further developments on top of it. It’s utilizing php-cli-tools and gives you easy access to all kind of parameter parsing, progress bars, menus, user input, tables and the like.

> With minor effort I was able to integrate my CLI exporter into it and now am trying to get commit access to the repository. I’ll likely implement new scripts on top of it from now on and if possible will port over some of the old stuff.

***

Digging In By Contributing
--------------------------

***

### My First Pull Request ([#165](https://github.com/wp-cli/wp-cli/pull/165))

> It would be nice if `wp post delete` accepted `--post_type` as an argument (as well as other arguments like date, etc.)

I turned this:

    wp post delete <ID> --force

... into this:

    wp post delete [<ID>] [--post_type=<value>] [--post_author=<value>] [--post_status=<value>] [--force]

Woo hoo!

***

### Revert!

But, my first pull request was reverted ([#181](https://github.com/wp-cli/wp-cli/pull/181)):

> Adding associative parameters to wp post delete was a mistake. It should have been a separate subcommand all along. It will make both documentation and implementation clearer.

***

### Design Decisions: Composability

* The output from one command should be easily pipe-able to another command.
* Similarly, there should be no overlapping functionality between commands.

<!-- Markdown formatting hack -->

    // Bad
    $ wp post delete --post_type=banana --force

    // Good
    $ wp post delete $(wp post list --format=ids --post_type=banana) --force

***

### Other Pull Requests


***

### Design Decisions: Run In Admin

1. Added `define( 'WP_ADMIN', true )` in v0.8.0 ([#164](https://github.com/wp-cli/wp-cli/pull/164)) to fix WP Super Cache.
2. Removed in v0.9.0 because WP-CLI no longer loaded advanced-cache.php ([#351](https://github.com/wp-cli/wp-cli/issues/351)).
3. Final verdict in v0.10.0 ([#385](https://github.com/wp-cli/wp-cli/pull/385#issuecomment-16661583)): WP-CLI is an alternative to wp-admin.
4. Ultimately, this means you have access to all admin functions.

***

### Behat: Easy BDD

* Behat is Behavior-Driven Development.
* Tests break into "Context-Action-Outcome".
* Why it's important: it's easy!

In features/user-meta.feature:

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

In features/config.feature:

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

In features/users.feature:

    Scenario: Impose Site Users
      Given a WP install
      And a site-users.yml file:
        """
        state: site
          users:
            editorone:
              email: editorone@example.com
              role: editor
        """

      When I run `wp dictator impose site-users.yml`
      Then STDOUT should not be empty

      When I run `wp dictator compare site-users.yml`
      Then STDOUT should be empty

***

### BDD For Your Commands

Surprise! [Let's ship some code](https://github.com/wp-cli/wp-cli/pull/1309).

***

Internals You Should Use
------------------------

***

### \WP_CLI\Formatter

* Output your results as a table, CSV, JSON, count or just IDs.
* Expects: format, an array of objects, and named fields to display.

<!-- Markdown formatting hack -->

    WP_CLI\Utils\format_items( 'json', get_users(), array( 'ID', 'user_login' ) );

***

### WP_CLI Class Static Methods

    // Run a command without launching a new process
    WP_CLI::run_command( array( 'user', 'create', 'danieltest', 'd+danieltest@danielbachhuber.com' ), array( 'role' => 'administrator' ) );

    // Launch a new process to run a command
    WP_CLI::launch_self( 'user create', array( 'danieltest2', 'd+danieltest2@danielbachhuber.com' ), array( 'role' => 'administrator' ) );

    // Logger wrappers
    WP_CLI::log( 'Updated post 785 title to "The New Post Title"');
    WP_CLI::success( 'Updated 9 posts' );
    WP_CLI::warning( 'Invalid post id.' );
    WP_CLI::error( 'Skynet is here' );

    // Colorize your strings
    WP_CLI::colorize( '%RApple%n%g' ); // "% R" for red bright, "% n % g" to restore to green

***

### [php-cli-tools](https://github.com/wp-cli/php-cli-tools)

* Heavily incorporated into WP-CLI — and we now maintain!
* Used to build table format, `WP_CLI::colorize()` and others.
* Includes other useful tools too.

<!-- Markdown formatting hack -->

    $progress = new \cli\progress\Bar( 'Demoing progress bar', 10 );
    for ( $i = 0; $i < 10; $i++ ) {
    	$progress->tick();
    	usleep( 500000 );
    }
    $progress->finish();

***

### \WP_CLI\Iterator

* Query and CSV iterators. Iterator == helps you process large data sets.
* Prevent performance issues from reading all data at once.

<!-- Markdown formatting hack -->

    $iterator = new \WP_CLI\Iterators\Query( "SELECT * FROM node" );
    foreach( $iterator as $i => $row ) {
    	if ( $post = Post::get_by_original_id( $row->nid ) ) {
    		WP_CLI::warning( "Post already exists" ) );
    		continue;
    	}
    	$post = Post::create_from_node_row( $row );
    }

    foreach ( new \WP_CLI\Iterators\CSV( 'users.csv' ) as $i => $new_user ) {
    	// Do whatever
    }

***

### \WP_CLI\Process (coming in 0.17.0)

* Create and run a system process.
* Returns `\WP_CLI\ProcessRun` so you can inspect results.

<!-- Markdown formatting hack -->

    wp> $process = \WP_CLI\Process::create( "rm -rf /" );
    wp> $process->run()

***

Thanks. Questions?
-------

[@danielbachhuber](https://twitter.com/danielbachhuber)
https://github.com/danielbachhuber/wcnyc2014
