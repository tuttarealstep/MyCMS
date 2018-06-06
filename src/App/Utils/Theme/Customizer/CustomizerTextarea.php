<?php
/**
 * User: tuttarealstep
 * Date: 06/06/18
 * Time: 9.15
 */

namespace MyCMS\App\Utils\Theme\Customizer;

class CustomizerTextarea extends CustomizerItem
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
                <textarea class="<?php echo $this->elementClass ?>"
                       id="<?php echo $this->id ?>" <?php
                if (!empty($this->customArgs) && is_array($this->customArgs)) {
                    foreach ($this->customArgs as $argName => $value) {
                        echo $argName . '="' . $value . '"';
                    }
                }
                ?>><?php echo $this->value ?></textarea>
            </span>
        </div>
        <?php
    }

    public function getSubMenuValues()
    {
        return ['subMenuId' => $this->subItemId, "callBack" => [$this, "buildHtmlCallBack"]];
    }
}