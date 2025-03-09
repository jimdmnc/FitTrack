@extends('layouts.app') <!-- Assuming you have a main layout file -->

@section('content')


            <section class="grid grid-cols-1  gap-4 mt-6">
                <div class="md:col-span-2 bg-white p-6 rounded-lg shadow">
                    <h2 class="font-bold text-lg sm:text-3xl"><span class="text-indigo-700">Dashboard</span></h2>
                </div>
            </section>
            
            <section class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div class="md:col-span-2 bg-white p-6 rounded-lg shadow">
                    <h2 class="font-bold">Welcome Banner, <span class="text-indigo-700">Martell</span></h2>
                    <p class="text-sm text-gray-500">Lorem ipsum dolor sit amet consectetur adipisicing elit. Quam alias tempora enim, voluptatum nisi perferendis eos fugit at cupiditate. Minus qui accusantium reprehenderit ab tenetur, assumenda enim eaque commodi! Veritatis deleniti, deserunt officia delectus fugit maiores fuga error amet eveniet perspiciatis iusto sit eos mollitia aperiam atque iste voluptas laboriosam.</p>
                </div>
                <!-- <div class="bg-white p-6 rounded-lg shadow">Calendar</div> -->
                <div class="bg-white p-6 rounded-lg shadow">Inventory</div>
            </section>
            
            <section class="grid grid-cols-3 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-white p-6 rounded-lg shadow md:col-span-2 col-span-4 ">
                <h2 class="font-bold">Sales</h2>
                        
                        <!-- Sales Graph -->
                        <div class="mt-4">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>

                    <!-- ITO AY TEMPORARY DESIGN PURPOSES ONLY HAKHAKAHAK HIKOPHIKOP -->
                    <!-- Chart.js Library -->
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                    <!-- Script to Generate the Chart -->
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const ctx = document.getElementById('salesChart').getContext('2d');
                            
                            new Chart(ctx, {
                                type: 'bar', // Bar chart
                                data: {
                                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'], // Months
                                    datasets: [{
                                        label: 'Monthly Sales (in PHP)',
                                        data: [1200, 1500, 1800, 2200, 1700, 2500], // Example sales data
                                        backgroundColor: [
                                            'rgba(54, 162, 235, 0.7)',
                                            'rgba(75, 192, 192, 0.7)',
                                            'rgba(255, 159, 64, 0.7)',
                                            'rgba(153, 102, 255, 0.7)',
                                            'rgba(255, 205, 86, 0.7)',
                                            'rgba(201, 203, 207, 0.7)'
                                        ],
                                        borderColor: 'rgba(0, 0, 0, 0.2)',
                                        borderWidth: 1,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: {
                                                display: true,
                                                text: 'Sales in PHP'
                                            }
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: 'Months'
                                            }
                                        }
                                    }
                                }
                            });
                        });
                    </script>
                    <div class="bg-white p-6 rounded-lg shadow md:col-span-1 col-span-4">
                        <h2 class="font-bold">Coaches</h2>
                        <ul class="mt-4 space-y-2">
                            <li class="flex items-center">🔵 Jim Dominic</li>
                            <li class="flex items-center">⚫ Eulice Mage</li>
                            <li class="flex items-center">⚫ John Christopher</li>
                        </ul>
                    </div>
                       
                    <div class="col-span-4 md:col-span-4 bg-white p-6 rounded-lg shadow">
                        <h2 class="font-bold">Active Members</h2>
                        
                        <!-- Search Bar and View All Button -->
                        <div class="mt-4 flex justify-between items-center">
                            <input type="text" class="w-2/3 p-2 border-2 rounded border-gray-400" placeholder="Search">
                            <a href="#" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                                View All
                            </a>
                        </div>

                        <!-- Members Table -->
                        <div class="mt-4 overflow-x-auto">
                            <table class="w-full border-collapse border border-gray-300">
                                <thead>
                                    <tr class="bg-gray-200">
                                        <th class="border border-gray-300 p-2">#</th>
                                        <th class="border border-gray-300 p-2">Name</th>
                                        <th class="border border-gray-300 p-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-gray-100">
                                        <td class="border border-gray-300 p-2">1</td>
                                        <td class="border border-gray-300 p-2">Leniño Nequinto</td>
                                        <td class="border border-gray-300 p-2">Active</td>
                                    </tr>
                                    <tr class="bg-white">
                                        <td class="border border-gray-300 p-2">2</td>
                                        <td class="border border-gray-300 p-2">Carlos Roi Barretto</td>
                                        <td class="border border-gray-300 p-2">Active</td>
                                    </tr>
                                    <tr class="bg-gray-100">
                                        <td class="border border-gray-300 p-2">3</td>
                                        <td class="border border-gray-300 p-2">Nomer Aguado</td>
                                        <td class="border border-gray-300 p-2">Active</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

            </section>
            @endsection