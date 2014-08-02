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

@todo example of old bin script

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
      Then STDOUT should not be empty

      When I run `wp user meta get 1 foo`
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
* Iterator == increment through large data set.
* Prevent performance issues from reading all data at once.

```
foreach( new \WP_CLI\Iterators\Query( "SELECT * FROM node ORDER BY nid ASC" ) as $i => $node_row ) {
	
}
```

***

### `\WP_CLI\Process` (to come in 0.17.0)

* Create and run a system process.
* Returns `\WP_CLI\ProcessRun` so you can inspect results.

***

Thanks!
-------

@danielbachhuber
https://github.com/danielbachhuber/wcnyc2014
