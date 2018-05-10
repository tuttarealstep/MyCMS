<?php
/**
 * User: tuttarealstep
 * Date: 21/01/17
 * Time: 10.58
 */

namespace MyCMS\App\Utils\Theme\Customizer;

class CustomizerSimpleSelect extends CustomizerItem
{
    function __construct($id, $subItemId, $values)
    {
        if (isset($values["options"])) {
            $this->options = $values["options"];
        }
        parent::__construct($id, $subItemId, $values);
    }

    public function buildHtmlCallBack()
    {
        ?>
        <div class="info_div" style="margin-bottom: 10px;">
            <span class="small_text">
            <?php echo $this->label ?>:<br>
                <?php if (!empty($this->description)) { ?>
                    <span class="subItemDescription"><?php echo $this->description; ?></span>
                <?php } ?>
                <select class="<?php echo $this->elementClass ?>"
                       id="<?php echo $this->id ?>" <?php
                if (!empty($this->customArgs) && is_array($this->customArgs)) {
                    foreach ($this->customArgs as $argName => $value) {
                        echo $argName . '="' . $value . '"';
                    }
                }
                ?>>
                    <?php
                if (!empty($this->options) && is_array($this->options)) {
                    foreach ($this->options as $optionName=> $optionValue) {
                        echo "<option value='$optionValue'>$optionName</option>";
                    }
                }
                ?>
                </select>
            </span>
        </div>
        <?php
    }

    public function getSubMenuValues()
    {
        return ['subMenuId' => $this->subItemId, "callBack" => [$this, "buildHtmlCallBack"]];
    }
}