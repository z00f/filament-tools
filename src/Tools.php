<?php

namespace z00f\FilamentTools;

use Closure;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use z00f\FilamentTools\Exceptions\ToolsException;

class Tools extends Page
{
    use InteractsWithForms;

    protected static string $view = 'filament-tools::tools';

    protected static ?string $navigationGroup = null;

    protected static ?string $navigationIcon = null;

    protected static ?Closure $canCallback = null;

    /** @var array<\z00f\FilamentTools\Tool> */
    protected static array $tools = [];

    public $data = [];

    public function mount()
    {
        abort_if(static::$canCallback !== null && ! app()->call(static::$canCallback, ['user' => Auth::user()]), 403);
    }

    /** @return array<\z00f\FilamentTools\Tool> */
    public function getToolsProperty(): array
    {
        return static::$tools;
    }

    /** @internal */
    public function notification(string $status, string $message): static
    {
        $this->notify($status, $message);

        return $this;
    }

    public function callToolSubmitAction(string $id): void
    {
        /** @var \z00f\FilamentTools\Tool $tool */
        $tool = $this->tools[$id];

        if ($action = $tool->getSubmitAction()) {
            $input = new ToolInput($this->getCachedForm($id)->getState());

            $action($input->component($this)->tool($tool));
        }
    }

    /** @param \Closure(\Filament\Pages\Page): \Filament\Pages\Page $configure */
    public static function register(Closure $configure): void
    {
        /** @var \z00f\FilamentTools\Tool $tool */
        $tool = app()->call($configure, [
            'tool' => new Tool(),
        ]);

        if (! $tool instanceof Tool) {
            throw ToolsException::expectedTool(actual: $tool);
        }

        $tool->assert();

        static::$tools[$tool->getId()] = $tool;
    }

    protected function getForms(): array
    {
        return collect(static::$tools)->mapWithKeys(function (Tool $tool) {
            return [$tool->getId() => $tool->getForm($this->makeForm())];
        })->merge(parent::getForms())->all();
    }

    protected static function shouldRegisterNavigation(): bool
    {
        if (static::$canCallback === null) {
            return true;
        }

        return app()->call(static::$canCallback, ['user' => Auth::user()]);
    }

    public static function can(Closure $callback): void
    {
        static::$canCallback = $callback;
    }

    public static function navigationGroup(string $group): void
    {
        static::$navigationGroup = $group;
    }

    public static function navigationIcon(string $icon): void
    {
        static::$navigationIcon = $icon;
    }
}
