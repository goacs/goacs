<?php

/** @var \App\ACS\Entities\Device $device */
/** @var \App\Models\Device $deviceModel */
/** @var \App\ACS\Logic\Script\Functions $func */

$mac = $func->getParameterValue('InternetGatewayDevice.LANDevice.1.LANEthernetInterfaceConfig.1.MACAddress');
$mac4 = substr(str_replace(':','', $mac), 8, 12);
$ssid = 'Multiplay_' . $mac4;
if ($func->parameterExist('InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.EnableSSIDPrefix') === true) {
    $func->setParameter('InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.EnableSSIDPrefix', '0');
}

if ($func->parameterExist('InternetGatewayDevice.DeviceInfo.X_ZTE-COM_AdminAccount.Password') === true) {
    $func->setParameter('InternetGatewayDevice.DeviceInfo.X_ZTE-COM_AdminAccount.Password', 'CHANGEME');
}
$func->setParameter('InternetGatewayDevice.ManagementServer.ConnectionRequestUsername', 'ACS');
$func->setParameter('InternetGatewayDevice.ManagementServer.ConnectionRequestPassword', 'XD'.$deviceModel->serial_number);
$func->setParameter('InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.SSID', $ssid);
$func->setParameter('InternetGatewayDevice.ManagementServer.PeriodicInformInterval', '600');

