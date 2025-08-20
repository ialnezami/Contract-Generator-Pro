<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Models\Contract;
use App\Models\ContractTemplate;
use App\Models\User;

class PerformanceOptimizationService
{
    /**
     * Cache duration in minutes
     */
    private const CACHE_DURATION = 60;

    /**
     * Get cached contract statistics
     */
    public function getCachedContractStatistics(User $user): array
    {
        $cacheKey = "user_contract_stats_{$user->id}";
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($user) {
            return $this->calculateContractStatistics($user);
        });
    }

    /**
     * Get cached template statistics
     */
    public function getCachedTemplateStatistics(): array
    {
        $cacheKey = 'template_statistics';
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            return $this->calculateTemplateStatistics();
        });
    }

    /**
     * Get cached popular templates
     */
    public function getCachedPopularTemplates(int $limit = 10): array
    {
        $cacheKey = "popular_templates_{$limit}";
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($limit) {
            return ContractTemplate::query()
                ->with(['category', 'variables'])
                ->where('is_public', true)
                ->orderBy('usage_count', 'desc')
                ->orderBy('rating', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    /**
     * Get cached user dashboard data
     */
    public function getCachedUserDashboard(User $user): array
    {
        $cacheKey = "user_dashboard_{$user->id}";
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($user) {
            return $this->calculateUserDashboard($user);
        });
    }

    /**
     * Clear user-specific caches
     */
    public function clearUserCaches(User $user): void
    {
        Cache::forget("user_contract_stats_{$user->id}");
        Cache::forget("user_dashboard_{$user->id}");
    }

    /**
     * Clear template caches
     */
    public function clearTemplateCaches(): void
    {
        Cache::forget('template_statistics');
        Cache::forget('popular_templates_10');
        Cache::forget('popular_templates_20');
    }

    /**
     * Optimize database queries
     */
    public function optimizeQueries(): array
    {
        $results = [];

        // Check for slow queries
        $slowQueries = DB::select("
            SELECT 
                query,
                COUNT(*) as count,
                AVG(time) as avg_time,
                MAX(time) as max_time
            FROM mysql.slow_log 
            WHERE start_time > DATE_SUB(NOW(), INTERVAL 1 DAY)
            GROUP BY query 
            ORDER BY avg_time DESC 
            LIMIT 10
        ");

        $results['slow_queries'] = $slowQueries;

        // Check table sizes
        $tableSizes = DB::select("
            SELECT 
                table_name,
                ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
            FROM information_schema.tables 
            WHERE table_schema = DATABASE()
            ORDER BY (data_length + index_length) DESC
        ");

        $results['table_sizes'] = $tableSizes;

        return $results;
    }

    /**
     * Warm up caches
     */
    public function warmUpCaches(): array
    {
        $results = [];

        // Warm up popular templates cache
        $this->getCachedPopularTemplates(10);
        $results['popular_templates'] = 'Warmed up';

        // Warm up template statistics
        $this->getCachedTemplateStatistics();
        $results['template_statistics'] = 'Warmed up';

        // Warm up user dashboards for active users
        $activeUsers = User::where('last_login_at', '>', now()->subDays(7))->limit(100)->get();
        foreach ($activeUsers as $user) {
            $this->getCachedUserDashboard($user);
        }
        $results['user_dashboards'] = "Warmed up for {$activeUsers->count()} active users";

        return $results;
    }

    /**
     * Monitor cache performance
     */
    public function getCachePerformance(): array
    {
        if (config('cache.default') === 'redis') {
            $redis = Redis::connection();
            $info = $redis->info();
            
            return [
                'cache_driver' => 'redis',
                'redis_version' => $info['redis_version'] ?? 'unknown',
                'connected_clients' => $info['connected_clients'] ?? 0,
                'used_memory' => $info['used_memory_human'] ?? 'unknown',
                'hit_rate' => $this->calculateRedisHitRate(),
            ];
        }

        return [
            'cache_driver' => config('cache.default'),
            'status' => 'Performance monitoring not available for this driver',
        ];
    }

    /**
     * Optimize database indexes
     */
    public function suggestIndexOptimizations(): array
    {
        $suggestions = [];

        // Check for missing indexes on frequently queried columns
        $frequentQueries = [
            'contracts' => ['user_id', 'status', 'expires_at', 'created_at'],
            'contract_templates' => ['category_id', 'is_public', 'rating', 'usage_count'],
            'users' => ['email', 'created_at', 'last_login_at'],
        ];

        foreach ($frequentQueries as $table => $columns) {
            foreach ($columns as $column) {
                $indexExists = DB::select("
                    SELECT COUNT(*) as count 
                    FROM information_schema.statistics 
                    WHERE table_schema = DATABASE() 
                    AND table_name = ? 
                    AND column_name = ?
                ", [$table, $column]);

                if ($indexExists[0]->count == 0) {
                    $suggestions[] = "Add index on {$table}.{$column}";
                }
            }
        }

        return $suggestions;
    }

    /**
     * Calculate contract statistics
     */
    private function calculateContractStatistics(User $user): array
    {
        return [
            'total_contracts' => Contract::where('user_id', $user->id)->count(),
            'active_contracts' => Contract::where('user_id', $user->id)->where('status', 'active')->count(),
            'draft_contracts' => Contract::where('user_id', $user->id)->where('status', 'draft')->count(),
            'pending_approval' => Contract::where('user_id', $user->id)->where('status', 'pending_approval')->count(),
            'expired_contracts' => Contract::where('user_id', $user->id)->where('status', 'expired')->count(),
            'total_value' => Contract::where('user_id', $user->id)->sum('total_value'),
            'contracts_this_month' => Contract::where('user_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->count(),
        ];
    }

    /**
     * Calculate template statistics
     */
    private function calculateTemplateStatistics(): array
    {
        return [
            'total_templates' => ContractTemplate::count(),
            'public_templates' => ContractTemplate::where('is_public', true)->count(),
            'private_templates' => ContractTemplate::where('is_public', false)->count(),
            'total_usage' => ContractTemplate::sum('usage_count'),
            'average_rating' => ContractTemplate::where('rating_count', '>', 0)->avg('rating'),
            'premium_templates' => ContractTemplate::whereNotNull('price')->count(),
        ];
    }

    /**
     * Calculate user dashboard data
     */
    private function calculateUserDashboard(User $user): array
    {
        return [
            'recent_contracts' => Contract::where('user_id', $user->id)
                ->with(['template', 'parties'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->toArray(),
            'upcoming_expirations' => Contract::where('user_id', $user->id)
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->where('expires_at', '<', now()->addDays(30))
                ->orderBy('expires_at')
                ->limit(5)
                ->get()
                ->toArray(),
            'pending_approvals' => Contract::where('user_id', $user->id)
                ->where('status', 'pending_approval')
                ->with('approvals.approver')
                ->limit(5)
                ->get()
                ->toArray(),
        ];
    }

    /**
     * Calculate Redis hit rate
     */
    private function calculateRedisHitRate(): float
    {
        try {
            $redis = Redis::connection();
            $info = $redis->info('stats');
            
            $hits = $info['keyspace_hits'] ?? 0;
            $misses = $info['keyspace_misses'] ?? 0;
            
            if ($hits + $misses > 0) {
                return round(($hits / ($hits + $misses)) * 100, 2);
            }
            
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get memory usage statistics
     */
    public function getMemoryUsage(): array
    {
        $memoryLimit = ini_get('memory_limit');
        $memoryUsage = memory_get_usage(true);
        $peakMemoryUsage = memory_get_peak_usage(true);
        
        return [
            'memory_limit' => $memoryLimit,
            'current_usage' => $this->formatBytes($memoryUsage),
            'peak_usage' => $this->formatBytes($peakMemoryUsage),
            'usage_percentage' => round(($memoryUsage / $this->parseMemoryLimit($memoryLimit)) * 100, 2),
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Parse memory limit string to bytes
     */
    private function parseMemoryLimit(string $memoryLimit): int
    {
        $unit = strtolower(substr($memoryLimit, -1));
        $value = (int) substr($memoryLimit, 0, -1);
        
        switch ($unit) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }
        
        return $value;
    }
}
