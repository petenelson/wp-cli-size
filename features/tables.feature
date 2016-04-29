Feature: size database command

  Scenario: Test that the database size is displayed
    Given a WP install

    When I run `wp size tables --format=csv`
    Then STDOUT should contain:
      """
      wp_commentmeta,"48 kB"
      """
    And STDOUT should contain:
      """
      wp_comments,"96 kB"
      """
    And STDOUT should contain:
      """
      wp_options,"48 kB"
      """
    And STDOUT should contain:
      """
      wp_postmeta,"48 kB"
      """
    And STDOUT should contain:
      """
      wp_posts,"80 kB"
      """
    And STDOUT should contain:
      """
      wp_term_relationships,"32 kB"
      """
    And STDOUT should contain:
      """
      wp_term_taxonomy,"48 kB"
      """
    And STDOUT should contain:
      """
      wp_termmeta,"48 kB"
      """
    And STDOUT should contain:
      """
      wp_terms,"48 kB"
      """
    And STDOUT should contain:
      """
      wp_usermeta,"48 kB"
      """
    And STDOUT should contain:
      """
      wp_users,"64 kB"
      """
