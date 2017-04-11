Feature: size database command

  Scenario: Test that the database size is displayed
    Given a WP install

    When I run `wp size database --format=csv`
    Then STDOUT should contain:
      """
      wp_cli_test,"640 KB"
      """
