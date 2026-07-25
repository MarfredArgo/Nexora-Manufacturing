<?php

namespace App\Services\Departments;

use App\Models\EcommerceDeptConfiguratorConfig;
use App\Models\EcommerceDeptPrebuiltConfig;
use App\Models\EcommerceDeptProduct;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class EcommerceService
{
    /**
     * @return array<string, mixed>
     */
    public function getSnapshot(): array
    {
        return [
            'catalog_value' => $this->totalCatalogValue(),
            'total_products' => $this->totalProductsListed(),
            'sold_out_count' => $this->soldOutCount(),
            'sold_out_rate_percent' => $this->soldOutRatePercent(),
            'average_rating' => $this->averageRating(),
            'top_products' => $this->topProducts(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getKpiSummaryForAi(): array
    {
        return [
            'catalog_value' => $this->totalCatalogValue(),
            'total_products' => $this->totalProductsListed(),
            'sold_out_count' => $this->soldOutCount(),
            'average_rating' => $this->averageRating(),
            'top_products' => $this->topProducts(3),
        ];
    }

    /**
     * Sum of price across all catalog items still available for sale
     * (sold-out gaming laptops are excluded; prebuilt/configurator
     * items carry no sold-out flag in the source data, so they're
     * always counted). Proxy for "total revenue" — see class docblock.
     */
    public function totalCatalogValue(): float
    {
        $products = (float) EcommerceDeptProduct::where('is_sold_out', false)->sum('price');
        $prebuilts = (float) EcommerceDeptPrebuiltConfig::sum('price');
        $configs = (float) EcommerceDeptConfiguratorConfig::sum('price');

        return $products + $prebuilts + $configs;
    }

    /**
     * Count of catalog items across all three tables. Proxy for
     * "units sold" — see class docblock.
     */
    public function totalProductsListed(): int
    {
        return EcommerceDeptProduct::count()
            + EcommerceDeptPrebuiltConfig::count()
            + EcommerceDeptConfiguratorConfig::count();
    }

    /**
     * Only the products (gaming laptops) table tracks a sold-out flag —
     * prebuilt/configurator configs are build-to-order and have no
     * stock concept.
     */
    public function soldOutCount(): int
    {
        return EcommerceDeptProduct::where('is_sold_out', true)->count();
    }

    /**
     * Percentage of catalog products (gaming laptops only — see
     * soldOutCount()) that are currently sold out.
     */
    public function soldOutRatePercent(): float
    {
        $total = EcommerceDeptProduct::count();

        if ($total === 0) {
            return 0.0;
        }

        return round(($this->soldOutCount() / $total) * 100, 2);
    }

    /**
     * Average customer rating across products and configurator configs.
     * Prebuilt configs carry no rating field in the source data, so
     * they're excluded rather than assumed.
     */
    public function averageRating(): float
    {
        $productAvg = EcommerceDeptProduct::whereNotNull('rating')->avg('rating');
        $configAvg = EcommerceDeptConfiguratorConfig::whereNotNull('rating')->avg('rating');

        $ratings = collect([$productAvg, $configAvg])->filter(fn($v) => $v !== null);

        if ($ratings->isEmpty()) {
            return 0.0;
        }

        return round((float) $ratings->avg(), 2);
    }

    /**
     * Highest-priced items across the three catalogs, unified into one
     * ranked list. Proxy for "top selling products" — with no order
     * volume to rank by, price is the closest available signal.
     *
     * @return array<int, array{product_name: string, price: float, source: string, rating: ?float}>
     */
    public function topProducts(int $limit = 10): array
    {
        $products = EcommerceDeptProduct::query()
            ->orderByDesc('price')
            ->limit($limit)
            ->get()
            ->map(fn($row) => [
                'product_name' => $row->name,
                'price' => (float) $row->price,
                'source' => 'Gaming Laptop',
                'rating' => $row->rating !== null ? (float) $row->rating : null,
            ]);

        $prebuilts = EcommerceDeptPrebuiltConfig::query()
            ->orderByDesc('price')
            ->limit($limit)
            ->get()
            ->map(fn($row) => [
                'product_name' => $row->name,
                'price' => (float) $row->price,
                'source' => 'Prebuilt PC',
                'rating' => null,
            ]);

        $configs = EcommerceDeptConfiguratorConfig::query()
            ->orderByDesc('price')
            ->limit($limit)
            ->get()
            ->map(fn($row) => [
                'product_name' => $row->name,
                'price' => (float) $row->price,
                'source' => 'Custom Build',
                'rating' => $row->rating !== null ? (float) $row->rating : null,
            ]);

        /** @var Collection $combined */
        $combined = $products->concat($prebuilts)->concat($configs);

        return $combined
            ->sortByDesc('price')
            ->take($limit)
            ->values()
            ->toArray();
    }

    /**
     * Total price value of catalog items newly listed per day over the
     * trailing window, based on `source_created_at`. Structurally
     * replaces the old day-by-day revenue trend chart, but represents
     * new-listing value, not sales revenue — see class docblock.
     *
     * @return array<int, array{date: string, total: float}>
     */
    public function catalogGrowthTrend(int $days = 7): array
    {
        $start = Carbon::today()->subDays($days - 1);

        $totalsByDay = collect();

        foreach ([EcommerceDeptProduct::class, EcommerceDeptPrebuiltConfig::class, EcommerceDeptConfiguratorConfig::class] as $modelClass) {
            $rows = $modelClass::where('source_created_at', '>=', $start)
                ->selectRaw('DATE(source_created_at) as day, SUM(price) as total')
                ->groupBy('day')
                ->pluck('total', 'day');

            foreach ($rows as $day => $total) {
                $totalsByDay[$day] = ($totalsByDay[$day] ?? 0.0) + (float) $total;
            }
        }

        $trend = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->toDateString();
            $trend[] = [
                'date' => $date,
                'total' => (float) ($totalsByDay[$date] ?? 0.0),
            ];
        }

        return $trend;
    }
}
