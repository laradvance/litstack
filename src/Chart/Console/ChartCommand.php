<?php

namespace Ignite\Chart\Console;

use Ignite\Console\GeneratorCommand;
use Illuminate\Support\Str;

class ChartCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lit:chart {name}
                            {--area : Whether to create area chart }
                            {--donut : Whether to create donut chart }
                            {--progress : Whether to create progress chart }
                            {--bar : Whether to create bar chart }
                            {--number : Whether to create number chart }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will create a chart config. Default chart type is "area".';

    /**
     * Chart type.
     *
     * @var string
     */
    protected $chartType = 'area';

    /**
     * Execute the console command.
     *
     * @return bool|null
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        if ($this->setChartType() === false) {
            $this->error('Only one chart type can be selected');

            return false;
        }

        parent::handle();
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return [
            'donut'    => lit_vendor_path('stubs/chart.config.donut.stub'),
            'progress' => lit_vendor_path('stubs/chart.config.progress.stub'),
            'bar'      => lit_vendor_path('stubs/chart.config.bar.stub'),
            'number'   => lit_vendor_path('stubs/chart.config.number.stub'),
            'area'     => lit_vendor_path('stubs/chart.config.area.stub'),
        ][$this->chartType];
    }

    /**
     * Set chart type from options.
     *
     * @return bool|null
     */
    public function setChartType()
    {
        $default = $this->chartType;
        $this->chartType = null;

        foreach ([
            'donut', 'progress', 'bar', 'number', 'area',
        ] as $type) {
            if (! $this->option($type)) {
                continue;
            }

            // Returning false when type has already been set since multiple
            // types are not allowed.
            if ($this->chartType) {
                return false;
            }

            $this->chartType = $type;
        }

        if (! $this->chartType) {
            $this->chartType = $default;
        }

        $this->type = ucfirst($this->chartType).' chart config';
    }

    /**
     * Get the destination class path.
     *
     * @param  string $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->litstack->path(
            str_replace('\\', '/', $name).'.php'
        );
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Config\Charts';
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        if (! Str::endsWith($name, 'Config')) {
            $name .= 'Config';
        }

        return parent::qualifyClass(ucfirst($name));
    }
}
