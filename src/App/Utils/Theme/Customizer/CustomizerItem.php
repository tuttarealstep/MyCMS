<?php
    /**
     * User: tuttarealstep
     * Date: 21/01/17
     * Time: 11.00
     */

    namespace MyCMS\App\Utils\Theme\Customizer;

    class CustomizerItem
    {
        public $id;

        public $subItemId = "";

        public $label = "";

        public $value = "";

        public $description = "";

        public $type = "text";

        public $customArgs = [];

        public $elementClass = "form-control";

        function __construct($id, $subItemId, $values = [])
        {
            $this->id = $id;
            $this->subItemId = $subItemId;

            $keys = array_keys(get_object_vars($this));
            foreach ($keys as $key) {
                if (isset($values[ $key ])) {
                    $this->$key = $values[ $key ];
                }
            }
        }
    }