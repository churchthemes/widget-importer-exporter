=== Widget Importer & Exporter ===
Contributors: stevengliebe, churchthemes
Tags: widgets, widget, importer, exporter, import, export, backup
Requires at least: 3.5
Tested up to: 3.6
Stable tag: trunk
License: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Import and export your widgets.

== Description ==

Widget Importer & Exporter is useful for moving widgets from one WordPress site to another, backing up widgets and for theme developers to provide users with sample widgets.

= Importing =

Importing is a matter of uploading an export file created by the plugin. The results of an import are shown in a nicely formatted table with an explanation of what happened with each sidebar and widget.

Importation takes into consideration sidebars not existing in the current theme (widgets imported as *Inactive*), widgets that already exist in the same sidebar (widgets not duplicated) and widgets that are not supported by the site (widgets not imported).

= Exporting =

Widget Importer & Exporter can create an export file (in JSON format with .wie extension) out of currently active widgets. This file can be imported into other sites using this plugin or used to restore widgets to the same site later.

= Translations =

The following translations are included.

* English (default)
* Spanish (es_ES) by [Eduardo Larequi](http://www.labitacoradeltigre.com/)

= Developers =

The ``wie_before_import`` action fires after a file is uploaded but before the data is imported. ``wie_after_import`` fires after the data is imported. Other filters are used throughout. Let me know if you need more actions or filters.

Please jump on [GitHub](https://github.com/stevengliebe/widget-importer-exporter) to report issues and follow development.

== Installation ==

Please see [Installing Plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins) in the WordPress Codex.

After activation, go to *Tools > Widget Import/Export*

== Screenshots ==

1. Main import/export screen
2. Results from an import

== Changelog ==

See [releases](https://github.com/stevengliebe/widget-importer-exporter/releases) on GitHub.