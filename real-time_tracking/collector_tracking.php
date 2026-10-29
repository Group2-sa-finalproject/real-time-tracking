
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Garbage Truck Tracking</title>
</head>
<body>

    <h2>Garbage Truck GPS Tracking</h2>
    <p id="status">Tracking has not started.</p>

    <button onclick="startTracking()">Start Tracking</button>
    <button onclick="stopTracking()">Stop Tracking</button>

    <script>
        let watchId = null;

        function startTracking() {
            if (watchId !== null) return;

            if (!navigator.geolocation) {
                document.getElementById("status").textContent =
                    "GPS is not supported by this browser.";
                return;
            }

            watchId = navigator.geolocation.watchPosition(
                async function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    document.getElementById("status").textContent =
                        "GPS found. Sending location...";

                    try {
                        const response = await fetch("update_location.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                latitude: latitude,
                                longitude: longitude
                            })
                        });

                        if (!response.ok) {
                            throw new Error("Server error");
                        }

                        document.getElementById("status").textContent =
                            "Location sent successfully! " +
                            latitude + ", " + longitude;

                    } catch (error) {
                        document.getElementById("status").textContent =
                            "Failed to send location.";
                    }
                },

                function(error) {
                    document.getElementById("status").textContent =
                        "GPS error: " + error.message;
                },

                {
                    enableHighAccuracy: true,
                    maximumAge: 2000,
                    timeout: 15000
                }
            );
        }

        function stopTracking() {
            if (watchId !== null) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }

            document.getElementById("status").textContent =
                "Tracking stopped.";
        }
    </script>

</body>
</html>
