=== Widget Importer & Exporter ===
Contributors: wpultimate, churchthemes, stevengliebe, mauryaratan, wido
Tags: widgets, widget, importer, exporter, import, export, widget import, widget export, widget importer, widget exporter, backup, migration
Requires at least: 3.5
Tested up to: 4.9
Stable tag: 1.5.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Import and export your widgets.

== Description ==

Widget Importer & Exporter is useful for moving widgets from one WordPress site to another, backing up widgets and for theme developers to provide users with sample widgets. See the [details](https://wpultimate.com/widget-importer-exporter) on WP Ultimate.

= Importing =

Importing is done by uploading an export file created by the plugin. The results of an import are shown in a nicely formatted table with an explanation of what happened with each widget area and widget.

Importation takes into consideration widget areas not existing in the current theme (widgets imported as *Inactive*), widgets that already exist in the same widget area (widgets not duplicated) and widgets that are not supported by the site (widgets not imported).

= Exporting =

Widget Importer & Exporter can create an export file (in JSON format with .wie extension) out of currently active widgets. This file can be imported into other sites using this plugin or used to restore widgets to the same site later.

= Translations =

The following translations are available.

* English (default)
* Spanish by [Eduardo Larequi](http://www.labitacoradeltigre.com/)
* French by [French Translation Team](https://make.wordpress.org/polyglots/teams/?locale=fr_FR)
* German by [Alexander Kesting](http://alex-dune.de)
* Italian by [venerdi](https://profiles.wordpress.org/venerdi/)
* Dutch by [Paul Backus](http://backups.nl/)
* Serbian by Borisa Djuraskovic of [Web Hosting Hub](http://www.webhostinghub.com)
* Japanese by [miccweb](https://profiles.wordpress.org/miccweb/)
* Hebrew by [Rami Yushuvaev](https://profiles.wordpress.org/ramiy/)
* Persian by [ekfarshid](https://profiles.wordpress.org/ekfarshid/), [Yousefzadeh](https://profiles.wordpress.org/cg-team/) and [PersianScript](https://profiles.wordpress.org/persianscript/)
* More at [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/widget-importer-exporter)

= Developers =

The ``wie_before_import`` action fires after a file is uploaded but before the data is imported. ``wie_after_import`` fires after the data is imported. The ``wie_import_data`` filter can be used to filter data before it is imported. Other filters are used throughout. Submit an issue on GitHub if you need more hooks (pull requests encouraged).

Please jump on [GitHub](https://github.com/stevengliebe/widget-importer-exporter) to report issues and follow development.

= Follow us =

* Visit [WP Ultimate](https://wpultimate.com)
* We're on [Twitter](https://twitter.com/WPUltimateHQ), [Facebook](https://www.facebook.com/wpultimate) and have a [Newsletter](http://wpultimate.us5.list-manage.com/subscribe?u=a0fec2c146a67b2dc509154d1&id=47f3733a8e)
* Lead developer: [stevengliebe.com](http://stevengliebe.com)

== Installation ==

Please see [Installing Plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins) in the WordPress Codex.

After activation, go to *Tools > Widget Importer & Exporter*

== Frequently Asked Questions ==

= Why does the JSON export file use a .wie extension? =

The export file contains JSON data that is not compatible with any other plugin. Therefore it has a proprietary file extension. This keeps people from confusing export files from other widget import/export plugins.

== Screenshots ==

1. Main import/export screen
2. Results from an import

== Changelog ==

See [releases](https://github.com/stevengliebe/widget-importer-exporter/releases) on GitHub.