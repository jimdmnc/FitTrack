<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack - Waiting for Approval</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="180x180" href="{{ asset('images/rockiesLogo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            background: linear-gradient(90deg, #FF0000, rgb(255, 251, 0)); /* Red gradient */
            border-radius: 10px;
            width: 0%;
            animation: pulse 2s infinite ease-in-out;
        }
        
        @keyframes pulse {
            0% { width: 10%; opacity: 0.7; }
            50% { width: 70%; opacity: 1; }
            100% { width: 10%; opacity: 0.7; }
        }

        /* Enhanced X animation */
        @keyframes xAnimation {
            0% { transform: scale(0.5); opacity: 0; }
            20% { transform: scale(1.2); opacity: 1; }
            40% { transform: scale(1); opacity: 1; }
            60% { transform: rotate(-5deg); }
            80% { transform: rotate(5deg); }
            100% { transform: rotate(0deg); }
        }

        .x-animation {
            animation: xAnimation 0.8s ease-in-out forwards;
        }

        /* Custom X icon */
        .x-icon {
            position: relative;
            display: inline-block;
            width: 80px;
            height: 80px;
            margin: 20px auto;
        }

        .x-icon:before, .x-icon:after {
            content: '';
            position: absolute;
            width: 100%;
            height: 8px;
            background-color: #ef4444; /* red-500 */
            border-radius: 4px;
            top: 50%;
            left: 0;
            margin-top: -4px;
        }

        .x-icon:before {
            transform: rotate(45deg);
        }

        .x-icon:after {
            transform: rotate(-45deg);
        }

        .rejection-message {
            background-color: #fee2e2;
            border: 1px solid #ef4444;
            color: #b91c1c;
            padding: 16px;
            border-radius: 8px;
            margin-top: 20px;
        }

        /* Responsive text sizing */
        @media (max-width: 640px) {
            #statusTitle {
                font-size: 1.5rem;
            }
            #statusSubtitle {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body class="bg-neutral-900 flex justify-center items-center min-h-screen m-0 p-0 font-sans">
    <div class="w-11/12 max-w-lg p-8 m-5 rounded-xl shadow-lg bg-neutral-800 text-center">
        <div class="mb-6">
            <!-- Status icon area -->
            <div class="flex justify-center">
                <img src="{{ asset('images/loadinghand3.gif') }}" alt="Loading animation" class="h-32 w-auto" id="statusGif" />
                <div id="xIcon" class="x-icon hidden"></div>
            </div>
            <h2 class="text-2xl font-bold text-orange-500 mb-3" id="statusTitle">Your Request is Processing</h2>
            <p class="text-white text-lg mb-8" id="statusSubtitle">
                @if(session('success'))
                    {{ session('success') }}
                @elseif(session('info'))
                    {{ session('info') }}
                @else
                    Our team is reviewing your request. You'll be automatically redirected once approved.
                @endif
            </p>
        </div>
        
        <div class="progress-container" id="progressContainer">
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
            <p class="time-estimate text-white text-xs mt-2">Typical approval time: <span id="estimatedTime">2-5 minutes</span></p>
        </div>
        
        <div class="mt-5">
            <p id="statusMessage" class="text-gray-300">Waiting for staff review...</p>
        </div>
        
        <!-- Rejection message -->
        <div id="rejectionContainer" class="hidden">
            <div class="rejection-message">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-circle text-red-600 mr-2 text-xl"></i>
                    <h3 class="font-bold text-red-700">Request Rejected</h3>
                </div>
                <p id="rejectionReason" class="text-red-800 text-sm">Your request could not be approved at this time.</p>
            </div>
            
            <div class="mt-6 flex justify-center gap-4">
                <a href="{{ route('self.landingProfile') }}" class="inline-block px-6 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition duration-300">Go to Profile</a>
                <form method="POST" action="{{ route('logout.custom') }}">
                    @csrf
                    <button type="submit" class="inline-block px-6 py-2 bg-gray-700 hover:bg-gray-800 text-white font-medium rounded-lg transition duration-300">
                        <i class="fas fa-door-open mr-2"></i> Sign Out
                    </button>
                </form>
            </div>
        </div>
        
        <div class="mt-6 bg-gray-50 p-4 rounded-lg text-left border border-gray-200">
            <div class="flex items-center text-gray-600 cursor-pointer" onclick="toggleTips()">
                <i class="fas fa-lightbulb text-red-500 mr-2"></i>
                <span>While you wait</span>
                <i class="fas fa-chevron-down ml-auto" id="tipsChevron"></i>
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
            const chevron = document.getElementById('tipsChevron');
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                chevron.className = 'fas fa-chevron-up ml-auto';
            } else {
                content.classList.add('hidden');
                chevron.className = 'fas fa-chevron-down ml-auto';
            }
        }
        
        // Show rejection UI with animated X
        function showRejection(reason) {
            // Hide progress elements
            document.getElementById('progressContainer').classList.add('hidden');
            
            // Hide loading GIF and show X icon with animation
            document.getElementById('statusGif').classList.add('hidden');
            const xIcon = document.getElementById('xIcon');
            xIcon.classList.remove('hidden');
            xIcon.classList.add('x-animation');
            
            // Update status elements for rejection
            document.getElementById('statusTitle').textContent = "Request Not Approved";
            document.getElementById('statusTitle').className = "text-2xl font-bold text-red-500 mb-3";
            document.getElementById('statusSubtitle').textContent = "Unfortunately, your request was not approved.";
            document.getElementById('statusMessage').classList.add('hidden');
            
            // Show rejection container
            document.getElementById('rejectionContainer').classList.remove('hidden');
            
            // Set rejection reason if provided
            document.getElementById('rejectionReason').textContent = reason || "Your request could not be approved at this time.";
        }

        // Status message updates
        const statusMessages = [
            "Waiting for staff review...",
            "Request in queue...",
            "Processing your information...",
            "Almost there! Finalizing review..."
        ];
        
        let messageIndex = 0;
        
        const messageInterval = setInterval(function() {
            messageIndex = (messageIndex + 1) % statusMessages.length;
            document.getElementById('statusMessage').textContent = statusMessages[messageIndex];
        }, 8000);

        // Check approval status
        const statusCheck = setInterval(function() {
            fetch('{{ route('self.checkApproval') }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.approved) {
                    // Clear intervals
                    clearInterval(messageInterval);
                    clearInterval(statusCheck);
                    
                    // Update UI for approval
                    document.getElementById('statusMessage').textContent = "Approved! Redirecting...";
                    document.getElementById('statusGif').src = "{{ asset('images/success1.gif') }}";
                    document.getElementById('statusGif').alt = "Success";
                    
                    // Redirect after delay
                    setTimeout(function() {
                        window.location.href = '{{ route('self.landingProfile') }}';
                    }, 1500);
                } else if (data.rejected) {
                    // Clear intervals
                    clearInterval(messageInterval);
                    clearInterval(statusCheck);
                    
                    // Show rejection UI with reason
                    showRejection(data.reason);
                }
            })
            .catch(error => {
                console.error("Error checking approval status:", error);
                document.getElementById('statusMessage').textContent = "Network error. Retrying...";
            });
        }, 10000); // Poll every 10 seconds
    </script>
</body>
</html>