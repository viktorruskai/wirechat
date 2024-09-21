<?php

namespace Namu\WireChat\Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Foundation\Testing\Concerns\InteractsWithContainer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Livewire\LivewireServiceProvider;
use Namu\WireChat\WireChatServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Illuminate\Support\Facades\View;

use Laravel\Dusk\DuskServiceProvider;
use Namu\WireChat\Tests\CreatesApplication as TestsCreatesApplication;

use function Orchestra\Testbench\workbench_path;
use Orchestra\Testbench\BrowserKit\TestCase as BrowserBaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

use Orchestra\Testbench\Concerns\CreatesApplication;

//abstract class DuskTestCase extends  \Orchestra\Testbench\Dusk\TestCase
//abstract class DuskTestCase extends  BaseTestCase
abstract class DuskTestCase extends  BrowserBaseTestCase
{
    use WithWorkbench; 
  //  use CreatesApplication;
    //use CreatesApplication;
    use RefreshDatabase; 
    use InteractsWithContainer;
    protected static $baseServeHost = '127.0.0.1';
    protected static $baseServePort = 8001;


  public $baseUrl = 'http://127.0.0.1';
  
    // /**
    //  * Prepare for Dusk test execution.
    //  */
    #[BeforeClass]
    public static function prepare(): void
    {
        // if (! static::runningInSail()) {
           // static::startChromeDriver();
        // }
    }

    // /**
    //  * Create the RemoteWebDriver instance.
    //  */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([ '--start-maximized',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                //'--headless=new',
            ]);
        })->all());

        return RemoteWebDriver::create( 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    
    protected function getPackageProviders($app)
    {
        return [
          //  ServiceProvider::class,
            LivewireServiceProvider::class,
            WireChatServiceProvider::class,
            DuskServiceProvider::class
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // $this->loadMigrationsFrom(
        //     workbench_path('database/migrations')
        // );
        //\Orchestra\Testbench\Dusk\Options::withUI();
        $this->withoutVite();
     //   $this->loadRoutesFrom(workbench_path('routes/web.php'));
        //here we add a new ile in the name of the mixture of the berir d 
        // $this->loadMigrationsFrom(__DIR__.'/migrations');
        // $this->loadMigrationsFrom(dirname(__DIR__).'/migrations');
    }
    
    protected function getEnvironmentSetUp($app)
    {

       // $this->overrideApplicationProviders($app);
       //$this->overrideApplicationAliases($app);
       $this->overrideApplicationBindings($app);

        View ::addLocation('../resources/views');
        tap($app['session'], function ($session) {
            $session->put('_token', str()->random(40));
        });

        tap($app['config'], function ($config) {
            $config->set('app.env', 'testing');

            $config->set('app.debug', true);
            
            //update wirechat userModel
            $config->set('app.debug', true);


            $config->set('view.paths', [__DIR__.'/views', resource_path('views')]);

            $config->set('app.key', 'base64:Hupx3yAySikrM2/edkZQNQHslgDWYfiBfCuSThJ5SK8=');

            $config->set('database.default', 'testbench');

            $config->set('database.default', 'sqlite');
            $config->set('database.connections.testbench', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]);
                      // Setup queue database connections.
                      $config->set('queue.batching.database', 'testbench'); 
                      $config->set('queue.failed.database', 'testbench');
        });
    }

    /**
    * Make sure all integration tests use the same Laravel "skeleton" files.
    * This avoids duplicate classes during migrations.
    *
    * Overrides \Orchestra\Testbench\Dusk\TestCase::getBasePath
    *       and \Orchestra\Testbench\Concerns\CreatesApplication::getBasePath
    *
    * @return string
    */
    protected function getBasePath()
    {
        // Adjust this path depending on where your override is located.
        return __DIR__.'/../vendor/orchestra/testbench-dusk/laravel'; 
    }


 }
