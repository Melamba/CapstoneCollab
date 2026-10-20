<?php
require 'CapstoneSample_db.php';

$logId = isset($_GET['log_id']) ? (int)$_GET['log_id'] : 0;
$info  = null;

if ($logId > 0) {
    $stmt = $pdo->prepare(
        "SELECT v.firstname, v.middlename, v.lastname, v.birthdate, v.visitor_type, l.building
         FROM visitor_log vl
         JOIN visitor v ON v.visitor_id = vl.visitor_id
         LEFT JOIN location l ON l.log_id = vl.log_id
         WHERE vl.log_id = ?"
    );
    $stmt->execute([$logId]);
    $info = $stmt->fetch() ?: null;
}

$fullName  = $info ? trim($info['firstname'] . ' ' . ($info['middlename'] ?? '') . ' ' . $info['lastname']) : '-';
$birthdate = ($info && $info['birthdate']) ? date('F j, Y', strtotime($info['birthdate'])) : '-';
$age       = ($info && $info['birthdate']) ? (new DateTime($info['birthdate']))->diff(new DateTime('today'))->y : '-';
$type      = $info['visitor_type'] ?? '-';
$building  = $info['building'] ?? '';

// Fill in your own descriptions here (key = building name)
$buildingDesc = [
    'Admin Building' => '', 'FDT' => '', 'Anatomy Building' => '', 'Arch' => '', 'PGT Building' => '',
    'GPL Building' => '', 'LRC Building' => '', 'Purple Owl Complex' => '', 'Centennial Gymnasium' => '',
];
$desc = $buildingDesc[$building] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Dashboard</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
           #map
        {
            width: 80%;
            height: 80%;
            margin: auto;
            border: solid 10px #4a265e;
            z-index: 1;
            transition: width 1s ease, height 1s ease;
        }
            #map:hover
            {
                width: 85%;
                height: 85%;
            }
       
        body
        {
            margin: 0 0 0 0;
           
        }
         .absTask a
    {
        color: white;
    }
     .absTask
    {
        
        color: white;
        position: absolute;
        top: 0vh;
        width:100%;
        height: 5%;
        background-color: #4a265e;
        transition: height 2s ease;
        align-content: center;
        padding-left: 1vw;
    }
      .absTask:hover
    {
        position: absolute;
        top: 0vh;
        width:100%;
        height: 10%;
        background-color: #4a265e;
    }
        .classR
        {
        display: flex;
        flex-direction: row;
        width: 100%;  
        height: 100%;
        min-height: 100%;
        flex: 1 1 auto;
        overflow: hidden;
        }
        .classC
        {
        display: flex;
        flex-direction: column;
         width: 100%;  
        height: 100%;
        min-height: 100%;
        flex: 1 1 auto;
        overflow: hidden;
        }
        .divc
        {
            flex: 1 1 0px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
       
        .divcB
        {
            flex: 0.9 0px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: flex 1s ease;
            
        }
        .divcB:hover
        {
            flex: 1 0 0;
        }
        .pRibbon
        {
            display: flex;
            flex-direction: column;
            width: 100vw;  
            height: 100vh;
            min-height: 100vh;
        }
        .tRibbon
        {
           background-color: #4a265e;
           min-height: 5vh; 
           max-height: 8vh;
            width: 100%;
        z-index: 9999; 
        }
    
    .contDes
    {
    background-color: white;
    border: #4a265e solid 2px;
    border-radius: 12px; 
    color: black;
    width:80%;
    min-height: 30vh;
    padding: 3vh;
    box-sizing: border-box;
    margin: auto;
    box-shadow: 10px 10px 30px rgba(0, 0, 0, 0.3);
    }
      .contDesAlt
        {
            margin-top: 0px;
            min-height: 42VH;
        }
    .idParent
    {
        display: flex;
        flex-direction: row;
        min-height: 100%;
        max-height: 100%;
        align-items: flex-start;
        gap:1vh;
    }
    .idImgcont
    {
       
       width: 30%;
        overflow: hidden;
        display: flex;
        align-items: flex-start; 
    }
    .idImg
    {
    width: 90%;
    height: auto; 
    aspect-ratio: 1 / 1; 
    object-fit: cover;
    object-position: center;
    flex-shrink: 0; 
    border: solid black 1px;
    }
    .idInfo
    {
        color:black;
        display: flex;
        flex-direction: column;
        flex: 1 1;
      padding: 0%;
    }
    .idInfoChild
    {
        padding: 0;
        line-height: 5vh;
        font-size: large;
         align-items: flex-start;
    }
    .idInfoCont
    {
    color: black;
    background-color: lightgrey;
    height: 2vh;
    }
    </style>
</head>
<body>
    <div class="pRibbon">
        <div class="absTask">
            <a href="visitorDashboard.php<?= $logId ? '?log_id=' . $logId : '' ?>">Dashboard</a>
        </div>
        <div class="classR">
            <div class="divc">
                <div id="map"></div>
            </div>
            <div class="divc">
                <div class="divcB">
                    <div class="contDes">
                        <h2>Building Name: <?= e($building ?: '-') ?></h2>
                        <br>
                        <h3>Building Desc: <?= e($desc ?: 'No description yet.') ?></h3>
                    </div>
                </div>
                <div class="divcB">
                    <div class="contDes contDesAlt">
                        <div class="idParent">
                            <div class="idInfo">
                                <h3>VISITOR INFORMATION CARD</h3>
                                <div class="idInfoChild">Name:</div>
                                <div class="idInfoCont"><?= e($fullName) ?></div>
                                <div class="idInfoChild">Age:</div>
                                <div class="idInfoCont"><?= e($age) ?></div>
                                <div class="idInfoChild">Birthdate:</div>
                                <div class="idInfoCont"><?= e($birthdate) ?></div>
                                <div class="idInfoChild">Visitor Type:</div>
                                <div class="idInfoCont"><?= e($type) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@7/turf.min.js"></script>
    <script>
        const myBuilding = <?= json_encode($building) ?>;

        const campusBounds = L.latLngBounds(
            [14.661016559725885, 120.98713847292149],
            [14.657335733123084, 120.98542608229778]
        );

        const map = L.map('map', {
            minZoom: 17.5,
            maxZoom: 24,
            maxBounds: campusBounds,
            maxBoundsViscosity: 1.0
        }).setView([14.65835117638478, 120.98624458460917], 20);

        L.tileLayer('https://api.maptiler.com/maps/openstreetmap/{z}/{x}/{y}.jpg?key=76drzXxYrFFwuQRnFNKS', {
            attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>',
            minZoom: 17.5,
            maxZoom: 22,
            maxNativeZoom: 19
        }).addTo(map);

        const leafletIcon = L.icon({
            iconUrl: 'https://64.media.tumblr.com/193610c5a76a3e0801f40790981a2816/a50f3003a25ecf54-d3/s400x600/adca53d6e4fc0485806a187e2918672c2ffebe74.pnj',
            iconSize: [33, 29]
        });

        // names must match the Building dropdown values in logbookForm.html
        const buildings = [
            ['Admin Building',        14.657660229212729, 120.98671314851723],
            ['FDT',                   14.65834967909162,  120.98625004709956],
            ['Anatomy Building',      14.658000163009532, 120.9867319239792],
            ['Arch',                  14.65776532330548,  120.98641274112585],
            ['PGT Building',          14.659267858564103, 120.98567713880672],
            ['GPL Building',          14.659045187468603, 120.98609128594734],
            ['LRC Building',          14.659267123462485, 120.98645539027785],
            ['Purple Owl Complex',    14.66049141773283,  120.98591887871622],
            ['Centennial Gymnasium',  14.660531785242888, 120.98636658748819]
        ];

        const markers = {};
        buildings.forEach(function (b) {
            markers[b[0]] = L.marker([b[1], b[2]], { icon: leafletIcon }).addTo(map).bindPopup(b[0]);
        });

        // zoom to the building the visitor picked
        let focusedOnBuilding = false;
        if (myBuilding && markers[myBuilding]) {
            map.setView(markers[myBuilding].getLatLng(), 20);
            markers[myBuilding].openPopup();
            focusedOnBuilding = true;
        }

        L.control.scale({ metric: true }).addTo(map);

       
        let userMarker = null, userCircle = null, centered = false, alerted = false;
        navigator.geolocation.watchPosition(success, error);
        
         /*function success(pos) {
           const latlng = [pos.coords.latitude, pos.coords.longitude];
         if (!userMarker) {
                userMarker = L.marker(latlng).addTo(map);
                userCircle = L.circle(latlng, { radius: 5 }).addTo(map);
            } else {
                userMarker.setLatLng(latlng);
                userCircle.setLatLng(latlng).setRadius(5);
            }
            
            if (!centered && !focusedOnBuilding && campusBounds.contains(latlng)) {
                map.setView(latlng, 20);
                centered = true;
            }
        }

        function error(err) {
            if (alerted) return;   // watchPosition repeats errors; only warn once
            alerted = true;
            if (err.code === 1) {
                alert("Please allow access for the geolocation");
            } else {
                alert("Cannot get current location");
            }
        }
     OG CODE NOLI ME TANGERE ISSUE: BADING*/


// di na dumodoble sa at home testing
//sana di din bading
function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(success, error);
  } else {
    x.innerHTML = "Geolocation is not supported by this browser.";
  }
}

function success(position) {
  let lat = position.coords.latitude;
  let lng = position.coords.longitude;
  const latlng = [lat,lng]
        if (!userMarker) {
                userMarker = L.marker(latlng).addTo(map);
                userCircle = L.circle(latlng, { radius: 5 }).addTo(map);
            } else {
                userMarker.setLatLng(latlng);
                userCircle.setLatLng(latlng).setRadius(5);
            }
            
            if (!centered && !focusedOnBuilding && campusBounds.contains(latlng)) {
                map.setView(latlng, 20);
                centered = true;
            }
        }

        function error(err) {
            if (alerted) return;   // watchPosition repeats errors; only warn once
            alerted = true;
            if (err.code === 1) {
                alert("Please allow access for the geolocation");
            } else {
                alert("Cannot get current location");
            }
        } 

        //trigger building
        //test
       const AnatomyBuilding = L.polygon([
        [14.658232872290656, 120.98657575138434],
        [14.65822134412704, 120.98686041137574],
        [14.657815471762621, 120.98685889619858],
        [14.657813483611413, 120.98659996195562]
        ]).addTo(map);
        const AdminBuilding = L.polygon([
        [14.657768508687427, 120.98658662579614],
        [14.657804837497359, 120.98689172706737],
        [14.657569348854521, 120.98686825773883],
        [14.657560915372251, 120.98658930800512]
             ]).addTo(map);
       const FDTBuilding = 
       L.polygon([
        [14.658800040220255, 120.98590777201574],
        [14.658781875898569, 120.98646030706517],
        [14.657895292240127, 120.98645982966937],
        [14.657922191327673, 120.98595670968454]
        
        ]).addTo(map);

        const GPLBuilding = 
       L.polygon([
        [14.659185136805187, 120.98593223629094],
        [14.659174757211238, 120.98620783326345],
        [14.658836757242089, 120.98620842040823],
        [14.658860125535947, 120.98594162402237]
        
        ]).addTo(map);
        
        const LRCBuilding = 
        L.polygon([
        [14.65950317501802, 120.98622011965583],
        [14.65950317501802, 120.986660396316],
        [14.658839756376546, 120.98666689007204],
        [14.65880708798243, 120.98619284588041]
        
        ]).addTo(map);

        const PGTBuilding = 
        L.polygon([
        [14.65965922655244, 120.98555183803205],
        [14.659655695448865, 120.98582193229306],
        [14.658824118966637, 120.98583470702268],
        [14.65882941564208, 120.9855664377229] 
        ]).addTo(map);

        const BECBuilding = 
        L.polygon([
        [14.659976434167568, 120.98587959606887],
        [14.659953185512336, 120.98667261869676],
        [14.6597797146952, 120.98662640525717],
        [14.6597797146952, 120.98671328652364],
        [14.659547227817592, 120.98670034676054],
        [14.659552592902159, 120.98612545157175],
        [14.659718910458736, 120.9861088147335],
        [14.65973858241943, 120.98589623291126]
        ]).addTo(map);

        const PPOWLBuilding = 
        L.polygon([
        [14.660667863345342, 120.98575280705774],
        [14.660675748556242, 120.98604215264211],
        [14.660039673964175, 120.98605302008188],
        [14.660047559197693, 120.98575959920761]
        ]).addTo(map);

        const CGym = 
        L.polygon([
        [14.660784827278011, 120.9861304505904],
        [14.66079928349006, 120.98657194033183],
        [14.66007499795028, 120.98655343106589],
        [14.660073430064543, 120.986208233266],
        [14.660145552796692, 120.98612882156556]
        ]).addTo(map);

        const OOB = 
        L.polygon([
        [14.660790310847363, 120.98545208955804],
        [14.661470932586278, 120.98731516636899],
        [14.657402220931582, 120.98726338705264],
        [14.657431918324873, 120.98548760251096]
        ]).addTo(map);

        const logId = <?= (int)$logId ?>;

const polygons = [
    ["Anatomy Building", AnatomyBuilding],
    ["Admin Building", AdminBuilding],
    ["FDT", FDTBuilding],
    ["GPL Building", GPLBuilding],
    ["LRC Building", LRCBuilding],
    ["PGT Building", PGTBuilding],
    ["BEC Building", BECBuilding],
    ["Purple Owl Complex", PPOWLBuilding],
    ["Centennial Gymnasium", CGym]
];
const bounds = [
    ["in-campus", OOB],
];



navigator.geolocation.watchPosition(function(pos) {
    const point = turf.point([
        pos.coords.longitude,
        pos.coords.latitude
    ]);

    polygons.forEach(function(item) {
        const coords = item[1].getLatLngs()[0]
            .map(p => [p.lng, p.lat]);

        coords.push(coords[0]);

            if (turf.booleanPointInPolygon(
                point,
                turf.polygon([coords])
            )) {
                if (lastBuilding !== item[0]) 
                    {
                    lastBuilding = item[0];
                    alert(item[0])
                    // table update dito ngani
                    //echo '<input type="hidden" name="org_id" value="'.$lastBuilding.'" />'
                    //$org_id = $_POST['org_id']; https://stackoverflow.com/questions/9084749/how-do-i-submit-a-post-variable-with-javascript
                     fetch("updateBuilding.php", {
                    method: "POST",
                    headers: {
                        "Content-Type":
                            "application/x-www-form-urlencoded"
                    },
                    body: "log_id=" + logId +
                          "&building=" +
                          encodeURIComponent(item[0])
                });
    
}
            }
    });
});
 let lastBuilding = ""; 

const watchId = navigator.geolocation.watchPosition(function(pos) {
    

    const point = turf.point([
        pos.coords.longitude,
        pos.coords.latitude
    ]);


    polygons.forEach(function(item) {
        const buildingName = item[0];
        const buildingLayer = item[1];

        const polyGeoJSON = buildingLayer.toGeoJSON().geometry;

        if (turf.booleanPointInPolygon(point, polyGeoJSON)) {
            if (lastBuilding !== buildingName) {
                lastBuilding = buildingName;
                alert(buildingName);

                fetch("updateBuilding.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "log_id=" + logId + "&building=" + encodeURIComponent(buildingName)
                });
            }
        }
    });


    let inBounds = false;

    bounds.forEach(function(item) {
        const boundaryLayer = item[1];
        const boundGeoJSON = boundaryLayer.toGeoJSON().geometry;

        if (turf.booleanPointInPolygon(point, boundGeoJSON)) {
            inBounds = true;
        }
    });


    if (!inBounds) {
        
        navigator.geolocation.clearWatch(watchId); 
        
        alert("Out of bounds! Ending session.");
        
        //pagawa nga ng exit thank you card tas indication na wala na
        window.location.href = "https://www.youtube.com/watch?v=dQw4w9WgXcQ&list=RDdQw4w9WgXcQ&start_radio=1"; 
    }

}, function(err) {
    console.error("Location tracking failure: ", err);
}, {
    enableHighAccuracy: true
});
    </script>
</body>
</html>
