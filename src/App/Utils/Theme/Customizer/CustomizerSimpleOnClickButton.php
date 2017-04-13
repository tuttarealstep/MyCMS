<?php
    /**
     * User: tuttarealstep
     * Date: 07/02/17
     * Time: 18.12
     */

    namespace MyCMS\App\Utils\Theme\Customizer;

    class CustomizerSimpleOnClickButton extends CustomizerItem
    {
        function __construct($id, $subItemId, $values)
        {
            if (isset($values["jsFunction"])) {
                $values = array_merge_recursive($values, ["customArgs" => ["onClick" => $values["jsFunction"]]]);
            }

            if (isset($values["href"])) {
                $values = array_merge_recursive($values, ["customArgs" => ["href" => $values["href"]]]);
            } else {
                $values = array_merge_recursive($values, ["customArgs" => ["href" => "#"]]);
            }

            $this->elementClass = "";

            parent::__construct($id, $subItemId, $values);
        }

        public function buildHtmlCallBack()
        {
            ?>
            <?php if (!empty($this->description)) { ?>
            <span class="subItemDescription"><?php echo $this->description; ?></span>
        <?php } ?>
            <div class="info_div" style="margin-bottom: 10px;">
                <a class="<?php echo $this->elementClass; ?>" style="color: #fff; font-size: 14px"
                   id="<?php echo $this->id ?>" <?php
                    if (!empty($this->customArgs) && is_array($this->customArgs)) {
                        foreach ($this->customArgs as $argName => $value) {
                            echo $argName . '="' . $value . '"';
                        }
                    }
                ?>><?php echo $this->value ?></a>
            </div>
            <?php
        }

        public function getSubMenuValues()
        {
            return ['subMenuId' => $this->subItemId, "callBack" => [$this, "buildHtmlCallBack"]];
        }
    }