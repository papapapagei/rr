.. _releasenotes:

=============
Release Notes
=============

Version 5.3
===========

Build 1257
----------

New Features
++++++++++++

* Included framedrop handling for both HTTP and RTMP streaming, allowing switches in case of insufficient client resources (e.g. a netbook attempting to play an HD stream.
* Automatic fallback to Tunneled RTMP / RTMPe (in case regular RTMP is blocked).
* RTMP dynamic streaming can now be setup together with loadbalancing (using a SMIL XML file).
* RTMP DVR now using Adobe's official DVRCast application instead of a custom serverside script.
* Support for HTTP DVR streaming as offered by the Bitgravity CDN.
* With PNG skinning, the description and image of playlist buttons are automatically hidden when the playlistbutton is less than 40px high and/or less than 240px wide.

Bug Fixes
+++++++++

* Fixed a bug that caused current bandwidth not to store in a cookie, resulting in continous bitrate switching after 2 seconds.
* Fixed a bug that caused the duration textfield of a playlistitem would not be placed to the right.
* Fixed a bug that caused PNG skin playlists not to show the item.png on rollout if there was no itemActive.
* 

Version 5.2
===========

Build 1151
----------

Bug Fixes
+++++++++

 * Fixes problem initializing externally-loaded MediaProviders
 * Fixes minor issue sending sound metadata events to javascript 
 * Support for an alternate YouTube URL scheme (http://www.youtube.com/v/{video_id})
 * Fixes black-on-black error messages in FireFox with Flash 10.1 

Other Changes
+++++++++++++

 * Replaces encryption logic for Wowza secure token with Wowza's own class
 * Pre-loading error screen now displays error message instead of simply showing an error icon
 

Build 1065
----------

New Features
++++++++++++

Version 5.2 introduces a number of new features to the XML/PNG skinning model.

* Support for customized font settings (face, weight, style, color) in controlbar and playlist text fields.
* Ability to set custom *backgroundcolor* for each element.
* Ability to set custom *overcolor* and *activecolor* for playlist items.

   These colorization settings replace the generic *backcolor*, *frontcolor*, *lightcolor* and *screencolor* :ref:`options <options>`, allowing for more fine-grained control.

* Customized controlbar layout:

  * Allows placement of any button, text field or slider available in the controlbar
  * Adds the ability to insert arbitrary divider images
  * Adds the ability to insert arbitrary *spacer* elements

* New skinning elements:

   * Left and right end caps for time and volume sliders (*timeSliderCapLeft*, *timeSliderCapRight*, *volumeSliderCapLeft*, *volumeSliderCapRight*)
   * Active state for playlist item background (*itemActive* element)
   * Image placeholder for playlist item images (*itemImage* element)
   * Top and bottom end caps for playlist slider (*sliderCapTop*, *sliderCapBottom*)
   * Background images for text fields (*elapsedBackground*, *durationBackground*)
   * Over states for display icons (*playIconOver*, *muteIconOver*, *bufferIconOver*)

* Ability to control rate and amount of display *bufferIcon* rotation.
* Ability to use SWF assets in addition to JPGs and PNGs in XML skinning

An in-depth walkthrough of all new skinning features can be found in the :ref:`XML/PNG Skinning Guide <skinning>`.

Bug Fixes
+++++++++

 * Allows YouTube videos to be embedded in HTTPS pages
 * Makes the YouTube logo clickable
 * Fixes an issue where some dynamic streams only switch on resize events
 * Fixes an issue which would cause dynamically switched RTMP livestreams to close early
 * No longer hides the the display image when playing AAC or M4A audio files
 * Allows querystring parameters for .flv files streamed over RTMP. This fixes a problem some Amazon CloudFront users were having with private content.


Version 5.1
===========

Build 897
---------

Bug Fixes
+++++++++

 * Fixed an issue where load-balanced RTMP streams with bitrate switching could cause an error
 * Fixed buffer icon centering and rotation for v5 skins

Build 854
---------

New Features
++++++++++++

 * Since 5.0 branched off from 4.5, version 5.1 re-integrates changes from 4.6+ into the 5.x branch, including:
 
  * Bitrate Switching
  * Bandwidth detection
  
 * DVR functionality for [wiki:FlashMediaServerDVR RTMP live streams].

Major Bug Fixes
+++++++++++++++

 * Allows loading images from across domains without :ref:`security restrictions <crossdomain>`.
 * Fixes some RTMP live/recorded streaming issues
 * Fixes an issue where the *volume* flashvar is not respected when using RTMP
 * Fixes issue where adjusting volume for YouTube videos doesn't work in Internet Explorer
 * Various JavaScript API fixes
 * Various visual tweaks
 * Brings back icons=false functionality
 * Brings back *id* flashvar, for Linux compatibility
 * Better support of loadbalancing using the SMIL format

A full changelog can be found `here <http://developer.longtailvideo.com/trac/query?group=status&milestone=Flash+5.1&order=type>`_

Version 5.0
===========

Build 753
---------

Features new to 5.0
+++++++++++++++++++

 * Bitmap Skinning (PNG, JPG, GIF)
 * API Update for V5 plugins
 
  * Player resizes plugins when needed
  * Player sets X/Y coordinates of plugins
  * Plugins can request that the player block (stop playback) or lock (disable player controls).
  
 * MXMLC can be used to [browser:/trunk/fl5/README.txt compile the player].
 * Backwards compatibility
 
  * SWF Skins
  * Version 4.x plugins
  * Version 4.x JavaScript

4.x features not available in 5.0
+++++++++++++++++++++++++++++++++

 * Bitrate switching, introduced in 4.6
 * *displayclick* flashvar
 * *logo* flashvar (for non-commercial players)

A full changelog can be found [/query?group=status&milestone=Flash+5.0&order=type here]
