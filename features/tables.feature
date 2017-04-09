Feature: size database command

  Scenario: Test that the database size is displayed
    Given a WP install

    When I run `wp size tables --format=csv`
    Then STDOUT should contain:
      """
      wp_commentmeta,"48 kB",49152,0
      """
    And STDOUT should contain:
      """
      wp_comments,"96 kB",98304,1
      """
    And STDOUT should contain:
      """
      wp_links,"32 KB",32768,0
      """
     And STDOUT should contain:
      """
      wp_options,"48 KB",49152,104
      """
   And STDOUT should contain:
      """
      wp_postmeta,"48 KB",49152,1
      """
    And STDOUT should contain:
      """
      wp_posts,"80 KB",81920,2
      """
    And STDOUT should contain:
      """
      wp_term_relationships,"32 kB",32768,1
      """
    And STDOUT should contain:
      """
      wp_term_taxonomy,"48 kB",49152,1
      """
    And STDOUT should contain:
      """
      wp_termmeta,"48 kB",49152,0
      """
    And STDOUT should contain:
      """
      wp_terms,"48 kB",49152,0
      """
    And STDOUT should contain:
      """
      wp_usermeta,"48 kB",49152,14
      """
    And STDOUT should contain:
      """
      wp_users,"64 kB",65536,1
      """
