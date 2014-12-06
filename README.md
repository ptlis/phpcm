# phptr - PHP's test-responsibility

A simple tool to determine how many newly lines of code were added vs how many lines were tested.

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