<?php

OW::getRouter()->addRoute(new OW_Route('spodapi.api', 'spodapi', "SPODAPI_CTRL_RoomsUsingDataset", 'roomsusingdataset'));
OW::getRouter()->addRoute(new OW_Route('spodapi.import', 'spodapi', "SPODAPI_CTRL_ImportDatalet", 'importdatalet'));
