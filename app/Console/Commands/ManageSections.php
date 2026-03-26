<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Section;

class ManageSections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manage:sections 
        {action=list : Action to perform (list, assign, check)}
        {--user= : User email to manage sections for}
        {--sections= : Comma-separated section IDs to assign}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage user sections (assign/check sections for supervisors and managers)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        switch ($action) {
            case 'list':
                return $this->listSections();
            case 'assign':
                return $this->assignSections();
            case 'check':
                return $this->checkUserSections();
            default:
                $this->error("Invalid action. Use: list, assign, or check");
                return 1;
        }
    }
    
    /**
     * List all sections
     */
    protected function listSections()
    {
        $sections = Section::all();
        
        $this->info("📋 Available Sections:");
        $this->newLine();
        
        $data = $sections->map(function($section) {
            return [
                'ID' => $section->id,
                'Name' => $section->name,
                'Users' => $section->users()->count(),
            ];
        });
        
        $this->table(['ID', 'Name', 'Users'], $data);
        
        return 0;
    }
    
    /**
     * Assign sections to user
     */
    protected function assignSections()
    {
        $userEmail = $this->option('user') ?? $this->ask('Enter user email');
        
        $user = User::where('email', $userEmail)->first();
        
        if (!$user) {
            $this->error("❌ User with email {$userEmail} not found");
            return 1;
        }
        
        $roleName = $user->role ? $user->role->name : 'No role';
        $this->info("User: {$user->name} ({$roleName})");
        $this->newLine();
        
        // Show available sections
        $sections = Section::all();
        $currentSections = $user->sections->pluck('id')->toArray();
        
        $this->info("Available Sections:");
        foreach ($sections as $section) {
            $assigned = in_array($section->id, $currentSections) ? ' ✅' : '';
            $this->line("  {$section->id}. {$section->name}{$assigned}");
        }
        $this->newLine();
        
        // Get sections to assign
        $sectionInput = $this->option('sections') ?? $this->ask('Enter section IDs (comma-separated) or "all"');
        
        if (strtolower(trim($sectionInput)) === 'all') {
            $selectedSections = $sections->pluck('id')->toArray();
        } else {
            $selectedSections = array_map('trim', explode(',', $sectionInput));
        }
        
        // Sync sections
        $user->sections()->sync($selectedSections);
        
        $sectionNames = Section::whereIn('id', $selectedSections)->pluck('name')->toArray();
        
        $this->info("✅ Sections assigned successfully!");
        $this->info("Assigned sections: " . implode(', ', $sectionNames));
        
        return 0;
    }
    
    /**
     * Check user sections
     */
    protected function checkUserSections()
    {
        $userEmail = $this->option('user');
        
        if ($userEmail) {
            // Check specific user
            $user = User::where('email', $userEmail)->first();
            
            if (!$user) {
                $this->error("❌ User not found");
                return 1;
            }
            
            $this->showUserSections($user);
        } else {
            // Check all supervisors and managers
            $supervisors = User::whereHas('role', function($q) {
                $q->where('name', 'Supervisor');
            })->with(['sections', 'role'])->get();
            
            $managers = User::whereHas('role', function($q) {
                $q->where('name', 'Manager');
            })->with(['sections', 'role'])->get();
            
            if ($supervisors->count() > 0) {
                $this->info("👨‍💼 SUPERVISORS:");
                $this->newLine();
                foreach ($supervisors as $user) {
                    $this->showUserSections($user);
                    $this->newLine();
                }
            }
            
            if ($managers->count() > 0) {
                $this->info("👔 MANAGERS:");
                $this->newLine();
                foreach ($managers as $user) {
                    $this->showUserSections($user);
                    $this->newLine();
                }
            }
        }
        
        return 0;
    }
    
    /**
     * Show user's assigned sections
     */
    protected function showUserSections($user)
    {
        $roleName = $user->role ? $user->role->name : 'No role';
        
        $this->line("📧 {$user->name} ({$user->email})");
        $this->line("   Role: {$roleName}");
        
        $sections = $user->sections;
        
        if ($sections->count() > 0) {
            $this->line("   Sections: " . $sections->pluck('name')->implode(', '));
        } else {
            $this->warn("   ⚠️  No sections assigned");
        }
    }
}
