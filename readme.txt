=== Widget Importer & Exporter ===
Contributors: stevengliebe, churchthemes
Tags: widgets, widget, importer, exporter, import, export, backup
Requires at least: 3.5
Tested up to: 4.5.2
Stable tag: 1.3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Import and export your widgets.

== Description ==

Widget Importer & Exporter is useful for moving widgets from one WordPress site to another, backing up widgets and for theme developers to provide users with sample widgets.

= Importing =

Importing is a matter of uploading an export file created by the plugin. The results of an import are shown in a nicely formatted table with an explanation of what happened with each sidebar and widget.

Importation takes into consideration sidebars not existing in the current theme (widgets imported as *Inactive*), widgets that already exist in the same sidebar (widgets not duplicated) and widgets that are not supported by the site (widgets not imported).

= Exporting =

Widget Importer & Exporter can create an export file (in JSON format with .wie extension) out of currently active widgets. This file can be imported into other sites using this plugin or used to restore widgets to the same site later.

= Translations =

The following translations are available.

* English (default)
* Spanish (es_ES) by [Eduardo Larequi](http://www.labitacoradeltigre.com/)
* French (fr_FR) by [French Translation Team](https://make.wordpress.org/polyglots/teams/?locale=fr_FR)
* German (de_DE) by [Alexander Kesting](http://alex-dune.de)
* Dutch (nl_NL) by [Paul Backus](http://backups.nl/)
* Serbian (sr_RS) by Borisa Djuraskovic of [Web Hosting Hub](http://www.webhostinghub.com)
* More at [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/widget-importer-exporter)

= Developers =

The ``wie_before_import`` action fires after a file is uploaded but before the data is imported. ``wie_after_import`` fires after the data is imported. The ``wie_import_data`` filter can be used to filter data before it is imported. Other filters are used throughout. Submit an issue on GitHub if you need more hooks (pull requests encouraged).

Please jump on [GitHub](https://github.com/stevengliebe/widget-importer-exporter) to report issues and follow development.

= Follow me =

Find my website at [stevengliebe.com](http://stevengliebe.com).

This project is made possible by [churchthemes.com](http://churchthemes.com) ([@churchthemes](https://twitter.com/churchthemes)).

== Installation ==

Please see [Installing Plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins) in the WordPress Codex.

After activation, go to *Tools > Widget Import/Export*

== Frequently Asked Questions ==

= Why does the JSON export file use a .wie extension? =

The export file contains JSON data that is not compatible with any other plugin. Therefore it has a proprietary file extension. This keeps people from confusing export files from other widget import/export plugins.

== Screenshots ==

1. Main import/export screen
2. Results from an import

== Changelog ==

See [releases](https://github.com/stevengliebe/widget-importer-exporter/releases) on GitHub.