<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\App;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\ImportAirports::class,
        Commands\ImportOldDatabase::class,
        Commands\LoadCurrencyRate::class,
        Commands\AnimalsWithoutSpanishName::class,
        Commands\CheckAnimalCatalogPicture::class,
        Commands\NewSurplus::class,
        Commands\NewWanted::class,
        Commands\SurplusToMembers::class,
        Commands\CrateNamesUppercase::class,
        Commands\GenerateInventoryListPdf::class,
        Commands\CheckTodayTasks::class,
        Commands\AnonymizeBankAccountsData::class,
        Commands\AnonymizeContactsData::class,
        Commands\AnonymizeUsersData::class,
        Commands\AnonymizeOrganizationsData::class,
        Commands\ListNewAnimalsCreated::class,
        Commands\SendNewAnimalInstitutions::class,
        Commands\ListNewWantedCreated::class,
        Commands\ListNewInstitutionsCreated::class,
        Commands\ListNewOfferSendOut::class,
        Commands\ListOfferOverSevenDaysCreated::class,
        Commands\ListNewOfferForApproval::class,
        Commands\InboxEmail::class,
        Commands\ListNewOrder::class,
        Commands\ListRealizedOrder::class,
        Commands\TaskNotComplete::class,
        Commands\UpdateNewContactNewToEmail::class,
        Commands\DeleteEmailsAddress::class,
        Commands\TaskDueReminders::class,
        Commands\TaskForApproval::class,
        Commands\TaskComplete::class,
        Commands\TaskDueExpired::class,
        // SendSystemEmailsWithOffice365, what is this?
        Commands\SendEmailReminders::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('zoo:rates')->dailyAt('01:00');
        if (App::environment('production')) {
            $schedule->command('send:surplus-to-members')->dailyAt('6:00');
            $schedule->command('send:list-animals-created')->dailyAt('6:00');
            $schedule->command('send:new-wanted')->dailyAt('20:00');
            $schedule->command('send:animal-without-spanish-name')->cron('0 8 * * 1-5');
            $schedule->command('send:animal-without-catalog-picture')->cron('0 8 * * 1-5');
            $schedule->command('send:offers-to-approve')->cron('0 7 * * 1-5');
            $schedule->command('send:offers-to-remind')->cron('0 7 * * 1-5');
            $schedule->command('generate:inventory-list')->dailyAt('01:00');
            $schedule->command('send:today-tasks')->dailyAt('01:00');
            $schedule->command('send:new-animal-institutions')->everyFiveMinutes();
            $schedule->command('send:list-wanted-created')->dailyAt('15:00');
            $schedule->command('send:list-institutions-created')->dailyAt('15:00');
            $schedule->command('send:list-offers-send-out')->dailyAt('15:00');
            $schedule->command('send:list-offers-over-seven-days')->dailyAt('15:00');
            $schedule->command('send:list-offers-for-approval')->dailyAt('15:00');
            $schedule->command('get:inbox-email')->hourly();
            $schedule->command('send:list-new-orders')->dailyAt('15:00');
            $schedule->command('send:list-realized-orders')->dailyAt('15:00');
            $schedule->command('update:label-new-contact')->everyTenMinutes();
            $schedule->command('delete:emails-address')->everyThirtyMinutes();
            $schedule->command('send:task-due-reminders')->dailyAt('15:00');
            $schedule->command('send:task-for-approval')->dailyAt('15:00');
            $schedule->command('send:task-complete')->dailyAt('15:00');
            $schedule->command('send:task-due-expired')->dailyAt('15:00');
            $schedule->command('send:email-due-reminders')->dailyAt('15:00');
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
