.. _javascriptapi:

JavaScript API
==============

The JW Player for Flash supports a flexible JavaScript API. It is possible to read the config/playlist variables of the player, send events to the player (e.g. to pause or load a new video) and listen (and respond) to player events. A small initialization routine is needed to connect your apps to the player.


Initialization
--------------
 
Please note that the player will **NOT** be available the instant your HTML page is loaded and the first JavaScript is executed. The SWF file (90k) has to be loaded and instantiated first! You can catch this issue by defining a simple global JavaScript function. By default, it is called *playerReady()* and every player that's successfully instantiated will call it. 

.. code-block:: html

   var player;
   function playerReady(object) {
     alert('the player is ready');
     player = document.getElementById(object.id);
   };


The *object* the player sends to the function contains the following properties:

.. describe:: id

   ID of the player (the *<embed>* code) in the HTML DOM. Use it to get a reference to the player with *getElementById()*.

.. describe:: version

   Exact version of the player in MAJOR.MINOR.REVISION format *e.g. 5.2.1065*.

.. describe:: client

  Plugin version and platform the player uses, e.g. *FLASH WIN 10.0.47.0*.

.. note:: On Windows and Mac OS X, the player automatically reads its *ID* from the *id* and *name* attributes of the player's `HTML embed code <embedding>`. On Linux however, this functionality **does not work**. If you target Linux users with your scripting, you can circumvent this issue by including an  :ref:`id option <options-behavior>` in your list of flashvars in the embed code.


Custom playerready
^^^^^^^^^^^^^^^^^^

It is possible to ask the player to call a different javascript function after it completes its initialization. This can be done with an :ref:`option <options>` called **playerready**. Here is an example SWFObject :ref:` embed code <embedding>` using the function *registerPlayer()*:

.. code-block:: html

   <p id="container1">You don't have Flash ...</p>

   <script type="text/javascript">
     var flashvars = { file:'/data/bbb.mp4',playerready:'registerPlayer' };
     var params = { allowfullscreen:'true', allowscriptaccess:'always' };
     var attributes = { id:'player1', name:'player1' };
     swfobject.embedSWF('player.swf','container1','480','270','9.0.115','false',
       flashvars, params, attributes);

     var player;
     function registerPlayer(obj) { 
       alert('The player with ID '+obj.id + 'is ready!');
       player = document.getElementById(obj.id);
     };
   </script>

No playerready
^^^^^^^^^^^^^^

If you are not interested in calling the player immediately after the page loads, you won't need the *playerReady()* function. You can then simply use the ID of the embed/object tag that embeds the player to get a reference. So for example with this embed tag:

.. code-block:: html

   <embed id="myplayer" name="myplayer" src="/upload/player.swf" width="400" height="200" />

You can get a pointer to the player with this line of code:

.. code-block:: html

   var player = document.getElementById('myplayer');

.. note:: 

   Note you must add both the **id** and **name** attributes in the *<embedding>* in order to get back an ID in all browsers.


Reading variables
-----------------

There's two variable calls you can make through the API: *getConfig()* and *getPlaylist()*.

getConfig()
^^^^^^^^^^^

getConfig() returns an object with state variables of the player. For example, here we request the current audio volume, the current player width and the current playback state:

.. code-block:: html

   var volume = player.getConfig().volume;
   var width = player.getConfig().width;
   var state = player.getConfig().state;

Here's the full list of state variables:

.. describe:: bandwidth

   Current bandwidth of the player to the server, in kbps (e.g. *1431*). This is only available for the :ref:video  <mediaformats>`, :ref:`http <httpstreaming>` and :ref:`rtmp <rtmpstreaming>` providers.

.. describe:: fullscreen

   Current fullscreen state of the player, as boolean (e.g. *false*).

.. describe:: height

   Current height of the player, in pixels (e.g. *270*).

.. describe:: item

   Currently active (playing, paused) playlist item, as zero-index (e.g. *0*). Note that *0* means the first playlistitem is playing and *1* means the second one is playing.

.. describe:: level

   Currently active bitrate level, in case multipe bitrates are supplied to the player. This is only useful for  :ref:`httpstreaming` and :ref:`rtmpstreaming`. Note that *0* always refers to the highest quality bitrate.

.. describe:: state

   Current playback state of the player, as an uppercase string. It can be one of the following:

   * *IDLE*: The current playlist item is not loading and not playing.
   * *BUFFERING*: the current playlistitem is loading. When sufficient data has loaded, it will automatically start playing.
   * *PLAYING*: the current playlist item is playing.
   * *PAUSED*: playback of the current playlistitem is not paused by the player.

.. describe:: mute

   Current audio mute state of the player, as boolean (e.g. *false*). 

.. describe:: volume

   Current audio volume of the player, as a number from 0 to 100 (e.g. *90*). 

.. describe:: width

   Current width of the player, in pixels (e.g. *480*).

.. Note:: 

   In fact, all the :ref:`options` will be available in the response to *getConfig()*. In certain edge cases, this might be useful, e.g. when you want to know if the player did **autostart** or not.


getPlaylist()
^^^^^^^^^^^^^

getPlaylist() returns the current playlist of the player as an array. Each entry of this array is in turn again a hashmap with all the :ref:`playlist properties <playlistformats>` the player recognizes. Here's a few examples:

.. code-block:: html

   var playlist = player.getPlaylist();
   alert("There are " + playlist.length + " videos in the playlist");
   alert("The title of the first entry is " + playlist[0].title);
   alert("The poster image of the second entry is " + playlist[1].image);
   alert("The media file of the third entry is " + playlist[2].file);
   alert("The media provider of the fourth entry is " + playlist[3].provider);

Playlist items can contain properties supported by the provider. Examples of such properties are:

* **http.startparam**, when using the :ref:`HTTP provider <httpstreaming>`.
* **rtmp.loadbalance**, when using the :ref:`RTMP provider <rtmpstreaming>`.

Playlist items can  also contain properties supported by certain plugins. Examples of such properties are:

* **hd.file**, which is used by the HD plugin.
* **captions.file**, which is used by the Captions plugin.

More information, and the full list of 12 default playlist properties, can be found in :ref:`playlistformats`.

Sending events
--------------

The player can be controlled from JavaScript by sending events (e.g. to pause it or change the volume). Sending events to the player is done through the *sendEvent()* call. Some of the event need a parameter and some don't. Here's a few examples:

.. code-block:: html

   // this will toggle playback.
   player.sendEvent("play");
   // this sets the volume to 90%
   player.sendEvent("volume","true");
   // This loads a new video in the player
   player.sendEvent('load','http://www.mysite.com/videos/bbb.mp4');

Here's the full list of events you can send, plus their parameters:


.. describe:: ITEM ( index:Number )

   Start playback of a specific item in the playlist. If *index* isn't set, the current playlistitem will start.

.. describe:: LINK ( index:Number )

   Navigate to the *link* of a specific item in the playlist. If *index* is not set, the player will navigate to the link of the current playlistitem.

.. describe:: LOAD ( url:String )

   Load a new media file or playlist into the player. The *url* must always be sent.

.. describe:: MUTE ( state:Boolean )

   Mute or unmute the player's sound. If the *state* is not set, muting will be toggled.

.. describe:: NEXT

   Jump to the next entry in the playlist.  No parameters.

.. describe:: PLAY ( state:Boolean )

   Play (set *state* to *true*) or pause (set *state* to *false*) playback. If the *state* is not set, the player will toggle playback.

.. describe:: PREV

   Jump to the previous entry in the playlist.  No parameters.

.. describe:: SEEK ( position:Number )

   Seek to a certain position in the currently playing media file. The *position* must be in seconds (e.g. *65* for one minute and five seconds). 

   .. note::

      Seeking does not work if the player is in the *IDLE* state. Make sure to check the *state* variable before attempting to seek. Additionally, for the *video* media :ref:`provider <mediaformats>`, the player can only seek to portions of the video that are already loaded. Other media providers do not have this additional restriction.

.. describe:: STOP

   Stop playback of the current playlist entry and unload it. The player will revert to the *IDLE* state and the poster image will be shown. No parameters.

.. describe:: VOLUME ( percentage:Number )

   Change the audio volume of the player to a certain percentage (e.g. *90*). If the player is muted, it will automatically be unmuted when a volume event is sent.

.. note:: 

   Due to anti-phishing restrictions in the Adobe Flash runtime, it is not possible to enable/disable fullscreen playback of the player from JavaScript.

Setting listeners
-----------------

In order to let JavaScript respond to player updates, you can assign listener functions to various events the player fires. An example of such event is the *VOLUME* event, when the volume of the player is changed. The player will call the listener function with one parameter, a *key:value* populated object that contains more info about the event.

In the naming of the listener functions, the internal architecture of the JW Player sines through a little. Internally, the player is built using a Mode-View-Controller design pattern:

* The *Model* takes care of the actual media playback. It sends events to the View.
* The *View* distributes all events from the Model to the plugins and API. It also collects all input from the plugins and API.
* The *Controller* receives and checks all events from the View. In turn, it sends events to the Model.

Basically, the events from the View are those you send out using the *sendEvent()* API function. With two other API functions, you can listen to events from the Model (playback updates) and Controller (control updates). These API functions are  *addModelListener()* and *addControllerListener()*. Here's a few examples:

.. code-block:: html

   function stateTracker(obj) { 
      alert('the playback state is changed from '+obj.oldstate+' to '+obj.newstate);
   };
   player.addModelListener("STATE","stateTracker");

   function volumeTracker(obj) {
      alert('the audio volume is changed to: '+obj.percentage'+ percent');
   };
   player.addControllerListener("VOLUME","volumeTracker");

If you only need to listen to a certain event for a limited amount of time (or just once), use the *removeModelListener()* and removeControllerListener()* functions to unsubscribe your listener function. The syntax is exactly the same:

.. code-block:: html

   player.removeModelListener("STATE","stateTracker");
   player.removeControllerListener("VOLUME","volumeTracker");

.. note:: 

   You MUST string representations of a function for the function parameter!

Model events
^^^^^^^^^^^^

Here's an overview of all events the *Model* sends. Note that the data of every event contains the *id*, *version* and *client* parameters that are also sent on :ref:`playerReady <javascriptapi>`.

.. describe:: ERROR

   Fired when a playback error occurs (e.g. when the video is not found or the stream is dropped). Data:

   * *message* ( String ): the error message, e.g. *file not found*  or *no suiteable playback codec found*.

.. describe:: BUFFER

   Fired when the player loads some media into its buffer.

   * *percentage* ( Number ): The percentage (0-100) of seconds buffered versus the media's duration.  i.e. if the media is 60 seconds long, and half of the video has been buffered, a buffer event will be fired with percentage=50.

.. describe:: META

   Fired when metadata on the currently playing media file is received. The exact metadata that is sent with this event varies per individual media file. Here are some examples:

   * *duration* ( Number) : sent for *video*, *youtube*, *http* and *rtmp* media. In seconds.
   * *height* ( Number ): sent for all media providers, except for *youtube*. In pixels.
   * *width* ( Number ): sent for all media providers, except for *youtube*. In pixels.
   * Codecs, framerate, seekpoints, channels: sent for *video*, *http* and *rtmp* media.
   * TimedText, captions, cuepoints: additional metadata that is embedded at a certain position in the media file. Sent for *video*, *http* and *rtmp* media.
   * ID3 info (genre, name, artist, track, year, comment): sent for MP3 files (the *sound* :ref:`media provider <mediaformats>`).


   .. note:: 

      Due to the :ref:`crossdomain` restrictions of Flash, you cannot load a ID3 data from an MP3 on one domain in a player on another domain. This issue can be circumvented by placing a *crossdomain.xml* file on the server that hosts your MP3s.

.. describe:: state

   Fired when the playback state of the video changes. Data:

   * *oldstate* ( 'IDLE','BUFFERING','PLAYING','PAUSED','COMPLETED' ): the previous playback state.
   * *newstate* ( 'IDLE','BUFFERING','PLAYING','PAUSED','COMPLETED' ): the new playback state.

   .. note:: 

      You will not be able to check if a video is completed by polling for *getConfig().state*. The player will only be in the COMPLETED state for a very short time, before jumping to IDLE again. Always use *addModelListener('state',...)* if you want to check if a video is completed.

.. describe:: time

   Fired when the playback position is changing (i.e. the media file is playing). It is fired with a resolution of 1/10 second, so there'll be a lot of events! Data:

   * *duration* ( Number ): total duration of the media file in seconds, e.g. *150* for two and a half minutes.
   * *position* ( Number ): current playback position in the file, in seconds.

Controller events
^^^^^^^^^^^^^^^^^

Here's an overview of all events the *Controller* sends. Note that the data of every event contains the *id*, *version* and *client* parameters that are also sent on :ref:`playerReady <javascriptapi>`.

.. describe:: ITEM

   Fired when the player switches to a new playlist entry. The new item will immediately start playing. Data:

  * *index* ( Number ): playlist index of the media file that starts playing.

.. describe:: MUTE

   Fired when the player's audio is muted or unmuted. Data:

   * *state* ( Boolean ): the new mute state. If *true*, the player is muted.
 
.. describe:: PLAY

   Fired when the player toggles playback (playing/paused). Data:

   * *state* ( Boolean ): the new playback state. If *true*, the player plays. If *false*, the player pauses.

.. describe:: PLAYLIST

   Fired when a new playlist (a single file is also pushed as a playlist!) has been loaded into the player. Data:

   * *playlist* ( Array ): The new playlist. It has exactly the same structure as the return of the *getPlaylist()* call.

.. describe:: RESIZE

   Fired when the player is resized. This includes entering/leaving fullscreen mode. Data:

   * *fullscreen* ( Boolean ): The new fullscreen state. If *true*, the player is in fullscreen.
   * *height* ( Number ): The overall height of the player.
   * *width* ( Number ): The overall width of the player.

.. describe:: SEEK

   Fired when the player is seeking to a new position in the video/sound/image. Parameters:

   * *position* ( Number ): the new position in the file, in seconds (e.g. *150* for two and a half minute).

.. describe:: STOP

   Fired when the player stops loading and playing. The playback state will turn to *IDLE* and the position of a video will be set to 0. No data.

.. describe:: VOLUME

   Fired when the volume level is changed. Data:

   * *percentage* ( Number ): new volume percentage, from 0 to 100 (e.g. *90*).
