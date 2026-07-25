<?php

namespace Modules\Ecommerce\Models;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Modules\Ecommerce\Models\Concerns\BelongsToClient;

class StorefrontLayout extends Model
{
    use BelongsToClient;

    protected $fillable = ['draft_layout', 'published_layout'];

    protected $casts = [
        'draft_layout' => 'array',
        'published_layout' => 'array',
    ];

    public static function defaultFor(Company $company): array
    {
        return [
            'brand_name' => $company->company_name,
            'tagline' => 'Official Nexora storefront',
            'primary_color' => '#ff6b00',
            'accent_color' => '#f59e0b',
            'logo_path' => null,
            'custom_pages' => [],
            'sections' => [
                [
                    'id' => 'hero',
                    'enabled' => true,
                    'title' => 'Products built for your next big move.',
                    'highlight' => 'Shop with confidence.',
                    'body' => 'Explore products that are available from this client store, backed by live inventory availability.',
                    'button_label' => 'Browse products',
                    'button_url' => '#products',
                    'image_path' => null,
                    'hero_stats' => [
                        ['value' => '4,200+', 'label' => 'Units Shipped'],
                        ['value' => '4.9&starf;', 'label' => 'Avg Rating'],
                        ['value' => '72 hr', 'label' => 'Avg Delivery'],
                    ],
                    'hero_marquee' => [
                        ['text' => 'CERTIFIED BUILD TECHNICIANS'],
                        ['text' => 'RTX 4090 IN STOCK'],
                        ['text' => '3-YEAR WARRANTY INCLUDED'],
                        ['text' => 'FREE SHIPPING OVER â‚±50,000'],
                        ['text' => 'ZERO THERMAL THROTTLING'],
                        ['text' => '72-HOUR STRESS TESTED'],
                    ],
                ],
                [
                    'id' => 'tiers',
                    'enabled' => true,
                    'title' => "Select\nYour Tier",
                    'body' => 'Four configurations. Every one tested under load for 72 hours before it leaves our facility.',
                    'blocks' => [
                        ['listing_id' => ''],
                        ['listing_id' => ''],
                        ['listing_id' => ''],
                        ['listing_id' => '']
                    ]
                ],
                [
                    'id' => 'prebuilts',
                    'enabled' => true,
                    'title' => "Pre-Built\nSystems",
                    'body' => 'Ready to ship. Professionally assembled and stress-tested for out-of-the-box performance.',
                    'blocks' => [
                        ['listing_id' => ''],
                        ['listing_id' => ''],
                        ['listing_id' => ''],
                        ['listing_id' => '']
                    ]
                ],
                [
                    'id' => 'categories',
                    'enabled' => true,
                    'title' => "Explore\nCategories",
                    'body' => 'Find exactly what you need. From ready-to-ship systems to fully custom workstations.',
                ],
                [
                    'id' => 'cta',
                    'enabled' => true,
                    'title' => "Stop Settling.",
                    'subtitle' => "Start Winning.",
                    'body' => 'Free shipping. Free setup support. 30-day no-questions return policy. Your next machine is three clicks away.',
                    'primary_button_label' => 'Build Yours Now',
                    'primary_button_url' => '/configurator',
                    'secondary_button_label' => 'Talk To An Expert',
                    'secondary_button_url' => '/contact',
                    'tag_text' => 'READY_TO_BUILD',
                ],
            ],
        ];
    }

    public static function publishedFor(Company $company): array
    {
        $layout = static::query()->first();

        return $layout?->published_layout ?: static::defaultFor($company);
    }

    public static function editableFor(Company $company): array
    {
        $layout = static::query()->first();

        return $layout?->draft_layout ?: $layout?->published_layout ?: static::defaultFor($company);
    }
}
