<?php

namespace App\Services;

use App\Models\Plant;
use App\Models\AggregatedDataSnapshot;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PlantDataCacheService
{
    /**
     * Directly fetch and process plant data (no cache)
     */
    public function getPlantData(string $plantId, string $date): array
    {
        return $this->fetchAndProcessPlantData($plantId, $date);
    }
    
    /**
     * Fetch plant data for a specific date with timestamps
     */
    public function getPlantDataByTimestamps(string $plantId, int $startTimestamp, int $endTimestamp): array
    {
        $date = Carbon::createFromTimestamp($startTimestamp)->format('Y-m-d');
        
        // Fetch fresh data
        return $this->fetchPlantDataByTimestamps($plantId, $startTimestamp, $endTimestamp);
    }
    
    /**
     * Fetch and process plant data from database
     */
    private function fetchAndProcessPlantData(string $plantId, string $date): array
    {
        $plant = Plant::where('uid', $plantId)
                     ->orWhere('uuid', $plantId)
                     ->orWhere('id', $plantId)
                     ->first();
        
        if (!$plant) {
            Log::error("Plant not found: {$plantId}");
            return $this->getEmptyDataStructure();
        }
        
        // Get date range for the day
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->endOfDay();
        
        // Fetch aggregated data snapshots
        $snapshots = AggregatedDataSnapshot::where('plant_id', $plant->id)
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->orderBy('created_at')
            ->get();
        
        Log::info("Fetched {$snapshots->count()} snapshots for plant {$plantId} on {$date}");
        
        return $this->processSnapshots($snapshots);
    }
    
    /**
     * Fetch plant data by Unix timestamps
     */
    private function fetchPlantDataByTimestamps(string $plantId, int $startTimestamp, int $endTimestamp): array
    {
        $plant = Plant::where('uid', $plantId)
                     ->orWhere('uuid', $plantId)
                     ->orWhere('id', $plantId)
                     ->first();
        
        if (!$plant) {
            Log::error("Plant not found: {$plantId}");
            return $this->getEmptyDataStructure();
        }
        
        $startDate = Carbon::createFromTimestamp($startTimestamp);
        $endDate = Carbon::createFromTimestamp($endTimestamp);
        
        // Fetch aggregated data snapshots
        $snapshots = AggregatedDataSnapshot::where('plant_id', $plant->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at')
            ->get();
        
        return $this->processSnapshots($snapshots);
    }
    
    /**
     * Process snapshots into chart data format
     */
    private function processSnapshots($snapshots): array
    {
        $energyChart = [];
        $batteryPrice = [];
        $batterySavings = [];
        
        foreach ($snapshots as $snapshot) {
            $timestamp = $snapshot->created_at->toISOString();
            
            // Energy chart data
            $energyChart[$timestamp] = [
                'pv_p' => $snapshot->pv_p ?? 0,
                'battery_p' => $snapshot->battery_p ?? 0,
                'grid_p' => $snapshot->grid_p ?? 0,
            ];
            
            // Battery price data
            $batteryPrice[$timestamp] = [
                'battery_p' => $snapshot->battery_p ?? 0,
                'tariff' => $snapshot->tariff ?? 0.15,
            ];
            
            // Battery savings data
            $savings = $snapshot->battery_savings;
            if ($savings === null && $snapshot->battery_p !== null) {
                // Calculate savings if not stored
                $batteryPower = abs($snapshot->battery_p);
                $tariff = $snapshot->tariff ?? 0.15;
                // Convert W to kW and multiply by time interval
                // Assuming data points are every 30 minutes (0.5 hours)
                $timeIntervalHours = 0.5; // 30 minutes = 0.5 hours
                $savings = ($batteryPower / 1000) * $tariff * $timeIntervalHours;
            }
            
            $batterySavings[$timestamp] = [
                'battery_savings' => $savings ?? 0,
            ];
        }
        
        return [
            'energy_chart' => $energyChart,
            'battery_price' => $batteryPrice,
            'battery_savings' => $batterySavings,
        ];
    }
    
    /**
     * Get empty data structure
     */
    private function getEmptyDataStructure(): array
    {
        return [
            'energy_chart' => [],
            'battery_price' => [],
            'battery_savings' => [],
        ];
    }
}
