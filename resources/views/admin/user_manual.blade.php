<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Manual Video</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-100 to-gray-300 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-3xl w-full bg-white rounded-2xl shadow-xl p-6 border border-gray-200">
        
        <h2 class="text-3xl font-bold text-gray-800 mb-4 text-center">
            📘 User Manual & Tutorial
        </h2>

        <p class="text-gray-600 text-center mb-6">
            Watch this quick video to understand how to use the application.
        </p>

        @php
            $file = public_path("manual/$id.mp4");
        @endphp

        <div class="rounded-lg overflow-hidden shadow-md">
            <video class="w-full h-auto rounded-lg" controls>
                @if(file_exists($file))
                    <source src="{{ asset("manual/$id.mp4") }}" type="video/mp4">
                @else
                    <p class="text-red-600 p-3">Video not found!</p>
                @endif
            </video>
        </div>

        <div class="text-center mt-6">
            <button onclick="document.querySelector('video').play()" 
                class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                ▶ Play Video
            </button>
        </div>

    </div>

</body>
</html>
