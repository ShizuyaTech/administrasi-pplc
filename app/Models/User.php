<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'employee_id',
        'name',
        'email',
        'password',
        'role_id',
        'section_id',
        'shift',
        'signature_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Many-to-Many: Sections that Supervisor/Manager can manage
     */
    public function sections()
    {
        return $this->belongsToMany(Section::class, 'section_user')
                    ->withTimestamps();
    }

    // Helper methods
    public function isSuperAdmin()
    {
        // Deprecated: Use permission-based checks instead
        return $this->hasPermission('manage-all-sections') || 
               ($this->role && $this->role->permissions()->count() >= 40); // Super admin has most permissions
    }

    public function hasPermission($permissionSlug)
    {
        return $this->role && $this->role->hasPermission($permissionSlug);
    }

    public function canAccessSection($sectionId)
    {
        // Super admin can access all sections
        if ($this->canManageAllSections()) {
            return true;
        }
        
        // Check primary section (from employee)
        if ($this->section_id == $sectionId) {
            return true;
        }
        
        // Check if user has this section in their managed sections (for Supervisor/Manager)
        if ($this->isSupervisor() || $this->isManager()) {
            return $this->sections()->where('sections.id', $sectionId)->exists();
        }
        
        return false;
    }

    /**
     * Check if user can manage all sections (like Super Admin)
     */
    public function canManageAllSections()
    {
        return $this->hasPermission('manage-all-sections') || 
               $this->hasPermission('view-users'); // Users with user management typically can see all sections
    }

    /**
     * Check if user can approve overtimes
     */
    public function canApproveOvertimes()
    {
        return $this->hasPermission('approve-overtime-supervisor') || 
               $this->hasPermission('approve-overtime-manager') ||
               $this->hasPermission('manage-overtimes');
    }

    /**
     * Check if user can approve business trips
     */
    public function canApproveBusinessTrips()
    {
        return $this->hasPermission('approve-business-trip') || 
               $this->hasPermission('manage-business-trips');
    }

    /**
     * Check if user can manage users (for user management pages)
     */
    public function canManageUsers()
    {
        return $this->hasPermission('view-users') || 
               $this->hasPermission('create-user') || 
               $this->hasPermission('edit-user');
    }

    /**
     * Check if user can manage roles and permissions
     */
    public function canManageRoles()
    {
        return $this->hasPermission('view-roles') || 
               $this->hasPermission('manage-permissions');
    }

    /**
     * Check if user is a driver
     */
    public function isDriver()
    {
        return $this->role && stripos($this->role->name, 'driver') !== false;
    }

    /**
     * Check if user is a leader or foreman (can approve)
     */
    public function isLeaderOrForeman()
    {
        if (!$this->role) return false;
        
        $roleName = strtolower($this->role->name);
        return stripos($roleName, 'leader') !== false || 
               stripos($roleName, 'foreman') !== false ||
               str_contains($roleName, 'kepala') ||
               str_contains($roleName, 'mandor');
    }

    /**
     * Check if user is Supervisor (can approve overtime tahap 1)
     */
    public function isSupervisor()
    {
        return $this->hasPermission('approve-overtime-supervisor');
    }

    /**
     * Check if user is Manager (can approve overtime tahap 2)
     */
    public function isManager()
    {
        return $this->hasPermission('approve-overtime-manager');
    }

    /**
     * Check if user has uploaded signature
     */
    public function hasSignature()
    {
        return !empty($this->signature_path) && 
               file_exists(public_path('storage/' . $this->signature_path));
    }

    /**
     * Get signature URL
     */
    public function getSignatureUrlAttribute()
    {
        if ($this->hasSignature()) {
            return asset('storage/' . $this->signature_path);
        }
        return null;
    }

    /**
     * Get all section IDs that user can access
     * (For Supervisor/Manager with multiple sections)
     */
    public function getAccessibleSectionIds()
    {
        // Super admin can access all
        if ($this->canManageAllSections()) {
            return Section::pluck('id')->toArray();
        }
        
        $sectionIds = [];
        
        // Add primary section (from employee)
        if ($this->section_id) {
            $sectionIds[] = $this->section_id;
        }
        
        // Add managed sections (for Supervisor/Manager)
        if ($this->isSupervisor() || $this->isManager()) {
            $managedSectionIds = $this->sections()->pluck('sections.id')->toArray();
            $sectionIds = array_merge($sectionIds, $managedSectionIds);
        }
        
        return array_unique($sectionIds);
    }
}
