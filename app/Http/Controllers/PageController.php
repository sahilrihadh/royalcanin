<?php
// app/Http/Controllers/PageController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Question;
use App\Models\Poll;
use App\Models\PollVote;
use App\Models\PollOption; 
use App\Models\Reaction;
use App\Models\PreviousSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\CertificateMail;
use App\Events\PollStatusChanged;
use App\Models\LoginDetails;

class PageController extends Controller
{
    // Remove the constructor completely

    // Webcast Page
    public function webcast()
    {
        $user = Auth::user();
        return view('pages.webcast', compact('user'));
    }

    // Previous Sessions Page
    // Previous Sessions Page
public function previousSessions()
{
    $faqs = [
        ['question' => 'What is the drug of choice for pancreatitis?', 'answer' => '<p>There are no universally recommended drugs except <strong>Fuzapladib sodium monohydrate</strong>, currently licensed in Japan and conditionally approved in the USA.</p><p>Most cases are managed symptomatically for abdominal pain, hypovolemia/shock, gastrointestinal signs, dietary therapy, and monitoring for complications.</p><p class="mb-0">Therapy should be individualized and customized for each case.</p>'],

        ['question' => 'Can you explain the cytokine storm in acute pancreatitis?', 'answer' => '<p>The inflammatory response in acute pancreatitis (AP) is complex and may be localized or systemic, determining disease severity and progression.</p><p>Release of pancreatic enzymes leads to neutrophilic inflammation, production of reactive oxygen species, nitric oxide, cytokines, and activation of multiple inflammatory pathways.</p><p>Key mechanisms include:</p><ul><li>Trypsinogen activation</li><li>Activation of nuclear factor kappa B (NF-κB)</li><li>Release of inflammatory mediators</li><li>Interleukin-6 (IL-6) production</li><li>Neutrophil invasion</li></ul><p class="mb-0">Refer to <strong>Cridge et al</strong> and <strong>Mansfield et al (JVIM)</strong> for detailed review.</p>'],

        ['question' => 'Can we use SNAP cPL of dogs for cats if fPL is unavailable?', 'answer' => '<p><strong>Unlikely and not validated.</strong></p><p class="mb-0">The feline pancreatic lipase immunoreactivity (fPLI) assay is one of the most sensitive and specific tests for feline pancreatitis.</p>'],

        ['question' => 'What are the limitations of ultrasonography in early pancreatitis?', 'answer' => '<p class="mb-0">The pancreas may appear normal in the early stages before morphological ultrasonographic changes develop. Monitor closely if pancreatitis is strongly suspected, especially with elevated cPL values.</p>'],

        ['question' => 'Is there any benefit of adding oral pancreatic enzymes in pancreatitis?', 'answer' => '<p class="mb-0"><strong>Unlikely.</strong> Additional oral pancreatic enzymes generally do not provide benefit unless the patient develops exocrine pancreatic insufficiency (EPI).</p>'],

        ['question' => 'Is a low-fat GI diet suitable for EPI too?', 'answer' => '<p class="mb-0"><strong>Yes.</strong> A low-fat gastrointestinal diet can also be suitable for EPI patients.</p>'],

        ['question' => 'Dog is epileptic and on Gardenal. Should treatment continue during acute pancreatitis?', 'answer' => '<p>Phenobarbitone\'s direct role in causing acute pancreatitis is not yet proven and may instead be associated with obesity or polyphagia.</p><p class="mb-0">Alternatives like <strong>levetiracetam</strong> may be considered until pancreatitis resolves, but generally <strong>Gardenal is not stopped abruptly.</strong></p>'],

        ['question' => 'How can we differentiate GERD from pancreatitis in dogs?', 'answer' => '<ul><li>GERD is less common in dogs compared to humans</li><li>GERD patients generally do not appear severely ill</li><li>Regurgitation is more common than vomiting</li><li>Systemic signs are uncommon in GERD</li><li>Blood work is usually normal unless chronic disease exists</li></ul><p class="mb-0">Acute pancreatitis patients usually appear significantly sicker.</p>'],

        ['question' => 'Is NAC good to give IV for acute pancreatitis?', 'answer' => '<p class="mb-0">Not validated, but generally considered safe if clinically indicated.</p>'],

        ['question' => 'Can freeze-dried raw pancreas and cobalamin be used for AP?', 'answer' => '<p>Excellent results are reported for <strong>EPI management</strong>.</p><p class="mb-0">However, this approach is generally <strong>not recommended for acute pancreatitis (AP).</strong></p>'],

        ['question' => 'Management approach for epilepsy patients on high-fat diets who develop pancreatitis?', 'answer' => '<p>Acute pancreatitis should be managed first until recovery.</p><p class="mb-0">High-fat diets are not always essential for epilepsy management. Alternatives such as MCT oil or coconut oil may be considered.</p>'],

        ['question' => 'How do you manage babesiosis-induced acute pancreatitis?', 'answer' => '<p>Treat babesiosis with recommended specific therapy while simultaneously managing acute pancreatitis symptomatically.</p><p class="mb-0">Control inflammation, shock, pain, and dehydration. Drug conflicts affecting the pancreas are considered unlikely.</p>'],

        ['question' => 'What is the most underestimated mechanism driving morbidity in pancreatitis?', 'answer' => '<p>Traditional theories focus on trypsin activation, while newer concepts emphasize cytokine storm pathways.</p><p>No single chemokine or cytokine appears solely responsible. It is a cascade of inflammatory events triggered by NF-κB and related mediators.</p><p class="mb-0">Refer to <strong>Cridge et al</strong> and <strong>Mansfield et al (JVIM)</strong> for detailed review.</p>']
    ];

    return view('pages.previous-sessions', compact('faqs'));
}

public function sendCertificate(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'fullName' => 'required|string',
        'webinarId' => 'required|string'
    ]);

    $email = $request->email;
    $webinarId = $request->webinarId;
    $fullName = $request->fullName;

    // Check if certificate already sent
    $existing = PreviousSession::where('email_id', $email)
        ->where('session_name', $webinarId)
        ->first();

    if ($existing && $existing->certificate_status == 1) {
        return response()->json([
            'success' => false,
            'message' => 'Certificate already sent for this webinar',
            'already_sent' => true
        ]);
    }

    // Define webinar configurations
    $webinarConfig = [
        'webinar1' => ['event_date' => '27th May 2026', 'template' => 'assets/img/Certificate1.png'],
        'webinar2' => ['event_date' => '10th November 2026', 'template' => 'assets/img/Certificate_10_November.png'],
        'webinar3' => ['event_date' => '24th November 2026', 'template' => 'assets/img/Certificate_24_November.png'],
        'webinar4' => ['event_date' => '15th October 2026', 'template' => 'assets/img/Certificate_15_Oct.png'],
        'webinar5' => ['event_date' => '26th November 2026', 'template' => 'assets/img/Certificate_26_Nov.png'],
    ];

    if (!isset($webinarConfig[$webinarId])) {
        return response()->json(['success' => false, 'message' => 'Invalid webinar ID'], 400);
    }

    // Generate certificate image
    $certificatePath = $this->generateCertificateImage($fullName, $webinarConfig[$webinarId]['template']);

    if (!$certificatePath) {
        return response()->json(['success' => false, 'message' => 'Failed to generate certificate'], 500);
    }

    // Save/Update record
    if ($existing) {
        $existing->update([
            'certificate_status' => 1,
            'certificate_path' => $certificatePath,
            'count' => $existing->count + 1
        ]);
    } else {
        PreviousSession::create([
            'name' => $fullName,
            'email_id' => $email,
            'session_name' => $webinarId,
            'watched_on' => now(),
            'certificate_status' => 1,
            'certificate_path' => $certificatePath,
            'count' => 1
        ]);
    }

    // Send email
    try {
        Mail::to($email)->send(new CertificateMail($fullName, $webinarConfig[$webinarId]['event_date'], $certificatePath));

        return response()->json([
            'success' => true,
            'message' => 'Certificate sent successfully to ' . $email
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to send email: ' . $e->getMessage()
        ], 500);
    }
}

private function generateCertificateImage($fullName, $templatePath)
{
    try {
        $fullTemplatePath = public_path($templatePath);

        // Check if template exists
        if (!file_exists($fullTemplatePath)) {
            Log::error("Certificate template not found: {$templatePath}");
            return false;
        }

        // Load template image
        $templateImage = imagecreatefrompng($fullTemplatePath);
        if (!$templateImage) {
            Log::error("Failed to load certificate template: {$templatePath}");
            return false;
        }

        // Find available font
        $fontFile = $this->getAvailableFont();
        if (!$fontFile) {
            Log::error("No font available for certificate generation");
            imagedestroy($templateImage);
            return false;
        }

        // Set font color (black)
        $fontColor = imagecolorallocate($templateImage, 0, 0, 0);

        // Add text to image
        $result = imagettftext($templateImage, 46, 0, 900, 850, $fontColor, $fontFile, $fullName);

        if (!$result) {
            Log::error("Failed to add text to certificate");
            imagedestroy($templateImage);
            return false;
        }

        // Create certificates directory if not exists
        $certificateDir = storage_path('app/public/certificates');
        if (!file_exists($certificateDir)) {
            mkdir($certificateDir, 0777, true);
        }

        // Save certificate
        $fileName = time() . '_' . rand(1000, 9999) . '.png';
        $fullPath = $certificateDir . '/' . $fileName;

        $saved = imagepng($templateImage, $fullPath);
        imagedestroy($templateImage);

        if (!$saved) {
            Log::error("Failed to save certificate: {$fileName}");
            return false;
        }

        Log::info("Certificate generated for: {$fullName}");
        return 'certificates/' . $fileName;
        
    } catch (\Exception $e) {
        Log::error('Certificate generation failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get available font for certificate generation
 */
private function getAvailableFont()
{
    $fonts = [
        public_path('assets/fonts/D-DIN-PRO-500-Medium.otf'),
        public_path('assets/fonts/D-DIN-PRO-Medium.otf'),
        public_path('assets/fonts/D-DIN-PRO-Bold.otf'),
        public_path('assets/fonts/arial.ttf'),
        public_path('assets/fonts/Roboto-Regular.ttf')
    ];

    foreach ($fonts as $font) {
        if (file_exists($font)) {
            return $font;
        }
    }

    return false;
}

    // Player Page (Video Stream)
    public function player()
    {
        return view('pages.player');
    }

    // Rest of your methods remain the same...
    public function submitQuestion(Request $request)
{
    try {
        // Validate the request
        $request->validate([
            'question_input' => 'required|string|min:5|max:1000'
        ]);
        
        $user = Auth::user();
        
        // Check if user is logged in
        if (!$user) {
            return response()->json(0); // Not authenticated
        }
        
        // Check if user already submitted a question in this session (last 24 hours)
        $existingQuestion = Question::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->first();
            
        if ($existingQuestion) {
            return response()->json(2); // Already submitted
        }
        
        // Create new question
        $question = Question::create([
            'user_id' => $user->id,
            'question_text' => $request->question_input,
            'asked_at' => now(),
            'is_answered' => false,
            'answer_text' => null
        ]);
        
        // Trigger Pusher event for new question (if you have an event)
        // broadcast(new NewQuestionEvent($question))->toOthers();
        
        // Log success for debugging
        \Log::info('Question submitted successfully', [
            'user_id' => $user->id,
            'question_id' => $question->id
        ]);
        
        return response()->json(1); // Success
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(0);
    } catch (\Exception $e) {
        // Log error for debugging
        \Log::error('Question submission error: ' . $e->getMessage());
        return response()->json(0); // Error
    }
}

    public function getQuestions(Request $request)
{
    try {
        $questions = Question::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        
        if ($questions->isEmpty()) {
            return '<div class="text-center text-muted p-4">No questions asked yet.</div>';
        }
        
        $html = '';
        foreach ($questions as $question) {
            $userName = $question->user ? ($question->user->full_name ?? $question->user->name ?? 'Anonymous') : 'Anonymous';
            $questionText = htmlspecialchars($question->question_text ?? $question->question_input);
            $askedAt = $question->created_at->diffForHumans();
            
            $html .= '<div class="question-item mb-3">';
            $html .= '<div class="message-bubble">';
            $html .= '<div class="question-box">';
            $html .= '<div class="message-header">';
            $html .= '<strong><i class="fas fa-user"></i> ' . $userName . '</strong>';
            $html .= '<span class="message-time"><i class="far fa-clock"></i> ' . $askedAt . '</span>';
            $html .= '</div>';
            $html .= '<div class="message-text">' . nl2br($questionText) . '</div>';
            $html .= '</div>';
            
            // Show answer if answered
            if ($question->is_answered && $question->answer_text) {
                $answeredAt = $question->answered_at ? $question->answered_at->diffForHumans() : $askedAt;
                $html .= '<div class="answer-box mt-2 pt-2">';
                $html .= '<div class="answer-label">';
                $html .= '<i class="fas fa-reply-all text-success"></i>';
                $html .= '<strong class="text-success">Answer:</strong>';
                $html .= '<span class="message-time ms-2">' . $answeredAt . '</span>';
                $html .= '</div>';
                $html .= '<div class="answer-text">' . nl2br(htmlspecialchars($question->answer_text)) . '</div>';
                $html .= '</div>';
            }
            
            $html .= '</div>';
            $html .= '</div>';
        }
        
        return $html;
        
    } catch (\Exception $e) {
        \Log::error('Error fetching questions: ' . $e->getMessage());
        return '<div class="text-center text-danger p-4">Error loading questions. Please refresh.</div>';
    }
}

    
public function checkPoll(Request $request)
{
    try {
        $activePoll = Poll::where('is_active', true)
            ->with('options')
            ->first();
        
        if (!$activePoll) {
            return response()->json('NO_POLL_ACTIVE');
        }
        
        $user = Auth::user();
        
        // Debug: Log user info
        Log::info('Check poll - User: ', ['user_id' => $user ? $user->id : 'Not logged in']);
        
        // Check if user is logged in
        if (!$user) {
            // User not logged in, show vote form or login message
            $html = '<div class="mb-4">';
            $html .= '<h4 class="text-danger poll-title">' . e($activePoll->question) . '</h4>';
            $html .= '<div class="alert alert-warning">Please login to vote in this poll.</div>';
            $html .= '</div>';
            
            return response()->json([
                'has_poll' => true,
                'poll' => [
                    'id' => $activePoll->id,
                    'html' => $html
                ]
            ]);
        }
        
        // Check if user has voted - FIXED: Use poll_id directly
        $hasVoted = PollVote::where('poll_id', $activePoll->id)
            ->where('user_id', $user->id)
            ->exists();
        
        // Debug: Log vote status
        Log::info('Poll vote status', [
            'poll_id' => $activePoll->id,
            'user_id' => $user->id,
            'has_voted' => $hasVoted
        ]);
        
        $totalVotes = $activePoll->options()->sum('vote_count');
        
        $html = '<div class="mb-4">';
        
        if (!$hasVoted) {
            // Show vote form
            $html .= '<h4 class="text-danger poll-title">' . e($activePoll->question) . '</h4>';
            $html .= '<form id="poll-form" method="post">';
            $html .= '<input type="hidden" name="poll_id" value="' . $activePoll->id . '">';
            
            foreach ($activePoll->options as $option) {
                $html .= '<div class="form-check mb-2">';
                $html .= '<label class="form-check-label">';
                $html .= '<input type="radio" class="form-check-input" name="poll" value="' . $option->id . '">';
                $html .= e($option->option_text);
                $html .= '<i class="input-helper"></i>';
                $html .= '</label>';
                $html .= '</div>';
            }
            
            $html .= '<button type="submit" class="btn btn-canin mt-4" id="but_vote">Vote</button>';
            $html .= '</form>';
        } else {
            // User has already voted - Show results
            $userVote = PollVote::where('poll_id', $activePoll->id)
                ->where('user_id', $user->id)
                ->first();
            
            $html .= '<h4 class="text-danger poll-title">' . e($activePoll->question) . '</h4>';
            
            // Show result message
            if ($userVote && $userVote->is_correct) {
                $html .= '<div class="alert alert-success mb-3"><i class="fas fa-check-circle"></i> Congratulations! You selected the correct answer!</div>';
            } elseif ($userVote && !$userVote->is_correct) {
                $correctOption = $activePoll->options->where('is_correct', true)->first();
                $correctAnswerText = $correctOption ? e($correctOption->option_text) : 'Unknown';
                $html .= '<div class="alert alert-info mb-3"><i class="fas fa-info-circle"></i> Your answer was incorrect. The correct answer is: <strong>' . $correctAnswerText . '</strong></div>';
            }
            
            $html .= '<div class="poll-results">';
            $html .= '<p class="text-muted mb-3">Poll Results:</p>';
            
            $colors = ['#6993ff', '#008080', '#e3242b', '#ffbd59', '#050357'];
            $colorIndex = 0;
            
            foreach ($activePoll->options as $option) {
                $percentage = $totalVotes > 0 ? round(($option->vote_count / $totalVotes) * 100) : 0;
                $bgColor = $colors[$colorIndex % count($colors)];
                
                // Highlight correct answer
                $isCorrectOption = $option->is_correct;
                $correctBadge = $isCorrectOption ? ' <span class="badge bg-success ms-2"><i class="fas fa-check"></i> Correct Answer</span>' : '';
                
                // Add checkmark if user selected this option
                $userSelectedBadge = '';
                if ($userVote && $userVote->poll_option_id == $option->id) {
                    $userSelectedBadge = ' <span class="badge bg-primary ms-2"><i class="fas fa-user-check"></i> Your Answer</span>';
                }
                
                $html .= '<div class="mb-3">';
                $html .= '<div class="d-flex justify-content-between mb-1">';
                $html .= '<strong>' . e($option->option_text) . $correctBadge . $userSelectedBadge . '</strong>';
                $html .= '<span>' . $percentage . '%</span>';
                $html .= '</div>';
                $html .= '<div class="progress" style="height: 30px;">';
                $html .= '<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" ';
                $html .= 'style="width: ' . $percentage . '%; background-color: ' . $bgColor . ';" ';
                $html .= 'aria-valuenow="' . $percentage . '" aria-valuemin="0" aria-valuemax="100">';
                $html .= $option->vote_count . ' votes';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                
                $colorIndex++;
            }
            
            $html .= '<div class="mt-3 pt-2 border-top">';
            $html .= '<div class="text-muted">Total votes: ' . $totalVotes . '</div>';
            $html .= '<div class="text-muted small mt-1"><i class="fas fa-chart-bar"></i> You have already voted in this poll.</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        return response()->json([
            'has_poll' => true,
            'poll' => [
                'id' => $activePoll->id,
                'html' => $html
            ]
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error checking poll: ' . $e->getMessage());
        return response()->json('NO_POLL_ACTIVE');
    }
}

public function submitVote(Request $request)
{
    try {
        $optionId = $request->poll ?: $request->option_id;
        
        \Log::info('Submit vote request', [
            'option_id' => $optionId,
            'request_data' => $request->all()
        ]);
        
        // Find the option with poll relationship
        $option = PollOption::with('poll')->find($optionId);
        if (!$option) {
            return response()->json(['success' => false, 'message' => 'Invalid option']);
        }
        
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please login to vote']);
        }
        
        // Check if already voted - FIXED: Check properly
        $existingVote = PollVote::where('poll_id', $option->poll_id)
            ->where('user_id', $user->id)
            ->first();
            
        if ($existingVote) {
            return response()->json(['success' => false, 'message' => 'You have already voted in this poll']);
        }
        
        // Check if this is the correct answer
        $isCorrect = ($option->is_correct == 1 || $option->is_correct === true);
        
        // Save vote
        $option->increment('vote_count');
        
        $vote = PollVote::create([
            'poll_id' => $option->poll_id,
            'poll_option_id' => $option->id,
            'user_id' => $user->id,
            'is_correct' => $isCorrect
        ]);
        
        \Log::info('Vote saved successfully', [
            'vote_id' => $vote->id,
            'user_id' => $user->id,
            'poll_id' => $option->poll_id,
            'is_correct' => $isCorrect
        ]);
        
        return response()->json([
            'success' => true, 
            'message' => 'Vote submitted successfully!',
            'is_correct' => $isCorrect
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error submitting vote: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error submitting vote: ' . $e->getMessage()]);
    }
}

/**
 * Get active poll with options
 */
private function getActivePoll()
{
    $poll = Poll::where('is_active', true)
        ->with('options')
        ->first();
    
    if (!$poll) {
        return null;
    }
    
    $optionsArray = [];
    foreach ($poll->options as $index => $option) {
        $optionsArray[] = [
            'id' => $option->id,
            'text' => $option->option_text,
            'votes' => $option->vote_count ?? 0
        ];
    }
    
    return [
        'id' => $poll->id,
        'question' => $poll->question,
        'options' => $optionsArray,
        'total_votes' => $poll->votes()->count(),
        'html' => $this->getPollHtml($poll, $optionsArray)
    ];
}

/**
 * Generate poll HTML for broadcasting
 */
private function getPollHtml($poll, $options)
{
    if (!$poll) {
        return null;
    }
    
    $totalVotes = $poll->votes()->count();
    
    $html = '<div class="poll-container p-4 border rounded bg-white">';
    $html .= '<h4 class="mb-3 fw-bold">' . e($poll->question) . '</h4>';
    $html .= '<div class="poll-options">';
    
    foreach ($options as $option) {
        $percentage = $totalVotes > 0 ? round(($option['votes'] / $totalVotes) * 100) : 0;
        
        $html .= '<div class="poll-option mb-3" data-option-id="' . $option['id'] . '">';
        $html .= '<div class="d-flex justify-content-between align-items-center mb-1">';
        $html .= '<span class="fw-medium">' . e($option['text']) . '</span>';
        $html .= '<span class="badge bg-primary rounded-pill">' . $percentage . '%</span>';
        $html .= '</div>';
        $html .= '<div class="progress" style="height: 10px;">';
        $html .= '<div class="progress-bar bg-success" style="width: ' . $percentage . '%"></div>';
        $html .= '</div>';
        $html .= '<div class="mt-2">';
        $html .= '<button class="btn btn-sm btn-outline-primary vote-btn" onclick="votePoll(' . $poll->id . ', ' . $option['id'] . ')">';
        $html .= '<i class="fas fa-vote-yea"></i> Vote</button>';
        $html .= '<span class="ms-2 small text-muted">' . $option['votes'] . ' votes</span>';
        $html .= '</div>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    $html .= '<div class="mt-3 text-muted small">Total votes: ' . $totalVotes . '</div>';
    $html .= '</div>';
    
    return $html;
}


/**
 * Admin: Activate poll
 */
public function activatePoll(Request $request)
{
    try {
        $request->validate([
            'poll_id' => 'required|exists:polls,id'
        ]);
        
        // Deactivate all other polls first
        Poll::where('is_active', true)->update(['is_active' => false]);
        
        // Activate the selected poll
        $poll = Poll::findOrFail($request->poll_id);
        $poll->is_active = true;
        $poll->save();
        
        // Get poll data for broadcasting
        $pollData = $this->getActivePoll();
        
        // Broadcast to all connected clients
        broadcast(new PollStatusChanged($pollData));
        
        return response()->json([
            'success' => true,
            'message' => 'Poll activated successfully',
            'poll' => $pollData
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error activating poll: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => 'Failed to activate poll'
        ], 500);
    }
}

/**
 * Admin: Deactivate poll
 */
public function deactivatePoll(Request $request)
{
    try {
        // Deactivate all active polls
        Poll::where('is_active', true)->update(['is_active' => false]);
        
        // Broadcast to all connected clients
        broadcast(new PollStatusChanged(null));
        
        return response()->json([
            'success' => true,
            'message' => 'Poll deactivated successfully'
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error deactivating poll: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => 'Failed to deactivate poll'
        ], 500);
    }
}




   

    /**
 * Store user reactions (love, like, applause)
 * Each user can submit each reaction type only once
 */
public function storeReaction(Request $request)
{
    try {
        $request->validate([
            'reaction' => 'required|in:love,like,applause'
        ]);

        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Check if user already submitted THIS SPECIFIC reaction type
        $existingReaction = Reaction::where('user_id', $user->id)
            ->where('reaction_type', $request->reaction)
            ->exists();

        if ($existingReaction) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted this reaction',
                'already_submitted' => true
            ], 429);
        }

        // Store the reaction
        $reaction = Reaction::create([
            'user_id' => $user->id,
            'reaction_type' => $request->reaction,
            'session_id' => session()->getId(),
            'ip_address' => $request->ip()
        ]);

        Log::info('Reaction stored', [
            'user_id' => $user->id,
            'reaction_type' => $request->reaction,
            'reaction_id' => $reaction->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reaction recorded successfully',
            'reaction_type' => $request->reaction
        ]);

    } catch (\Exception $e) {
        Log::error('Error storing reaction: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => 'Failed to store reaction'
        ], 500);
    }
}

    /**
 * Track user login when they authenticate
 */
public function trackLogin(Request $request)
{
    try {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Check if there's an active session without logout
        $activeSession = LoginDetails::where('user_id', $user->id)
            ->whereNull('logout_time')
            ->first();

        // If no active session, create new login record
        if (!$activeSession) {
            LoginDetails::create([
                'user_id' => $user->id,
                'login_time' => now(),
                'logout_time' => null
            ]);
            
            Log::info('User login tracked', [
                'user_id' => $user->id,
                'login_time' => now()
            ]);
        }

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        Log::error('Error tracking login: ' . $e->getMessage());
        return response()->json(['success' => false], 500);
    }
}

/**
 * Track user activity and update logout time if inactive for 5+ minutes
 */
public function trackActivity(Request $request)
{
    try {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Update user's last seen and online status
        $user->update([
            'last_seen_at' => now(),
            'is_online' => true
        ]);

        // Get active login session
        $activeSession = LoginDetails::where('user_id', $user->id)
            ->whereNull('logout_time')
            ->first();

        // If no active session, create one
        if (!$activeSession) {
            LoginDetails::create([
                'user_id' => $user->id,
                'login_time' => now(),
                'logout_time' => null
            ]);
        } else {
            // Check if last activity was more than 5 minutes ago
            // If yes, this is a new session, so close old and create new
            if ($activeSession->login_time < now()->subMinutes(5)) {
                $activeSession->update([
                    'logout_time' => $activeSession->login_time
                ]);
                
                // Create new session
                LoginDetails::create([
                    'user_id' => $user->id,
                    'login_time' => now(),
                    'logout_time' => null
                ]);
            }
        }

        // Clean up old offline status (users who haven't pinged in 5+ minutes)
        $inactiveUsers = User::where('last_seen_at', '<', now()->subMinutes(5))
            ->update(['is_online' => false]);

        // Close sessions for users who haven't had activity in 5+ minutes
        $inactiveSessions = LoginDetails::whereNull('logout_time')
            ->where('login_time', '<', now()->subMinutes(5))
            ->get();

        foreach ($inactiveSessions as $session) {
            $lastActivity = User::where('id', $session->user_id)->value('last_seen_at');
            if (!$lastActivity || $lastActivity < now()->subMinutes(5)) {
                $session->update([
                    'logout_time' => $session->login_time
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'user_id' => $user->id,
            'last_seen' => $user->last_seen_at,
            'is_online' => true,
            'timestamp' => now()->toIso8601String()
        ]);

    } catch (\Exception $e) {
        Log::error('Error tracking activity: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => 'Failed to track activity'
        ], 500);
    }
}

/**
 * Track user logout (call this when user explicitly logs out)
 */
public function trackLogout(Request $request)
{
    try {
        $user = Auth::user();
        
        if ($user) {
            // Update active session with logout time
            $activeSession = LoginDetails::where('user_id', $user->id)
                ->whereNull('logout_time')
                ->first();

            if ($activeSession) {
                $activeSession->update([
                    'logout_time' => now()
                ]);
            }

            // Update user status
            $user->update([
                'is_online' => false
            ]);

            Log::info('User logout tracked', [
                'user_id' => $user->id,
                'logout_time' => now()
            ]);
        }

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        Log::error('Error tracking logout: ' . $e->getMessage());
        return response()->json(['success' => false], 500);
    }
}


    /**
     * Helper method to track user activity from within other methods
     */
    private function trackUserActivity($user)
    {
        if ($user) {
            $user->update([
                'last_seen_at' => now(),
                'is_online' => true
            ]);
        }
    }

    

  
}
