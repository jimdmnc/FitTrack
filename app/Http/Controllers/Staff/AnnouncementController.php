<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{










    public function landing()
    {
        // Get announcements ordered by latest first
        $announcements = Announcement::orderBy('created_at', 'desc')->get();
        
        // Make sure the view path matches exactly where your blade file is located
        return view('self.landingProfile', compact('announcements'));
    }




    public function index()
    {
        $announcements = Announcement::latest()->get();
        return view('staff.dashboard', compact('announcements'));
    }

    public function create()
    {
        return view('announcements.create');
    }

    public function store(Request $request)
    {
        Log::info('Announcement store request data:', $request->all());

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'schedule' => 'nullable|date|after:now',
            'type' => 'required|in:Maintenance,Event,Update',
        ], [
            'title.required' => 'The title is required.',
            'title.max' => 'The title must not exceed 255 characters.',
            'content.required' => 'The content is required.',
            'schedule.date' => 'The schedule must be a valid date.',
            'schedule.after' => 'The schedule must be a future date.',
            'type.required' => 'The type is required.',
            'type.in' => 'The type must be Maintenance, Event, or Update.',
        ]);

        try {
            $announcement = Announcement::create($validated);
            Log::info('Announcement created successfully:', $announcement->toArray());
            return redirect()->route('staff.dashboard')->with('success', 'Announcement created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create announcement: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create announcement. Please try again.');
        }
    }

    public function edit(Announcement $announcement)
    {
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'schedule' => 'nullable|date',
            'type' => 'required|in:Maintenance,Event,Update',
        ]);

        try {
            $announcement->update($request->all());
            Log::info('Announcement updated successfully:', $announcement->toArray());
            return redirect()->route('staff.dashboard')->with('success', 'Announcement updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update announcement: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update announcement. Please try again.');
        }
    }

    public function destroy(Announcement $announcement)
    {
        try {
            $announcement->delete();
            Log::info('Announcement deleted successfully:', ['id' => $announcement->id]);
            return redirect()->route('staff.dashboard')->with('success', 'Announcement deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete announcement: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete announcement. Please try again.');
        }
    }
}