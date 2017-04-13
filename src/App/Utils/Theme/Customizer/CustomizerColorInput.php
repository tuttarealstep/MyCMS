<?php
    namespace MyCMS\App\Utils\Theme\Customizer;

    if (!defined("MY_CMS_PATH")) {
        die("NO SCRIPT");
    }

    class CustomizerColorInput extends CustomizerItem
    {
        function __construct($id, $subItemId, $values)
        {
            $this->type = "color";
            $this->elementClass = "colorPicker";
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
                <input class="<?php echo $this->elementClass ?>" type="color" value="<?php echo $this->value ?>"
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