petenelson/wp-cli-size
======================



[![Build Status](https://travis-ci.org/petenelson/wp-cli-size.svg?branch=master)](https://travis-ci.org/petenelson/wp-cli-size)

Quick links: [Installing](#installing) | [Usage](#usage) | [Contributing](#contributing)

## Installing

Installing this package requires WP-CLI v0.23.0 or greater. Update to the latest stable release with `wp cli update`.

Once you've done so, you can install this package with `wp package install petenelson/wp-cli-size`

## Usage

Some examples, use `wp help size` for full options

Database size: `wp size database`

```
+-------------------+------+---------+
| Name              | Size | Bytes   |
+-------------------+------+---------+
| wordpress_default | 2 MB | 1703936 |
+-------------------+------+---------+
```

Table sizes: `wp size tables`

```
+-----------------------+-------+---------+------+
| Name                  | Size  | Bytes   | Rows |
+-----------------------+-------+---------+------+
| wp_commentmeta        | 48 kB | 49152   | 0    |
| wp_comments           | 96 kB | 98304   | 1    |
| wp_links              | 32 kB | 32768   | 0    |
| wp_options            | 1 MB  | 1114112 | 152  |
| wp_postmeta           | 48 kB | 49152   | 36   |
| wp_posts              | 80 kB | 81920   | 10   |
| wp_term_relationships | 32 kB | 32768   | 1    |
| wp_term_taxonomy      | 48 kB | 49152   | 1    |
| wp_termmeta           | 48 kB | 49152   | 0    |
| wp_terms              | 48 kB | 49152   | 1    |
| wp_usermeta           | 48 kB | 49152   | 21   |
| wp_users              | 48 kB | 49152   | 1    |
+-----------------------+-------+---------+------+
```

Filesystem size: `wp size filesystem`

```
+--------------------------+-------+----------+
| Name                     | Size  | Bytes    |
+--------------------------+-------+----------+
| /var/www/baab.de/htdocs/ | 30 MB | 31072772 |
+--------------------------+-------+----------+
```

CSV format: `wp size tables --format=csv`

```
Name,Size,Bytes,Rows
wp_commentmeta,"48 kB",49152,0
wp_comments,"96 kB",98304,1
wp_links,"32 kB",32768,0
wp_options,"1 MB",1114112,152
wp_postmeta,"48 kB",49152,36
wp_posts,"80 kB",81920,10
wp_term_relationships,"32 kB",32768,1
wp_term_taxonomy,"48 kB",49152,1
wp_termmeta,"48 kB",49152,0
wp_terms,"48 kB",49152,1
wp_usermeta,"48 kB",49152,21
wp_users,"48 kB",49152,1
```

## Contributing

Code and ideas are more than welcome.

Please [open an issue](https://github.com/petenelson/wp-cli-size/issues) with questions, feedback, and violent dissent. Pull requests are expected to include test coverage.
