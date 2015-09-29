<?php

namespace RachidLaasri\LaravelInstaller\Helpers;

use Exception;
use Illuminate\Support\Facades\Artisan;

class UpgradeManager
{
    /**
     * Execute migrations and seeders.
     *
     * @return array
     */
    public function updateDatabaseAndSeedTables()
    {
        return $this->updateDatabase();
    }

    /**
     * Update the database.
     *
     * @return array
     */
    private function updateDatabase()
    {
        try {

            Artisan::call('migrate');

        } catch (Exception $e) {

            return $this->response($e->getMessage(), 'danger');

        }

        return $this->seed();
    }

    /**
     * Seed the database.
     *
     * @return array
     */
    private function seed()
    {
        if ( ! empty(config('installer.upgrade.seeds'))) {
            try {
                $this->seedSpecificClasses();
            }
            catch
                (Exception $e){
                return $this->response($e->getMessage(), 'danger');
            }
        }

        return $this->response(trans('messages.final.finished'), 'success');
    }

    /**
     * Return a formatted messages.
     *
     * @param string $status
     * @param $message
     * @return array
     */
    private function response($message, $status = 'success')
    {
        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    /*
     * Seed specific classes if array is not empty.
     *
     * @return void
     */
    private function seedSpecificClasses()
    {
        foreach (config('installer.upgrade.seeds') as $class) {
            Artisan::call('db:seed', ['--class' => $class]);
        }
    }
}