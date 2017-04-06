<?php
$suppliers = [
    ['Yeoman Latvia Ltd / Group De Cloedt',"Manufacturing, production industry",'Latvia'],
['ABB Switzerland',"Manufacturing, production industry",'Switzerland'],
['ALPA Centrums',"Transportation, logistics and storage",'Latvia'],
['Alstom','Other','Latvia'],
['Alstom Transport SA',"Manufacturing, production industry",'France'],
['ALVORA UAB','Construction','Lithuania'],
['Baltic Rail AS',"Transportation, logistics and storage",'Estonia'],
['Baltic Techno Group OÜ',"Transportation, logistics and storage",'Estonia'],
["voestalpine VAE Legetecha, UAB","Manufacturing, production industry",'Lithuania'],
['ViaCon Group',"Manufacturing, production industry",'Estonia'],
['ViaCon Latvija SIA','Construction','Latvia'],
['Tensar International Ltd','Construction','Sweden'],
['SWETRAK UAB',"Manufacturing, production industry",'Lithuania'],
['PJ Automation Oy','Construction','Latvia'],
['Ostas celtnieks','Construction','Latvia'],
['OÜ Maksmi','Wholesale and retail trade','Estonia'],
['Pandrol Track Systems',"Manufacturing, production industry",'United Kingdom'],
["Obrascón Huarte Laín, S.A. (OHL, S.A.)",'Construction','Spain'],
["Obrascón Huarte Laín, S.A. (OHL)",'Construction','Spain'],
['OK Būvmateriāli Ltd.','Wholesale and retail trade','Latvia'],
['Network Certification Body',"Transportation, logistics and storage",'United Kingdom'],
['LNK Properties SIA','Real estate','Latvia'],
['ILF Consulting Engineers Austria GmbH','Consulting','Austria'],
['Hill International','Consulting','Poland'],
['GULERMAK AS','Construction','Sweden'],
['Fugro RailData','Consulting','Netherlands'],
['EVR Cargo AS',"Transportation, logistics and storage",'Estonia'],
["Ernst & Young, Germany",'Consulting','Germany'],
['Ernst & Young Baltic (Lithuania)','Consulting','Lithuania'],
['Eco Baltia Grupa SIA',"Manufacturing, production industry",'Latvia'],
['Edif ERA','Consulting','United Kingdom'],
['EGIS RAIL','Consulting','France'],
['DAN Communications','IT and communications','Latvia'],
["ByteToken, Ltd",'IT and communications','United Kingdom'],
['Bureau Veritas Lit UAB','Law and regulations','Lithuania'],
['Pipelife Latvia SIA',"Manufacturing, production industry",'Latvia'],
];

return [
    'on' => [
        'table' => 'suppliers',
        'posthook' => function() use ($suppliers) {
            foreach ( $suppliers as $supplier ) {
                ( new Suppliers() )->create( [
                    'company' => $supplier[0],
                    'industry' => $supplier[1],
                    'state' => $supplier[2],
                ]);
            }
        }
    ],

    'down' => [
        'table' => 'suppliers',
        'drop' => false,
        'posthook' => function() use ($suppliers) {
            foreach ( $suppliers as $supplier ) {
                ( new Suppliers() )->where('company', $supplier[0])->where('industry', $supplier[1])->where('state', $supplier[2])->delete(1);
            }
        }
    ]
];