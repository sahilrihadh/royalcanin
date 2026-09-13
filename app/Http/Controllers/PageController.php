<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Question;
use App\Models\Poll;
use App\Models\PollVote;
use App\Models\PollOption;
use App\Models\Reaction;
use App\Models\PreviousSession;
use App\Models\LoginDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\CertificateMail;
use App\Events\PollStatusChanged;

class PageController extends Controller
{
    // ==================== PAGES ====================

    public function webcast()
    {
        return view('pages.webcast', ['user' => Auth::user()]);
    }

    public function player()
    {
        return view('pages.player');
    }

    public function previousSessions()
    {
        $faqs = [
            [
                'question' => 'What is the drug of choice for pancreatitis?',
                'answer' => '<p>There are no universally recommended drugs except <strong>Fuzapladib sodium monohydrate</strong>, currently licensed in Japan and conditionally approved in the USA.</p><p>Most cases are managed symptomatically for abdominal pain, hypovolemia/shock, gastrointestinal signs, dietary therapy, and monitoring for complications.</p><p class="mb-0">Therapy should be individualized and customized for each case.</p>'
            ],
            [
                'question' => 'Can you explain the cytokine storm in acute pancreatitis?',
                'answer' => '<p>The inflammatory response in acute pancreatitis (AP) is complex and may be localized or systemic, determining disease severity and progression.</p><p>Release of pancreatic enzymes leads to neutrophilic inflammation, production of reactive oxygen species, nitric oxide, cytokines, and activation of multiple inflammatory pathways.</p><p>Key mechanisms include:</p><ul><li>Trypsinogen activation</li><li>Activation of nuclear factor kappa B (NF-κB)</li><li>Release of inflammatory mediators</li><li>Interleukin-6 (IL-6) production</li><li>Neutrophil invasion</li></ul><p class="mb-0">Refer to <strong>Cridge et al</strong> and <strong>Mansfield et al (JVIM)</strong> for detailed review.</p>'
            ],
            [
                'question' => 'Can we use SNAP cPL of dogs for cats if fPL is unavailable?',
                'answer' => '<p><strong>Unlikely and not validated.</strong></p><p class="mb-0">The feline pancreatic lipase immunoreactivity (fPLI) assay is one of the most sensitive and specific tests for feline pancreatitis.</p>'
            ],
            [
                'question' => 'What are the limitations of ultrasonography in early pancreatitis?',
                'answer' => '<p class="mb-0">The pancreas may appear normal in the early stages before morphological ultrasonographic changes develop. Monitor closely if pancreatitis is strongly suspected, especially with elevated cPL values.</p>'
            ],
            [
                'question' => 'Is there any benefit of adding oral pancreatic enzymes in pancreatitis?',
                'answer' => '<p class="mb-0"><strong>Unlikely.</strong> Additional oral pancreatic enzymes generally do not provide benefit unless the patient develops exocrine pancreatic insufficiency (EPI).</p>'
            ],
            [
                'question' => 'Is a low-fat GI diet suitable for EPI too?',
                'answer' => '<p class="mb-0"><strong>Yes.</strong> A low-fat gastrointestinal diet can also be suitable for EPI patients.</p>'
            ],
            [
                'question' => 'Dog is epileptic and on Gardenal. Should treatment continue during acute pancreatitis?',
                'answer' => '<p>Phenobarbitone\'s direct role in causing acute pancreatitis is not yet proven and may instead be associated with obesity or polyphagia.</p><p class="mb-0">Alternatives like <strong>levetiracetam</strong> may be considered until pancreatitis resolves, but generally <strong>Gardenal is not stopped abruptly.</strong></p>'
            ],
            [
                'question' => 'How can we differentiate GERD from pancreatitis in dogs?',
                'answer' => '<ul><li>GERD is less common in dogs compared to humans</li><li>GERD patients generally do not appear severely ill</li><li>Regurgitation is more common than vomiting</li><li>Systemic signs are uncommon in GERD</li><li>Blood work is usually normal unless chronic disease exists</li></ul><p class="mb-0">Acute pancreatitis patients usually appear significantly sicker.</p>'
            ],
            [
                'question' => 'Is NAC good to give IV for acute pancreatitis?',
                'answer' => '<p class="mb-0">Not validated, but generally considered safe if clinically indicated.</p>'
            ],
            [
                'question' => 'Can freeze-dried raw pancreas and cobalamin be used for AP?',
                'answer' => '<p>Excellent results are reported for <strong>EPI management</strong>.</p><p class="mb-0">However, this approach is generally <strong>not recommended for acute pancreatitis (AP).</strong></p>'
            ],
            [
                'question' => 'Management approach for epilepsy patients on high-fat diets who develop pancreatitis?',
                'answer' => '<p>Acute pancreatitis should be managed first until recovery.</p><p class="mb-0">High-fat diets are not always essential for epilepsy management. Alternatives such as MCT oil or coconut oil may be considered.</p>'
            ],
            [
                'question' => 'How do you manage babesiosis-induced acute pancreatitis?',
                'answer' => '<p>Treat babesiosis with recommended specific therapy while simultaneously managing acute pancreatitis symptomatically.</p><p class="mb-0">Control inflammation, shock, pain, and dehydration. Drug conflicts affecting the pancreas are considered unlikely.</p>'
            ],
            [
                'question' => 'What is the most underestimated mechanism driving morbidity in pancreatitis?',
                'answer' => '<p>Traditional theories focus on trypsin activation, while newer concepts emphasize cytokine storm pathways.</p><p>No single chemokine or cytokine appears solely responsible. It is a cascade of inflammatory events triggered by NF-κB and related mediators.</p><p class="mb-0">Refer to <strong>Cridge et al</strong> and <strong>Mansfield et al (JVIM)</strong> for detailed review.</p>'
            ],
        ];

        return view('pages.previous-sessions', compact('faqs'));
    }

    // ==================== POLL ====================

    public function checkPoll(Request $request)
    {
        try {
            $activePoll = Poll::where('is_active', true)->with('options')->first();

            if (!$activePoll) {
                return response()->json('NO_POLL_ACTIVE');
            }

            $user = Auth::user();
            $totalVotes = $activePoll->options()->sum('vote_count');

            $html = '<div class="mb-4">';
            $html .= '<h4 class="text-danger poll-title">' . e($activePoll->question) . '</h4>';

            if (!$user) {
                $html .= '<div class="alert alert-warning">Please login to vote in this poll.</div>';
                $html .= '</div>';
                return response()->json(['has_poll' => true, 'poll' => ['id' => $activePoll->id, 'html' => $html]]);
            }

            $userVote = PollVote::where('poll_id', $activePoll->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$userVote) {
                // Show voting form
                $html .= '<form id="poll-form" method="post">';
                $html .= '<input type="hidden" name="poll_id" value="' . $activePoll->id . '">';
                foreach ($activePoll->options as $option) {
                    $html .= '<div class="form-check mb-2">';
                    $html .= '<label class="form-check-label">';
                    $html .= '<input type="radio" class="form-check-input" name="poll" value="' . $option->id . '">';
                    $html .= e($option->option_text);
                    $html .= '<i class="input-helper"></i></label></div>';
                }
                $html .= '<button type="submit" class="btn btn-canin mt-4" id="but_vote">Vote</button>';
                $html .= '</form>';
            } else {
                // Show results
                if ($userVote->is_correct) {
                    $html .= '<div class="alert alert-success mb-3"><i class="fas fa-check-circle"></i> Congratulations! You selected the correct answer!</div>';
                } else {
                    $correctOption = $activePoll->options->where('is_correct', true)->first();
                    $html .= '<div class="alert alert-info mb-3"><i class="fas fa-info-circle"></i> Your answer was incorrect. The correct answer is: <strong>' . e($correctOption?->option_text ?? 'N/A') . '</strong></div>';
                }

                $html .= '<div class="poll-results"><p class="text-muted mb-3">Poll Results:</p>';
                $colors = ['#6993ff', '#008080', '#e3242b', '#ffbd59', '#050357'];

                foreach ($activePoll->options as $i => $option) {
                    $percentage = $totalVotes > 0 ? round(($option->vote_count / $totalVotes) * 100) : 0;
                    $correctBadge = $option->is_correct ? ' <span class="badge bg-success ms-2"><i class="fas fa-check"></i> Correct Answer</span>' : '';
                    $yourBadge = $userVote->poll_option_id == $option->id ? ' <span class="badge bg-primary ms-2"><i class="fas fa-user-check"></i> Your Answer</span>' : '';

                    $html .= '<div class="mb-3">';
                    $html .= '<div class="d-flex justify-content-between mb-1"><strong>' . e($option->option_text) . $correctBadge . $yourBadge . '</strong><span>' . $percentage . '%</span></div>';
                    $html .= '<div class="progress" style="height:30px;">';
                    $html .= '<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width:' . $percentage . '%;background-color:' . $colors[$i % count($colors)] . ';" aria-valuenow="' . $percentage . '" aria-valuemin="0" aria-valuemax="100">' . $option->vote_count . ' votes</div>';
                    $html .= '</div></div>';
                }

                $html .= '<div class="mt-3 pt-2 border-top">';
                $html .= '<div class="text-muted">Total votes: ' . $totalVotes . '</div>';
                $html .= '<div class="text-muted small mt-1"><i class="fas fa-chart-bar"></i> You have already voted in this poll.</div>';
                $html .= '</div></div>';
            }

            $html .= '</div>';

            return response()->json(['has_poll' => true, 'poll' => ['id' => $activePoll->id, 'html' => $html]]);

        } catch (\Exception $e) {
            Log::error('checkPoll error: ' . $e->getMessage());
            return response()->json('NO_POLL_ACTIVE');
        }
    }

    public function submitVote(Request $request)
    {
        try {
            $option = PollOption::with('poll')->find($request->poll ?? $request->option_id);

            if (!$option) {
                return response()->json(['success' => false, 'message' => 'Invalid option']);
            }

            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Please login to vote']);
            }

            $alreadyVoted = PollVote::where('poll_id', $option->poll_id)
                ->where('user_id', $user->id)
                ->exists();

            if ($alreadyVoted) {
                return response()->json(['success' => false, 'message' => 'You have already voted in this poll']);
            }

            $isCorrect = (bool) $option->is_correct;
            $option->increment('vote_count');

            PollVote::create([
                'poll_id'        => $option->poll_id,
                'poll_option_id' => $option->id,
                'user_id'        => $user->id,
                'is_correct'     => $isCorrect,
            ]);

            Log::info('Vote recorded', ['user_id' => $user->id, 'poll_id' => $option->poll_id, 'option_id' => $option->id]);

            return response()->json(['success' => true, 'message' => 'Vote submitted successfully!', 'is_correct' => $isCorrect]);

        } catch (\Exception $e) {
            Log::error('submitVote error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error submitting vote']);
        }
    }

    public function activatePoll(Request $request)
    {
        try {
            $request->validate(['poll_id' => 'required|exists:polls,id']);

            Poll::where('is_active', true)->update(['is_active' => false]);

            $poll = Poll::findOrFail($request->poll_id);
            $poll->update(['is_active' => true]);

            $pollData = $this->getActivePoll();
            broadcast(new PollStatusChanged($pollData));

            return response()->json(['success' => true, 'message' => 'Poll activated', 'poll' => $pollData]);

        } catch (\Exception $e) {
            Log::error('activatePoll error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to activate poll'], 500);
        }
    }

    public function deactivatePoll()
    {
        try {
            Poll::where('is_active', true)->update(['is_active' => false]);
            broadcast(new PollStatusChanged(null));
            return response()->json(['success' => true, 'message' => 'Poll deactivated']);
        } catch (\Exception $e) {
            Log::error('deactivatePoll error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to deactivate poll'], 500);
        }
    }

    private function getActivePoll()
    {
        $poll = Poll::where('is_active', true)->with('options')->first();
        if (!$poll) return null;

        $options = $poll->options->map(fn($o) => [
            'id'    => $o->id,
            'text'  => $o->option_text,
            'votes' => $o->vote_count ?? 0,
        ])->toArray();

        return [
            'id'          => $poll->id,
            'question'    => $poll->question,
            'options'     => $options,
            'total_votes' => $poll->votes()->count(),
            'html'        => $this->getPollHtml($poll, $options),
        ];
    }

    private function getPollHtml($poll, $options)
    {
        $totalVotes = $poll->votes()->count();
        $html  = '<div class="poll-container p-4 border rounded bg-white">';
        $html .= '<h4 class="mb-3 fw-bold">' . e($poll->question) . '</h4><div class="poll-options">';

        foreach ($options as $option) {
            $percentage = $totalVotes > 0 ? round(($option['votes'] / $totalVotes) * 100) : 0;
            $html .= '<div class="poll-option mb-3" data-option-id="' . $option['id'] . '">';
            $html .= '<div class="d-flex justify-content-between align-items-center mb-1">';
            $html .= '<span class="fw-medium">' . e($option['text']) . '</span>';
            $html .= '<span class="badge bg-primary rounded-pill">' . $percentage . '%</span></div>';
            $html .= '<div class="progress" style="height:10px;">';
            $html .= '<div class="progress-bar bg-success" style="width:' . $percentage . '%"></div></div>';
            $html .= '<div class="mt-2"><button class="btn btn-sm btn-outline-primary vote-btn" onclick="votePoll(' . $poll->id . ',' . $option['id'] . ')"><i class="fas fa-vote-yea"></i> Vote</button>';
            $html .= '<span class="ms-2 small text-muted">' . $option['votes'] . ' votes</span></div></div>';
        }

        $html .= '</div><div class="mt-3 text-muted small">Total votes: ' . $totalVotes . '</div></div>';
        return $html;
    }

    // ==================== QUESTIONS ====================

    public function submitQuestion(Request $request)
    {
        try {
            $request->validate(['question_input' => 'required|string|min:5|max:1000']);

            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'code' => 0, 'message' => 'Please login to submit a question.']);
            }

            $recentCount = Question::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subHour())
                ->count();

            if ($recentCount >= 5) {
                return response()->json(['success' => false, 'code' => 3, 'message' => 'You have asked too many questions. Please wait before asking more.']);
            }

            $question = Question::create([
                'user_id'       => $user->id,
                'question_text' => $request->question_input,
                'asked_at'      => now(),
                'is_answered'   => false,
                'answer_text'   => null,
            ]);

            Log::info('Question submitted', ['user_id' => $user->id, 'question_id' => $question->id]);

            return response()->json([
                'success' => true,
                'code'    => 1,
                'message' => 'Question submitted successfully!',
                'question' => [
                    'id'         => $question->id,
                    'text'       => $question->question_text,
                    'created_at' => $question->created_at->diffForHumans(),
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'code' => 0, 'message' => 'Validation failed: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('submitQuestion error: ' . $e->getMessage());
            return response()->json(['success' => false, 'code' => 0, 'message' => 'Failed to submit question. Please try again.']);
        }
    }

    public function getQuestions()
    {
        try {
            $questions = Question::with('user')->orderBy('created_at', 'desc')->limit(50)->get();

            if ($questions->isEmpty()) {
                return '<div class="text-center text-muted p-4">No questions asked yet.</div>';
            }

            $html = '';
            foreach ($questions as $question) {
                $userName     = $question->user?->full_name ?? $question->user?->name ?? 'Anonymous';
                $questionText = htmlspecialchars($question->question_text ?? $question->question_input ?? '');
                $askedAt      = $question->created_at->diffForHumans();

                $html .= '<div class="question-item mb-3"><div class="message-bubble"><div class="question-box">';
                $html .= '<div class="message-header"><strong><i class="fas fa-user"></i> ' . $userName . '</strong>';
                $html .= '<span class="message-time"><i class="far fa-clock"></i> ' . $askedAt . '</span></div>';
                $html .= '<div class="message-text">' . nl2br($questionText) . '</div></div>';

                if ($question->is_answered && $question->answer_text) {
                    $answeredAt = $question->answered_at?->diffForHumans() ?? $askedAt;
                    $html .= '<div class="answer-box mt-2 pt-2"><div class="answer-label">';
                    $html .= '<i class="fas fa-reply-all text-success"></i>';
                    $html .= '<strong class="text-success">Answer:</strong>';
                    $html .= '<span class="message-time ms-2">' . $answeredAt . '</span></div>';
                    $html .= '<div class="answer-text">' . nl2br(htmlspecialchars($question->answer_text)) . '</div></div>';
                }

                $html .= '</div></div>';
            }

            return $html;

        } catch (\Exception $e) {
            Log::error('getQuestions error: ' . $e->getMessage());
            return '<div class="text-center text-danger p-4">Error loading questions. Please refresh.</div>';
        }
    }

    // ==================== REACTIONS ====================

    public function storeReaction(Request $request)
    {
        try {
            $request->validate(['reaction' => 'required|in:love,like,applause']);

            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $already = Reaction::where('user_id', $user->id)
                ->where('reaction_type', $request->reaction)
                ->exists();

            if ($already) {
                return response()->json(['success' => false, 'message' => 'Already submitted', 'already_submitted' => true], 429);
            }

            Reaction::create([
                'user_id'       => $user->id,
                'reaction_type' => $request->reaction,
                'session_id'    => session()->getId(),
                'ip_address'    => $request->ip(),
            ]);

            return response()->json(['success' => true, 'reaction_type' => $request->reaction]);

        } catch (\Exception $e) {
            Log::error('storeReaction error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to store reaction'], 500);
        }
    }

    // ==================== ACTIVITY TRACKING ====================

    public function trackLogin()
    {
        try {
            $user = Auth::user();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $hasActive = LoginDetails::where('user_id', $user->id)->whereNull('logout_time')->exists();
            if (!$hasActive) {
                LoginDetails::create(['user_id' => $user->id, 'login_time' => now()]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('trackLogin error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    public function trackActivity()
    {
        try {
            $user = Auth::user();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $user->update(['last_seen_at' => now(), 'is_online' => true]);

            $session = LoginDetails::where('user_id', $user->id)->whereNull('logout_time')->first();

            if (!$session) {
                LoginDetails::create(['user_id' => $user->id, 'login_time' => now()]);
            } elseif ($session->login_time < now()->subMinutes(5)) {
                $session->update(['logout_time' => $session->login_time]);
                LoginDetails::create(['user_id' => $user->id, 'login_time' => now()]);
            }

            // Mark inactive users offline
            User::where('last_seen_at', '<', now()->subMinutes(5))->update(['is_online' => false]);

            // Close stale sessions
            LoginDetails::whereNull('logout_time')
                ->where('login_time', '<', now()->subMinutes(5))
                ->whereDoesntHave('user', fn($q) => $q->where('last_seen_at', '>=', now()->subMinutes(5)))
                ->update(['logout_time' => now()]);

            return response()->json(['success' => true, 'timestamp' => now()->toIso8601String()]);

        } catch (\Exception $e) {
            Log::error('trackActivity error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    public function trackLogout()
    {
        try {
            $user = Auth::user();
            if ($user) {
                LoginDetails::where('user_id', $user->id)->whereNull('logout_time')->update(['logout_time' => now()]);
                $user->update(['is_online' => false]);
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('trackLogout error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    // ==================== CERTIFICATES ====================

    public function sendCertificate(Request $request)
    {
        $request->validate([
            'email'     => 'required|email',
            'fullName'  => 'required|string',
            'webinarId' => 'required|string',
        ]);

        $webinarConfig = [
            'webinar1' => ['event_date' => '27th May 2026',      'template' => 'assets/img/Certificate1.png'],
            'webinar2' => ['event_date' => '26th June 2026', 'template' => 'assets/img/Certificate_26_June.png'],
            'webinar3' => ['event_date' => '22nd July 2026', 'template' => 'assets/img/Certificate_22_July.png'],
            'webinar4' => ['event_date' => '19th August 2026',  'template' => 'assets/img/Certificate_19_August.png'],
            'webinar5' => ['event_date' => '23rd September 2026', 'template' => 'assets/img/Certificate_23_September.png'],
            'webinar6' => ['event_date' => '21st October 2026', 'template' => 'assets/img/Certificate_21_October.png'],   
        ];

        if (!isset($webinarConfig[$request->webinarId])) {
            return response()->json(['success' => false, 'message' => 'Invalid webinar ID'], 400);
        }

        $config   = $webinarConfig[$request->webinarId];
        $existing = PreviousSession::where('email_id', $request->email)->where('session_name', $request->webinarId)->first();

        if ($existing?->certificate_status == 1) {
            return response()->json(['success' => false, 'message' => 'Certificate already sent', 'already_sent' => true]);
        }

        $certificatePath = $this->generateCertificateImage($request->fullName, $config['template']);
        if (!$certificatePath) {
            return response()->json(['success' => false, 'message' => 'Failed to generate certificate'], 500);
        }

        if ($existing) {
            $existing->update(['certificate_status' => 1, 'certificate_path' => $certificatePath, 'count' => $existing->count + 1]);
        } else {
            PreviousSession::create([
                'name'                => $request->fullName,
                'email_id'            => $request->email,
                'session_name'        => $request->webinarId,
                'watched_on'          => now(),
                'certificate_status'  => 1,
                'certificate_path'    => $certificatePath,
                'count'               => 1,
            ]);
        }

        try {
            Mail::to($request->email)->send(new CertificateMail($request->fullName, $config['event_date'], $certificatePath));
            return response()->json(['success' => true, 'message' => 'Certificate sent to ' . $request->email]);
        } catch (\Exception $e) {
            Log::error('Certificate email failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()], 500);
        }
    }

    private function generateCertificateImage(string $fullName, string $templatePath): string|false
    {
        try {
            $fullTemplatePath = public_path($templatePath);

            if (!file_exists($fullTemplatePath)) {
                Log::error("Certificate template not found: {$templatePath}");
                return false;
            }

            $image = imagecreatefrompng($fullTemplatePath);
            if (!$image) {
                Log::error("Failed to load template: {$templatePath}");
                return false;
            }

            $fontFile = $this->getAvailableFont();
            if (!$fontFile) {
                Log::error('No font available for certificate generation');
                imagedestroy($image);
                return false;
            }

            $color  = imagecolorallocate($image, 0, 0, 0);
            $result = imagettftext($image, 46, 0, 900, 850, $color, $fontFile, $fullName);

            if (!$result) {
                Log::error('Failed to write text on certificate');
                imagedestroy($image);
                return false;
            }

            $dir = storage_path('app/public/certificates');
            if (!file_exists($dir)) mkdir($dir, 0777, true);

            $fileName = time() . '_' . rand(1000, 9999) . '.png';
            $saved    = imagepng($image, $dir . '/' . $fileName);
            imagedestroy($image);

            if (!$saved) {
                Log::error("Failed to save certificate: {$fileName}");
                return false;
            }

            Log::info("Certificate generated for: {$fullName}");
            return 'certificates/' . $fileName;

        } catch (\Exception $e) {
            Log::error('generateCertificateImage error: ' . $e->getMessage());
            return false;
        }
    }

    private function getAvailableFont(): string|false
    {
        $fonts = [
            public_path('assets/fonts/D-DIN-PRO-500-Medium.otf'),
            public_path('assets/fonts/D-DIN-PRO-Medium.otf'),
            public_path('assets/fonts/D-DIN-PRO-Bold.otf'),
            public_path('assets/fonts/arial.ttf'),
            public_path('assets/fonts/Roboto-Regular.ttf'),
        ];

        foreach ($fonts as $font) {
            if (file_exists($font)) return $font;
        }

        return false;
    }
}