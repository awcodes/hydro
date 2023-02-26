<?php

namespace Awcodes;

use Filament\Context;
use Filament\Contracts\Plugin;
use Filament\Support\Assets\Theme;
use Filament\Support\Color;
use Filament\Support\Facades\FilamentAsset;

class Curator implements Plugin
{
    public function getId(): string
    {
        return 'curator-theme';
    }

    public function register(Context $context): void
    {
        FilamentAsset::register([
            Theme::make('curator', __DIR__.'/../resources/dist/curator.css'),
        ]);

        $context
            ->font('DM Sans')
            ->primaryColor(Color::Amber)
            ->secondaryColor(Color::Gray)
            ->warningColor(Color::Amber)
            ->dangerColor(Color::Rose)
            ->successColor(Color::Green)
            ->grayColor(Color::Slate)
            ->sidebarWidth('20rem')
            ->collapsedSidebarWidth('5.4rem')
            ->theme('curator');
    }

    public function boot(Context $context): void
    {
        //
    }
}
