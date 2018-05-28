<?php
$test = $this->container;
$this->container['plugins']->addEvent('admin_notice', function () use ($test)
{
    if(isset($test['router']->match()['params']['page'] ) && $test['router']->match()['params']['page'] == "plugins")
    {
        ?>
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="alert alert-success" role="alert">test attivo!</div>
                    </div>
                </div>
            </div>
        <?php
    } else {

    }
});

$this->container['plugins']->addEvent('my_page_after_header', function () use ($test)
{
        ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-success" role="alert">test attivo!</div>
                </div>
            </div>
        </div>
        <?php
});
?>