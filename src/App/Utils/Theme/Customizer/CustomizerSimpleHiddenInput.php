<?php

    namespace MyCMS\App\Utils\Theme\Customizer;

    class CustomizerSimpleHiddenInput extends CustomizerItem
    {
        function __construct($id, $subItemId, $values)
        {
            if (isset($values["placeholder"])) {
                $values = array_merge($values, ["customArgs" => ["placeholder" => $values["placeholder"]]]);
            }

            $values = array_merge($values, ["type" => "hidden"]);

            parent::__construct($id, $subItemId, $values);
        }

        public function buildHtmlCallBack()
        {
            ?>
            <?php if (!empty($this->description)) { ?>
            <span class="subItemDescription"><?php echo $this->description; ?></span>
        <?php } ?>
            <input class="<?php echo $this->elementClass; ?>" type="<?php echo $this->type; ?>"
                   value="<?php echo $this->value ?>" id="<?php echo $this->id ?>" <?php
                if (!empty($this->customArgs) && is_array($this->customArgs)) {
                    foreach ($this->customArgs as $argName => $value) {
                        echo $argName . '="' . $value . '"';
                    }
                }
            ?>/>
            <?php
        }

        public function getSubMenuValues()
        {
            return ['subMenuId' => $this->subItemId, "callBack" => [$this, "buildHtmlCallBack"]];
        }
    }