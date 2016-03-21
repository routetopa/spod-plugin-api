<?php

class SPODAPI_CTRL_ImportDatalet extends OW_ActionController
{
    const FIELD_USER_ID = 'email';
    const FIELD_DATALET_TYPE = 'type';
    const FIELD_DATA_URL = 'dataurl';
    const FIELD_FIELDS = 'fields';
    const FIELD_TITLE = 'title';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_X_LABEL = 'xlabel';
    const FIELD_Y_LABEL = 'ylabel';
    const FIELD_SUFFIX = 'suffix';
    const FIELD_AGGREGATORS = 'aggregators';

    private $DATALET_TYPES = [
        'areachart-datalet',
        'barchart-datalet',
        'columnchart-datalet',
        'datatable-datalet',
        'heatmap-datalet',
        'linechart-datalet',
        'scatterchart-datalet',
        'treemap-datalet',
    ];

    const INPUT_SOURCE = 1; // 1=json_body; 2=urlencoded_body; 3=debug

    private function output_success($result) {
        echo json_encode([
            'status' => 'success',
            'result' => $result,
        ], JSON_UNESCAPED_SLASHES);
        die();
    }

    private function output_error($error) {
        echo json_encode([
            'status' => 'error',
            'error' => $error,
        ]);
        die();
    }

    function index() {
        if ( ! OW::getRequest()->isPost() )
        {
            $this->output_error("Method not supported");
        }

        switch (self::INPUT_SOURCE) {
            case 1: // Read JSON
                $data = json_decode(file_get_contents('php://input'), true);
                break;
            case 2: // Read url-encoded
                $data = $_GET;
                break;
            case 3: // Use debug data
                $data = [
                    self::FIELD_USER_ID => 'webmaster@routetopa.eu',
                    self::FIELD_DATALET_TYPE => 'columnchart-datalet',
                    self::FIELD_DATA_URL => 'http://vmdatagov01.deri.ie:8080/api/action/datastore_search?resource_id=8445ab4c-b39b-403f-8e45-cb2116d35a2d',
                    self::FIELD_TITLE => 'My title',
                    self::FIELD_DESCRIPTION => 'Datalet description',
                    self::FIELD_X_LABEL => 'X axis',
                    self::FIELD_Y_LABEL => 'Y axis',
                    self::FIELD_SUFFIX => '',
                    self::FIELD_FIELDS => '"result,records,Asset Type","result,records,Estimated Duration in weeks"',
                    self::FIELD_AGGREGATORS => [
                        [ "field" => "Asset Type", "operation" => "GROUP BY" ],
                        [ "field" => "Estimated Duration in weeks", "operation" => "AVG" ],
                    ],
                ];
                break;
        }

        $user_email   = isset( $data[SELF::FIELD_USER_ID]      ) ? $data[SELF::FIELD_USER_ID]      : '';
        $datalet_type = isset( $data[self::FIELD_DATALET_TYPE] ) ? $data[self::FIELD_DATALET_TYPE] : '';
        $data_url     = isset( $data[self::FIELD_DATA_URL]     ) ? $data[self::FIELD_DATA_URL]     : '';
        $title        = isset( $data[self::FIELD_TITLE]        ) ? $data[self::FIELD_TITLE]        : '';
        $description  = isset( $data[self::FIELD_DESCRIPTION]  ) ? $data[self::FIELD_DESCRIPTION]  : '';
        $fields       = isset( $data[self::FIELD_FIELDS]       ) ? $data[self::FIELD_FIELDS]       : '';
        $x_axis_label = isset( $data[self::FIELD_X_LABEL]      ) ? $data[self::FIELD_X_LABEL]      : '';
        $y_axis_label = isset( $data[self::FIELD_Y_LABEL]      ) ? $data[self::FIELD_Y_LABEL]      : '';
        $suffix       = isset( $data[self::FIELD_SUFFIX]       ) ? $data[self::FIELD_SUFFIX]       : '';
        $aggregators  = isset( $data[self::FIELD_AGGREGATORS]  ) ? $data[self::FIELD_AGGREGATORS]  : [];

        $user_id = BOL_UserService::getInstance()->findByEmail($user_email)->id;

        if (!$user_id) {
            $this->output_error("User id missing");
        }

        if ('-datalet' != substr($datalet_type, -strlen('-datalet'))) {
            $datalet_type .= '-datalet';
        }
        if ( ! in_array( $datalet_type, $this->DATALET_TYPES ) ) {
            $this->output_error("{$datalet_type} not supported");
        }

        $params = [
            'data-url' => $data_url,
            'title' => $title,
            'description' => $description,
            'x-axis-label' => $x_axis_label,
            'y-axis-label' => $y_axis_label,
            'suffix' => $suffix,
            'filters' => '[]',
            'aggregators' => json_encode( $aggregators ),
            'orders' => '[]',
        ];
        $post_id = null; // CHECK THIS
        $plugin = null; // check this, too...
        $cache = null;

        if( ODE_CLASS_Helper::validateDatalet($datalet_type, $params, $fields) )
        {
            $results = SPODPR_BOL_Service::getInstance()->dataletCard(
                $user_id,
                $datalet_type,
                $fields,
                json_encode($params),
                '', // data
                '', // dataletId
                ''  // cardId
            );

            // array("card-id" => $card->id, "datalet-id" => $dtId);
            if ($results['card-id'] && $results['datalet-id']) {
                $this->output_success($results);
            }
        }

        die();
        $this->output_error('Missing data.');
    }

}