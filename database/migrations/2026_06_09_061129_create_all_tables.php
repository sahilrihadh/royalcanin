<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ==================== USERS & AUTH TABLES ====================
        
        // Users table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('mobile_number')->unique();
            $table->string('email_id')->unique();
            $table->string('clinic_name')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('city_pincode')->nullable();
            $table->boolean('terms_accepted')->default(false);
            $table->boolean('sale_consent')->default(false);
            $table->boolean('research_consent')->default(false);
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamp('last_seen_at')->nullable();
            $table->boolean('is_online')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Admins table
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('full_name')->nullable();
            $table->enum('user_role', ['admin', 'client'])->default('client');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        // Password reset tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Sessions table
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->foreignId('admin_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // ==================== CACHE & QUEUE TABLES ====================
        
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedSmallInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('connection');
            $table->string('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // ==================== WEBINAR & QUESTIONS TABLES ====================
        
        // Webinar sessions
        Schema::create('webinar_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Questions table
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('webinar_session_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->timestamp('asked_at')->useCurrent();
            $table->boolean('is_answered')->default(false);
            $table->text('answer_text')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();
        });

        // ==================== CERTIFICATES TABLES ====================
        
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('webinar_session_id')->constrained()->onDelete('cascade');
            $table->string('certificate_code')->unique();
            $table->timestamp('issued_at')->useCurrent();
            $table->string('file_path')->nullable();
            $table->timestamps();
        });

        // ==================== POLLS TABLES ====================
        
        // Polls table (without webinar_session_id)
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->boolean('is_active')->default(true);
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
        });

        // Poll options table with is_correct field
        Schema::create('poll_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->onDelete('cascade');
            $table->string('option_text');
            $table->boolean('is_correct')->default(false);
            $table->integer('vote_count')->default(0);
            $table->timestamps();
        });

        // Poll votes table with is_correct field
        Schema::create('poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('poll_option_id')->constrained()->onDelete('cascade');
            $table->boolean('is_correct')->default(false);
            $table->timestamp('voted_at')->useCurrent();
            $table->unique(['poll_id', 'user_id']);
            $table->timestamps();
        });

        // ==================== REACTIONS TABLE ====================
        
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reaction_type'); // love, like, applause
            $table->string('session_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('reaction_type');
        });

        // ==================== LOGIN DETAILS TABLE ====================
        
        Schema::create('login_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('login_time')->nullable();
            $table->timestamp('logout_time')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'login_time']);
        });

        // ==================== PREVIOUS SESSIONS TABLE ====================
        
        Schema::create('previous_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email_id');
            $table->string('session_name');
            $table->timestamp('watched_on')->nullable();
            $table->boolean('certificate_status')->default(false);
            $table->string('certificate_path')->nullable();
            $table->integer('count')->default(1);
            $table->timestamps();
            
            $table->index(['email_id', 'session_name']);
        });

        // ==================== ANNOUNCEMENTS TABLE ====================
        
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['show', 'hide'])->default('hide');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order to avoid foreign key constraints
        
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('previous_sessions');
        Schema::dropIfExists('login_details');
        Schema::dropIfExists('reactions');
        Schema::dropIfExists('poll_votes');
        Schema::dropIfExists('poll_options');
        Schema::dropIfExists('polls');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('webinar_sessions');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('users');
    }
};