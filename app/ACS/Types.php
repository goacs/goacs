<?php

declare(strict_types=1);


namespace App\ACS;


class Types
{
    const EMPTY = 'Empty';
    const INFORM = 'Inform';
    const INFORMResponse = 'InformResponse';
    const GetRPCMethodsRequest = 'GetRPCMethods';
    const GetRPCMethodsResponse = 'GetRPCMethodsResponse';
    const GetParameterNames = 'GetParameterNames';
    const GetParameterNamesResponse = 'GetParameterNamesResponse';
    const GetParameterValues = 'GetParameterValues';
    const GetParameterValuesResponse = 'GetParameterValuesResponse';
    const SetParameterValues = 'SetParameterValues';
    const SetParameterValuesResponse = 'SetParameterValuesResponse';
    const AddObject = 'AddObject';
    const AddObjectResponse = 'AddObjectResponse';
    const DeleteObject = 'DeleteObject';
    const DeleteObjectResponse = 'DeleteObjectResponse';
    const Download = 'Download';
    const DownloadResponse = 'DownloadResponse';
    const FaultResponse = 'Fault';
    const TransferComplete = 'TransferComplete';
    const TransferCompleteResponse = 'TransferCompleteResponse';
    const Reboot = 'Reboot';
    const RebootResponse = 'RebootResponse';
    const FactoryReset = 'FactoryReset';
    const FactoryResetResponse = 'FactoryResetResponse';


    const RunScript = 'RunScript';

    const SetParameterValuesProcessor = 'SetParameterValuesProcessor';

}
