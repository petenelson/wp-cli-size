Feature: size database command

  Scenario: Test that the database size is displayed
    Given a WP install

    When I run `wp size tables --format=csv`
    Then STDOUT should contain:
      """
      wp_commentmeta,"48 KB"
      """
    And STDOUT should contain:
      """
      wp_comments,"96 KB"
      """
    And STDOUT should contain:
      """
      wp_options,"48 KB"
      """
    And STDOUT should contain:
      """
      wp_postmeta,"48 KB"
      """
    And STDOUT should contain:
      """
      wp_posts,"80 KB"
      """
    And STDOUT should contain:
      """
      wp_term_relationships,"32 KB"
      """
    And STDOUT should contain:
      """
      wp_term_taxonomy,"48 KB"
      """
    And STDOUT should contain:
      """
      wp_termmeta,"48 KB"
      """
    And STDOUT should contain:
      """
      wp_terms,"48 KB"
      """
    And STDOUT should contain:
      """
      wp_usermeta,"48 KB"
      """
    And STDOUT should contain:
      """
      wp_users,"64 KB"
      """
