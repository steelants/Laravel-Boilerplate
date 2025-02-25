<?php

namespace SteelAnts\LaravelBoilerplate\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class MakeBasicTestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:basic-tests
                            {--force : Overwrite existing views by default}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create basic set of tests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $testFilePath = base_path() . '/tests/Feature/BasicCoverageTest.php';
        if (file_exists($testFilePath) && !$this->option('force')) {
            if (!$this->components->confirm("The [" . $testFilePath . "] test already exists. Do you want to replace it?")) {
                return;
            }
        }

        $routeCollection = Route::getRoutes();
        $pages = [];

        foreach ($routeCollection as $value) {
            if (
                in_array('GET', $value->methods())
                && $value->uri() != '/'
                && $value->getName() != ''
                && strpos($value->uri(), '{') === false
                && strpos($value->uri(), '_') === false
                && strpos($value->uri(), 'csrf') === false
                && strpos($value->uri(), 'testing') === false
                && strpos($value->uri(), 'system') === false
                && strpos($value->getActionName(), 'App\Http\Controllers\Auth\\') === false
            ) {
                $pages[] = $value->uri();
            }
        }

        $this->info("Routes:");
        foreach ($pages as $page) {
            $this->info(" > " . $page);
        }

        $content = '';

        foreach ($pages as $page) {
            $content .= $this->generateMethod('guest_cant_access', $page, 'guest', 302, '/login');
        }

        foreach ($pages as $page) {
            $content .= $this->generateMethod('stranger_cant_access', $page, 'stranger', 403);
        }

        foreach ($pages as $page) {
            $content .= $this->generateMethod('unauthorized_is_redirected_to_login_from', $page, false, 302, '/login');
        }

        $this->saveTest('BasicCoverageTest', $content);
    }

    private function generateMethod($prefix, $page, $user = false, $status = 200, $redirect = false)
    {
        return '
        public function test_' . $prefix . '_' . str_replace('/', '_', str_replace('-', '_', $page)) . '()
        {
            $response = $this' . ($user ? '->actingAs($this->' . $user . ')' : '') . '->get("' . $page . '");
            $response->assertStatus(' . $status . ');
            ' . ($redirect ? '$response->assertRedirect("' . $redirect . '");' : '') . '
        }
        ';
    }

    private function getHead($className)
    {
        return "<?php

        namespace Tests\Feature;

        use Tests\BaseTestTemplate;

        class $className extends BaseTestTemplate
        {
        ";
    }

    private function getFoot()
    {
        return "\n\n}
        ";
    }

    private function saveTest($name, $content)
    {
        if (empty($content)) {
            return;
        }

        $testFilePath = base_path() . '/tests/Feature/' . $name . '.php';

        $fp = fopen($testFilePath, 'w');
        fwrite($fp, $this->getHead($name) . $content . $this->getFoot());
        fclose($fp);

        $this->info("Generated test file " . $name . '.php');
    }
}
