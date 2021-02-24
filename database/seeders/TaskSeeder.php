<?php

namespace Database\Seeders;

use App\ACS\Types;
use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->GPVGlobalTask();
    }

    private function GPVGlobalTask()
    {
        if(Task::where(['for_type' => Task::TYPE_GLOBAL, 'name' => Types::GetParameterValues])->exists()) {
            return;
        }

        $task = new Task();
        $task->for_type = Task::TYPE_GLOBAL;
        $task->for_id = 0;
        $task->on_request = Types::GetParameterValuesResponse;
        $task->name = Types::RunScript;
        $task->infinite = true;
        $task->payload = ['script' => '
$mac = $func->getParameterValue("InternetGatewayDevice.LANDevice.1.LANEthernetInterfaceConfig.1.MACAddress");
$mac4 = substr(str_replace(":","", $mac), 8, 12);
$ssid = "Multiplay_" . $mac4;
if ($func->parameterExist("InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.EnableSSIDPrefix") === true) {
    $func->setParameter("InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.EnableSSIDPrefix", "0");
}

if ($func->parameterExist("InternetGatewayDevice.DeviceInfo.X_ZTE-COM_AdminAccount.Password") === true) {
    $func->setParameter("InternetGatewayDevice.DeviceInfo.X_ZTE-COM_AdminAccount.Password", "CHANGEME");
}
$func->setParameter("InternetGatewayDevice.ManagementServer.ConnectionRequestUsername", "ACS");
$func->setParameter("InternetGatewayDevice.ManagementServer.ConnectionRequestPassword", "ACS");
        '];


        $task->save();
    }
}
