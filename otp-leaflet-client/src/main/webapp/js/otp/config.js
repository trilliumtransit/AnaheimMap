// make sure we have otp.config and otp.config.locale defined
if(typeof(otp) == "undefined" || otp == null) otp = {};
if(typeof(otp.config) == "undefined" || otp.config == null) otp.config = {};
//if(typeof(otp.config.locale) == "undefined" || otp.config.locale == null) otp.config.locale = otp.locale.English;


otp.config = {
    debug: false,

    /**
     * The OTP web service locations
     */
    hostname : "",
    //municoderHostname : "http://localhost:8080",
    //datastoreUrl : 'http://localhost:9000',
    // In the 0.10.x API the base path is "otp-rest-servlet/ws"
    // From 0.11.x onward the routerId is a required part of the base path.
    // If using a servlet container, the OTP WAR should be deployed to context path /otp/v0
    restService: "otp/routers/default",

    /**
     * Base layers: the base map tile layers available for use by all modules.
     * Expressed as an array of objects, where each object has the following 
     * fields:
     *   - name: <string> a unique name for this layer, used for both display
     *       and internal reference purposes
     *   - tileUrl: <string> the map tile service address (typically of the
     *       format 'http://{s}.yourdomain.com/.../{z}/{x}/{y}.png')
     *   - attribution: <string> the attribution text for the map tile data
     *   - [subdomains]: <array of strings> a list of tileUrl subdomains, if
     *       applicable
     *       
     */
     
    baseLayers: [
        {
            name: 'Mapbox Anaheim Basemap',
            // tileUrl: 'http://{s}.tiles.mapbox.com/v3/'+map_id_labels+'/{z}/{x}/{y}.png',
            // copied from Anaheim Map. not sure where map_id_labels comes from.
            tileUrl: 'http://{s}.tiles.mapbox.com/v3/trilliumtransit.e8e8e512/{z}/{x}/{y}.png',
            extraTileLayers: [
                { tileUrl: 'http://{s}.tiles.mapbox.com/v3/trilliumtransit.ca9f8a4a/{z}/{x}/{y}.png',
                  subdomains : ['a','b','c','d'],
                  zIndex: 5 },
                { tileUrl: 'http://{s}.tiles.mapbox.com/v3/trilliumtransit.b1c25bd2/{z}/{x}/{y}.png',
                  subdomains : ['a','b','c','d'],
                  zIndex: 10 } ], 
            subdomains : ['a','b','c','d'],
            attribution : '© Mapbox © OpenStreetMap'
        },
        {
            name: 'Mapbox Anaheim Road Names',
            // tileUrl: 'http://{s}.tiles.mapbox.com/v3/'+map_id_labels+'/{z}/{x}/{y}.png',
            // copied from Anaheim Map. not sure where map_id_labels comes from.
            tileUrl: 'http://{s}.tiles.mapbox.com/v3/trilliumtransit.b1c25bd2/{z}/{x}/{y}.png',
            subdomains : ['a','b','c','d'],
            attribution : 'Mapbox'
        },
        {
            name: 'Mapbox Anaheim Alignments',
            // tileUrl: 'http://{s}.tiles.mapbox.com/v3/'+map_id_labels+'/{z}/{x}/{y}.png',
            // copied from Anaheim Map. not sure where map_id_labels comes from.
            tileUrl: 'http://{s}.tiles.mapbox.com/v3/trilliumtransit.ca9f8a4a/{z}/{x}/{y}.png',
            subdomains : ['a','b','c','d'],
            attribution : 'Mapbox'
        },
        {
            name: 'MapQuest OSM',
            tileUrl: 'http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png',
            subdomains : ['otile1','otile2','otile3','otile4'],
            attribution : 'Data, imagery and map information provided by <a href="http://open.mapquest.com" target="_blank">MapQuest</a>, <a href="http://www.openstreetmap.org/" target="_blank">OpenStreetMap</a> and contributors.'
        },
        {
            name: 'MapQuest Aerial',
            tileUrl: 'http://{s}.mqcdn.com/tiles/1.0.0/sat/{z}/{x}/{y}.png',
            tileUrl: 'http://{s}.mqcdn.com/tiles/1.0.0/sat/{z}/{x}/{y}.png',
            subdomains : ['otile1','otile2','otile3','otile4'],
            attribution : 'Data, imagery and map information provided by <a href="http://open.mapquest.com" target="_blank">MapQuest</a>, <a href="http://www.openstreetmap.org/" target="_blank">OpenStreetMap</a> and contributors.'
        },           
    ],
    

    /**
     * Map start location and zoom settings: by default, the client uses the
     * OTP metadata API call to center and zoom the map. The following
     * properties, when set, override that behavioir.
     */
      
   // [33.797984, -117.924412],
   // [33.813340, -117.909644]
      
    initLatLng : new L.LatLng((33.797984 + 33.813340) / 2.0, (-117.924412 + -117.909644 ) / 2.0),
     
    // initLatLng : new L.LatLng(<lat>, <lng>),
     initZoom : 14,
     minZoom : 10,
     maxZoom : 20,
    
    /* Whether the map should be moved to contain the full itinerary when a result is received. */
    zoomToFitResults    : false,

    /**
     * Site name / description / branding display options
     */

    siteName            : "Anaheim Trip Planner",
    siteDescription     : "An OpenTripPlanner deployment.",
    logoGraphic         : 'images/otp_logo_darkbg_40px.png',
    // bikeshareName    : "",

    showLogo            : true,
    showTitle           : true,
    showModuleSelector  : true,
    metric              : true,


    /**
     * Modules: a list of the client modules to be loaded at startup. Expressed
     * as an array of objects, where each object has the following fields:
     *   - id: <string> a unique identifier for this module
     *   - className: <string> the name of the main class for this module; class
     *       must extend otp.modules.Module
     *   - [defaultBaseLayer] : <string> the name of the map tile base layer to
     *       used by default for this module
     *   - [isDefault]: <boolean> whether this module is shown by default;
     *       should only be 'true' for one module
     */
    
    modules : [
        {
            id : 'planner',
            className : 'otp.modules.multimodal.MultimodalPlannerModule',
            // defaultBaseLayer : 'Mapbox Anaheim Alignments',
            // how can we have more than one base layer in OTP?
            defaultBaseLayer : 'Mapbox Anaheim Basemap',
            // defaultBaseLayer : 'MapQuest OSM',
            isDefault: true
        },
        {
            id : 'analyst',
            className : 'otp.modules.analyst.AnalystModule',
        }
    ],
    
    
    /**
     * Geocoders: a list of supported geocoding services available for use in
     * address resolution. Expressed as an array of objects, where each object
     * has the following fields:
     *   - name: <string> the name of the service to be displayed to the user
     *   - className: <string> the name of the class that implements this service
     *   - url: <string> the location of the service's API endpoint
     *   - addressParam: <string> the name of the API parameter used to pass in
     *       the user-specifed address string
     */

    geocoders : [
    ],

    
    /**
     * Info Widgets: a list of the non-module-specific "information widgets"
     * that can be accessed from the top bar of the client display. Expressed as
     * an array of objects, where each object has the following fields:
     *   - content: <string> the HTML content of the widget
     *   - [title]: <string> the title of the widget
     *   - [cssClass]: <string> the name of a CSS class to apply to the widget.
     *        If not specified, the default styling is used.
     */


    infoWidgets: [
        {
            title: 'About',
            content: '<p>Anaheim Map</p>',
            //cssClass: 'otp-contactWidget',
        },
        {
            title: 'Contact',
            content: '<p>Anaheim Map'
        },           
    ],
    
    
    /**
     * Support for the "AddThis" display for sharing to social media sites, etc.
     */
     
    showAddThis     : false,
    //addThisPubId    : 'your-addthis-id',
    //addThisTitle    : 'Your title for AddThis sharing messages',


    /**
     * Formats to use for date and time displays, expressed as ISO-8601 strings.
     */    
     
    timeFormat  : "h:mma",
    dateFormat  : "MMM Do YYYY"
};

