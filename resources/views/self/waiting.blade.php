<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Processing</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="180x180" href="{{ asset('images/rockiesLogo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>

.progress-container {
            margin: 25px 0;
        }
        
        .progress-bar {
            height: 8px;
            background-color: #e5e7eb; /* gray-200 */
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }
        
        .progress-fill {
            position: absolute;
            height: 100%;
            background: linear-gradient(90deg, #FF0000,rgb(255, 251, 0)); /* Red gradient */
            border-radius: 10px;
            width: 0%;
            animation: pulse 2s infinite ease-in-out;
        }
        
        @keyframes pulse {
            0% { width: 10%; opacity: 0.7; }
            50% { width: 70%; opacity: 1; }
            100% { width: 10%; opacity: 0.7; }
        }        

    </style>
</head>
<body class="bg-neutral-900 flex justify-center items-center min-h-screen m-0 p-0 font-sans">
    <div class="w-11/12 max-w-lg p-8 m-5 rounded-xl shadow-lg bg-neutral-800 text-center">
        <div class="mb-6">
            <!-- Replacing the clock icon with a GIF -->
            <div class="flex justify-center">
                <img src="images/loadinghand3.gif" alt="Loading animation" class="h-32 w-auto" id="statusGif" />
            </div>
            <h2 class="text-2xl font-bold text-orange-500 mb-3">Your Request is Processing</h2>
            <p class="text-white text-lg mb-8">Our team is reviewing your request. You'll be automatically redirected once approved.</p>
        </div>
        
        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
            <p class="time-estimate text-white text-xs mt-2">Typical approval time: <span id="estimatedTime">2-5 minutes</span></p>
        </div>
        
        <div class="mt-5">
            <p id="statusMessage" class="text-gray-300">Waiting for staff review...</p>
        </div>
        
        <div class="mt-6 bg-gray-50 p-4 rounded-lg text-left border border-gray-200">
            <div class="flex items-center text-gray-600 cursor-pointer" onclick="toggleTips()">
                <i class="fas fa-lightbulb text-red-500 mr-2"></i>
                <span>While you wait</span>
                <i class="fas fa-chevron-down ml-auto"></i>
            </div>
            <div class="hidden pt-3 text-gray-600" id="tipsContent">
                <p class="my-1">• Make sure you've completed all required fields in your request</p>
                <p class="my-1">• First-time requests typically take longer for security verification</p>
                <p class="my-1">• Our team processes requests in the order they're received</p>
                <p class="my-1">• You don't need to refresh this page - it will update automatically</p>
            </div>
        </div>
    </div>

    <script>
        // Toggle tips section
        function toggleTips() {
            const content = document.getElementById('tipsContent');
            const chevron = document.querySelector('.fa-chevron-down');
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                chevron.className = 'fas fa-chevron-up ml-auto';
            } else {
                content.classList.add('hidden');
                chevron.className = 'fas fa-chevron-down ml-auto';
            }
        }

        // Status message updates
        const statusMessages = [
            "Waiting for staff review...",
            "Request in queue...",
            "Processing your information...",
            "Almost there! Finalizing review..."
        ];
        
        let messageIndex = 0;
        
        setInterval(function() {
            messageIndex = (messageIndex + 1) % statusMessages.length;
            document.getElementById('statusMessage').textContent = statusMessages[messageIndex];
        }, 8000);

        // Check approval status
        setInterval(function() {
            fetch('{{ route("self.checkApproval") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.approved) {
                        document.getElementById('statusMessage').textContent = "Approved! Redirecting...";
                        // Change the loading GIF to a success GIF or image
                        document.getElementById('statusGif').src = "images/success1.gif";
                        document.getElementById('statusGif').alt = "Success";
                        setTimeout(function() {
                            window.location.href = '{{ route("self.landing") }}';
                        }, 1500);
                    }
                })
                .catch(error => {
                    console.error("Error checking approval status:", error);
                });
        }, 5000);
    </script>
</body>
</html>