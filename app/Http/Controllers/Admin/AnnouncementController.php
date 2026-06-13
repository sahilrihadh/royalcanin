<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Events\ShowAnnouncement;
use App\Events\HideAnnouncement;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->get();
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:show,hide'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $announcement = Announcement::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status
        ]);

        // Broadcast if status is 'show'
        if ($announcement->status === 'show') {
            broadcast(new ShowAnnouncement($announcement))->toOthers();
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully!');
    }

    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:show,hide'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $oldStatus = $announcement->status;
        $announcement->update([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status
        ]);

        // Handle broadcasting on update
        if ($request->status === 'show' && $oldStatus !== 'show') {
            broadcast(new ShowAnnouncement($announcement))->toOthers();
        } elseif ($request->status === 'hide' && $oldStatus !== 'hide') {
            broadcast(new HideAnnouncement($announcement->id))->toOthers();
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully!');
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        
        // Hide the announcement before deleting
        if ($announcement->status === 'show') {
            broadcast(new HideAnnouncement($announcement->id))->toOthers();
        }
        
        $announcement->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Announcement deleted successfully!']);
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->status = $announcement->status === 'show' ? 'hide' : 'show';
        $announcement->save();

        if ($announcement->status === 'show') {
            broadcast(new ShowAnnouncement($announcement))->toOthers();
        } else {
            broadcast(new HideAnnouncement($announcement->id))->toOthers();
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => 'Status updated successfully!',
                'status' => $announcement->status
            ]);
        }

        return redirect()->back()->with('success', 'Status updated!');
    }

    public function getActive()
    {
        $activeAnnouncements = Announcement::where('status', 'show')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($activeAnnouncements);
    }
}