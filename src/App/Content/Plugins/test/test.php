<?php
$test = $this->container;
$this->container['plugins']->addEvent('admin_notice', function () use ($test)
{
    if($test['router']->match()['params']['page'] == "plugins")
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
    }
});
?>