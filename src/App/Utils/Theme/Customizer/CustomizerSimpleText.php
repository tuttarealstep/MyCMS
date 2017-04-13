<?php
    /**
     * User: tuttarealstep
     * Date: 21/01/17
     * Time: 10.58
     */

    namespace MyCMS\App\Utils\Theme\Customizer;

    if (!defined("MY_CMS_PATH")) {
        die("NO SCRIPT");
    }

    class CustomizerSimpleText extends CustomizerItem
    {
        function __construct($id, $subItemId, $values)
        {
            if (isset($values["placeholder"])) {
                $values = array_merge($values, ["customArgs" => ["placeholder" => $values["placeholder"]]]);
            }

            parent::__construct($id, $subItemId, $values);
        }

        public function buildHtmlCallBack()
        {
            ?>
            <div class="info_div">
            <span class="small_text">
            <?php echo $this->label ?>:<br>
                <?php if (!empty($this->description)) { ?>
                    <span class="subItemDescription"><?php echo $this->description; ?></span>
                <?php } ?>
                <input class="<?php echo $this->elementClass ?>" type="text" value="<?php echo $this->value ?>"
                       id="<?php echo $this->id ?>" <?php
                    if (!empty($this->customArgs) && is_array($this->customArgs)) {
                        foreach ($this->customArgs as $argName => $value) {
                            echo $argName . '="' . $value . '"';
                        }
                    }
                ?>/>
            </span>
            </div>
            <?php
        }

        public function getSubMenuValues()
        {
            return ['subMenuId' => $this->subItemId, "callBack" => [$this, "buildHtmlCallBack"]];
        }
    }