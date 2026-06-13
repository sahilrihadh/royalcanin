<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Events\QuestionAnswered;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $questions = Question::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        $stats = [
            'total' => Question::count(),
            'answered' => Question::where('is_answered', true)->count(),
            'pending' => Question::where('is_answered', false)->count(),
        ];

        return view('admin.questions.index', compact('questions', 'stats'));
    }
    
    public function show($id)
    {
        $question = Question::with('user')->findOrFail($id);
        return view('admin.questions.show', compact('question'));
    }
    
    public function answer(Request $request, $id)
    {
        try {
            $request->validate([
                'answer_text' => 'required|string|min:3|max:5000'
            ]);
            
            $question = Question::findOrFail($id);
            
            $wasAnswered = $question->is_answered;
            
            $question->update([
                'answer_text' => $request->answer_text,
                'is_answered' => true,
                'answered_at' => now()
            ]);
            
            // Broadcast event ONLY when answering (not on edit)
            // Or broadcast on both - your choice
            broadcast(new QuestionAnswered($question))->toOthers();
            
            $message = $wasAnswered ? 'Answer updated successfully!' : 'Answer submitted successfully!';
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'was_updated' => $wasAnswered
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit answer: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $question = Question::findOrFail($id);
            $question->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Question deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete question'
            ], 500);
        }
    }
    
    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->ids;
            
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No records selected'
                ], 400);
            }
            
            $deleted = Question::whereIn('id', $ids)->delete();
            
            return response()->json([
                'success' => true,
                'message' => $deleted . ' questions deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete questions'
            ], 500);
        }
    }
}