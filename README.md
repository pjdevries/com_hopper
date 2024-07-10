# Hopper Joomla! Extension

## Raison d'être

The idea for this extension came from Herman Peeren and is a spin-off of his Joomla! Magazine article series **Tools to
Make a Joomla Component**. The series focuses on customising Joomla!, comparing various approaches, from using custom
fields and CCKs to developing a custom component.

Custom fields are fields that can be added to Joomla! core content types (articles, categories, users, contacts) to
extend or enhance their usability. While custom components can easily be installed on multiple Joomla! websites and most
CCKs allow project exports for import into other Joomla! websites, custom fields are not easily transferable. Hopper
aims to make that possible.

## Minimal Requirements

- Joomla! 5.1.x
- PHP 8.1

The extension is developed and (not very well) tested on Joomla! 5.1.x. It will most likely also work on Joomla! 4.x,
but it hasn't been tried there.

## Installation and configuration ##

Installation is the same as for any other Joomla extension. In its current form, it requires no configuration.

## Description

The extension is a component designed to export custom fields and associated metadata, and import them into other
Joomla! sites.

**BEWARE!**: *The component currently does not take any existing data on the target website into account. Therefore, it
is ill-advised to import data into websites that are not empty. Only import data into new, empty, and freshly installed
Joomla! websites.*

In its current form, the component's functionality is quite limited and rudimentary, and it has not been extensively
tested.

> “From then on, when anything went wrong with a computer, we said it had bugs in it.”
_(Grace Hopper)_

Notwithstanding the above, an effort has been made to make the component as robust as possible. However, Joomla itself
does not allow us to make it as fail-safe as we would like. As a result, if an error occurs during the import phase, the
imported data will most likely not be consistent. This can not be remedied by performing the import a second time.
First, all dat that was already imported must be removed. While doing so, don't forget to empty the **Trash**.