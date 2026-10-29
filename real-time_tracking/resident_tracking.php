
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Garbage Truck Live Tracking</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        #map {
            height: 500px;
            width: 100%;
        }

        #status {
            padding: 10px;
            background: #f1f5f9;
        }
    </style>
</head>
<body>

    <h2>Garbage Truck Live Tracking</h2>
    <p id="status">Loading truck location...</p>
    <div id="map"></div>

    <script>
        let map;
        let truckMarker;

        function initMap() {
            map = new google.maps.Map(
                document.getElementById("map"),
                {
                    center: { lat: 15.22, lng: 120.57 },
                    zoom: 14
                }
            );

            updateTruckLocation();

            // Refresh the truck's location every 3 seconds.
            setInterval(updateTruckLocation, 3000);
        }

        async function updateTruckLocation() {
            try {
                const response = await fetch(
                    "get_location.php",
                    { cache: "no-store" }
                );

                if (!response.ok) {
                    throw new Error("Unable to get location");
                }

                const truck = await response.json();

                if (
                    truck.latitude === null ||
                    truck.longitude === null
                ) {
                    document.getElementById("status").textContent =
                        "Waiting for the truck's GPS location...";
                    return;
                }

                const position = {
                    lat: Number(truck.latitude),
                    lng: Number(truck.longitude)
                };

                if (!truckMarker) {
                    truckMarker = new google.maps.Marker({
                        position: position,
                        map: map,
                        title: "Garbage Collection Truck",
                        label: "T"
                    });

                    map.setCenter(position);
                    map.setZoom(16);
                } else {
                    truckMarker.setPosition(position);
                }

                document.getElementById("status").textContent =
                    "Truck location updated: " + truck.updated_at;

            } catch (error) {
                document.getElementById("status").textContent =
                    "Unable to load location. Check your API key and connection.";
            }
        }

        window.initMap = initMap;
    </script>

    <script async
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCRCF376EglnWvet2Z-B0AW2-_H461EfcA&callback=initMap">
    </script>

</body>
</html>
