<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
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
            Announcement::create($validated);
            return redirect()->route('staff.dashboard')->with('success', 'Announcement created successfully.');
        } catch (\Exception $e) {
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

        $announcement->update($request->all());
        return redirect()->route('staff.dashboard')->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('staff.dashboard')->with('success', 'Announcement deleted successfully.');
    }
}