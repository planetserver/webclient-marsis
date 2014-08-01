var map;
var GlobalMOLARGB;
var GlobalTHEMISIRday;
var GaleCTXWms;
var GaleHRSCWms;
var fake;
var vector_layer;
var vector_layer2;
var vector_layer3;
var vector_layer4;

// OpenLayers
OpenLayers.Util.VincentyConstants={a:3396190,b:3396190,f:0}
OpenLayers.IMAGE_RELOAD_ATTEMPTS = 2;
OpenLayers.Util.onImageLoadErrorColor = "transparent";
OpenLayers.ImgPath = "js/OpenLayers/img/"
// OpenLayers.Projection.addTransform(
    // 'EPSG:4326',
    // 'PS:1',
    // OpenLayers.Projection.nullTransform);
    // OpenLayers.Projection.addTransform(
    // 'PS:1',
    // 'EPSG:4326',
    // OpenLayers.Projection.nullTransform);
OpenLayers.Feature.Vector.style['default']['strokeWidth'] = '2';
var maxextent = new OpenLayers.Bounds(-180,-90,180,90);
//

function initmap()
    {
    map = new OpenLayers.Map( 'map' , {
                controls: [
                    new OpenLayers.Control.Navigation(),
                    new OpenLayers.Control.PanZoomBar(),
                    new OpenLayers.Control.LayerSwitcher({'ascending':false}),
                    //new OpenLayers.Control.Permalink(),
                    //new OpenLayers.Control.ScaleLine(),
                    //new OpenLayers.Control.Permalink('permalink'),
                    new OpenLayers.Control.MousePosition()
//                    new OpenLayers.Control.OverviewMap(),
                    //new OpenLayers.Control.KeyboardDefaults()
                ],
                numZoomLevels: 20, // How can you set the max zoomlevels to HiRISE max?
                projection: 'PS:1', 
                displayProjection: new OpenLayers.Projection("PS:1"), 
                units: 'degrees',
                maxExtent: maxextent});
    map.addControl(new OpenLayers.Control.ScaleLine({geodesic: true}));

    // A fake base layer
    var baseLayerOptions = {
        isBaseLayer: true, 
        displayInLayerSwitcher: false
        };

    fake = new OpenLayers.Layer('fake', baseLayerOptions);
     
    GlobalMOLARGB = new OpenLayers.Layer.MapServer("MOLA RGB",
	                planetserver_ms_wms,
	                { map: "mola.map", layers: 'molargb', projection: 'PS:1'},
                    {isBaseLayer: false, transitionEffect: 'resize', wrapDateLine: true});
                    
    GlobalTHEMISIRday = new OpenLayers.Layer.MapServer("THEMIS IR day",
	                planetserver_ms_wms,
	                { map: "themisirday.map", layers: 'themisirday', projection: 'PS:2?0', transparent: 'true'},
                    {isBaseLayer: false, opacity: 0.5, transitionEffect: 'resize', wrapDateLine: true});

	

    MARSISorbits = new OpenLayers.Layer.Vector("MARSIS footprints", {
            strategies: [new OpenLayers.Strategy.Fixed()],
            protocol: new OpenLayers.Protocol.HTTP({
                url: "ofp/marsis_orbits_footprints.kml",
//                url: "ofp/mars_mex_marsis_test.kml",
//                url: "ofp/mars_mex_marsis_rdrss_with_time.kml",
                format: new OpenLayers.Format.KML({
                    extractStyles: true, 
                    extractAttributes: true,
                    maxDepth: 0
                })
            })
        });

// MARSISorbits = new OpenLayers.Layer("MARSIS footprints", {isBaseLayer: false, displayInLayerSwitcher: true});

    var MarsExpress = new OpenLayers.Layer.Markers( "Mars Express" );
//    map.addLayer(MarsExpress);

    var MElonLat = new OpenLayers.LonLat( 0.0 ,0.0 )
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            map.getProjectionObject() // to Spherical Mercator Projection
          );
    var size = new OpenLayers.Size(47,38);
//    var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
    var icon = new OpenLayers.Icon('images/MarsExpress.png', size);
    MarsExpress.addMarker(new OpenLayers.Marker(MElonLat,icon));

    
    // ATTEMPTS to get http://commondatastorage.googleapis.com/ctx_mosaic_1/ctx_mosaic_1.kml
    // var kmlctx = new OpenLayers.Layer.TileCache("CTX Mosaic", {
            // projection: map.displayProjection,
            // strategies: [new OpenLayers.Strategy.Refresh()],
        // protocol: new OpenLayers.Protocol.HTTP({
            // url: "http://commondatastorage.googleapis.com/ctx_mosaic_1/ctx_mosaic_1.kml",
            // format: new OpenLayers.Format.KML({
                // extractStyles: true,
                // extractAttributes: true,
                // maxDepth: 4
            // })
        // })
    // });
    
    // kmlctx.setVisibility(true);     
    // var kmlctx = new OpenLayers.Layer.TileCache("CTX Mosaic",{
            // "projection": map.displayProjection,
            // "strategies": [new OpenLayers.Strategy.Refresh()], 
            // "protocol": new OpenLayers.Protocol.HTTP({
                // "url": "http://commondatastorage.googleapis.com/ctx_mosaic_1/ctx_mosaic_1.kml",
                // "format": new OpenLayers.Format.KML({
                    // "extractStyles": true,
                    // "extractAttributes": true
                // })
            // })
        // });
 
    map.addLayers([fake, GlobalMOLARGB, GlobalTHEMISIRday, /*MarsExpress,*/ MARSISorbits]); //, GaleHRSCWms, GaleCTXWms]);
    map.zoomTo(3);

	select = new OpenLayers.Control.SelectFeature(MARSISorbits);
            
            MARSISorbits.events.on({
                "featureselected": onFeatureSelect,
                "featureunselected": onFeatureUnselect
            });

            map.addControl(select);
            select.activate();   

    }

        function onPopupClose(evt) {
            select.unselectAll();
        }
        function onFeatureSelect(event) {
            var feature = event.feature;
            // Since KML is user-generated, do naive protection against
            // Javascript.
//              var content = "<h2>Orbit ID:"+feature.attributes.name;
            var content = "<strong>Orbit ID:"+feature.attributes.name + "</strong><br>" + feature.attributes.description;
                content = content + "<hr><hr>";
                content = content + "<form id =\"orbitform\" method=\"POST\" action=\"/showdata.php\" target=\"_blank\">";
                content = content + "<input type=\"hidden\" name=\"orbit\" value=\"" + feature.attributes.name + "\">";
                content = content + "<small>Filter Selection</small><br>";
                content = content + "<select name=\"filter\" form=\"orbitform\">";
                content = content + "<option value=\"echo_mod_m1_f1\" >Frequency 1 (-1) </option>";
                content = content + "<option value=\"echo_mod_00_f1\" >Frequency 1 ( 0) </option>";
                content = content + "<option value=\"echo_mod_p1_f1\" >Frequency 1 (+1) </option>";
                content = content + "<option value=\"echo_mod_m1_f2\" >Frequency 2 (-1) </option>";
                content = content + "<option value=\"echo_mod_00_f2\" >Frequency 2 ( 0) </option>";
                content = content + "<option value=\"echo_mod_p1_f2\" >Frequency 2 (+1) </option>";
                content = content + "</select>";
                content = content + "<hr>";
                content = content + "<small>Echoes range Selection</small><br>";
                content = content + "<p align=\"left\" ><small>From:</small> <input type=\"number\" name=\"echofrom\" value=\"1\" min=\"\" size=\"4\"></p>";
                content = content + "<p align=\"left\" ><small>To:</small>   <input type =\"number\" name=\"echoto\" value=\"0\" min=\"\" size=\"4\"><small>(0 to select all)</small></p>";
                content = content + "<hr>";
                content = content + "<small>Return time Selection</small><br>";
                content = content + "<p align=\"left\" ><small>From:</small> <input type=\"number\" name=\"timefrom\" value=\"0\"   min=\"\" max=\"511\" size=\"3\"></p>";
                content = content + "<p align=\"left\" ><small>To:</small>   <input type=\"number\" name=\"timeto\"   value=\"511\" min=\"\" max=\"511\" size=\"3\"></p>";
                content = content + "<hr>";
                content = content + "<input type=\"checkbox\" name=\"showradar\" value=\"showradargram\"><small>Show Radargram</small><br>";
                content = content + "<input type=\"checkbox\" name=\"showsim\" value=\"showsim\"><small>Show Simulation</small><br>";

                content = content + "<input type=\"submit\" name=\"showmeta\" value=\"Show Echoes Metadata\" />";
                content = content + "</form>";
//            var content = "<h2> Orbit ID: 1889</h2><br><a href=\"\"> Show radargram </a><br><a href=\"\"> Show surface clutter simulation </a>";
            if (content.search("<script") != -1) {
                content = "Content contained Javascript! Escaped content below.<br>" + content.replace(/</g, "&lt;");
            }
            popup = new OpenLayers.Popup.FramedCloud("chicken", 
                                     feature.geometry.getBounds().getCenterLonLat(),
                                     new OpenLayers.Size(100,100),
                                     content,
                                     null, true, onPopupClose);
            feature.popup = popup;
            map.addPopup(popup);
        }
        function onFeatureUnselect(event) {
            var feature = event.feature;
            if(feature.popup) {
                map.removePopup(feature.popup);
                feature.popup.destroy();
                delete feature.popup;
            }
        }
function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }
	var pre = document.createElement('pre');
    pre.innerHTML = out;
    document.body.appendChild(pre)
}
function initpanels()
    {
    var container = document.getElementById("panel");
    var panel = new OpenLayers.Control.Panel({ defaultControl: featureInfo, div: container });
    var container2 = document.getElementById("panel2");
    var panel2 = new OpenLayers.Control.Panel({ div: container2 });
    var container3 = document.getElementById("panel3");
    var panel3 = new OpenLayers.Control.Panel({ div: container3 });
    
    panel.addControls([
  //       featureInfo,
  //       featureInfo1,
  //       featureInfo2,
  //       featureInfo3,
         featureInfo4,
        zoomBox,
        new OpenLayers.Control.ZoomBox({title:"Zoom out box", displayClass: 'olControlZoomOutBox', out: true}),
        new OpenLayers.Control.DragPan({title:'Drag map', displayClass: 'olControlPanMap'}),
        zoomToContextExtent,
        navHistory.previous,
        navHistory.next,
        measureControls.line,
        measureControls.polygon
    ]); 
    panel2.addControls([
      //   toggleBands,
        toggleQuery,
 //       toggleSpectrum,
 //       toggleX3d
    ]);
    panel3.addControls([
         toggleTutorial,
        toggleAbout//,
        //permalink
    ]);
    // add the panel to the map
    map.addControl(panel);
    map.addControl(panel2);

    }
function initvectors()
    {
    var my_style = new OpenLayers.StyleMap({ 
        "default": new OpenLayers.Style( 
        { 
            fillColor: "${fcolor}"
        }) 
    });
    var my_style2 = new OpenLayers.StyleMap({ 
        "default": new OpenLayers.Style( 
        { 
            fillColor: "#4bb2c5"
        }) 
    });
    var my_style3 = new OpenLayers.StyleMap({ 
        "default": new OpenLayers.Style( 
        { 
            fillColor: "#ee9900",
            strokeWidth: 2,
            strokeColor: "#ee9900"
        }) 
    });
    // Create vector layers to record clicks
/*    vector_layer = new OpenLayers.Layer.Vector("Series", {
        styleMap: my_style,
        rendererOptions: { zIndexing: true, extractAttributes: true },
        displayInLayerSwitcher: false
    });
    vector_layer2 = new OpenLayers.Layer.Vector("Spectral ratio", {
        styleMap: my_style2,
        rendererOptions: { zIndexing: true },
        displayInLayerSwitcher: false
    });
    vector_layer3 = new OpenLayers.Layer.Vector("Elevation point", {
        styleMap: my_style3,
        rendererOptions: { zIndexing: true },
        displayInLayerSwitcher: false
    });
    vector_layer4 = new OpenLayers.Layer.Vector("Cross", {
        styleMap: my_style2,
        rendererOptions: { zIndexing: true },
        displayInLayerSwitcher: false
    });
    // Limit the number of features that can be added to each
    vector_layer.events.on({"beforefeatureadded": function() {
            var len = vector_layer.features.length;
             if (len == nrclicks) vector_layer.removeFeatures(vector_layer.features[0]);
         } 
     });
     vector_layer2.events.on({"beforefeatureadded": function() {
             if (vector_layer2.features.length == 2) vector_layer2.destroyFeatures();
         } 
     });
     vector_layer3.events.on({"beforefeatureadded": function() {
             if (vector_layer3.features.length == 3) vector_layer3.destroyFeatures();
         } 
     });
     vector_layer4.events.on({"beforefeatureadded": function() {
             if (vector_layer4.features.length == 1) vector_layer4.destroyFeatures();
         } 
     });*/

    //Add layers to the map
 //   map.addLayers([vector_layer,vector_layer2,vector_layer3,vector_layer4]);
    }
