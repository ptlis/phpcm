# phpcm - PHP's Coverage Monitor


A simple tool providing metrics on how well tested your project is over time as well as per-commit, per-contributor data..


## Goals

* Use the following inputs:
  * git-diff - determine what lines changed.
  * git-blame - determine who edited a particular line
  * PHPUnit's serialized test coverage - determine what lines are tested.
* From these three data sources we should be able to determine:
  * How many new lines of code were added.
  * How many of these have associated tests.
  * How many existing lines of code previously weren't tested but now are.
* We should be able to traverse the history of a project generating this data, as well as creating incremental builds using CI tools such as Jenkins.
* Generate useful reports on the health of a project.
    * Delta of tested LOC per commit.
    * Identification of:
        * Contributors with a positive impact on coverage.
        * Contributors with a negative impact on coverage.
    * Other interesting stuff?


## TODO

* Ensure that we make a distinction between text-based & binary file types - Simple solution - only proceed for files that end with .php ?
* The current implementation _will_ have memory issues on large projects.
* Config -> test spec should have better names (etc)
