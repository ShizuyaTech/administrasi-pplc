<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Overtime;
use App\Models\BusinessTrip;
use App\Models\Consumable;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $isSuperAdmin = $user->canManageAllSections();
        $sectionId = $user->section_id;
        
        // Statistics Cards
        $stats = [
            'total_absences' => $this->getTotalAbsences($isSuperAdmin, $sectionId),
            'total_overtimes' => $this->getTotalOvertimes($isSuperAdmin, $sectionId),
            'pending_overtimes' => $this->getPendingOvertimes($isSuperAdmin, $sectionId),
            'total_business_trips' => $this->getTotalBusinessTrips($isSuperAdmin, $sectionId),
            'pending_business_trips' => $this->getPendingBusinessTrips($isSuperAdmin, $sectionId),
            'low_stock_items' => $this->getLowStockItems($isSuperAdmin, $sectionId),
            'total_consumables' => $this->getTotalConsumables($isSuperAdmin, $sectionId),
        ];
        
        // Recent Activities
        $recentAbsences = $this->getRecentAbsences($isSuperAdmin, $sectionId);
        $recentOvertimes = $this->getRecentOvertimes($isSuperAdmin, $sectionId);
        $recentBusinessTrips = $this->getRecentBusinessTrips($isSuperAdmin, $sectionId);
        $lowStockConsumables = $this->getLowStockConsumables($isSuperAdmin, $sectionId);
        
        // Monthly Chart Data (Last 6 months)
        $monthlyData = $this->getMonthlyChartData($isSuperAdmin, $sectionId);
        
        return view('dashboard', compact('stats', 'recentAbsences', 'recentOvertimes', 'recentBusinessTrips', 'lowStockConsumables', 'monthlyData'));
    }
    
    private function getTotalAbsences($isSuperAdmin, $sectionId)
    {
        $query = Absence::query();
        if (!$isSuperAdmin) {
            $query->where('section_id', $sectionId);
        }
        return $query->whereMonth('date', now()->month)->count();
    }
    
    private function getTotalOvertimes($isSuperAdmin, $sectionId)
    {
        $query = Overtime::query();
        if (!$isSuperAdmin) {
            $query->where('section_id', $sectionId);
        }
        return $query->whereMonth('date', now()->month)->count();
    }
    
    private function getPendingOvertimes($isSuperAdmin, $sectionId)
    {
        $query = Overtime::where('status', 'pending');
        if (!$isSuperAdmin) {
            $query->where('section_id', $sectionId);
        }
        return $query->count();
    }
    
    private function getTotalBusinessTrips($isSuperAdmin, $sectionId)
    {
        $query = BusinessTrip::query();
        if (!$isSuperAdmin) {
            $query->where('section_id', $sectionId);
        }
        return $query->whereMonth('departure_date', now()->month)->count();
    }
    
    private function getPendingBusinessTrips($isSuperAdmin, $sectionId)
    {
        $query = BusinessTrip::where('status', 'draft');
        if (!$isSuperAdmin) {
            $query->where('section_id', $sectionId);
        }
        return $query->count();
    }
    
    private function getLowStockItems($isSuperAdmin, $sectionId)
    {
        $query = Consumable::whereRaw('current_stock <= minimum_stock');
        if (!$isSuperAdmin) {
            $query->where('section_id', $sectionId);
        }
        return $query->count();
    }
    
    private function getTotalConsumables($isSuperAdmin, $sectionId)
    {
        $query = Consumable::query();
        if (!$isSuperAdmin) {
            $query->where('section_id', $sectionId);
        }
        return $query->count();
    }
    
    private function getRecentAbsences($isSuperAdmin, $sectionId)
    {
        $query = Absence::with(['section', 'creator']);
        if (!$isSuperAdmin) {
            $query->where('section_id', $sectionId);
        }
        return $query->orderBy('date', 'desc')->take(5)->get();
    }
    
    private function getRecentOvertimes($isSuperAdmin, $sectionId)
    {
        $query = Overtime::with(['section', 'creator']);
        if (!$isSuperAdmin) {
            $query->where('section_id', $sectionId);
        }
        return $query->orderBy('date', 'desc')->take(5)->get();
    }
    
    private function getRecentBusinessTrips($isSuperAdmin, $sectionId)
    {
        $query = BusinessTrip::with(['section', 'creator']);
        if (!$isSuperAdmin) {
            $query->where('section_id', $sectionId);
        }
        return $query->orderBy('departure_date', 'desc')->take(5)->get();
    }
    
    private function getLowStockConsumables($isSuperAdmin, $sectionId)
    {
        $query = Consumable::with('section')->whereRaw('current_stock <= minimum_stock');
        if (!$isSuperAdmin) {
            $query->where('section_id', $sectionId);
        }
        return $query->orderBy('current_stock', 'asc')->take(10)->get();
    }
    
    private function getMonthlyChartData($isSuperAdmin, $sectionId)
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push([
                'month' => $date->format('M Y'),
                'absences' => $this->getMonthlyCount(Absence::class, $date, $isSuperAdmin, $sectionId),
                'overtimes' => $this->getMonthlyCount(Overtime::class, $date, $isSuperAdmin, $sectionId),
                'business_trips' => $this->getMonthlyCount(BusinessTrip::class, $date, $isSuperAdmin, $sectionId, 'departure_date'),
            ]);
        }
        return $months;
    }
    
    private function getMonthlyCount($model, $date, $isSuperAdmin, $sectionId, $dateColumn = 'date')
    {
        $query = $model::query();
        if (!$isSuperAdmin) {
            $query->where('section_id', $sectionId);
        }
        return $query->whereYear($dateColumn, $date->year)
                     ->whereMonth($dateColumn, $date->month)
                     ->count();
    }
}
