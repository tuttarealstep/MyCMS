<?php
/**
 * MyCMS 6
 *
 * New BETA installer
 */

ini_set("display_errors", 1);
error_reporting(E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING);

ini_set('max_execution_time', 300);
set_time_limit(0);

if (file_exists(dirname(__FILE__) . "/../../Configuration/my_config.php"))
{
    header("location: /");
    exit;
}

session_start();
$_SESSION['MY_CMS_INSTALLER'] = true;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name='robots' content='noindex,follow'/>
    <title>MyCMS - Installer</title>
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/app.css" rel="stylesheet">
</head>
<body>
<div class="container" id="step1">
    <div class="row" style="margin-bottom: 20px">
        <div class="col-md-6 col-md-offset-3 text-center">
            <h1>MyCMS</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-md-offset-3 beautifulDiv">
            <p>Welcome to the installation of <b>MyCMS</b>!<br>
                You will need to complete simple steps, everything else does MyCMS!</p>
            <br>
            <p>Before getting started, we need some information on the database.</p>
            <ol>
                <li>Database name</li>
                <li>Database username</li>
                <li>Database password</li>
                <li>Database host</li>
            </ol>
            <p>If you don’t have this information, you will need contact your Web Host before you can continue.</p>
            <p><b>Don't leave the page or refresh it during the installation</b></p>
            <hr>
            <p class="pull-left">This step will be simple <span class="glyphicon glyphicon-arrow-right"
                                                                aria-hidden="true"></span></p><a
                    class="btn btn-install pull-right" id="goToStep2Button">Next
                Step</a><br><br>
        </div>
    </div>
</div>

<div class="container" id="step2">
    <div class="row" style="margin-bottom: 20px">
        <div class="col-md-6 col-md-offset-3 text-center">
            <h1>MyCMS</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-md-offset-3 beautifulDiv ">
            <p>Basic settings to connect to the database!</p>
            <span class="label label-danger">*<b>This file needs permission for read and write.</b></span>
            <span class="label label-danger">*<b>Only for backup and update the cms write on your web directory!</b></span>
            <br><br>
            <div class="infoSecondStep">

            </div>
            <div class="row secondStepContent">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="config_host">Host</label>
                        <input type="text" name="config_host" id="config_host" class="form-control"
                               value="localhost"
                               required>
                    </div>
                    <div class="form-group">
                        <label for="config_user">User</label>
                        <input type="text" name="config_user" id="config_user" class="form-control" value="root"
                               required>
                    </div>
                    <div class="form-group">
                        <label for="config_password">Password</label>
                        <input type="password" name="config_password" id="config_password" class="form-control"
                               value="">
                    </div>
                    <div class="form-group">
                        <label for="config_database">Database Name</label>
                        <input type="text" name="config_database" id="config_database" class="form-control"
                               value="my_cms"
                               required>
                    </div>
                    <br>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="site_url_db">Site Url</label><br>
                        <input type="text" name="site_url_db" id="site_url_db" class="form-control"
                               value="http://localhost"
                               required>
                        <small>*Enter the address of your website without "/" final.<br> Ex: http://localhost
                        </small>
                    </div>
                </div>
            </div>
            <hr>
            <p class="pull-left">Next <span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span></p>
            <button name="config_button" class="btn btn-install pull-right" id="goToStep3Button">Next Step</button>
            <br><br>
        </div>
    </div>
</div>

<div class="container" id="step3">
    <div class="row" style="margin-bottom: 20px">
        <div class="col-md-6 col-md-offset-3 text-center">
            <h1>MyCMS</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-md-offset-3 beautifulDiv">
            <p>Your admin account</p>
            <div class="infoThirdStep">

            </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="user_name">Name</label>
                        <input type="text" name="user_name" id="user_name" class="form-control"
                               value="" required>
                    </div>
                    <div class="form-group">
                        <label for="user_surname">Surname</label>
                        <input type="text" name="user_surname" id="user_surname" class="form-control"
                               value="" required>
                    </div>
                    <div class="form-group">
                        <label for="user_password">Password</label>
                        <input type="password" name="user_password" id="user_password" class="form-control" value=""
                               required>
                    </div>
                    <div class="form-group">
                        <label for="user_confirm_password">Confirm Password</label>
                        <input type="password" name="user_confirm_password" id="user_confirm_password" class="form-control"
                               value=""
                               required>
                    </div>
                    <div class="form-group">
                        <label for="user_mail">Mail</label>
                        <input type="email" name="user_mail" id="user_mail" class="form-control"
                               value="" required>
                    </div>
                </div>
                <div class="col-md-12">
                    <br>
                    <hr>
                    <p class="pull-left">Next <span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span></p>
                    <button name="config_button" class="btn btn-install pull-right" id="goToStep4Button">Next Step</button>
                    <br><br>
                </div>
        </div>
    </div>
</div>


<div class="container" id="step4">
    <div class="row" style="margin-bottom: 20px">
        <div class="col-md-6 col-md-offset-3 text-center">
            <h1>MyCMS</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-md-offset-3 beautifulDiv text-center">
            <h1>MyCMS installed successfully</h1><br>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <p class="pull-left">Finish <span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span></p>
                    <a class="btn btn-install pull-right" href="/">Finish</a>
                    <br><br>
                </div>
                <!-- /.col-lg-12 -->
            </div>
        </div>
        <!-- /.col-md-4 -->
    </div>
    <!-- /.row -->
</div>
<script src="./js/jquery.min.js"></script>
<script src="./js/bootstrap.js"></script>
<script>
    function changeStep(step)
    {
        switch (step)
        {
            case 1:
                $("#step1").show()
                $("#step2").hide()
                $("#step3").hide()
                $("#step4").hide()
                break;
            case 2:
                $("#step1").hide()
                $("#step2").show()
                $("#step3").hide()
                $("#step4").hide()
                break;
            case 3:
                $("#step1").hide()
                $("#step2").hide()
                $("#step3").show()
                $("#step4").hide()
                break;
            case 4:
                $("#step1").hide()
                $("#step2").hide()
                $("#step3").hide()
                $("#step4").show()
                break;
        }
    }

    $("#goToStep2Button").click(function () {
        changeStep(2);
    })

    $("#goToStep3Button").click(function () {
        let configHost = $("#config_host").val()
        let configUser = $("#config_user").val()
        let configPassword = $("#config_password").val()
        let configDatabase = $("#config_database").val()
        let configSiteUrl = $("#site_url_db").val()

        $.post("/src/App/Content/Ajax/installerAjax.php", {
            configHost: configHost,
            configUser: configUser,
            configPassword: configPassword,
            configDatabase: configDatabase,
            configSiteUrl: configSiteUrl
        }).done(function (data) {
            if(data == "true")
            {
                changeStep(3)
            }  else if( data == "false")
            {
                $(".infoSecondStep").html("<span class='label label-danger'><b>Wrong credentials! Please retry with other settings!</b></span>")
            } else {
                $(".secondStepContent").html("<div class='col-md-12'><p><b>Cannot write the config file</b></p><p>Please copy this file in /src/App/Configuration/my_config.php</p></div><pre><code id='codeEditor'></pre></code>")
                $("#codeEditor").text(data)
            }
        });
    });

    $("#goToStep4Button").click(function () {
        let user_name = $("#user_name").val()
        let user_surname = $("#user_surname").val()
        let user_password = $("#user_password").val()
        let user_confirm_password = $("#user_confirm_password").val()
        let user_mail = $("#user_mail").val()

        if(user_password != user_confirm_password)
        {
            $(".infoThirdStep").html("<span class='label label-danger'><b>Passwords don't match.</b></span>")
        } else {
            $.post("/src/App/Content/Ajax/installerAjax.php", {
                user_name: user_name,
                user_surname: user_surname,
                user_password: user_password,
                user_mail: user_mail
            }).done(function (data) {
                if(data == "true")
                 {
                    changeStep(4)
                 }  else if( data == "false")
                 {
                    $(".infoThirdStep").html("<span class='label label-danger'><b>Wrong settings! Please retry with others!</b></span>")
                 }
            });
        }


    });


    changeStep(1)
</script>
</body>
</html>
