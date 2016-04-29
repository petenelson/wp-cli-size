Feature: size database command

  Scenario: Test that the database size is displayed
    Given a WP install

    When I run `wp size database`
    Then STDOUT should contain:
      """
      wp_cli_test
      """
    And STDOUT should contain:
      """
      Name
      """
    And STDOUT should contain:
      """
      Size
      """
    And STDOUT should contain:
      """
      Bytes
      """
