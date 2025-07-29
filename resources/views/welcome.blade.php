<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simulation API</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: system-ui, sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 10% auto;
            padding: 2rem;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            border-radius: 10px;
            text-align: center;
        }
        h1 {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        .badge {
            background: #3490dc;
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 5px;
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        .api-endpoints {
            margin-top: 1rem;
            font-size: 0.95rem;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Simulation API</h1>
        <p>Welcome to the Simulation API backend built with Laravel.</p>
        <div class="badge">Laravel {{ Illuminate\Foundation\Application::VERSION }}</div>

        <div class="api-endpoints">
            <p>Sample endpoints you can test:</p>
            <ul style="list-style: none; padding-left: 0;">
                <li>GET <code>/api/flights</code></li>
                <li>POST <code>/api/flights</code></li>
                <li>GET <code>/api/passengers</code></li>
                <li>... and more</li>
            </ul>
        </div>
    </div>
</body>
</html>
