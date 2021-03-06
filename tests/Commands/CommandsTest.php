<?php

namespace Modular\Tests\Commands;

class ConsoleCommandsTest extends CommandsTestCase
{
    protected $namespacedUser = 'use Illuminate\Foundation\Auth\User;';

    public function testCastMakeCommand()
    {
        $this->artisan('make:cast', [
            'module' => 'foo_bar',
            'name' => 'FooBarCast',
        ]);

        $contents = $this->assertCommandPath('Casts/FooBarCast.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Casts;', $contents);
        $this->assertStringContainsString('class FooBarCast', $contents);
    }

    public function testChannelMakeCommand()
    {
        $this->artisan('make:channel', [
            'module' => 'foo_bar',
            'name' => 'FooBarChannel',
        ]);

        $contents = $this->assertCommandPath('Broadcasting/FooBarChannel.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Broadcasting;', $contents);
        $this->assertStringContainsString($this->namespacedUser, $contents);
        $this->assertStringContainsString('class FooBarChannel', $contents);
        $this->assertStringContainsString('User $user', $contents);
    }

    public function testConsoleMakeCommand()
    {
        $this->artisan('make:command', [
            'module' => 'foo_bar',
            'name' => 'BazQux',
        ]);

        $contents = $this->assertCommandPath('Console/Commands/BazQux.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Console\\Commands;', $contents);
        $this->assertStringContainsString('class BazQux ', $contents);
        $this->assertStringContainsString('$signature = \'foo_bar:baz-qux', $contents);
    }

    public function testEventMakeCommand()
    {
        $this->artisan('make:event', [
            'module' => 'foo_bar',
            'name' => 'BazQux'
        ]);

        $contents = $this->assertCommandPath('Events/BazQux.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Events;', $contents);
        $this->assertStringContainsString('class BazQux', $contents);
    }

    public function testExceptionMakeCommand()
    {
        $this->artisan('make:exception', [
            'module' => 'foo_bar',
            'name' => 'FooBarException'
        ]);

        $contents = $this->assertCommandPath('Exceptions/FooBarException.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Exceptions;', $contents);
        $this->assertStringContainsString('class FooBarException ', $contents);
    }

    public function testFactoryMakeCommand()
    {
        $this->artisan('make:factory', [
            'module' => 'foo_bar',
            'name' => 'BazQuxFactory'
        ]);

        $contents = $this->assertCommandPath('Database/Factories/BazQuxFactory.php');

        $this->assertStringContainsString('use VFS\\FooBar\\Models\\BazQux', $contents);
        $this->assertStringContainsString('$model = BazQux::class', $contents);

        $this->artisan('make:factory', [
            'module' => 'foo_bar',
            'name' => 'FooFactory',
            '--model' => 'Foo'
        ]);

        $contents = $this->assertCommandPath('Database/Factories/FooFactory.php');

        $this->assertStringContainsString('use VFS\\FooBar\\Models\\Foo', $contents);
        $this->assertStringContainsString('$model = Foo::class', $contents);
    }

    public function testJobMakeCommand()
    {
        $this->artisan('make:job', [
            'module' => 'foo_bar',
            'name' => 'FooBarJob'
        ]);

        $contents = $this->assertCommandPath('Jobs/FooBarJob.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Jobs;', $contents);
        $this->assertStringContainsString('class FooBarJob ', $contents);
    }

    public function testListenerMakeCommand()
    {
        $this->artisan('make:listener', [
            'module' => 'foo_bar',
            'name' => 'FooBarListener'
        ]);

        $contents = $this->assertCommandPath('Listeners/FooBarListener.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Listeners;', $contents);
        $this->assertStringContainsString('class FooBarListener', $contents);
    }

    public function testListenerMakeCommandEvent()
    {
        $this->artisan('make:listener', [
            'module' => 'foo_bar',
            'name' => 'FooBarListener',
            '--event' => 'FooBarEvent'
        ]);

        $contents = $this->assertCommandPath('Listeners/FooBarListener.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Listeners;', $contents);
        $this->assertStringContainsString('use VFS\\FooBar\\Events\\FooBarEvent;', $contents);
        $this->assertStringContainsString('class FooBarListener', $contents);
        $this->assertStringContainsString('FooBarEvent $event', $contents);
    }

    public function testMailMakeCommand()
    {
        $this->artisan('make:mail', [
            'module' => 'foo_bar',
            'name' => 'FooBarMail',
            '--markdown' => 'foobar'
        ]);

        $contents = $this->assertCommandPath('Mail/FooBarMail.php');

        $this->assertCommandPath('Views/foobar.blade.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Mail;', $contents);
        $this->assertStringContainsString('class FooBarMail', $contents);
        $this->assertStringContainsString('markdown(\'foobar', $contents);
    }

    public function testMiddlewareMakeCommand()
    {
        $this->artisan('make:middleware', [
            'module' => 'foo_bar',
            'name' => 'FooBarMiddleware'
        ]);

        $contents = $this->assertCommandPath('Http/Middleware/FooBarMiddleware.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Http\\Middleware;', $contents);
        $this->assertStringContainsString('class FooBarMiddleware', $contents);
    }

    public function testMigrationMakeCommand()
    {
        $this->artisan('make:migration', [
            'module' => 'foo_bar',
            'name' => 'create_foo_table'
        ]);

        $dir = $this->assertCommandPath('Database/Migrations');

        $file = $dir->getChildren()[0];
        $contents = $file->getContent();

        $this->assertStringContainsString('create_foo_table', $file->getName());
        $this->assertStringContainsString('class CreateFooTable ', $contents);
    }

    public function testNotificationMakeCommand()
    {
        $this->artisan('make:notification', [
            'module' => 'foo_bar',
            'name' => 'FooBarNotification',
            '--markdown' => 'foobar'
        ]);

        $contents = $this->assertCommandPath('Notifications/FooBarNotification.php');

        $this->assertCommandPath('Views/foobar.blade.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Notifications;', $contents);
        $this->assertStringContainsString('class FooBarNotification', $contents);
        $this->assertStringContainsString('markdown(\'foobar', $contents);
    }

    public function testObserverMakeCommand()
    {
        $this->artisan('make:observer', [
            'module' => 'foo_bar',
            'name' => 'FooBarObserver',
            '--model' => 'Foo'
        ]);

        $contents = $this->assertCommandPath('Observers/FooBarObserver.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Observers;', $contents);
        $this->assertStringContainsString('use VFS\\FooBar\\Models\\Foo', $contents);
        $this->assertStringContainsString('class FooBarObserver', $contents);
        $this->assertStringContainsString('Foo $foo', $contents);
    }

    public function testPolicyMakeCommand()
    {
        $this->artisan('make:policy', [
            'module' => 'foo_bar',
            'name' => 'FooBarPolicy',
            '--model' => 'Foo'
        ]);

        $contents = $this->assertCommandPath('Policies/FooBarPolicy.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Policies;', $contents);
        $this->assertStringContainsString('use VFS\\FooBar\\Models\\Foo', $contents);
        $this->assertStringContainsString($this->namespacedUser, $contents);
        $this->assertStringContainsString('class FooBarPolicy', $contents);
        $this->assertStringContainsString('User $user, Foo $foo', $contents);
    }

    public function testProviderMakeCommand()
    {
        $this->artisan('make:provider', [
            'module' => 'foo_bar',
            'name' => 'FooBarProvider'
        ]);

        $contents = $this->assertCommandPath('Providers/FooBarProvider.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Providers;', $contents);
        $this->assertStringContainsString('class FooBarProvider', $contents);
    }

    public function testRequestMakeCommand()
    {
        $this->artisan('make:request', [
            'module' => 'foo_bar',
            'name' => 'FooBarRequest'
        ]);

        $contents = $this->assertCommandPath('Http/Requests/FooBarRequest.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Http\\Requests;', $contents);
        $this->assertStringContainsString('class FooBarRequest', $contents);
    }

    public function testResourceMakeCommand()
    {
        $this->artisan('make:resource', [
            'module' => 'foo_bar',
            'name' => 'FooBarResource'
        ]);

        $contents = $this->assertCommandPath('Http/Resources/FooBarResource.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Http\\Resources;', $contents);
        $this->assertStringContainsString('class FooBarResource ', $contents);

        $this->artisan('make:resource', [
            'module' => 'foo_bar',
            'name' => 'FooBarCollection',
            '--collection' => true
        ]);

        $contents = $this->assertCommandPath('Http/Resources/FooBarCollection.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Http\\Resources;', $contents);
        $this->assertStringContainsString('class FooBarCollection ', $contents);
    }

    public function testRuleMakeCommand()
    {
        $this->artisan('make:rule', [
            'module' => 'foo_bar',
            'name' => 'FooBarRule'
        ]);

        $contents = $this->assertCommandPath('Rules/FooBarRule.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Rules;', $contents);
        $this->assertStringContainsString('class FooBarRule', $contents);
    }

    public function testSeederMakeCommand()
    {
        $this->artisan('make:seeder', [
            'module' => 'foo_bar',
            'name' => 'FooBarSeeder'
        ]);

        $contents = $this->assertCommandPath('Database/Seeds/FooBarSeeder.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Database\\Seeds;', $contents);
        $this->assertStringContainsString('class FooBarSeeder ', $contents);
    }

    public function testTestMakeCommand()
    {
        $this->artisan('make:test', [
            'module' => 'foo_bar',
            'name' => 'FooBarTest'
        ]);

        $contents = $this->assertCommandPath('Tests/Feature/FooBarTest.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Tests\\Feature;', $contents);
//        $this->assertStringContainsString('use Base\\Tests\\TestCase;', $contents);
        $this->assertStringContainsString('class FooBarTest', $contents);
    }

    public function testTestMakeCommandUnit()
    {
        $this->artisan('make:test', [
            'module' => 'foo_bar',
            'name' => 'FooBarTest',
            '--unit' => true
        ]);

        $contents = $this->assertCommandPath('Tests/Unit/FooBarTest.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Tests\\Unit;', $contents);
//        $this->assertStringContainsString('use Base\\Tests\\TestCase;', $contents);
        $this->assertStringContainsString('class FooBarTest', $contents);
    }
}
